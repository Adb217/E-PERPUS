<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Novel' => 'novel',
            'Buku Mapel Kelas X' => 'mapel-x',
            'Buku Mapel Kelas XI' => 'mapel-xi',
            'Buku Mapel Kelas XII' => 'mapel-xii',
            'Kamus' => 'kamus',
            'Referensi Umum' => 'referensi-umum',
        ];

        foreach ($categories as $name => $slug) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $books = [
            ['category' => 'novel', 'title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'pages' => 529, 'rack' => 'Rak A-1', 'copies' => 3],
            ['category' => 'novel', 'title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'pages' => 535, 'rack' => 'Rak A-1', 'copies' => 2],
            ['category' => 'novel', 'title' => 'Negeri 5 Menara', 'author' => 'Ahmad Fuadi', 'pages' => 423, 'rack' => 'Rak A-2', 'copies' => 4],
            ['category' => 'mapel-x', 'title' => 'Matematika Kelas X', 'author' => 'Tim Kemendikbud', 'pages' => 210, 'rack' => 'Rak B-1', 'copies' => 10],
            ['category' => 'mapel-x', 'title' => 'Bahasa Indonesia Kelas X', 'author' => 'Tim Kemendikbud', 'pages' => 180, 'rack' => 'Rak B-1', 'copies' => 10],
            ['category' => 'mapel-xi', 'title' => 'Fisika Kelas XI', 'author' => 'Tim Kemendikbud', 'pages' => 245, 'rack' => 'Rak B-2', 'copies' => 8],
            ['category' => 'mapel-xii', 'title' => 'Ekonomi Kelas XII', 'author' => 'Tim Kemendikbud', 'pages' => 198, 'rack' => 'Rak B-3', 'copies' => 6],
            ['category' => 'kamus', 'title' => 'Kamus Besar Bahasa Indonesia', 'author' => 'Balai Pustaka', 'pages' => 1200, 'rack' => 'Rak C-1', 'copies' => 5],
            ['category' => 'kamus', 'title' => 'Kamus Inggris-Indonesia', 'author' => 'John M. Echols', 'pages' => 660, 'rack' => 'Rak C-1', 'copies' => 5],
            ['category' => 'referensi-umum', 'title' => 'Sejarah Dunia', 'author' => 'Tim Penulis', 'pages' => 340, 'rack' => 'Rak D-1', 'copies' => 3],
        ];

        foreach ($books as $index => $data) {
            $category = Category::where('slug', $data['category'])->first();

            $book = Book::create([
                'category_id' => $category->id,
                'title' => $data['title'],
                'author' => $data['author'],
                'page_count' => $data['pages'],
                'shelf_location' => $data['rack'],
                'publisher' => 'Penerbit Umum',
                'publish_year' => rand(2015, 2024),
                'language' => 'Indonesia',
            ]);

            $bookNumber = str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            for ($i = 1; $i <= $data['copies']; $i++) {
                $copyNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
                $book->copies()->create([
                    'copy_code' => "BK-{$bookNumber}-{$copyNumber}",
                ]);
            }
        }
    }
}