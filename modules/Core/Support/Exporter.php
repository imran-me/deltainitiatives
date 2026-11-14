<?php

namespace Modules\Core\Support;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams tabular data as CSV (UTF-8 with BOM so Excel shows Bangla correctly)
 * or as a native Excel .xlsx workbook.
 */
class Exporter
{
    /**
     * @param  array<int, string>  $headings
     * @param  iterable<int, array<int, mixed>>  $rows
     */
    public static function download(string $basename, array $headings, iterable $rows, string $format = 'csv'): StreamedResponse|BinaryFileResponse
    {
        $stamp = now()->setTimezone('Asia/Dhaka')->format('Y-m-d_His');

        if ($format === 'xlsx' && class_exists(\ZipArchive::class)) {
            $path = SimpleXlsxWriter::write($headings, $rows, ucfirst(str_replace('-', ' ', $basename)));

            return response()->download($path, "{$basename}_{$stamp}.xlsx", [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend();
        }

        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headings, escape: '');
            foreach ($rows as $row) {
                fputcsv($out, array_map([self::class, 'cell'], $row), escape: '');
            }
            fclose($out);
        }, "{$basename}_{$stamp}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Flatten a value for export. For CSV, values that a spreadsheet would treat as a
     * formula (=, +, -, @ …) are prefixed with an apostrophe (formula-injection guard).
     * XLSX cells are written as inline strings, which are never evaluated, so no prefix is needed.
     */
    public static function cell(mixed $value, bool $guardFormulas = true): string
    {
        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (is_bool($value)) {
            $value = $value ? 'Yes' : 'No';
        } elseif (is_array($value)) {
            $value = implode('; ', array_map(fn ($v) => is_scalar($v) ? (string) $v : json_encode($v), $value));
        }

        $value = (string) $value;

        if ($guardFormulas && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) && ! is_numeric($value)) {
            $value = "'".$value;
        }

        return $value;
    }
}
