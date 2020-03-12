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
         Route::post('customers/datatable', 'CustomerController@customersDatatable')->name('customersDatatable');
         Route::get('get-customer-detail', 'CustomerController@getCustomerDetail')->name('get-customer-detail');
         Route::get('add-customer-to-my', 'CustomerController@addCustomerToMy')->name('add-customer-to-my');
         Route::get('get-my-customer', 'CustomerController@getMyCustomer')->name('get-my-customer');
         Route::get('save-customer', 'CustomerController@saveCustomer')->name('save-customer');
         Route::get('delete-customer', 'CustomerController@deleteCustomer')->name('delete-customer');
         Route::post('import-customer', 'CustomerController@importCustomer')->name('import-customer');
         Route::get('export-customer', 'CustomerController@exportCustomer')->name('export-customer');
         Route::get('export-my-customer', 'CustomerController@exportMyCustomer')->name('export-my-customer');
        Route::post('serviced-customer-datatable', 'CustomerController@serviceCustomerDatatable')->name('serviceCustomerDatatable');

         Route::post('save-my-customer', 'CustomerController@saveMyCustomer')->name('save-my-customer');
         Route::get('customer-detail/{id?}', 'CustomerController@customerDetail')->where(['id'=>'[0-9]+'])->name('customer-detail');
         Route::get('customer-tracking', 'CustomerController@customerTracking')->name('customer-tracking');
         Route::post('post-comment-customer', 'CustomerController@postCommentCustomer')->name('post-comment-customer');
         Route::get('get-seller', 'CustomerController@getSeller')->name('get-seller');
         Route::post('move-customer', 'CustomerController@moveCustomer')->name('move-customer');
         Route::post('add-customer-note', 'CustomerController@addCustomerNote')->name('add-customer-note');
         Route::post('move-customers', 'CustomerController@moveCustomers')->name('move-customers');

         Route::get('move-customer-all', 'CustomerController@moveCustomerAll');
         Route::get('get-user-team', 'CustomerController@getUserTeam')->name('get-user-team');
         Route::get('get-customer-1', 'CustomerController@getCustomer1')->name('get_customer_1');
         Route::get('get-customer-2', 'CustomerController@getCustomer2')->name('get_customer_2');
         Route::post('move-customer-all', 'CustomerController@moveCustomersAll')->name('move-customer-all');

         Route::get('get-place-customer','CustomerController@getPlaceCustomer')->name('get-place-customer');
         Route::get('get-place-my-customer','CustomerController@getPlaceMyCustomer')->name('get_place_my_customer');
         Route::post('move-place','CustomerController@movePlace')->name('move_place');
         Route::get('get_user_form_team','CustomerController@getUserFromTeam')->name('get_user_form_team');
         Route::post('save-my-business','CustomerController@saveMyBusiness')->name('save_my_business');
         Route::get('get-import-template-customer','CustomerController@getImportTemplateCustomer')->name('get_import_template_customer');

         Route::get('/sellers-customer','CustomerController@sellerCustomer')->name('seller_customer');
         Route::post('/sellers-customer-datatable','CustomerController@sellerCustomerDatatable')->name('seller_customer_datatable');

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

        Route::get('customer-datatable', 'SmsController@dataTableCustomer')->name('marketing.customer.datatable');

    });

    Route::group(['prefix'=>'statistic', 'namespace'=>'Statistics'],function(){
        Route::group(['prefix' => 'customers'], function() {
            Route::get('/', 'CustomerController@index')->name('statisticsCustomer');
            Route::get('datatable', 'CustomerController@datatable')->name('statisticCustomerDatable');
        });
        Route::group(['prefix' => 'services'], function() {
            Route::get('/', 'ServiceController@index')->name('statisticsService');
            Route::get('datatable', 'ServiceController@datatable')->name('statisticServiceDatable');
        });
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
            Route::post('clone-update-website', 'PlaceController@cloneUpdateWebsite')->name('cloneUpdateWebsite');
            Route::get('/', 'PlaceController@index')->name('getPlaces');
            Route::get('/places-datatable', 'PlaceController@getPlacesDatatable')->name('getPlacesDatatable');
            Route::get('/users-datatable', 'PlaceController@getUsersDatatable')->name('getUsersDatatable');
            Route::post('lock-user', 'PlaceController@lockUser')->name('lockUser');


            Route::post('/change-password', 'PlaceController@changeNewPassword')->name('changeNewPassword');
            Route::get('/get-detail', 'PlaceController@getDetailPlace')->name('getDetailPlace');
            Route::get('/get-themes-datatable', 'PlaceController@getThemeDatatable')->name('getThemeDatatable');

            Route::get('/get-wp-datatable-by-place-id', 'PlaceController@getWpDatableByPlaceId')->name('getWpDatableByPlaceId');
            Route::get('/delete-value-property', 'PlaceController@deleteValueProperty')->name('deleteValueProperty');
            Route::post('/save-custom-value-property', 'PlaceController@saveCustomValueProperty')->name('saveCustomValueProperty');

            Route::get('/get-auto-template-datatable', 'PlaceController@getAutoTemplateDatatableDatatable')->name('Places.getAutoTemplateDatatable');
            Route::post('/save-auto-template', 'PlaceController@saveAutoTemplate')->name('Places.saveAutoTemplate');
            Route::get('/delete-auto-coupon', 'PlaceController@deleteAutoCoupon')->name('deleteAutoCoupon');
            Route::get('get-auto-template-by-id', 'PlaceController@getAutoTemplateById')->name('Places.getAutoTemplateById');

            Route::get('/get-service-place', 'PlaceController@getServicePlace')->name('get-service-place');
            Route::post('/save-expire-date', 'PlaceController@saveExpireDate')->name('save-expire-date');
            Route::post('/change-place-status', 'PlaceController@changePlaceStatus')->name('change_place_status');

            Route::post('save-detail', 'PlaceController@saveDetail')->name('saveDetailPlace');
            Route::get('/place-webbuilder/{place_id}', 'PlaceController@placeWebbuilder')->name('place.webbuilder');
        });

        Route::group(['prefix' => 'auto-template'], function() {
            Route::get('/', 'AutoTemplateController@index');
            Route::get('/get-auto-template-datatable', 'AutoTemplateController@getAutoTemplateDatatable')->name('getAutoTemplateDatatable');
            Route::get('/get-auto-template-by-id', 'AutoTemplateController@getAutoTemplateById')->name('getAutoTemplateById');
            Route::get('/delete-auto-template', 'AutoTemplateController@deleteAutoTemplate')->name('deleteAutoTemplate');
            Route::post('/save-auto-template', 'AutoTemplateController@saveAutoTemplate')->name('saveAutoTemplate');
            Route::get('get-services-by-place-id', 'AutoTemplateController@getServicesByPlaceId')->name('getServicesByPlaceId');
        });
        Route::group(['prefix' => 'demo-places'],function(){
            Route::get('/','DemoPlaceController@index');
            Route::get('datatable','DemoPlaceController@datatable')->name('demo_place.datatable');
            Route::get('change-demo-status','DemoPlaceController@changeDemoStatus')->name('demo_place.change_demo_status');
            Route::post('save-demo-place','DemoPlaceController@save')->name('demo_place.save');
            Route::post('delete','DemoPlaceController@delete')->name('demo_place.delete');
        });

        Route::group(['prefix' => 'app-background'], function() {
            Route::get('/', "AppBackgroundController@index")->name('appBackground');
            Route::get('/datatable', "AppBackgroundController@datatable")->name('appBackground.datatable');
            Route::post('/save', "AppBackgroundController@save")->name('appBackground.save');
            Route::post('/delete', "AppBackgroundController@delete")->name('appBackground.delete');

        });
         Route::group(['prefix' => 'web-service'], function() {
            Route::get('/', "DesignController@index")->name('web_service.index');
            Route::get('/datatable', "DesignController@datatable")->name('web_service.datatable');
            Route::post('/save', "DesignController@save")->name('web_service.save');
            Route::get('/edit/{id}', "DesignController@edit")->where(['id=>[0-9]+'])->name('web_service.edit');
            Route::post('/upload-multi-image', "DesignController@uploadMultiImages")->name('web_service.upload_multi_image');
            Route::post('/delete-service', "DesignController@deleteService")->name('web_service.delete_service');
            Route::post('/delete', "DesignController@delete")->name('web_service.delete');
            Route::get('/change-status', "DesignController@changeStatus")->name('web_service.change_status');

        });
    });
    Route::group(['prefix'=>'tools','namespace'=>'Webbuilder'],function(){

        Route::group(['prefix' => 'service-categories'],function(){
            Route::get('/','CateServiceController@index')->name('places.cateservice');
            Route::get('edit/{place_id}/{id?}','CateServiceController@edit')->where(['place_id' => '[0-9]+'])->where(['id' => '[0-9]+'])->name('places.cateservice.edit');
            Route::post('save/{id?}','CateServiceController@save')->name('places.cateservice.save');
            Route::post('delete','CateServiceController@delete')->name('places.cateservice.delete');
        });
        Route::group(['prefix' => 'services'],function(){
            Route::get('/','ServiceController@index')->name('places.services');
            Route::get('/edit/{place_id}/{id?}','ServiceController@edit')->where(['place_id' => '[0-9]+'])->where(['id' => '[0-9]+'])->name('places.service.edit');
            Route::post('/save','ServiceController@save')->name('places.service.save');
            Route::post('upload-multi-images-service','ServiceController@uploadMultiImages')->name('upload-multi-images-service');
            Route::get('remove-image-service','ServiceController@removeMultiImage')->name('remove-image-service');
            Route::get('/export','ServiceController@export')->name('places.service.export');
            Route::get('/import','ServiceController@import')->name('places.service.import');
            Route::post('/import','ServiceController@importServices')->name('places.service.post_import');
            Route::post('upload-image-service','ServiceController@uploadImageService')->name('upload-image-service');
            Route::post('/delete','ServiceController@delete')->name('places.service.delete');
            Route::post('/change-status','ServiceController@changeStatus')->name('places.service.change_status');
            Route::get('/template-import','ServiceController@templateImport')->name('places.service.template_import');

        });
        Route::group(['prefix' => 'menus'],function(){
            Route::get('/','MenuController@index')->name('places.menus');
            Route::get('/import','MenuController@import')->name('places.menus.import');
            Route::post('/import','MenuController@postImport')->name('places.menus.post_import');
            Route::get('/export','MenuController@export')->name('places.menus.export');
            Route::get('/edit/{place_id}/{id?}','MenuController@edit')->where(['place_id'=>'[0-9]+'])->where(['id' => '[0-9]+'])->name('places.menus.edit');
            Route::post('save','MenuController@save')->name('places.menus.save');
            Route::post('upload-multi-images','MenuController@uploadMultiImages')->name('upload-multi-images');
            Route::get('remove-image-menu','MenuController@removeMenu')->name('remove-image-menu');
            Route::post('/delete','MenuController@delete')->name('places.menus.delete');
            Route::get('/template-import','MenuController@templateImport')->name('places.menus.template_import');
        });
        Route::group(['prefix' => 'banners'],function(){
            Route::get('/','BannerController@index')->name('places.banners');
            Route::get('/{place_id}/{id?}','BannerController@edit')->where(['place_id'=>'[0-9]+'])->where(['id' => '[0-9]+'])->name('places.banners.edit');
            Route::post('/save','BannerController@save')->name('places.banners.save');
            Route::post('/delete','BannerController@delete')->name('places.banners.delete');
            Route::get('change-status','BannerController@changeStatus')->name('places.banners.change_status');
        });
        Route::group(['prefix' => 'socail-network'],function(){
            Route::get('/','SocialController@index')->name('places.socail_network');
            Route::get('/list','SocialController@list')->name('places.social_network.list');
            Route::post('/save','SocialController@save')->name('places.social_network.save');
        });
        Route::group(['prefix' => 'web-seo'],function(){
            Route::post('/','WebSeoController@save')->name('places.web_seo.save');
        });


    });

    Route::group(['prefix' => 'recentlog'], function() {
        Route::get('/', 'RecentLogController@index')->name('recentlog');
        Route::get('/datatable', 'RecentLogController@datatable')->name('recentlogDatatable');

        Route::get('/activity-log', 'RecentLogController@activityLog')->name('activity-log');
        Route::get('/activity-log-datatable', 'RecentLogController@activityLogDatatable')->name('activityLogDatatable');
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
        Route::post('save-service-combo', 'SetupServiceController@saveServiceCombo')->name('save-service-combo');
        Route::get('get-cs', 'SetupServiceController@getCs')->name('get-cs');
        Route::post('delete-cs', 'SetupServiceController@deleteService')->name('delete_service');

        Route::get('team-type-datatable', 'SetupTeamController@teamTypeDatatable')->name('team-type-datatable');
        Route::get('change-status-team-type', 'SetupTeamController@changeStatusTeamtype')->name('change-status-team-type');
        Route::get('add-team-type', 'SetupTeamController@addTeamType')->name('add-team-type');
        Route::get('delete-team-type', 'SetupTeamController@deleteTeamType')->name('delete-team-type');

        Route::get('setup-template-sms','SetupSmsController@setupTemplateSms')->name('setup-template-sms');
        Route::get('sms-template-datatable','SetupSmsController@smsTemplateDatatable')->name('sms-template-datatable');
        Route::post('delete-template','SetupSmsController@deleteTemplate')->name('delete-template');
        Route::post('save-template-sms','SetupSmsController@saveTemplateSms')->name('save-template-sms');
        Route::get('short-link','SetupSmsController@shortLink')->name('short_link');

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

        Route::post('delete-event','EventHolidayController@deleteEvent')->name('delete-event');
        Route::post('change-status-event','EventHolidayController@changeStatusEvent')->name('change-status-event');

        //SETTING SERVICE TYPE
        Route::get('setup-service-type','SetupServiceController@setServiceType')->name('setup-service-type');
        Route::get('service-type-datatable','SetupServiceController@serviceTypeDatatable')->name('service-type-datatable');
        Route::get('change-status-service-type','SetupServiceController@changeStatusServiceType')->name('change-status-service-type');
        Route::get('add-service-type','SetupServiceController@addServiceType')->name('add-service-type');

        //SETTING MENU
        Route::get('setup-menu','MenuController@index')->name('setup-menu');
        Route::get('setup-permission-begin','MenuController@setPermission')->name('setup-permission-begin');

        Route::get('menu','MenuController@setupMenu')->name('menu');
        Route::get('setup-permission','MenuController@permission')->name('setup-permission');

        Route::get('permission-datatable','MenuController@permissionDatatable')->name('permission-datatable');
        Route::post('change-status-permission','MenuController@changeStatusPermission')->name('change-status-permission');
        Route::get('save-permission','MenuController@savePermission')->name('save-permission');
        Route::delete('delete-permission','MenuController@deletePermission')->name('delete-permission');

        Route::group(['prefix' => 'setup-type-template'], function() {
            Route::get('/', 'SetupTypeTemplateController@index')->name('setupTypeTemplate');
            Route::get('datatable', 'SetupTypeTemplateController@getDatatable')->name('getDatatableSetupTypeTemplate');
            Route::post('save', 'SetupTypeTemplateController@save')->name('saveSetupTypeTemplate');
            Route::post('delete', 'SetupTypeTemplateController@delete')->name('deleteSetupTypeTemplate');
        });
        Route::group(['prefix' => 'setup-cskh-team'], function() {
            Route::get('/', 'SetupTeamController@indexCskh');
            Route::get('datatable-cskh', 'SetupTeamController@cskhDatatable')->name('cskh_datatable');
            Route::get('datatable-other', 'SetupTeamController@otherDatatable')->name('other_datatable');
            Route::get('datatable-teams', 'SetupTeamController@teamsDatatable')->name('teams_datatable');
            Route::get('user-cskh-datatable', 'SetupTeamController@userCskhDatatable')->name('user_cskh_datatable');
            Route::post('add-team-to-team-cskh', 'SetupTeamController@addTeamToTeamCskh')->name('add_team_to_team_cskh');
            Route::post('remove-team', 'SetupTeamController@removeTeam')->name('remove_team');
            Route::post('save', 'SetupTeamController@cskhSave');
        });

        Route::group(['prefix' => 'setup-term-service'], function() {
            Route::get('/', 'SetupTermService@index')->name('setup-term-service');
            Route::get('/datatable', 'SetupTermService@datatable')->name('setup_term_service.datatable');
            Route::post('/save', 'SetupTermService@save')->name('setup_term_sevice.save');
            Route::post('/delete', 'SetupTermService@destroy')->name('setup_term_service.delete');
            Route::get('/get-files', 'SetupTermService@getFiles')->name('setup_term_service.get_files');
            Route::post('/upload-file', 'SetupTermService@uploadFile')->name('setup_term_service.upload_file');
            Route::get('/change-status', 'SetupTermService@changeStatus')->name('setup_term_service.change_status');
        });
        //SETTING SALE TEAM
        Route::group(['prefix' => 'setup-sale-team'], function() {
            Route::get('/', 'SetupSaleTeam@index')->name('setting.sale_team.index');
            Route::get('datatable', 'SetupSaleTeam@datatable')->name('setting.sale_team.datatable');
            Route::post('save', 'SetupSaleTeam@save')->name('setting.sale_team.save');
            Route::get('datatable-team', 'SetupSaleTeam@datatableTeam')->name('setting.sale_team.datatable_teams');
            Route::post('save-team', 'SetupSaleTeam@saveTeam')->name('setting.sale_team.save_team');
        });
        Route::get('menu-app','SetupServiceController@menuApp')->name('get_menu_app');
    });

    Route::group(['prefix'=>'user'],function(){

        Route::get('list','UserController@index')->name('userList');
        Route::get('user-datatable','UserController@userDataTable')->name('user-datatable');
        Route::get('change-user-status','UserController@changeStatusUser')->name('change-user-status');

        Route::get('roles','UserController@roleList')->name('role-list');
        Route::get('role-datatable','UserController@roleDatatable')->name('role-datatable');
        Route::get('change-status-role','UserController@changeStatusRole')->name('change-status-role');
        Route::get('add-role','UserController@addRole')->name('add-role');
        Route::post('delete-role','UserController@deleteRole')->name('delete-role');

        Route::get('user-add/{id?}','UserController@userAdd')->where(['id'=>'[0-9]+'])->name('user-add');
        Route::post('user-save','UserController@userSave')->name('user-save');
        Route::post('user-delete','UserController@userDelete')->name('user-delete');
        Route::get('user-export','UserController@userExport')->name('user-export');

        Route::get('service-permission','UserController@servicePermission')->name('service-permission');
        Route::get('change-service-permission','UserController@changeServicePermission')->name('change-service-permission');

        Route::get('user-permission','UserController@userPermission')->name('user-permission');

        Route::post('change-permission-role','UserController@changePermissionRole')->name('change-permission-role');

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
        Route::get('get-data-input-form', 'OrdersController@getDataInputForm')->name('input_form.task');
        Route::get('get-status-order', 'OrdersController@getStatusOrder')->name('get_status_order');


        Route::get('payment-orders-list','OrdersController@paymentOrderList')->name('payment-order-list');
        Route::get('payment-orders/{id}','OrdersController@paymentOrder')->name('payment-order');
        Route::post('add-order','OrdersController@addOrder')->name('post-add-order');
        Route::get('payment-order-datatable', 'OrdersController@paymentOrderDatatable')->name('payment-order-datatable');
        Route::get('delivered-order-datatable', 'OrdersController@deliveredOrderDatatable')->name('delivered-order-datatable');
        Route::post('order-calling','OrdersController@orderCalling')->name('order.calling');
        Route::get('finish-call','OrdersController@finishCall')->name('order.finish_call');

        Route::group(['prefix' => 'old-order'], function() {
            Route::get('/','OldOrderController@index')->name('orders.old_order');
            Route::get('/search-business','OldOrderController@searchBusiness')->name('orders.old_order.search_business');
            Route::get('/search-customer','OldOrderController@searchCustomer')->name('orders.old_order.search_customer');
            Route::post('/save','OldOrderController@save')->name('orders.old_order.save');
        });

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
        Route::get('receiver-task-datatable', 'TaskController@receiverTaskDatatable')->name('receiver-task-datatable');
        Route::get('my-created-task-datatable', 'TaskController@myCreatedTaskDatatable')->name('my_created_task_datatable');
        Route::get('task-dashboard-datatable', 'TaskController@taskDashboardDatatable')->name('task_dashboard_datatable');
        Route::get('expired-task-dashboard-datatable', 'TaskController@taskExpiredDashboardDatatable')->name('expired_task_dashboard_datatable');

        Route::get('all-task', 'TaskController@allTask')->name('all-task');
        Route::get('all-task-datatable', 'TaskController@allTaskDatatable')->name('all-task-datatable');

        Route::get('cskh-task', 'TaskController@cskhTask')->name('cskh-task');
        Route::get('get-status-task-order', 'TaskController@getStatusTaskOrder')->name('get_status_task_order');
        Route::get('get-review', 'TaskController@getReview')->name('get_review');
        Route::post('save-review', 'TaskController@saveReview')->name('save_review');
        Route::post('update-assign-task', 'TaskController@updateAssignTask')->name('update_assign_task');
        Route::get('get_assign_to', 'TaskController@getAssignTo')->name('get_assign_to');
        Route::post('search-customer-task', 'TaskController@searchCustomerTask')->name('search_customer_task');


    });
    //confirm event
    Route::get('confirm-event', 'DashboardController@confirmEvent')->name('confirm-event');
    Route::get('confirm-birthday', 'DashboardController@confirmBirthday')->name('confirm-birthday');
    Route::get('search-customer', 'DashboardController@searchCustomer')->name('search-customer');
    Route::get('check-all-notification', 'DashboardController@checkAllNotification')->name('check-all-notification');
    Route::get('get-notification', 'DashboardController@getNotification')->name('get-notification');
    Route::get('customer-service-datatable', 'DashboardController@customerServiceDatatable')->name('customer-service-datatable');
    Route::get('review-customer-datatable', 'DashboardController@reviewDashboardDatatable')->name('datatable_dashboard_review');



    Route::group(['prefix' => 'notification'], function() {
        Route::get('/', 'NotificationController@index')->name('notification-list');
        Route::get('notification-receive-datatable', 'NotificationController@notificationReceiveDatatable')->name('notification-receive-datatable');
        Route::post('notification-mark-read', 'NotificationController@notificationMarkRead')->name('notification-mark-read');
        Route::get('notification-sent-datatable', 'NotificationController@notificationSentDatatable')->name('notification-sent-datatable');
        Route::post('send-notification', 'NotificationController@sendNotification')->name('send-notification');
        Route::get('view-notification/{id}', 'NotificationController@viewNotification')->name('view-notification');

    });

    Route::group(['prefix' => 'reports'], function() {
        Route::group(['prefix' => 'customers'], function () {
            Route::get('/', 'ReportController@customers')->name('report.customers');
            Route::get('datatable', 'ReportController@customersDataTable')->name('report.customers.datatable');
            Route::post('total', 'ReportController@customersTotal')->name('report.customers.total_customer');
            Route::get('export/{team_id?}', 'ReportController@customersExport')->name('report.customers.export');
        });
        Route::group(['prefix' => 'services'], function () {
            Route::get('/', 'ReportController@services')->name('report.services');
            Route::get('datatable', 'ReportController@servicesDataTable')->name('report.services.datatable');
            Route::get('export', 'ReportController@serviceExport')->name('report.services.export');
        });
        Route::group(['prefix' => 'sellers'], function () {
            Route::get('/', 'ReportController@sellers')->name('report.sellers');
            Route::get('datatable', 'ReportController@sellersDataTable')->name('report.sellers.datatable');
            Route::get('export', 'ReportController@sellerExport')->name('report.sellers.export');
            Route::post('get-history-call','ReportController@getHistoryCall')->name('report.sellers.call_history');
            Route::post('log-list','ReportController@logList')->name('report.sellers.log_list');
        });
        Route::group(['prefix' => 'reviews'], function () {
            Route::get('/', 'ReportController@reviews')->name('report.reviews');
            Route::get('datatable', 'ReportController@reviewsDataTable')->name('report.reviews.datatable');
            Route::get('export', 'ReportController@reviewsExport')->name('report.reviews.export');
            Route::get('review-today', 'ReportController@reviewsToday')->name('report.reviews.review_today');
        });
        Route::group(['prefix' => 'rating-customer'], function () {
            Route::get('/', 'ReportController@ratingCustomer')->name('report.rating-customer');
            Route::get('datatable', 'ReportController@ratingCustomerDataTable')->name('report.rating-customer.datatable');
            Route::get('export', 'ReportController@reviewsExport')->name('report.reviews.export');
            Route::get('review-today', 'ReportController@reviewsToday')->name('report.reviews.review_today');
        });
        Route::group(['prefix' => 'registering-customer'], function () {
            Route::get('/', 'ReportController@registeringCustomer')->name('report.registering-customer');
            Route::get('datatable', 'ReportController@registeringCustomerDataTable')->name('report.registering-customer.datatable');
            Route::get('change-status', 'ReportController@registeringCustomerChangeStatus')->name('report.registering-customer.change-status');
        });
    });
});


