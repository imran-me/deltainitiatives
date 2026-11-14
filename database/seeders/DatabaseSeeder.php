<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * php artisan db:seed
 *
 * Seeds the first Super Admin, the institutional copy (draft — edit in Admin),
 * reference lists (programme and partnership types) and SAMPLE records.
 * Sample records carry is_demo = true, show a "Sample" badge on the site and
 * can be removed in one step: Admin → Dashboard → Remove samples
 * (or: php artisan delta:purge-demo).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seeders = [
            \Modules\Users\Seeders\AdminUserSeeder::class,
            \Modules\CMS\Seeders\CMSSeeder::class,
            \Modules\Programmes\Seeders\ProgrammesSeeder::class,
            \Modules\Partnerships\Seeders\PartnershipsSeeder::class,
            \Modules\Applications\Seeders\ApplicationsSeeder::class,
            \Modules\Contact\Seeders\ContactSeeder::class,
            \Modules\IdeaPortal\Seeders\IdeaPortalSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            if (class_exists($seeder)) {
                $this->call($seeder);
            }
        }
    }
}
