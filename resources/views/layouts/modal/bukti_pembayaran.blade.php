<div id="imageModal"
    class="fixed inset-0 z-[999] hidden items-center justify-center bg-teal-900/60 backdrop-blur-sm transition-opacity duration-300"
    onclick="closeModal()">

    <div id="modalContent"
        class="relative w-full max-w-xl mx-4 bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 overflow-hidden"
        onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-teal-800">Bukti Pembayaran</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <div class="p-4 flex justify-center bg-gray-100">
            <img id="modalImage" src="" alt="Bukti" class="max-h-[70vh] rounded-lg shadow-md object-contain">
        </div>
    </div>
</div>
