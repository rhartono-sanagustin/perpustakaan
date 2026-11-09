<?php

namespace Database\Seeders;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fiksi' => 'Novel, cerpen, dan karya imajinatif lainnya.',
            'Nonfiksi' => 'Buku pengetahuan umum, esai, dan kajian populer.',
            'Sains' => 'Buku pelajaran dan pengayaan ilmu pengetahuan alam.',
            'Sejarah' => 'Buku sejarah Indonesia dan dunia.',
            'Teknologi' => 'Buku komputer, pemrograman, dan teknologi terapan.',
            'Bahasa' => 'Buku pelajaran dan pengayaan bahasa.',
            'Seni dan Budaya' => 'Buku seni, musik, tari, dan budaya.',
            'Referensi' => 'Kamus, ensiklopedia, dan buku rujukan.',
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description, 'is_active' => true]
            );
        }

        $authors = [
            'Andrea Hirata' => 'Penulis novel Laskar Pelangi asal Belitung.',
            'Pramoedya Ananta Toer' => 'Sastrawan Indonesia penulis tetralogi Buru.',
            'Dee Lestari' => 'Penulis dan penyanyi asal Bandung.',
            'Ahmad Fuadi' => 'Penulis novel Negeri 5 Menara.',
            'Habiburrahman El Shirazy' => 'Penulis novel Ayat-Ayat Cinta.',
            'Yuval Noah Harari' => 'Sejarawan dan penulis buku Sapiens.',
            'Carl Sagan' => 'Astronom dan penulis sains populer asal Amerika Serikat.',
            'David Halliday' => 'Fisikawan dan penulis buku Fisika Dasar.',
            'Robert Resnick' => 'Fisikawan dan pendidik sains.',
            'Jearl Walker' => 'Fisikawan dan penulis buku Fisika Dasar.',
            'M. C. Ricklefs' => 'Sejarawan Australia yang mendalami sejarah Indonesia.',
            'Abdul Kadir' => 'Penulis buku teknologi informasi dan pemrograman.',
            'Badan Pengembangan dan Pembinaan Bahasa' => 'Lembaga pemerintah pengelola bahasa Indonesia.',
            'Sem Cornelyus Bangun' => 'Guru dan penulis buku seni budaya.',
            'Yulianti' => 'Penulis buku pelajaran bahasa Indonesia.',
        ];

        foreach ($authors as $name => $bio) {
            Author::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'bio' => $bio]
            );
        }

        $publishers = [
            'Bentang Pustaka' => ['address' => 'Yogyakarta', 'phone' => '0274-888888', 'email' => 'info@bentangpustaka.com'],
            'Gramedia Pustaka Utama' => ['address' => 'Jakarta', 'phone' => '021-53650110', 'email' => 'info@gramedia.com'],
            'Lentera Dipantara' => ['address' => 'Jakarta', 'phone' => '021-555555', 'email' => 'info@lenteradipantara.com'],
            'Republika' => ['address' => 'Jakarta', 'phone' => '021-555666', 'email' => 'redaksi@republika.co.id'],
            'Kepustakaan Populer Gramedia' => ['address' => 'Jakarta', 'phone' => '021-53650111', 'email' => 'info@kpg.id'],
            'Erlangga' => ['address' => 'Jakarta', 'phone' => '021-555777', 'email' => 'info@erlangga.co.id'],
            'Serambi Ilmu Semesta' => ['address' => 'Jakarta', 'phone' => '021-555888', 'email' => 'info@serambi.co.id'],
            'Andi Publisher' => ['address' => 'Yogyakarta', 'phone' => '0274-777777', 'email' => 'info@andipublisher.com'],
            'Balai Pustaka' => ['address' => 'Jakarta', 'phone' => '021-555999', 'email' => 'info@balai-pustaka.co.id'],
            'Yrama Widya' => ['address' => 'Bandung', 'phone' => '022-666666', 'email' => 'info@yramawidya.co.id'],
        ];

        foreach ($publishers as $name => $data) {
            Publisher::updateOrCreate(
                ['slug' => Str::slug($name)],
                array_merge(['name' => $name], $data)
            );
        }

        $books = [
            [
                'title' => 'Laskar Pelangi',
                'slug' => 'laskar-pelangi',
                'category' => 'Fiksi',
                'publisher' => 'Bentang Pustaka',
                'authors' => ['Andrea Hirata'],
                'isbn' => '9789793062792',
                'publication_year' => 2005,
                'edition' => 'Cetakan 1',
                'summary' => 'Kisah sepuluh anak Belitung yang bersekolah di SD Muhammadiyah dengan segala keterbatasan, tetapi tetap memiliki semangat belajar dan mimpi yang besar.',
                'copies' => [
                    ['LP-001', 'Rak A-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['LP-002', 'Rak A-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['LP-003', 'Rak A-2', BookCondition::RusakRingan, BookCopyStatus::Rusak],
                ],
            ],
            [
                'title' => 'Bumi Manusia',
                'slug' => 'bumi-manusia',
                'category' => 'Fiksi',
                'publisher' => 'Lentera Dipantara',
                'authors' => ['Pramoedya Ananta Toer'],
                'isbn' => '9789799731234',
                'publication_year' => 1980,
                'edition' => 'Cetakan 7',
                'summary' => 'Novel pertama tetralogi Buru yang mengisahkan Minke, pemuda pribumi terpelajar, di tengah pergolakan sosial pada akhir masa kolonial.',
                'copies' => [
                    ['BM-001', 'Rak A-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['BM-002', 'Rak A-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Perahu Kertas',
                'slug' => 'perahu-kertas',
                'category' => 'Fiksi',
                'publisher' => 'Bentang Pustaka',
                'authors' => ['Dee Lestari'],
                'isbn' => '9786020332949',
                'publication_year' => 2009,
                'edition' => 'Cetakan 3',
                'summary' => 'Kisah Kugy dan Keenan yang dipertemukan oleh mimpi, persahabatan, dan pilihan hidup yang tidak selalu sederhana.',
                'copies' => [
                    ['PK-001', 'Rak A-3', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['PK-002', 'Rak A-3', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Negeri 5 Menara',
                'slug' => 'negeri-5-menara',
                'category' => 'Fiksi',
                'publisher' => 'Gramedia Pustaka Utama',
                'authors' => ['Ahmad Fuadi'],
                'isbn' => '9789792244866',
                'publication_year' => 2009,
                'edition' => 'Cetakan 2',
                'summary' => 'Perjalanan Alif dan sahabat-sahabatnya di pondok pesantren, dengan mantra man jadda wajada yang menguatkan mereka meraih cita-cita.',
                'copies' => [
                    ['N5M-001', 'Rak A-3', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['N5M-002', 'Rak A-4', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['N5M-003', 'Rak A-4', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Ayat-Ayat Cinta',
                'slug' => 'ayat-ayat-cinta',
                'category' => 'Fiksi',
                'publisher' => 'Republika',
                'authors' => ['Habiburrahman El Shirazy'],
                'isbn' => '9789793601656',
                'publication_year' => 2004,
                'edition' => 'Cetakan 5',
                'summary' => 'Kisah Fahri, mahasiswa Indonesia di Kairo, yang menghadapi ujian cinta, iman, dan keadilan dalam kehidupannya.',
                'copies' => [
                    ['AAC-001', 'Rak A-4', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['AAC-002', 'Rak A-5', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'slug' => 'sapiens-riwayat-singkat-umat-manusia',
                'category' => 'Nonfiksi',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'authors' => ['Yuval Noah Harari'],
                'isbn' => '9786024243336',
                'publication_year' => 2011,
                'edition' => 'Cetakan 4',
                'summary' => 'Uraian populer tentang perjalanan Homo sapiens dari pemburu-pengumpul hingga peradaban modern, termasuk revolusi kognitif, pertanian, dan sains.',
                'copies' => [
                    ['SP-001', 'Rak B-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['SP-002', 'Rak B-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Kosmos',
                'slug' => 'kosmos',
                'category' => 'Sains',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'authors' => ['Carl Sagan'],
                'isbn' => '9789799106337',
                'publication_year' => 1980,
                'edition' => 'Edisi Revisi',
                'summary' => 'Penjelajahan ilmiah tentang alam semesta, evolusi bintang, asal-usul kehidupan, dan tempat manusia di dalam kosmos.',
                'copies' => [
                    ['KS-001', 'Rak B-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['KS-002', 'Rak B-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Fisika Dasar Jilid 1',
                'slug' => 'fisika-dasar-jilid-1',
                'category' => 'Sains',
                'publisher' => 'Erlangga',
                'authors' => ['David Halliday', 'Robert Resnick', 'Jearl Walker'],
                'isbn' => '9789790158108',
                'publication_year' => 2010,
                'edition' => 'Edisi 7',
                'summary' => 'Buku teks fisika dasar yang membahas mekanika, gelombang, dan termodinamika dengan contoh soal serta penerapan konsep.',
                'copies' => [
                    ['FD-001', 'Rak B-3', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['FD-002', 'Rak B-3', BookCondition::RusakBerat, BookCopyStatus::Rusak],
                ],
            ],
            [
                'title' => 'Sejarah Indonesia Modern 1200-2008',
                'slug' => 'sejarah-indonesia-modern-1200-2008',
                'category' => 'Sejarah',
                'publisher' => 'Serambi Ilmu Semesta',
                'authors' => ['M. C. Ricklefs'],
                'isbn' => '9789791116039',
                'publication_year' => 2008,
                'edition' => 'Cetakan 2',
                'summary' => 'Rekonstruksi sejarah Indonesia dari masa kerajaan awal hingga era reformasi, ditulis dengan pendekatan kronologis dan tematis.',
                'copies' => [
                    ['SIM-001', 'Rak C-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['SIM-002', 'Rak C-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Pemrograman Web dengan PHP 7',
                'slug' => 'pemrograman-web-dengan-php-7',
                'category' => 'Teknologi',
                'publisher' => 'Andi Publisher',
                'authors' => ['Abdul Kadir'],
                'isbn' => '9786237347040',
                'publication_year' => 2019,
                'edition' => 'Edisi 1',
                'summary' => 'Panduan praktis membangun aplikasi web menggunakan PHP 7, mulai dari dasar sintaks, form, session, hingga koneksi basis data.',
                'copies' => [
                    ['PHP-001', 'Rak C-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['PHP-002', 'Rak C-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Kamus Besar Bahasa Indonesia Edisi V',
                'slug' => 'kamus-besar-bahasa-indonesia-edisi-v',
                'category' => 'Referensi',
                'publisher' => 'Balai Pustaka',
                'authors' => ['Badan Pengembangan dan Pembinaan Bahasa'],
                'isbn' => '9789792234976',
                'publication_year' => 2016,
                'edition' => 'Edisi 5',
                'summary' => 'Kamus rujukan resmi bahasa Indonesia yang memuat lema, makna, dan contoh penggunaan terkini.',
                'copies' => [
                    ['KBBI-001', 'Rak D-1', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
            [
                'title' => 'Seni Budaya untuk SMA/MA Kelas X',
                'slug' => 'seni-budaya-untuk-sma-ma-kelas-x',
                'category' => 'Seni dan Budaya',
                'publisher' => 'Yrama Widya',
                'authors' => ['Sem Cornelyus Bangun'],
                'isbn' => '9786023744001',
                'publication_year' => 2017,
                'edition' => 'Cetakan 1',
                'summary' => 'Buku pelajaran seni budaya yang membahas seni rupa, musik, tari, dan teater dalam konteks budaya Indonesia.',
                'copies' => [
                    ['SB-001', 'Rak D-2', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['SB-002', 'Rak D-2', BookCondition::Baik, BookCopyStatus::Nonaktif],
                ],
            ],
            [
                'title' => 'Bahasa Indonesia untuk SMA/MA Kelas X',
                'slug' => 'bahasa-indonesia-untuk-sma-ma-kelas-x',
                'category' => 'Bahasa',
                'publisher' => 'Yrama Widya',
                'authors' => ['Yulianti'],
                'isbn' => '9786020323107',
                'publication_year' => 2018,
                'edition' => 'Cetakan 2',
                'summary' => 'Buku pelajaran bahasa Indonesia yang mengembangkan keterampilan menyimak, membaca, menulis, dan berbicara.',
                'copies' => [
                    ['BI-001', 'Rak D-3', BookCondition::Baik, BookCopyStatus::Tersedia],
                    ['BI-002', 'Rak D-3', BookCondition::Baik, BookCopyStatus::Tersedia],
                ],
            ],
        ];

        foreach ($books as $bookData) {
            $category = Category::where('slug', Str::slug($bookData['category']))->firstOrFail();
            $publisher = Publisher::where('slug', Str::slug($bookData['publisher']))->firstOrFail();

            $book = Book::updateOrCreate(
                ['slug' => $bookData['slug']],
                [
                    'category_id' => $category->id,
                    'publisher_id' => $publisher->id,
                    'title' => $bookData['title'],
                    'isbn' => $bookData['isbn'],
                    'publication_year' => $bookData['publication_year'],
                    'edition' => $bookData['edition'],
                    'language' => 'Indonesia',
                    'summary' => $bookData['summary'],
                    'is_active' => true,
                ]
            );

            $authorIds = Author::whereIn('slug', collect($bookData['authors'])->map(fn (string $name) => Str::slug($name))->all())
                ->pluck('id')
                ->all();
            $book->authors()->sync($authorIds);

            foreach ($bookData['copies'] as [$code, $shelf, $condition, $status]) {
                BookCopy::updateOrCreate(
                    ['inventory_code' => $code],
                    [
                        'book_id' => $book->id,
                        'shelf_location' => $shelf,
                        'condition' => $condition,
                        'status' => $status,
                        'notes' => null,
                    ]
                );
            }
        }
    }
}
