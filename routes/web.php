<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 // Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@postLogin');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name("password.reset");
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['middleware' => ['auth']], function () {  
    Route::get('/edit-profile', 'UserController@index')->name('editProfile');
    Route::post('/edit-profile', 'UserController@edit');

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return "Cache is cleared";
    });
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    
    Route::group(['prefix'=>'customer', 'namespace'=>'Customer'],function(){
         Route::get('customers', 'CustomerController@listCustomer')->name('customers');
         Route::get('merchants', 'CustomerController@listMerchant')->name('merchants');
         Route::get('add', 'CustomerController@addCustomer')->name('addCustomer');
         Route::get('edit', 'CustomerController@editCustomer')->name('editCustomer');
    });
    
    Route::group(['prefix'=>'marketing', 'namespace'=>'Marketing'],function(){
         
    });
    
    Route::group(['prefix'=>'statistics', 'namespace'=>'Statistics'],function(){
         
    });
   Route::group(['prefix'=>'datasetup', 'namespace'=>'DataSetup'],function(){
         Route::get('combos', 'ComboController@index')->name('listCombo');
         Route::get('combo/add', 'ComboController@add')->name('addCombo');
         Route::get('combo/edit', 'ComboController@edit')->name('editCombo');
         Route::get('services', 'ServiceController@index')->name('listService');
         Route::get('service/add', 'ServiceController@add')->name('addService');
         Route::get('service/edit', 'ServiceController@edit')->name('editService');
         Route::get('servicedetails', 'ServiceDetailController@index')->name('listServiceDetail');
         Route::get('servicedetail/add', 'ServiceDetailController@add')->name('addServiceDetail');
         Route::get('servicedetail/edit', 'ServiceDetailController@edit')->name('editServiceDetail');
         Route::get('themes', 'ThemeController@index')->name('listTheme');
         Route::get('theme/add', 'ThemeController@add')->name('addTheme');
         Route::get('theme/edit', 'ThemeController@edit')->name('editTheme');
         Route::get('licenses', 'LicenseController@index')->name('listLicenses');
         Route::get('license/generate', 'LicenseController@generate')->name('generateLicenses');
    });
    
    Route::group(['prefix'=>'tools'],function(){
        Route::get('clonewebsite', 'ItToolsController@cloneWebsite')->name('cloneWebsite');
        Route::get('updatewebsite', 'ItToolsController@updateWebsite')->name('updateWebsite');
    });
    
    Route::get('recentlog', 'RecentLogController@index')->name('recentlog');
    Route::get('setting', 'SettingController@index')->name('setting');
    
});