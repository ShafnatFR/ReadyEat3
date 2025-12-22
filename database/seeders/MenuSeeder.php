<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // KATERING category
            [
                'name' => 'Nasi Box Premium',
                'description' => 'Nasi box lengkap dengan lauk pilihan, sayur, dan buah',
                'category' => 'Katering',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19',
                'daily_limit' => 100,
                'is_available' => true,
            ],
            [
                'name' => 'Paket Tumpeng Mini',
                'description' => 'Tumpeng kecil untuk acara spesial, lengkap dengan lauk tradisional',
                'category' => 'Katering',
                'price' => 250000,
                'image' => 'https://images.unsplash.com/photo-1596974909708-beb4d68ebd8b',
                'daily_limit' => 20,
                'is_available' => true,
            ],
            [
                'name' => 'Snack Box Meeting',
                'description' => 'Paket snack untuk meeting atau acara kantor',
                'category' => 'Katering',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1576618148400-f54bed99fcfd',
                'daily_limit' => 150,
                'is_available' => true,
            ],
            [
                'name' => 'Prasmanan Nasi Kuning',
                'description' => 'Prasmanan nasi kuning untuk 10 orang dengan berbagai lauk',
                'category' => 'Katering',
                'price' => 500000,
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836',
                'daily_limit' => 10,
                'is_available' => true,
            ],
            [
                'name' => 'Paket Rantangan Harian',
                'description' => 'Paket makan siang harian dengan menu bervariasi',
                'category' => 'Katering',
                'price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1589302168068-964664d93dc0',
                'daily_limit' => 80,
                'is_available' => true,
            ],
            [
                'name' => 'Buffet Party Package',
                'description' => 'Paket prasmanan untuk 50 orang dengan menu lengkap',
                'category' => 'Katering',
                'price' => 2500000,
                'image' => 'https://images.unsplash.com/photo-1555244162-803834f70033',
                'daily_limit' => 5,
                'is_available' => true,
            ],
            [
                'name' => 'Nasi Kotak Ayam Bakar',
                'description' => 'Nasi dengan ayam bakar, sambal, dan lalapan',
                'category' => 'Katering',
                'price' => 28000,
                'image' => 'https://images.unsplash.com/photo-1630384669230-01f0c97a1c98',
                'daily_limit' => 120,
                'is_available' => true,
            ],
            [
                'name' => 'Paket Nasi Tumpeng Besar',
                'description' => 'Tumpeng besar untuk acara syukuran atau ulang tahun',
                'category' => 'Katering',
                'price' => 800000,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d',
                'daily_limit' => 8,
                'is_available' => true,
            ],
            [
                'name' => 'Box Kue Tradisional',
                'description' => 'Kumpulan kue tradisional Indonesia dalam satu box',
                'category' => 'Katering',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35',
                'daily_limit' => 60,
                'is_available' => true,
            ],
            [
                'name' => 'Paket Sarapan Pagi',
                'description' => 'Menu sarapan sehat dengan nasi uduk, telur, dan pelengkap',
                'category' => 'Katering',
                'price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666',
                'daily_limit' => 100,
                'is_available' => true,
            ],

            // INSTANT category
            [
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng dengan telur, ayam, dan bumbu spesial',
                'category' => 'Instant',
                'price' => 18000,
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b',
                'daily_limit' => 200,
                'is_available' => true,
            ],
            [
                'name' => 'Mie Goreng Jawa',
                'description' => 'Mie goreng khas Jawa dengan sayuran segar',
                'category' => 'Instant',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246',
                'daily_limit' => 180,
                'is_available' => true,
            ],
            [
                'name' => 'Ayam Geprek Crispy',
                'description' => 'Ayam goreng crispy dengan sambal geprek level 1-5',
                'category' => 'Instant',
                'price' => 22000,
                'image' => 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6',
                'daily_limit' => 150,
                'is_available' => true,
            ],
            [
                'name' => 'Soto Ayam Lamongan',
                'description' => 'Soto ayam khas Lamongan dengan koya dan sambal',
                'category' => 'Instant',
                'price' => 16000,
                'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d',
                'daily_limit' => 120,
                'is_available' => true,
            ],
            [
                'name' => 'Burger Beef Special',
                'description' => 'Burger daging sapi dengan keju dan sayuran segar',
                'category' => 'Instant',
                'price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',
                'daily_limit' => 100,
                'is_available' => true,
            ],
            [
                'name' => 'Siomay Bandung',
                'description' => 'Siomay ikan dengan kuah kacang khas Bandung',
                'category' => 'Instant',
                'price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb',
                'daily_limit' => 140,
                'is_available' => true,
            ],
            [
                'name' => 'Pizza Mini Mozarella',
                'description' => 'Pizza mini dengan topping keju mozarella melimpah',
                'category' => 'Instant',
                'price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591',
                'daily_limit' => 80,
                'is_available' => true,
            ],
            [
                'name' => 'Bakso Komplit',
                'description' => 'Bakso sapi dengan isian dan mie, kuah hangat',
                'category' => 'Instant',
                'price' => 17000,
                'image' => 'https://images.unsplash.com/photo-1622973536968-3ead9e780960',
                'daily_limit' => 160,
                'is_available' => true,
            ],
            [
                'name' => 'Nasi Uduk Komplit',
                'description' => 'Nasi uduk dengan ayam goreng, telur, dan sambal',
                'category' => 'Instant',
                'price' => 19000,
                'image' => 'https://images.unsplash.com/photo-1603073203463-691cf0c06f3b',
                'daily_limit' => 130,
                'is_available' => true,
            ],
            [
                'name' => 'Es Teh Manis',
                'description' => 'Teh manis dingin yang menyegarkan',
                'category' => 'Instant',
                'price' => 3000,
                'image' => 'https://images.unsplash.com/photo-1556881286-fc6915169721',
                'daily_limit' => 300,
                'is_available' => true,
            ],
            [
                'name' => 'Jus Alpukat Fresh',
                'description' => 'Jus alpukat segar tanpa pengawet',
                'category' => 'Instant',
                'price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1623065422902-30a2d299bbe4',
                'daily_limit' => 90,
                'is_available' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
