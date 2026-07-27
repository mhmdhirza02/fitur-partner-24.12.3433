<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VoucherController;

// Rute khusus untuk melayani file statis assets di lingkungan serverless Vercel dengan fallback ke Supabase Cloud
Route::get('/assets/{filename}', function ($filename) {
    $path = public_path('assets/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    $rootPath = base_path('assets/' . $filename);
    if (file_exists($rootPath)) {
        return response()->file($rootPath);
    }
    // Fallback otomatis ke cloud storage Supabase jika file tidak ada di storage serverless
    $baseUrl = rtrim(env('AWS_URL', 'https://bcmuergskjegjquvrpkc.supabase.co/storage/v1/object/public'), '/');
    return redirect($baseUrl . '/posters/assets/' . $filename);
});


// Rute User Area
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');
Route::get('/checkout', [EventController::class,'checkout'])->name('checkout');
Route::post('/checkout/check-voucher', [App\Http\Controllers\CheckoutController::class, 'checkVoucher'])->name('checkout.check-voucher');
Route::get('/my-ticket/{order_id}', [EventController::class, 'ticket'])->name('ticket');
Route::get('/my-tickets', [EventController::class, 'myTickets'])->name('my-tickets');
Route::get('/checkout/{event}', [App\Http\Controllers\CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);

// ===============================
// PARTNER PROFILE (PUBLIC)
// ===============================
Route::get('/organizer/{id}', [\App\Http\Controllers\PartnerProfileController::class, 'show'])->name('partner.profile');
// ===============================
// REVIEWS
// ===============================
Route::get('/reviews/create/{order_id}', [\App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
Route::post('/reviews/{order_id}', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
// ===============================
// GOOGLE SSO
// ===============================
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleSSOController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleSSOController::class, 'callback'])->name('auth.google.callback');
// ===============================
// MIDTRANS CALLBACK
Route::get('/login', [\App\Http\Controllers\Auth\UserAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\UserAuthController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\UserAuthController::class, 'logout'])->name('logout');

Route::get('/user/register', [\App\Http\Controllers\Auth\UserAuthController::class, 'showRegistrationForm'])->name('user.register');
Route::post('/user/register', [\App\Http\Controllers\Auth\UserAuthController::class, 'register'])->name('user.register.post');

Route::get('/register', [\App\Http\Controllers\Auth\PartnerRegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\PartnerRegisterController::class, 'register'])->name('register.post');

// Grouping utama untuk prefix 'admin' dan name 'admin.'
Route::prefix('admin')->name('admin.')->group(function () {

    // 1. Rute Publik (Tidak memerlukan middleware auth)
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // 2. Rute Terproteksi (Memerlukan middleware auth & admin)
    Route::middleware(['auth', 'admin'])->group(function () {

        // Halaman Peninjauan (Pending)
        Route::get('/pending', function () {
            if (auth()->user()->role === 'superadmin' || (auth()->user()->role === 'partner' && auth()->user()->partner->is_approved)) {
                return redirect()->route('admin.dashboard');
            }
            return view('admin.pending');
        })->name('pending');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Transaksi
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

        // Resources (CRUD)
        Route::resource('events', EventAdminController::class);
        Route::post('partners/{partner}/toggle-approve', [PartnerController::class, 'toggleApprove'])->name('partners.toggle-approve');
        Route::resource('partners', PartnerController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('vouchers', VoucherController::class);
    });
});