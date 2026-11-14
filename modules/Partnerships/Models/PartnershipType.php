<?php

namespace Modules\Partnerships\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Concerns\Publishable;

class PartnershipType extends Model
{
    use Publishable;

    protected $fillable = ['name', 'slug', 'summary', 'benefits', 'icon', 'sort_order', 'is_published'];

    public function inquiries(): HasMany
    {
        return $this->hasMany(PartnerInquiry::class);
    }

    /** @return array<int, string> */
    public function benefitList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $this->benefits))));
    }

    /** @return array<int, string> id => name, for admin selects */
    public static function options(): array
    {
        return static::query()->ordered()->pluck('name', 'id')->all();
    }
}
