<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class SetIsDefaultLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $language = Language::whereName('en')->first();
        $language->update(['is_default' => true]);
    }
}
