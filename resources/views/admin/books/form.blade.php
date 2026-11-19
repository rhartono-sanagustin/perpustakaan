@extends('layouts.admin')

@section('title', $book->exists ? 'Edit Buku' : 'Tambah Buku')
@section('breadcrumb', $book->exists ? 'Buku / Edit' : 'Buku / Tambah')

@section('content')
    <x-page-header
        :title="$book->exists ? 'Edit Buku' : 'Tambah Buku'"
        :subtitle="$book->exists ? 'Perbarui data bibliografi dan status katalog.' : 'Masukkan data bibliografi buku baru.'"
    >
        <x-slot:actions>
            <flux:button variant="outline" icon="arrow-left" href="{{ route('admin.books.index') }}">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <x-form-errors title="Periksa kembali data berikut:" />

    @php($selectedAuthorIds = array_map('intval', (array) old('authors', $selectedAuthors)))

    <form method="POST" action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}" enctype="multipart/form-data" data-loading>
        @csrf
        @method($book->exists ? 'PUT' : 'POST')

        <div class="grid gap-4 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Informasi Utama</flux:heading>
                    </div>
                    <div class="grid gap-5 p-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <flux:field>
                                <flux:label for="title">Judul buku <span class="text-rose-600">*</span></flux:label>
                                <flux:input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" maxlength="255" required autofocus />
                                <flux:error name="title" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label for="category_id">Kategori <span class="text-rose-600">*</span></flux:label>
                            <flux:select id="category_id" name="category_id" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('category_id', $book->category_id) === $category->id)>
                                        {{ $category->name }}{{ $category->is_active ? '' : ' (nonaktif)' }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:error name="category_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label for="isbn">ISBN</flux:label>
                            <flux:input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" maxlength="32" placeholder="Opsional untuk koleksi lama" />
                            <flux:error name="isbn" />
                        </flux:field>

                        <div class="sm:col-span-2">
                            <flux:field>
                                <flux:label>Penulis <span class="text-rose-600">*</span></flux:label>
                                <flux:description>Pilih satu atau lebih penulis. Daftar dapat digulir.</flux:description>
                                <div class="mt-2 max-h-56 overflow-y-auto rounded-lg border border-zinc-200 p-3 dark:border-zinc-700" data-scrollable-region>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        @foreach ($authors as $author)
                                            <flux:checkbox
                                                id="author-{{ $author->id }}"
                                                name="authors[]"
                                                value="{{ $author->id }}"
                                                :checked="in_array($author->id, $selectedAuthorIds, true)"
                                                label="{{ $author->name }}"
                                            />
                                        @endforeach
                                    </div>
                                </div>
                                <flux:error name="authors" />
                            </flux:field>
                        </div>
                    </div>
                </flux:card>

                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Informasi Penerbitan</flux:heading>
                    </div>
                    <div class="grid gap-5 p-5 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <flux:field>
                                <flux:label for="publisher_id">Penerbit</flux:label>
                                <flux:select id="publisher_id" name="publisher_id">
                                    <option value="">Pilih penerbit</option>
                                    @foreach ($publishers as $publisher)
                                        <option value="{{ $publisher->id }}" @selected((int) old('publisher_id', $book->publisher_id) === $publisher->id)>
                                            {{ $publisher->name }}
                                        </option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="publisher_id" />
                            </flux:field>
                        </div>

                        <div class="sm:col-span-2">
                            <flux:field>
                                <flux:label for="publication_year">Tahun terbit</flux:label>
                                <flux:input type="number" id="publication_year" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" min="1000" max="{{ date('Y') + 1 }}" />
                                <flux:error name="publication_year" />
                            </flux:field>
                        </div>

                        <div class="sm:col-span-1">
                            <flux:field>
                                <flux:label for="edition">Edisi</flux:label>
                                <flux:input type="text" id="edition" name="edition" value="{{ old('edition', $book->edition) }}" maxlength="100" />
                                <flux:error name="edition" />
                            </flux:field>
                        </div>

                        <div class="sm:col-span-3">
                            <flux:field>
                                <flux:label for="language">Bahasa <span class="text-rose-600">*</span></flux:label>
                                <flux:input type="text" id="language" name="language" value="{{ old('language', $book->language ?: 'Indonesia') }}" maxlength="50" required />
                                <flux:error name="language" />
                            </flux:field>
                        </div>
                    </div>
                </flux:card>

                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Deskripsi dan Sampul</flux:heading>
                    </div>
                    <div class="grid gap-5 p-5 sm:grid-cols-3">
                        <div class="sm:col-span-3">
                            <flux:field>
                                <flux:label for="summary">Ringkasan</flux:label>
                                <flux:textarea id="summary" name="summary" rows="6" maxlength="5000">{{ old('summary', $book->summary) }}</flux:textarea>
                                <flux:error name="summary" />
                            </flux:field>
                        </div>

                        <div>
                            <img src="{{ $book->cover_url }}" alt="Pratinjau sampul {{ $book->title ?: 'buku' }}" class="aspect-3/4 w-full rounded-lg border border-zinc-200 object-cover dark:border-zinc-700">
                        </div>

                        <div class="sm:col-span-2">
                            <flux:field>
                                <flux:label for="cover">Gambar sampul</flux:label>
                                <flux:input type="file" id="cover" name="cover" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                                <flux:description>Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB. Rasio yang disarankan 3:4.</flux:description>
                                <flux:error name="cover" />
                            </flux:field>

                            @if ($book->cover_path)
                                <div class="mt-4">
                                    <flux:checkbox
                                        id="remove_cover"
                                        name="remove_cover"
                                        value="1"
                                        :checked="(bool) old('remove_cover')"
                                        label="Hapus sampul saat ini"
                                    />
                                </div>
                            @endif
                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="space-y-4">
                <flux:card class="p-0!">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-white/10">
                        <flux:heading size="lg" level="2">Status Katalog</flux:heading>
                    </div>
                    <div class="p-5">
                        <input type="hidden" name="is_active" value="0">
                        <flux:switch
                            id="is_active"
                            name="is_active"
                            value="1"
                            :checked="(bool) old('is_active', $book->is_active ?? true)"
                            label="Tampilkan di katalog publik"
                            description="Buku yang diarsipkan tetap tersimpan beserta riwayat eksemplarnya, tetapi tidak tampil di katalog publik."
                        />
                        <flux:error name="is_active" />
                    </div>
                </flux:card>

                <flux:card>
                    <div class="flex flex-col gap-2">
                        <flux:button type="submit" variant="primary" icon="check" data-loading-text="Menyimpan...">
                            {{ $book->exists ? 'Simpan Perubahan' : 'Simpan Buku' }}
                        </flux:button>
                        <flux:button variant="outline" href="{{ route('admin.books.index') }}">
                            Batal
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>
    </form>
@endsection
