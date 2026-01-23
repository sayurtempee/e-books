<script>
    const customSwal = Swal.mixin({
        background: 'rgba(255, 255, 255, 0.85)',
        backdrop: 'rgba(20, 184, 166, 0.25)',
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
        didOpen: () => {
            const backdrop = document.querySelector('.swal2-backdrop');
            if (backdrop) {
                backdrop.style.backdropFilter = 'blur(8px)';
            }
        }
    });

    @if (session('success'))
        customSwal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            iconColor: '#14b8a6',
        });
    @endif

    @if (session('error'))
        customSwal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error')),
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            iconColor: '#f97316',
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

    {{--  Open Help Modal  --}}

    function openHelpModal() {
        customSwal.fire({
            title: 'Pusat Bantuan & QnA',
            html: `
                <div class="text-left text-sm text-gray-700 space-y-4 max-h-[60vh] overflow-y-auto px-2">
                    <p class="mb-4">Berikut adalah pertanyaan yang sering diajukan terkait penggunaan aplikasi <strong>Miimoys E-Books</strong>:</p>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-bold text-teal-700">1. Bagaimana cara membeli e-book?</h4>
                            <p>Pilih e-book yang Anda inginkan, klik tombol <strong>"Beli Sekarang"</strong>, lalu ikuti instruksi pembayaran yang tertera di layar hingga selesai.</p>
                        </div>

                        <div>
                            <h4 class="font-bold text-teal-700">2. Metode pembayaran apa saja yang tersedia?</h4>
                            <p>Kami mendukung berbagai metode pembayaran mulai dari <strong>Transfer Bank, E-Wallet (Dana, OVO, GoPay)</strong>, hingga QRIS (sesuai integrasi sistem).</p>
                        </div>

                        <div>
                            <h4 class="font-bold text-teal-700">3. Apakah e-book bisa langsung dibaca setelah bayar?</h4>
                            <p>Ya! Setelah pembayaran berhasil dikonfirmasi oleh sistem, e-book akan otomatis muncul di menu <strong>"E-book Saya"</strong> atau <strong>"Riwayat Pembelian"</strong>.</p>
                        </div>

                        <div>
                            <h4 class="font-bold text-teal-700">4. Bagaimana jika pembayaran gagal atau belum terverifikasi?</h4>
                            <p>Pastikan Anda mengunggah bukti transfer yang valid jika diminta. Jika dalam 1x24 jam status belum berubah, silakan hubungi admin melalui kontak di bawah.</p>
                        </div>

                        <div>
                            <h4 class="font-bold text-teal-700">5. Apakah saya bisa menjual e-book di sini?</h4>
                            <p>Bisa. Anda harus mendaftar atau beralih akun menjadi <strong>Seller</strong>. Setelah disetujui, Anda dapat mengunggah e-book Anda sendiri.</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <p class="text-xs text-gray-500">
                        Punya pertanyaan lain? Hubungi pengembang di:
                        <br>
                        <strong class="text-teal-600">farishilmiializa@gmail.com</strong>
                    </p>
                </div>
            `,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#0d9488', // Warna teal
        });
    }

    {{--  Icons Bootstrapp untuk bagian Role SIGN Up  --}}
    const roleSelect = document.querySelector('select[name="role"]');
    const roleIcon = document.getElementById('roleIcon');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'seller') {
            roleIcon.innerHTML = '<i class="bi bi-briefcase text-lg"></i>';
        } else if (this.value === 'buyer') {
            roleIcon.innerHTML = '<i class="bi bi-person-check text-lg"></i>';
        }
    });

    {{--  Modal Script (Group)  --}}

    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
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

    {{--  Modal Bukti Pembayaran  --}}

    function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');

        modalImg.src = imageSrc;
        modal.classList.remove('hidden');

        // Mencegah scrolling pada body saat modal buka
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');

        // Mengembalikan scrolling body
        document.body.style.overflow = 'auto';
    }

    // Menutup modal dengan tombol Escape di Keyboard
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
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

    {{--  Error Message  --}}
    // 1. Alert untuk Pesan Error (dari session 'error')
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonColor: '#3085d6',
        });
    @endif

    // 2. Alert untuk Pesan Sukses (dari session 'success')
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif

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
</script>
