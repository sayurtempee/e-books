<script>
    const customSwal = Swal.mixin({
        background: 'rgba(255, 255, 255, 0.85)',
        color: '#1f2937',
        confirmButtonColor: '#14b8a6',
        cancelButtonColor: '#64748b',
        showClass: {
            popup: 'animate__animated animate__fadeInDown',
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp',
        },
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-white/40',
            title: 'text-gray-800',
            confirmButton: 'px-6 py-2 rounded-lg font-semibold',
        },
        didOpen: (popup) => {
            if (!Swal.isVisible() || !Swal.getPopup()) return;

            const backdrop = document.querySelector('.swal2-backdrop');
            if (backdrop) {
                backdrop.style.backdropFilter = 'blur(8px)';
            }
        }
    });

    @if (session('success'))
        customSwal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'success',
            title: @json(session('success')),
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            iconColor: '#14b8a6',
            customClass: {
                popup: 'rounded-xl shadow-lg'
            }
        });
    @endif

    @if (session('error'))
        customSwal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'error',
            title: @json(session('error')),
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            iconColor: '#0f766e',
            customClass: {
                popup: 'rounded-xl shadow-lg'
            }
        });
    @endif

    {{--  Script Animate Spin Slow  --}}

    window.addEventListener('load', function() {
        const loader = document.getElementById('global-loader');
        loader.classList.add('opacity-0');
        setTimeout(() => loader.remove(), 300);
    });

    {{--  Confirm Logout  --}}

    function confirmLogout() {
        customSwal.fire({
            title: 'Are you sure?',
            text: "You will be logged out.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('form[action="{{ route('logout') }}"]').submit();
            }
        });
    }

    {{--  Confirm Forget Password  --}}

    function confirmForgetPassword() {
        customSwal.fire({
            title: 'Are you sure?',
            text: "A password reset link will be sent to your email.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, send link',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('form[action="{{ route('password.email') }}"]').submit();
            }
        });
    }

    {{--  Confirm Delete Seller  --}}

    function confirmDeleteSeller(id, name) {
        customSwal.fire({
            title: 'Are you sure?',
            text: `Apakah yakin ingin menghapus "${name}" ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
        }).then((result) => {
            if (result.isConfirmed) {
                document
                    .getElementById(`deleteSellerForm-${id}`)
                    .submit();
            }
        });
    }

    {{--  Confirm Delete Buyer  --}}

    function confirmDeleteBuyer(id, name) {
        customSwal.fire({
            title: 'Are you sure?',
            text: `Apakah yakin ingin menghapus "${name}" ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
        }).then((result) => {
            if (result.isConfirmed) {
                document
                    .getElementById(`deleteBuyerForm-${id}`)
                    .submit();
            }
        });
    }

    {{--  Confirm Delete Category  --}}

    function confirmDeleteCategory(id, name) {
        customSwal.fire({
            title: 'Are you sure?',
            text: `Apakah yakin ingin menghapus Kategori "${name}" ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
        }).then((result) => {
            if (result.isConfirmed) {
                document
                    .getElementById(`deleteCategoryForm-${id}`)
                    .submit();
            }
        });
    }

    {{--  Confirm Delete Book Product  --}}

    function confirmDeleteBook(id, name) {
        customSwal.fire({
            title: 'Are you sure?',
            text: `Apakah yakin ingin menghapus Produk "${name}" ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
        }).then((result) => {
            if (result.isConfirmed) {
                document
                    .getElementById(`deleteBookForm-${id}`)
                    .submit();
            }
        });
    }

    {{-- Approval Delete Order   --}}

    function confirmDeleteOrder(id) {
        Swal.fire({
            title: 'Hapus Pesanan?',
            text: "Data akan hilang dan stok buku bertambah kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById(`deleteOrderForm-${id}`).submit();
        });
    }

    /**
     * Fungsi untuk membuka modal bukti pembayaran
     * @param {string} imageUrl - URL path gambar dari storage
     */
    function openModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const content = document.getElementById('modalContent');
        const image = document.getElementById('modalImage');

        if (!modal || !image) return; // Guard clause jika elemen tidak ada

        // Set src gambar sebelum ditampilkan
        image.src = imageUrl;

        // Tampilkan modal (menggunakan flex karena Tailwind)
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Trigger animasi (opacity dan scale)
        // Gunakan requestAnimationFrame untuk performa animasi yang lebih baik daripada setTimeout(50)
        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    }

    /**
     * Fungsi untuk menutup modal
     */
    function closeModal() {
        const modal = document.getElementById('imageModal');
        const content = document.getElementById('modalContent');
        const image = document.getElementById('modalImage');

        if (!modal || !content) return;

        // Jalankan animasi keluar
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');

        // Sembunyikan elemen setelah animasi selesai (durasi 250ms sesuai transisi Tailwind)
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // Hapus src gambar agar saat modal dibuka lagi tidak muncul gambar lama sesaat
            image.src = '';
        }, 250);
    }

    // 1. Tutup dengan tombol ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('imageModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });

    // 2. Tutup saat klik area luar (Overlay)
    // Pastikan elemen #imageModal adalah background gelapnya
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                // Jika yang diklik adalah background (bukan konten putih di tengah)
                if (e.target === modal) {
                    closeModal();
                }
            });
        }
    });

    {{--  Toogle untuk deskripsi (order)  --}}

    function toggleDescription(id, button) {
        const p = document.getElementById(`desc-${id}`);

        if (p.classList.contains('line-clamp-2')) {
            // Tampilkan semua
            p.classList.remove('line-clamp-2', 'h-10');
            button.innerText = 'Sembunyikan';
        } else {
            // Sembunyikan kembali
            p.classList.add('line-clamp-2', 'h-10');
            button.innerText = 'Baca selengkapnya';
        }
    }

    // 3. Alert untuk Validasi Laravel (dari $errors)
    @if ($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            html: `
                <ul style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
        });
    @endif


    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Nomor rekening berhasil disalin!');
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            customSwal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
            });
        @endif

        // 2. Munculkan Modal Teal jika akses ditolak (karena sedang online)
        @if ($errors->has('login'))
            customSwal.fire({
                icon: 'warning',
                iconColor: '#008080',
                title: 'Akses Ditolak',
                text: "{{ $errors->first('login') }}",
                confirmButtonText: 'Saya Mengerti',
            });
        @endif
    });
</script>
