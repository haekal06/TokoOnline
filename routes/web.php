<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RajaOngkirController;


Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('beranda');
});

Route::get('backend/beranda', [BerandaController::class, 'berandaBackend'])
    ->name('backend.beranda')
    ->middleware('auth');

Route::get('backend/login', [LoginController::class, 'loginBackend'])
    ->name('backend.login');

Route::get('/user', [UserController::class, 'index'])->name('user.index');

Route::post('backend/login', [LoginController::class, 'authenticateBackend'])
    ->name('backend.login');

Route::post('backend/logout', [LoginController::class, 'logoutBackend'])
    ->name('backend.logout');

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Route::resource('backend/user', UserController::class)->middleware('auth');
Route::resource('backend/user', UserController::class, ['as' => 'backend'])
    ->middleware('auth');

Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend'])
    ->middleware('auth');

Route::resource('backend/produk', ProdukController::class, ['as' => 'backend'])
    ->middleware('auth');

// Frontend
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

// Route untuk menambahkan foto
Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])->name('backend.foto_produk.store')->middleware('auth');
// Route untuk menghapus foto
Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])->name('backend.foto_produk.destroy')->middleware('auth');

Route::get('backend/laporan/formuser', [UserController::class, 'formUser'])
    ->name('backend.laporan.formuser')
    ->middleware('auth');

Route::post('backend/laporan/cetakuser', [UserController::class, 'cetakUser'])
    ->name('backend.laporan.cetakuser')
    ->middleware('auth');

Route::get('backend/laporan/formproduk', [ProdukController::class, 'formProduk'])
    ->name('backend.laporan.formproduk')
    ->middleware('auth');

Route::post('backend/laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])
    ->name('backend.laporan.cetakproduk')
    ->middleware('auth');

Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])
    ->name('produk.detail');

Route::get('/produk/kategori/{id}', [ProdukController::class, 'produkKategori'])
    ->name('produk.kategori');

Route::get('/produk/all', [ProdukController::class, 'produkAll'])
    ->name('produk.all');

//API Google
Route::get('/auth/redirect', [CustomerController::class, 'redirect'])
    ->name('auth.redirect');
Route::get('/auth/google/callback', [CustomerController::class, 'callback'])
    ->name('auth.callback');
// Logout
Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');

// Route untuk Customer
Route::resource('backend/customer', CustomerController::class, ['as' => 'backend'])
    ->middleware('auth');

// Group route untuk customer
Route::middleware('is.customer')->group(function () {
    // Route untuk menampilkan halaman akun customer
    Route::get('/customer/akun/{id}', [CustomerController::class, 'akun'])
        ->name('customer.akun');
    // Route untuk mengupdate data akun customer
    Route::put('/customer/updateakun/{id}', [CustomerController::class, 'updateAkun'])
        ->name('customer.updateakun');

    // Group route untuk customer
    Route::middleware('is.customer')->group(function () {
        // Route untuk menampilkan halaman akun customer
        Route::get('/customer/akun/{id}', [CustomerController::class, 'akun'])
            ->name('customer.akun');
        // Route untuk mengupdate data akun customer
        Route::put('/customer/updateakun/{id}', [CustomerController::class, 'updateAkun'])
            ->name('customer.updateakun');
        // Route untuk menambahkan produk ke keranjang
        Route::post('add-to-cart/{id}', [OrderController::class, 'addToCart'])
            ->name('order.addToCart');
        Route::get('cart', [OrderController::class, 'viewCart'])->name('order.cart');
    });

    Route::get('/list-ongkir', function () {
        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = env('RAJAONGKIR_BASE_URL');

        $response = Http::withHeaders([
            'key' => $apiKey,
        ])->get("{$baseUrl}/destination/domestic-destination", [
            'search' => 'province_id',
            'limit' => 1000,
            'offset' => 0,
        ]);

        Log::info('API Response:', $response->json());

        dd($response->json());
    });

    Route::get('/cek-ongkir', function () {
        return view('ongkir');
    });
    Route::get('/provinces', [RajaOngkirController::class, 'getProvinces']);
    Route::get('/cities', [RajaOngkirController::class, 'getCities']);
    Route::post('/cost', [RajaOngkirController::class, 'getCost']);
});
