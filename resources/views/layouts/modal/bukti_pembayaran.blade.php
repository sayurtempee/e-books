<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto transition-all duration-300 ease-in-out"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- OVERLAY (TEAL BLUR) -->
    <div class="fixed inset-0 bg-teal-900/40 backdrop-blur-md transition-opacity" onclick="closeModal()">
    </div>

    <!-- MODAL WRAPPER -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white/90 backdrop-blur-xl
                   border border-teal-100
                   rounded-2xl max-w-2xl w-full shadow-2xl
                   transform transition-all scale-100"
            onclick="event.stopPropagation()">

            <!-- HEADER -->
            <div
                class="flex items-center justify-between p-5
                       border-b border-teal-100
                       bg-teal-50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-teal-700" id="modal-title">
                    Bukti Pembayaran
                </h3>

                <button onclick="closeModal()"
                    class="p-2 rounded-full
                           text-teal-500 hover:text-teal-700
                           hover:bg-teal-100 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- CONTENT -->
            <div class="p-4 flex justify-center
                       bg-teal-50 rounded-b-2xl">
                <img id="modalImage" src="" alt="Bukti Pembayaran"
                    class="max-w-full h-auto max-h-[80vh]
                           rounded-xl shadow-inner
                           object-contain
                           border border-teal-100">
            </div>

        </div>
    </div>
</div>
