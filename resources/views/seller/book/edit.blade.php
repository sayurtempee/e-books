{{-- MODAL EDIT BOOK --}}
<div x-show="openEditBookModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">

    {{-- MODAL BOX --}}
    <div @click.away="openEditBookModal = false"
        class="relative w-full max-w-4xl max-h-[95vh] overflow-y-auto bg-white rounded-3xl p-6 md:p-8 shadow-2xl">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <i class="bi bi-pencil-square text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Edit Data Buku</h3>
                    <p class="text-xs text-gray-500">Pastikan data harga dan stok sudah benar.</p>
                </div>
            </div>
            <button @click="openEditBookModal = false" class="text-gray-400 hover:text-gray-600 transition">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('seller.book.update', $book->id) }}" method="POST" enctype="multipart/form-data"
            x-data="{
                loading: false,
                preview: null,
                capital: {{ $book->capital ?? 0 }},
                price: {{ $book->price ?? 0 }},
            
                get marginValue() {
                    if (!this.capital || this.capital <= 0) return 0;
                    let res = ((this.price - this.capital) / this.capital) * 100;
                    return res.toFixed(2);
                },
            
                get marginDisplay() {
                    return this.marginValue.toString().replace('.', ',');
                }
            }" @submit="loading = true" class="space-y-5">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- LEFT SIDE: PHOTO & DESCRIPTION --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Foto
                            Produk</label>
                        <div class="flex items-start gap-4">
                            <div class="relative group">
                                <template x-if="preview">
                                    <img :src="preview"
                                        class="w-24 h-32 object-cover rounded-xl border-2 border-teal-500 shadow-md">
                                </template>
                                <template x-if="!preview">
                                    <img src="{{ asset('storage/' . $book->photos_product) }}"
                                        class="w-24 h-32 object-cover rounded-xl border shadow-sm">
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="photos_product" accept="image/*"
                                    @change="preview = URL.createObjectURL($event.target.files[0])"
                                    class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                                <p class="mt-2 text-[10px] text-gray-400">*Format JPG, PNG, JPEG. Maks 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Deskripsi</label>
                        <textarea name="description" rows="5"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition resize-none text-sm"
                            placeholder="Tulis deskripsi buku...">{{ $book->description }}</textarea>
                    </div>
                </div>

                {{-- RIGHT SIDE: DETAILS --}}
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Judul
                                Buku</label>
                            <input type="text" name="title" required value="{{ $book->title }}"
                                class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-teal-500 outline-none text-sm">
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Kategori</label>
                            <select name="category_id" required
                                class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-teal-500 outline-none text-sm bg-white">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($book->category_id == $category->id)>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Stok Saat
                                Ini</label>
                            <input type="text" value="{{ $book->stock }} {{ $book->unit }}" disabled
                                class="w-full h-11 px-4 rounded-xl bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed text-sm font-bold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Update
                                Stok</label>
                            <div class="flex">
                                <input type="number" name="stock" min="0" required value="0"
                                    class="w-full h-11 px-4 rounded-l-xl border border-gray-200 focus:border-teal-500 outline-none text-sm">
                                <select name="unit" required
                                    class="w-24 h-11 px-2 rounded-r-xl border-y border-r border-gray-200 bg-gray-50 text-xs">
                                    <option value="pcs" @selected($book->unit === 'pcs')>pcs</option>
                                    <option value="pack" @selected($book->unit === 'pack')>pack</option>
                                    <option value="box" @selected($book->unit === 'box')>box</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Harga
                                Modal (Rp)</label>
                            <input type="number" value="{{ $book->capital }}" disabled
                                class="w-full h-11 px-4 rounded-xl bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed text-sm">
                            {{-- Hidden input agar tetap terkirim jika controller membutuhkannya --}}
                            <input type="hidden" name="capital" value="{{ $book->capital }}">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Harga
                                Jual (Rp)</label>
                            <input type="number" name="price" min="0" required x-model.number="price"
                                class="w-full h-11 px-4 rounded-xl border border-gray-200 focus:border-teal-500 outline-none text-sm font-bold text-teal-700">
                        </div>

                        <div class="col-span-2">
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Keuntungan
                                (Margin)</label>
                            <div class="relative">
                                <input type="text" readonly :value="marginDisplay + '%'"
                                    class="w-full h-11 px-4 rounded-xl bg-teal-50 border border-teal-100 text-teal-700 font-black text-sm cursor-not-allowed">
                                <input type="hidden" name="margin" :value="marginValue">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-6 border-t mt-4">
                <button type="button" @click="openEditBookModal = false"
                    class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-semibold hover:bg-gray-50 transition active:scale-95">
                    Batal
                </button>

                <button type="submit" :disabled="loading"
                    class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-teal-700 text-white font-bold shadow-lg shadow-teal-200 hover:shadow-teal-300 transition active:scale-95 disabled:opacity-50">
                    <span x-show="!loading">Update Data</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Proses...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
