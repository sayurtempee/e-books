<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth Routes (Keamanan: 10 request/menit agar tidak terlalu ketat tapi tetap aman)
Route::get('/register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.submit');

Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'loginSubmit'])->middleware('throttle:10,1')->name('login.submit');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSubmit'])->middleware('throttle:10,1')->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPasswordUpdate'])->middleware('throttle:10,1')->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Grouped routes by role (Middleware auth & user.exists)
Route::middleware(['auth', 'user.exists'])->group(function () {

    // Akun Saya (60 request/menit)
    Route::put('/my-account', [AccountController::class, 'updateAccount'])
        ->middleware('throttle:60,1')
        ->name('account.update');

    Route::get('/help', function () {
        return view('help.index');
    })->name('help.index');

    Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {

        // Unread Count
        Route::get('/unread-count', function () {
            /** @var User $user */
            $user = Auth::user();
            return response()->json([
                'count' => $user->unreadNotifications->count()
            ]);
        });

        // Menandai semua dibaca
        Route::get('/mark-all-read', function () {
            /** @var User $user */
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            return back()->with('success', 'Semua notifikasi ditandai dibaca.');
        })->name('markAllRead');

        // Menghapus semua
        Route::delete('/clear-all', function () {
            /** @var User $user */
            $user = Auth::user();
            $user->notifications()->delete();
            return back()->with('success', 'Semua riwayat telah dihapus.');
        })->name('clearAll');

        // Membaca satu notifikasi & Redirect
        Route::get('/{id}/read', function ($id) {
            /** @var User $user */
            $user = Auth::user();
            $notification = $user->notifications()->findOrFail($id);

            $notification->markAsRead();
            $targetUrl = $notification->data['url'] ?? null;

            // Logika redirect berdasarkan role jika URL kosong
            if (!$targetUrl || $targetUrl === '#' || str_contains($targetUrl, '/notifications/')) {
                return match ($user->role) {
                    'admin'  => redirect()->route('admin.dashboard'),
                    'seller' => redirect()->route('seller.dashboard'),
                    'buyer'  => redirect()->route('buyer.dashboard'),
                    default  => redirect('/'),
                };
            }

            return redirect($targetUrl);
        })->name('readSingle');

        // Menghapus satu per satu
        Route::delete('/{id}', function ($id) {
            /** @var User $user */
            $user = Auth::user();
            $user->notifications()->findOrFail($id)->delete();
            return back()->with('success', 'Notifikasi dihapus.');
        })->name('destroy');
    });

    // Chat & Message
    Route::get('/chat/{user_id?}', [MessageController::class, 'index'])->name('chat.index');
    Route::post('/chat/send/{conversation_id}', [MessageController::class, 'store'])->name('chat.send');

    // --- ADMIN ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

        // Seller Management
        Route::get('/admin/sellers', [AdminController::class, 'listSellers'])->name('admin.sellers');
        Route::post('/admin/sellers/create', [AdminController::class, 'createSeller'])->middleware('throttle:60,1')->name('admin.sellers.create');
        Route::put('/admin/sellers/update/{id}', [AdminController::class, 'updateSeller'])->middleware('throttle:60,1')->name('admin.sellers.update');
        Route::delete('/admin/sellers/{id}', [AdminController::class, 'deleteSeller'])->middleware('throttle:60,1')->name('admin.sellers.delete');

        // Buyer Management
        Route::get('/admin/buyers', [AdminController::class, 'listBuyers'])->name('admin.buyers');
        Route::post('/admin/buyers/create', [AdminController::class, 'createBuyer'])->middleware('throttle:60,1')->name('admin.buyers.create');
        Route::put('/admin/buyers/update/{id}', [AdminController::class, 'updateBuyer'])->middleware('throttle:60,1')->name('admin.buyers.update');
        Route::delete('/admin/buyers/{id}', [AdminController::class, 'deleteBuyer'])->middleware('throttle:60,1')->name('admin.buyers.delete');

        // Category Management
        Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories');
        Route::post('/admin/categories', [CategoryController::class, 'store'])->middleware('throttle:60,1')->name('admin.categories.create');
        Route::put('/admin/categories/update/{id}', [CategoryController::class, 'update'])->middleware('throttle:60,1')->name('admin.categories.update');
        Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])->middleware('throttle:60,1')->name('admin.categories.delete');
    });

    // --- SELLER ---
    Route::middleware(['role:seller'])->group(function () {
        Route::get('/seller/dashboard', [DashboardController::class, 'seller'])->name('seller.dashboard');

        // Book Management
        Route::get('/seller/book', [BookController::class, 'index'])->name('seller.book.index');
        Route::post('/seller/book', [BookController::class, 'store'])->middleware('throttle:60,1')->name('seller.book.store');
        Route::put('/seller/book/{id}', [BookController::class, 'update'])->middleware('throttle:60,1')->name('seller.book.update');
        Route::delete('/seller/book/{id}', [BookController::class, 'destroy'])->middleware('throttle:60,1')->name('seller.book.delete');

        // Approval
        Route::get('/seller/approval', [TransactionController::class, 'indexApproval'])->name('seller.approval.index');
        Route::put('/seller/approval/{orderId}', [TransactionController::class, 'updateApproval'])->name('seller.approval.update');

        // Laporan
        Route::get('/seller/reports', [ReportController::class, 'index'])->name('seller.reports.index');
        Route::get('/seller/reports/download', [ReportController::class, 'download'])->name('seller.reports.download');
    });

    // --- BUYER ---
    Route::middleware(['role:buyer'])->group(function () {
        Route::get('/buyer/dashboard', [DashboardController::class, 'buyer'])->name('buyer.dashboard');

        // Order
        Route::get('/buyer/order', [OrderController::class, 'index'])->name('buyer.orders.index');

        // Checkout
        Route::get('/buyer/checkout/confirm', [CheckoutController::class, 'confirmPage'])->name('buyer.checkout.confirm');
        Route::post('/buyer/checkout', [CheckoutController::class, 'checkout'])->middleware('throttle:60,1')->name('buyer.checkout');
        Route::get('/buyer/pay/{order}', [CheckoutController::class, 'payPage'])->name('buyer.payment.pay');

        // Payment
        Route::post('/orders/{order}/payment-upload', [PaymentController::class, 'uploadPayment'])->name('buyer.payment.upload');

        // invoice
        Route::get('/orders/invoice/{order}/download', [OrderController::class, 'downloadInvoice'])->name('buyer.invoice.download');

        // Cart (60 request per menit untuk kemudahan +/- jumlah)
        Route::get('/buyer/carts', [CartController::class, 'index'])->name('buyer.carts.index');
        Route::post('/buyer/carts', [CartController::class, 'store'])->middleware('throttle:60,1')->name('buyer.carts.store');
        Route::delete('/buyer/carts/{id}', [CartController::class, 'destroy'])->middleware('throttle:60,1')->name('buyer.carts.destroy');

        // Track Package
        Route::get('/track-package', function () {
            $items = \App\Models\OrderItem::whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            })
                ->whereIn('status', ['approved', 'shipping', 'selesai'])
                ->with(['order', 'book.user'])
                ->latest()
                ->get()
                // Dikelompokkan per Order dan per Seller
                // (Karena 1 Order bisa berisi barang dari beberapa Seller berbeda)
                ->groupBy(function ($item) {
                return $item->order_id . '-' . $item->seller_id;
                });

            return view('buyer.track_package.index', compact('items'));
        })->name('buyer.orders.tracking');
    });
});