//Change data from csr

Route::get('transfer-user','ChangeDataController@transferUser');
Route::get('transfer-service','ChangeDataController@transferService');
Route::get('transfer-customer','ChangeDataController@transferCustomer');
Route::get('transfer-customer-status','ChangeDataController@transferCustomerStatus');
Route::get('transfer-customer-team-type','ChangeDataController@transferCustomerTeamType');


/*Route::get('add-column','ChangeDataController@addCoumn');
Route::get('remove-column','ChangeDataController@removeCoumn');

Route::get('add-slug','ChangeDataController@addSlug');*/
Route::get('add-customer-status','ChangeDataController@addCustomerStatus');
Route::get('add-customer-to-user','ChangeDataController@addCustomerToUser');
Route::get('replace-character-space','ChangeDataController@replaceCharacterSpace');
Route::get('transfer-customer-id','ChangeDataController@tranferCustomerId');
Route::get('check-customer','ChangeDataController@checkCustomer');

//
Route::get('merge-customer','ChangeDataController@mergeCustomer');
Route::get('disabled-customer','ChangeDataController@setDisabledCustomer');
Route::get('assigned-customer','ChangeDataController@setAssignedCustomer');
Route::get('serviced-customer','ChangeDataController@setServicedCustomer');
Route::get('user-order','ChangeDataController@userOrder');


Route::get('email_theme',function(){
    return view('email_theme_2');
});
// Route::get('customer-rating/{token}',function(){
//     return view('customer_rating');
// });
Route::get('customer-rating/{token}','CustomerRatingController@index');

Route::group(['prefix' => 'customer-rating'], function () {
    Route::get('/{token}', 'CustomerRatingController@index')->name('customer_rating.index');
    Route::post('/', 'CustomerRatingController@postRating')->name('customer_rating.post');
});

