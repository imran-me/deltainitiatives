<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Core\Concerns\HasDemoFlag;

class Article extends Model
{
    use HasDemoFlag;

    public const CATEGORIES = ['brief' => 'Policy brief', 'report' => 'Report', 'working' => 'Working paper', 'data' => 'Data note', 'explainer' => 'Explainer'];

    protected $fillable = ['slug', 'title', 'title_bn', 'category', 'area', 'tags', 'summary', 'body', 'cover_key', 'cover_path', 'author_name',
        'status_label', 'reading_minutes', 'sdgs', 'published_at', 'is_published', 'is_demo'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean'];
    }

    public function setAuthorNameAttribute($v): void
    {
        $this->attributes['author_name'] = ($v === null || trim((string) $v) === '') ? 'Delta Initiatives' : $v;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @return array<int, string> */
    public function tagList(): array
    {
        return collect(explode(',', (string) $this->tags))->map(fn ($t) => trim($t))->filter()->values()->all();
    }

    /** @return array<int, string> */
    public function summaryLines(): array
    {
        return collect(preg_split('/\r?\n/', (string) $this->summary) ?: [])->map(fn ($t) => trim($t))->filter()->values()->all();
    }

    /** @return array<int, int> */
    public function sdgList(): array
    {
        return collect(explode(',', (string) $this->sdgs))->map(fn ($n) => (int) trim($n))->filter()->values()->all();
    }

    public function categoryName(): string
    {
        return self::CATEGORIES[$this->category] ?? (string) $this->category;
    }

    public function minutes(): int
    {
        return (int) ($this->reading_minutes ?: max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200)));
    }

    /**
     * Rendered body with ids on h2 headings, plus the table of contents.
     *
     * @return array{html: string, toc: array<int, array{id: string, title: string}>}
     */
    public function rendered(): array
    {
        $html = (string) markdown($this->body);
        $toc = [];
        $html = (string) preg_replace_callback('/<h2>(.*?)<\/h2>/s', function ($m) use (&$toc) {
            $id = Str::slug(strip_tags($m[1])) ?: 'section-'.(count($toc) + 1);
            $toc[] = ['id' => $id, 'title' => strip_tags($m[1])];

            return '<h2 id="'.e($id).'">'.$m[1].'</h2>';
        }, $html);

        return ['html' => $html, 'toc' => $toc];
    }
}
