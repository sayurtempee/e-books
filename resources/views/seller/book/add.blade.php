{{-- MODAL ADD BOOK --}}
<div x-show="openAddBookModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center
           bg-gradient-to-br from-black/40 via-black/30 to-black/40
           backdrop-blur-md"
    style="display: none;">

    {{-- MODAL BOX --}}
    <div @click.away="openAddBookModal = false"
        class="relative w-full max-w-5xl h-auto max-h-[90vh] overflow-y-auto
               bg-white/90 backdrop-blur-xl
               rounded-3xl
               p-7 md:p-8
               shadow-[0_20px_60px_-15px_rgba(0,0,0,0.35)]
               ring-1 ring-white/50">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200/60">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 flex items-center justify-center
                           rounded-xl
                           bg-gradient-to-br from-teal-100 to-teal-200
                           text-teal-700 shadow-sm">
                    <i class="bi bi-journal-bookmark-fill text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 tracking-wide">
                    Tambah Buku
                </h3>
            </div>

            <button @click="openAddBookModal = false"
                class="p-2 rounded-xl text-gray-400
                       hover:text-gray-600 hover:bg-gray-100
                       active:scale-95 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('seller.book.store') }}" method="POST" enctype="multipart/form-data"
            x-data="{
                loading: false,
                preview: null,
                capital: 0,
                price: 0,

                // PROFIT (uang)
                get profit() {
                    return this.price - this.capital;
                },

                get marginValue() {
                    let cap = typeof this.capital === 'string' ?
                        parseFloat(this.capital.replace(/[^0-9]/g, '')) : this.capital;
                    let prc = typeof this.price === 'string' ?
                        parseFloat(this.price.replace(/[^0-9]/g, '')) : this.price;

                    if (cap > 0 && prc > 0) {
                        let res = ((prc - cap) / cap) * 100;
                        return res.toFixed(2);
                    }
                    return 0;
                },

                // NILAI TAMPILAN → untuk user (format Indonesia)
                get marginDisplay() {
                    return this.marginValue.toString().replace('.', ',');
                }
            }" @submit="loading = true" class="grid grid-cols-2 gap-6 text-sm text-gray-700">
            @csrf

            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            {{-- PHOTO --}}
            <div class="col-span-1">
                <label class="block mb-1 font-medium">Foto Buku</label>
                <input type="file" name="photos_product" accept="image/png,image/jpg,image/jpeg" required
                    @change="preview = URL.createObjectURL($event.target.files[0])"
                    class="w-full h-11 px-3 py-2 rounded-xl
                           border border-gray-300 bg-white
                           focus:border-teal-500 focus:ring-teal-500
                           file:mr-3 file:rounded-lg file:border-0
                           file:bg-teal-50 file:text-teal-700
                           hover:file:bg-teal-100">

                {{-- PREVIEW --}}
                <template x-if="preview">
                    <div class="mt-3">
                        <img :src="preview" alt="Preview"
                            class="w-32 h-40 object-cover rounded-xl
                                   border border-gray-200 shadow-md">
                    </div>
                </template>
            </div>

            {{-- CATEGORY --}}
            <div class="col-span-1">
                <label class="block mb-1 font-medium">Category</label>
                <select name="category_id" required
                    class="w-full h-11 px-4 rounded-xl
                           border border-gray-300
                           bg-white
                           focus:border-teal-500 focus:ring-teal-500">
                    <option value="" selected disabled>-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- TITLE --}}
            <div class="col-span-2">
                <label class="block mb-1 font-medium">Judul Buku</label>
                <input type="text" name="title" required placeholder="Judul buku"
                    class="w-full h-11 px-4 rounded-xl
                           border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- STOCK --}}
            <div class="col-span-1">
                <label class="block mb-1 font-medium">Stock</label>
                <div class="flex gap-2">
                    <input type="number" name="stock" min="0" required placeholder="Jumlah stok"
                        class="flex-1 h-11 px-4 rounded-xl
                               border border-gray-300
                               focus:border-teal-500 focus:ring-teal-500">
                    <select name="unit" required
                        class="w-28 h-11 px-3 rounded-xl
                               border border-gray-300
                               focus:border-teal-500 focus:ring-teal-500">
                        <option value="pcs">pcs</option>
                        <option value="pack">pack</option>
                        <option value="box">box</option>
                    </select>
                </div>
            </div>

            {{-- CAPITAL --}}
            <div class="col-span-1">
                <label class="block mb-1 font-medium">Harga Modal</label>
                <input type="number" name="capital" min="0" required x-model.number="capital"
                    class="w-full h-11 px-4 rounded-xl
                           border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- PRICE --}}
            <div class="col-span-1">
                <label class="block mb-1 font-medium">Harga Jual</label>
                <input type="number" name="price" min="0" required x-model.number="price"
                    class="w-full h-11 px-4 rounded-xl
                           border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500">
            </div>

            {{-- MARGIN --}}
            <div class="col-span-1">
                <label class="block mb-1 font-medium">Margin (%)</label>
                {{-- Tampilan untuk User --}}
                <input type="text" readonly :value="marginDisplay + '%'"
                    class="w-full h-11 px-4 rounded-xl bg-gray-100 border border-gray-200 text-gray-600 cursor-not-allowed">

                {{-- Nilai Asli untuk Database --}}
                <input type="hidden" name="margin" :value="marginValue">
            </div>

            {{-- DESCRIPTION --}}
            <div class="col-span-2">
                <label class="block mb-1 font-medium">Deskripsi</label>
                <textarea name="description" rows="3" required placeholder="Deskripsi buku"
                    class="w-full px-4 py-2 rounded-xl
                           border border-gray-300
                           focus:border-teal-500 focus:ring-teal-500
                           resize-none"></textarea>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-5 border-t border-gray-200/60 col-span-2">
                <button type="button" @click="openAddBookModal = false"
                    class="px-5 py-2.5 rounded-xl
                           border border-gray-300
                           text-gray-600
                           hover:bg-gray-100
                           active:scale-95 transition">
                    Cancel
                </button>

                <button type="submit" :disabled="loading"
                    class="px-6 py-2.5 rounded-xl
                           bg-gradient-to-br from-teal-600 to-teal-700
                           text-white
                           hover:from-teal-700 hover:to-teal-800
                           shadow-md
                           disabled:opacity-60 disabled:cursor-not-allowed
                           active:scale-95 transition">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
