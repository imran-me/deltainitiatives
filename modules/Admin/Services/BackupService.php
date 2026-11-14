<?php

namespace Modules\Admin\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Creates self-contained backups: a full SQL dump of the database plus every
 * private upload (idea files, CVs, brochure) and public upload (logos, photos),
 * zipped into storage/app/private/backups. Old backups are pruned.
 *
 * Complements Hostinger's own daily backups; the code itself lives in GitHub.
 */
class BackupService
{
    public const DIR = 'backups';

    public function create(): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('The PHP zip extension is required for backups.');
        }

        $disk = Storage::disk('local');
        $disk->makeDirectory(self::DIR);

        $name = 'delta-backup-'.now()->format('Y-m-d-His').'.zip';
        $zipPath = $disk->path(self::DIR.'/'.$name);
        $sqlPath = tempnam(sys_get_temp_dir(), 'dsql');

        $this->dumpDatabase($sqlPath);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create backup archive.');
        }

        $zip->addFile($sqlPath, 'database.sql');
        $zip->addFromString('README.txt', implode("\n", [
            'Delta Initiatives backup — '.now()->setTimezone('Asia/Dhaka')->toDayDateTimeString().' (BST)',
            'database.sql      Full SQL dump (import with: mysql -u USER -p DATABASE < database.sql)',
            'private/          storage/app/private (idea uploads, applications, brochure) — restore to the same path',
            'public/           storage/app/public (logos, team photos, event images) — restore to the same path',
        ]));

        $this->addDirectory($zip, storage_path('app/private'), 'private', [self::DIR]);
        $this->addDirectory($zip, storage_path('app/public'), 'public');

        $zip->close();
        @unlink($sqlPath);

        $this->prune();

        return $name;
    }

    /** @return array<int, array{name: string, size: int, created_at: Carbon}> */
    public function list(): array
    {
        $disk = Storage::disk('local');

        return collect($disk->files(self::DIR))
            ->filter(fn ($f) => str_ends_with($f, '.zip'))
            ->map(fn ($f) => [
                'name' => basename($f),
                'size' => $disk->size($f),
                'created_at' => Carbon::createFromTimestamp($disk->lastModified($f)),
            ])
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }

    public function path(string $name): string
    {
        abort_unless(preg_match('/^delta-backup-[0-9-]+\.zip$/', $name) === 1, 404);
        $path = Storage::disk('local')->path(self::DIR.'/'.$name);
        abort_unless(is_file($path), 404);

        return $path;
    }

    public function prune(): void
    {
        $keep = max(1, (int) config('admin.backups.keep', 14));

        foreach (array_slice($this->list(), $keep) as $old) {
            Storage::disk('local')->delete(self::DIR.'/'.$old['name']);
        }
    }

    protected function dumpDatabase(string $target): void
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true) && ($bin = config('admin.backups.mysqldump_path'))) {
            $cfg = $connection->getConfig();
            $process = new Process([
                $bin, '--single-transaction', '--quick', '--routines', '--no-tablespaces',
                '-h', $cfg['host'], '-P', (string) $cfg['port'], '-u', $cfg['username'], $cfg['database'],
                '--result-file='.$target,
            ], null, ['MYSQL_PWD' => (string) $cfg['password']]);
            $process->setTimeout(600)->run();

            if ($process->isSuccessful()) {
                return;
            }
        }

        $this->phpDump($target, $driver);
    }

    /** Portable dump that works on shared hosting without shell access. */
    protected function phpDump(string $target, string $driver): void
    {
        $handle = fopen($target, 'w');
        $pdo = DB::connection()->getPdo();
        $mysql = in_array($driver, ['mysql', 'mariadb'], true);

        fwrite($handle, '-- Delta Initiatives database backup '.now()->toIso8601String()."\n");
        if ($mysql) {
            fwrite($handle, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");
        }

        $tables = $mysql
            ? array_map(fn ($r) => array_values((array) $r)[0], DB::select('SHOW TABLES'))
            : array_map(fn ($r) => $r->name, DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"));

        foreach ($tables as $table) {
            $q = $mysql ? "`{$table}`" : "\"{$table}\"";

            if ($mysql) {
                $create = (array) DB::selectOne("SHOW CREATE TABLE {$q}");
                fwrite($handle, "DROP TABLE IF EXISTS {$q};\n".array_values($create)[1].";\n\n");
            } else {
                $sql = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table])->sql;
                fwrite($handle, "DROP TABLE IF EXISTS {$q};\n{$sql};\n\n");
            }

            $offset = 0;
            do {
                $rows = DB::table($table)->offset($offset)->limit(500)->get();
                foreach ($rows as $row) {
                    $values = array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), (array) $row);
                    $columns = implode(',', array_map(fn ($c) => $mysql ? "`{$c}`" : "\"{$c}\"", array_keys((array) $row)));
                    fwrite($handle, "INSERT INTO {$q} ({$columns}) VALUES (".implode(',', $values).");\n");
                }
                $offset += 500;
            } while ($rows->count() === 500);

            fwrite($handle, "\n");
        }

        if ($mysql) {
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        }

        fclose($handle);
    }

    /** @param array<int, string> $exclude top-level folder names to skip */
    protected function addDirectory(ZipArchive $zip, string $dir, string $prefix, array $exclude = []): void
    {
        if (! File::isDirectory($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($dir) + 1));
            if (in_array(explode('/', $relative)[0], $exclude, true) || $file->getFilename() === '.gitignore') {
                continue;
            }
            $zip->addFile($file->getPathname(), $prefix.'/'.$relative);
        }
    }
}
