@extends('layouts.admin')

@section('title', $book->exists ? 'Edit Buku' : 'Tambah Buku')
@section('breadcrumb', $book->exists ? 'Buku / Edit' : 'Buku / Tambah')

@section('content')
    <x-page-header
        :title="$book->exists ? 'Edit Buku' : 'Tambah Buku'"
        :subtitle="$book->exists ? 'Perbarui data bibliografi dan status katalog.' : 'Masukkan data bibliografi buku baru.'"
    >
        <x-slot:actions>
            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Periksa kembali data berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php($selectedAuthorIds = array_map('intval', (array) old('authors', $selectedAuthors)))

    <form method="POST" action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}" enctype="multipart/form-data" data-loading>
        @csrf
        @method($book->exists ? 'PUT' : 'POST')

        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Informasi Utama</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="title" class="form-label">Judul buku <span class="text-danger">*</span></label>
                                <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" class="form-control @error('title') is-invalid @enderror" maxlength="255" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $book->category_id) === $category->id)>
                                            {{ $category->name }}{{ $category->is_active ? '' : ' (nonaktif)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="form-control @error('isbn') is-invalid @enderror" maxlength="32" placeholder="Opsional untuk koleksi lama">
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="authors" class="form-label">Penulis <span class="text-danger">*</span></label>
                                <select id="authors" name="authors[]" class="form-select @error('authors') is-invalid @enderror" multiple size="7" required>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}" @selected(in_array($author->id, $selectedAuthorIds, true))>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                @error('authors')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Gunakan Ctrl (Windows) atau Cmd (macOS) untuk memilih lebih dari satu penulis.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Informasi Penerbitan</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="publisher_id" class="form-label">Penerbit</label>
                                <select id="publisher_id" name="publisher_id" class="form-select @error('publisher_id') is-invalid @enderror">
                                    <option value="">Pilih penerbit</option>
                                    @foreach ($publishers as $publisher)
                                        <option value="{{ $publisher->id }}" @selected((int) old('publisher_id', $book->publisher_id) === $publisher->id)>
                                            {{ $publisher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('publisher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="publication_year" class="form-label">Tahun terbit</label>
                                <input type="number" id="publication_year" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" class="form-control @error('publication_year') is-invalid @enderror" min="1000" max="{{ date('Y') + 1 }}">
                                @error('publication_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="edition" class="form-label">Edisi</label>
                                <input type="text" id="edition" name="edition" value="{{ old('edition', $book->edition) }}" class="form-control @error('edition') is-invalid @enderror" maxlength="100">
                                @error('edition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="language" class="form-label">Bahasa <span class="text-danger">*</span></label>
                                <input type="text" id="language" name="language" value="{{ old('language', $book->language ?: 'Indonesia') }}" class="form-control @error('language') is-invalid @enderror" maxlength="50" required>
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Deskripsi dan Sampul</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="summary" class="form-label">Ringkasan</label>
                                <textarea id="summary" name="summary" rows="6" class="form-control @error('summary') is-invalid @enderror" maxlength="5000">{{ old('summary', $book->summary) }}</textarea>
                                @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <img src="{{ $book->cover_url }}" alt="Pratinjau sampul {{ $book->title ?: 'buku' }}" class="book-cover border w-100">
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="cover" class="form-label">Gambar sampul</label>
                                <input type="file" id="cover" name="cover" class="form-control @error('cover') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                @error('cover')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB. Rasio yang disarankan 3:4.</div>

                                @if ($book->cover_path)
                                    <div class="form-check mt-3">
                                        <input type="checkbox" id="remove_cover" name="remove_cover" value="1" class="form-check-input" @checked(old('remove_cover'))>
                                        <label for="remove_cover" class="form-check-label">Hapus sampul saat ini</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Status Katalog</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $book->is_active ?? true))>
                            <label for="is_active" class="form-check-label">Tampilkan di katalog publik</label>
                        </div>
                        <div class="form-text">
                            Buku yang diarsipkan tetap tersimpan beserta riwayat eksemplarnya, tetapi tidak tampil di katalog publik.
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100" data-loading-text="Menyimpan...">
                            <i class="bi bi-save me-1" aria-hidden="true"></i>{{ $book->exists ? 'Simpan Perubahan' : 'Simpan Buku' }}
                        </button>
                        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
