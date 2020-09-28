<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::group([
    'middleware' => [
        'auth'
    ]
], function () {
    Route::get('dashboard', 'DefaultController@index')->name('dashboard');
    Route::get('profile', 'DefaultController@profile')->name('profile');
    Route::resource('/customers', 'CustomerController');
    Route::group([
        'prefix' => '/customers',
        'as' => 'customer_profile.'
    ], function () {
        Route::post('/{customer}/save_profile', 'CustomerController@saveProfile')->name('save');
        Route::post('/{customer}/change_profile_password', 'CustomerController@changePassword')->name('change_password');
        Route::post('/{customer}/change_profile_photo', 'CustomerController@changePhoto')->name('change_photo');
        Route::post('/{customer}/update_status', 'CustomerController@updateStatus')->name('update_status');
    });
    Route::resource('/traders', 'TraderController');
    Route::post('/products/{product}/update_status', 'ProductController@updateStatus')->name('products.update_status');
    Route::resource('/products', 'ProductController');
});

Route::get('get_profile_photo/{filename}/{width?}/{height?}', 'GuestController@getProfilePhoto')->name('get_profile_photo');
Route::get('get_product_photo/{filename}/{width?}/{height?}', 'GuestController@getProductPhoto')->name('get_product_photo');

/*
 * Admin Routes
 */
Route::prefix('admin')->group(function() {

    Route::middleware('auth:admin')->group(function() {
        // Dashboard
        Route::get('/', 'DashboardController@index');

        // Products
        Route::resource('/products','ProductController');

        // Orders
        Route::resource('/orders','OrderController');
        Route::get('/confirm/{id}','OrderController@confirm')->name('order.confirm');
        Route::get('/pending/{id}','OrderController@pending')->name('order.pending');

        // Users
        Route::resource('/users','UsersController');

        // Logout
        Route::get('/logout','AdminUserController@logout');

    });

    // Admin Login
    Route::get('/login', 'AdminUserController@index');
    Route::post('/login', 'AdminUserController@store');
});


/*
 * Front Routes
 */

Route::get('/', 'Front\HomeController@index');

// User Registration
Route::get('/user/register','Front\RegistrationController@index');
Route::post('/user/register','Front\RegistrationController@store');

// User Loging
Route::get('/user/login','Front\SessionsController@index');
Route::post('/user/login','Front\SessionsController@store');

// Logout
Route::get('/user/logout','Front\SessionsController@logout');

Route::get('/user/profile', 'Front\UserProfileController@index');
Route::get('/user/order/{id}','Front\UserProfileController@show');

// Cart
Route::get('/cart', 'Front\CartController@index');
Route::post('/cart','Front\CartController@store')->name('cart');
Route::patch('/cart/update/{product}','Front\CartController@update')->name('cart.update');
Route::delete('/cart/remove/{product}','Front\CartController@destroy')->name('cart.destroy');
Route::post('/cart/saveLater/{product}', 'Front\CartController@saveLater')->name('cart.saveLater');


// Save for later
Route::delete('/saveLater/destroy/{product}','Front\SaveLaterController@destroy')->name('saveLater.destroy');
Route::post('/cart/moveToCart/{product}','Front\SaveLaterController@moveToCart')->name('moveToCart');

// Checkout
Route::get('/checkout','Front\CheckoutController@index');
Route::post('/checkout','Front\CheckoutController@store')->name('checkout');

Route::get('empty', function() {
    Cart::instance('default')->destroy();
});
