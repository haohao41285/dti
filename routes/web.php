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
         Route::post('save-my-customer', 'CustomerControllercustomersDatatable@saveMyCustomer')->name('save-my-customer');
         Route::get('customer-detail/{id}', 'CustomerController@customerDetail')->where(['id'=>'[0-9]+'])->name('customer-detail');
         Route::get('customer-tracking', 'CustomerController@customerTracking')->name('customer-tracking');
         Route::post('post-comment-customer', 'CustomerController@postCommentCustomer')->name('post-comment-customer');
         Route::get('get-seller', 'CustomerController@getSeller')->name('get-seller');
         Route::post('move-customer', 'CustomerController@moveCustomer')->name('move-customer');
         Route::post('add-customer-note', 'CustomerController@addCustomerNote')->name('add-customer-note');
         Route::post('move-customers', 'CustomerController@moveCustomers')->name('move-customers');

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

        Route::group(['prefix' => 'news'], function() {
            Route::get('/', 'NewsController@index')->name('news');
            Route::get('news-type-datatable', 'NewsController@getNewsTypeDatatable')->name('getNewsTypeDatatable');
            Route::get('news-datatable', 'NewsController@getNewsDatatable')->name('getNewsDatatable');
            Route::post('news-type-delete', 'NewsController@deleteNewsType')->name('deleteNewsType');
            Route::post('news-delete', 'NewsController@deleteNews')->name('deleteNews');
            Route::post('news-type-save', 'NewsController@saveNewsType')->name('saveNewsType');
            Route::post('news-save', 'NewsController@saveNews')->name('saveNews');
            Route::get('get-news-by-id', 'NewsController@getNewsbyId')->name('getNewsbyId');
        });

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

   
    Route::group(['prefix'=>'tools','namespace'=>'ItTools'],function(){

        Route::get('clonewebsite', 'ItToolsController@cloneWebsite')->name('cloneWebsite');
        Route::get('updatewebsite', 'ItToolsController@updateWebsite')->name('updateWebsite');

        Route::group(['prefix' => 'website-themes'], function() {
            Route::get('/', 'WebsiteThemeController@index')->name('getWebsiteThemes');
            Route::get('/datatable','WebsiteThemeController@datatable')->name('getDatatableWebsiteThemes');
            Route::get('/get-by-id', 'WebsiteThemeController@getById')->name('getWebsiteThemesById');
            Route::post('/save', 'WebsiteThemeController@save')->name('saveWebsiteThemes');
            Route::get('/delete', 'WebsiteThemeController@delete')->name('deleteThemes');
            Route::get('/change-status', 'WebsiteThemeController@changeStatusThemes')->name('changeStatusThemes');
        });

        Route::group(['prefix' => 'app-banners'], function() {
            Route::get('/', "AppBannerController@index")->name('getAppBanner');
            Route::get('/app-datatable', "AppBannerController@appDataTable")->name('appDataTable');
            Route::get('/app-banner-datatable', "AppBannerController@appBannerDataTable")->name('appBannerDataTable');
            Route::post('/save-app', "AppBannerController@saveApp")->name('saveApp');
            Route::post('/save-app-banner', "AppBannerController@saveAppBanner")->name('saveAppBanner');
            Route::post('/delete-app', "AppBannerController@deleteApp")->name('deleteApp');
            Route::post('/delete-app-banner', "AppBannerController@deleteAppBanner")->name('deleteAppBanner');

        });

        Route::group(['prefix' => 'website-themes-properties'], function() {
            Route::get('/list-theme-properties','WebsiteThemePropertiesController@listThemePropertiesByThemeId')->name('listThemePropertiesByThemeId');
            Route::get('/list-value-properties','WebsiteThemePropertiesController@listValueProperties')->name('listValueProperties');
            Route::post('save', "WebsiteThemePropertiesController@save")->name('saveWebsiteThemesProperty');
            Route::post('save-value-property', "WebsiteThemePropertiesController@saveValueProperties")->name('saveValueProperties');
            Route::get('edit', "WebsiteThemePropertiesController@edit")->name('editWebsiteThemesProperty');
            Route::post('delete', "WebsiteThemePropertiesController@delete")->name('deleteWebsiteThemesProperty');
            Route::post('delete-value-properties', "WebsiteThemePropertiesController@deleteValueProperties")->name('deleteValueProperties');
        });

        Route::group(['prefix' => 'places'], function() {
            Route::get('/', 'PlaceController@index')->name('getPlaces');
            Route::get('/places-datatable', 'PlaceController@getPlacesDatatable')->name('getPlacesDatatable');
            Route::get('/users-datatable', 'PlaceController@getUsersDatatable')->name('getUsersDatatable');
            Route::post('/change-password', 'PlaceController@changeNewPassword')->name('changeNewPassword');
            Route::get('/get-detail', 'PlaceController@getDetailPlace')->name('getDetailPlace');
            Route::get('/get-themes-datatable', 'PlaceController@getThemeDatatable')->name('getThemeDatatable');
        });

        Route::group(['prefix' => 'build-code'], function() {
            Route::get('/', 'BuildCodeController@index');
        });
    });

    Route::group(['prefix' => 'recentlog'], function() {
        Route::get('/', 'RecentLogController@index')->name('recentlog');
        Route::get('/datatable', 'RecentLogController@datatable')->name('recentlogDatatable');
        
    });

    Route::group(['prefix' => 'setting','namespace' => 'Setting'], function() {
        Route::get('setup-team', 'SetupTeamController@index')->name('setupTeam');
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

        Route::group(['prefix' => 'login-background'], function() {
            Route::get('/', 'SetupLoginBackground@index')->name('loginBackground');
            Route::get('/datatable', "SetupLoginBackground@datatable")->name('datatableLoginBackground');
            Route::post('/save', "SetupLoginBackground@save")->name('saveLoginBackground');
            Route::post('/delete', "SetupLoginBackground@delete")->name('deleteLoginBackground');
        });

        //SETTING EVENT HOLIDAY
        Route::get('setup-event-holiday','EventHolidayController@index')->name('setup-event-holiday');
        Route::get('event-datatable','EventHolidayController@eventDatatable')->name('event-datatable');
        Route::post('add-event','EventHolidayController@addEvent')->name('add-event');

    });

    Route::group(['prefix'=>'user'],function(){

        Route::get('list','UserController@index')->name('userList');
        Route::get('user-datatable','UserController@userDataTable')->name('user-datatable');
        Route::get('change-user-status','UserController@changeStatusUser')->name('change-user-status');

        Route::get('roles','UserController@roleList')->name('role-list');
        Route::get('role-datatable','UserController@roleDatatable')->name('role-datatable');
        Route::get('change-status-role','UserController@changeStatusRole')->name('change-status-role');
        Route::get('add-role','UserController@addRole')->name('add-role');

        Route::get('role-permission/{id}','UserController@permission')->where(['id'=>'[0-9]+'])->name('permission');
        Route::get('change-permission','UserController@changePermission')->name('change-permission');
    });

    Route::group(['prefix' => 'orders','namespace' => 'Orders'], function() {
        Route::get('/all', 'OrdersController@index');
        Route::get('my-orders', 'OrdersController@getMyOrders')->name('my-orders');
        Route::get('sellers', 'OrdersController@getSellers');
        Route::get('add/{customer_id?}', 'OrdersController@add')->where(['customer_id'=>'[0-9]+'])->name('add-order');
        Route::post('authorize','OrdersController@authorizeCreditCard')->name('authorize');
        Route::get('get-customer-infor', 'OrdersController@getCustomerInfor')->name('get-customer-infor');
        Route::get('my-order-datatable', 'OrdersController@myOrderDatatable')->name('my-order-datatable');
        Route::get('seller-order-datatable', 'OrdersController@sellerOrderDatatable')->name('seller-order-datatable');
        Route::get('view/{id?}', 'OrdersController@orderView')->where(['id'=>'[0-9]+'])->name('order-view');
        Route::get('order-tracking', 'OrdersController@orderTracking')->name('order-tracking');
        Route::get('order-service', 'OrdersController@orderService')->name('order-service');
        Route::post('submit-info-task', 'OrdersController@submitInfoTask')->name('submit-info-task');
        Route::post('change-status-order', 'OrdersController@changeStatusOrder')->name('change-status-order');
        Route::post('resend-invoice', 'OrdersController@resendInvoice')->name('resend-invoice');
        Route::get('dowload-invoice/{id}', 'OrdersController@dowloadInvoice')->name('dowload-invoice');
    });
    Route::group(['prefix' => 'task','namespace' => 'Task'], function() {
        Route::get('/', 'TaskController@index')->name('my-task');
        Route::get('my-task-datatable', 'TaskController@myTaskDatatable')->name('my-task-datatable');
        Route::post('post-comment', 'TaskController@postComment')->name('post-comment');
        Route::post('down-image', 'TaskController@downImage')->name('down-image');
        Route::get('task-detail/{id}', 'TaskController@taskDetail')->where(['id'=>'[0-9]+'])->name('task-detail');
        Route::get('task-tracking', 'TaskController@taskTracking')->name('task-tracking');
        Route::get('add/{id?}', 'TaskController@taskAdd')->name('task-add');
        Route::post('save-task', 'TaskController@saveTask')->name('save-task');
        Route::get('get-task', 'TaskController@getTask')->name('get-task');
        Route::get('get-subtask', 'TaskController@getSubtask')->name('get-subtask');
        Route::get('edit-task/{id?}', 'TaskController@editTask')->where(['id'=>'[0-9]+'])->name('edit-task');
        Route::post('send-mail-notification', 'TaskController@sendMailNotification')->name('send-mail-notification');
        Route::get('theme-mail', 'TaskController@themeMail')->name('theme-mail');
        Route::get('get-subtask', 'TaskController@getSubTask')->name('get-subtask');
    });
    //confirm event
    Route::get('confirm-event', 'DashboardController@confirmEvent')->name('confirm-event');
});
