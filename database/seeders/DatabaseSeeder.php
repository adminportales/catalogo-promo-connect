<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GlobalAttribute;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);
        Provider::create([
            'company' => 'For Promotional',
            'email' => 'form@promotional',
            'phone' => '4738647878',
            'contact' => 'Contacto',
            'discount' => 25
        ]);
        Provider::create([
            'company' => 'Promo Opcion',
            'email' => 'promo@opcion',
            'phone' => '4738647878',
            'contact' => 'Contacto',
            'discount' => 0
        ]);
        Provider::create([
            'company' => 'Innovation',
            'email' => 'innova@promotional',
            'phone' => '4738647878',
            'contact' => 'Contacto',
            'discount' => 0
        ]);
        GlobalAttribute::create([
            'attribute' => 'Utilidad',
            'value' => '10',
        ]);
        $category = Category::create([
            'family' => 'Sin Categoria',
            'slug' => 'sin-categoria'
        ]);
        $category->subcategories()->create([
            'subfamily' => 'Sin Subcategoria',
            'slug' => 'sin-subcategoria',
            // 'category_id' => 1
        ]);
    }
}
