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
    Route::get('/edit-profile', 'UserController@editProfile')->name('editProfile');
    Route::post('/edit-profile', 'UserController@edit');

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return "Cache is cleared";
    });
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    
    Route::group(['prefix'=>'customer', 'namespace'=>'Customer'],function(){
         Route::get('my-customers', 'CustomerController@listMyCustomer')->name('myCustomers');
         Route::get('customers', 'CustomerController@listCustomer')->name('customers');
         Route::get('merchants', 'CustomerController@listMerchant')->name('merchants');
         Route::get('add', 'CustomerController@addCustomer')->name('addCustomer');
         Route::get('edit', 'CustomerController@editCustomer')->where(['id'=>'[0-9]+'])->name('editCustomer');
         Route::get('customers/datatable', 'CustomerController@customersDatatable')->name('customersDatatable');
         Route::get('get-customer-detail', 'CustomerController@getCustomerDetail')->name('get-customer-detail');
         Route::get('add-customer-to-my', 'CustomerController@addCustomerToMy')->name('add-customer-to-my');
         Route::get('get-my-customer', 'CustomerController@getMyCustomer')->name('get-my-customer');
         Route::get('save-customer', 'CustomerController@saveCustomer')->name('save-customer');
         Route::get('delete-customer', 'CustomerController@deleteCustomer')->name('delete-customer');
         Route::post('import-customer', 'CustomerController@importCustomer')->name('import-customer');
         Route::get('export-customer', 'CustomerController@exportCustomer')->name('export-customer');
         Route::get('export-my-customer', 'CustomerController@exportMyCustomer')->name('export-my-customer');
         Route::get('order-buy/{id}', 'CustomerController@orderBuy')->where(['id'=>'[0-9]+'])->name('order-buy');

    });
    
    Route::group(['prefix'=>'marketing', 'namespace'=>'Marketing'],function(){
        Route::get('sendsms','SmsController@view');
        Route::get('get-content-template','SmsController@getContentTemplate')->name('get-content-template');
        Route::get('download-template-file','SmsController@downloadTemplateFile')->name('download-template-file');
        Route::post('send-sms','SmsController@postSendSMS')->name('post-send-sms');
        Route::get('tracking-history','SmsController@trackingHistory')->name('tracking-history');
        Route::get('tracking-history-datatable','SmsController@trackingHistoryDatatable')->name('tracking-history-datatable');
        Route::get('/sms/event-detail','SmsController@eventDetail')->name('event-detail');
        Route::get('/sms/calculate-sms','SmsController@calculateSms')->name('calculate-sms');

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
    
    Route::group(['prefix' => 'setting','namespace' => 'Setting'], function() {
        Route::get('setup-team', 'SetupTeamController@index')->name('setupTeam');
        Route::get('setup-background', 'SetupBackground@index')->name('setupBackground');
        Route::get('get-team-list', 'SetupTeamController@getTemDatatable')->name('get-team-list');
        Route::get('edit-team', 'SetupTeamController@editTeam')->name('edit-team');
        Route::get('save-team', 'SetupTeamController@saveTeam')->name('save-team');
        Route::get('delete-team', 'SetupTeamController@deleteTeam')->name('delete-team');
        Route::get('get-member-list', 'SetupTeamController@getMemberList')->name('get-member-list');
        Route::get('get-user-list', 'SetupTeamController@getUserList')->name('get-user-list');
        Route::get('remove-member-from-team', 'SetupTeamController@removeMemberFromTeam')->name('remove-member-from-team');
        Route::get('add-member-to-team', 'SetupTeamController@addMemberToTeam')->name('add-member-to-team');
        Route::get('setup-team-type', 'SetupTeamController@setupTeamType')->name('setup-team-type');
        Route::get('setup-service', 'SetupServiceController@setupService')->name('setup-service');
        Route::get('service-datatable', 'SetupServiceController@serviceDatabase')->name('service-datatable');
        Route::get('change-status-cs', 'SetupServiceController@changeStatusCs')->name('change-status-cs');
        Route::get('get-service-combo', 'SetupServiceController@getServiceCombo')->name('get-service-combo');
        Route::get('save-service-combo', 'SetupServiceController@saveServiceCombo')->name('save-service-combo');
        Route::get('get-cs', 'SetupServiceController@getCs')->name('get-cs');

        Route::get('team-type-datatable', 'SetupTeamController@teamTypeDatatable')->name('team-type-datatable');
        Route::get('change-status-team-type', 'SetupTeamController@changeStatusTeamtype')->name('change-status-team-type');
        Route::get('add-team-type', 'SetupTeamController@addTeamType')->name('add-team-type');
        Route::get('delete-team-type', 'SetupTeamController@deleteTeamType')->name('delete-team-type');

        Route::get('setup-template-sms','SetupSmsController@setupTemplateSms')->name('setup-template-sms');
        Route::get('sms-template-datatable','SetupSmsController@smsTemplateDatatable')->name('sms-template-datatable');
        Route::post('delete-template','SetupSmsController@deleteTemplate')->name('delete-template');
        Route::post('save-template-sms','SetupSmsController@saveTemplateSms')->name('save-template-sms');
    });

    Route::group(['prefix'=>'user'],function(){

        Route::get('list','UserController@index')->name('userList');
        Route::get('user-datatable','UserController@userDataTable')->name('user-datatable');
        Route::get('change-user-status','UserController@changeStatusUser')->name('change-user-status');

        Route::get('roles','UserController@roleList')->name('role-list');
        Route::get('role-datatable','UserController@roleDatatable')->name('role-datatable');
        Route::get('change-status-role','UserController@changeStatusRole')->name('change-status-role');
        Route::get('add-role','UserController@addRole')->name('add-role');

        Route::get('rolepermission/{id}','UserController@permission')->where(['id'=>'[0-9]+'])->name('permission');
        Route::get('change-permission','UserController@changePermission')->name('change-permission');
    });

    Route::group(['prefix' => 'orders','namespace' => 'Orders'], function() {
        Route::get('/all', 'OrdersController@index');
        Route::get('my-orders', 'OrdersController@getMyOrders');
        Route::get('sellers', 'OrdersController@getSellers');
        Route::get('add', 'OrdersController@add');
        Route::get('authorize','OrdersController@authorizeCreditCard')->name('authorize');
    });
    
});
