<?php
use Illuminate\Support\Facades\Route;
Route::get('/dashboard', 'Admin\DashboardController@index')->name('admin.dashboard');

Route::get("manufacturer", "Admin\ManufacturerController@index")->name('manufacturer.index');
Route::get("manufacturer/create", "Admin\ManufacturerController@create")->name('manufacturer.create');
Route::post("manufacturer/change_status", "Admin\CitiesController@change_status")->name('manufacturer.change_status');
Route::get("manufacturer/edit/{id}", "Admin\ManufacturerController@edit")->name('manufacturer.edit');
Route::post("manufacturer/update/{id}", "Admin\ManufacturerController@update")->name('manufacturer.update');
Route::post("manufacturer/delete/{id}", "Admin\ManufacturerController@delete")->name('manufacturer.delete');
Route::post("manufacturer_save", "Admin\ManufacturerController@store")->name('manufacturer.store');

Route::get('user_roles/list','Admin\UserRoleController@index')->name('user_roles.list');
Route::get('user_roles/create','Admin\UserRoleController@create')->name('user_roles.create');
Route::get('user_roles/edit/{id}','Admin\UserRoleController@create')->name('user_roles.edit');
Route::post('user_roles/submit','Admin\UserRoleController@submit')->name('user_roles.submit');
Route::post('user_roles/delete/{id}','Admin\UserRoleController@delete')->name('user_roles.delete');
Route::post('user_roles/get_role_list','Admin\UserRoleController@getroleList')->name('getRoleList');
Route::post('user_roles/status_change/{id}','Admin\UserRoleController@change_status')->name('user_roles.status_change');



Route::get("vehilce/model/create", "Admin\VehicleModelController@create");
Route::get("vehilce/model", "Admin\VehicleModelController@index");
Route::get("vehilce/model/edit/{id}", "Admin\VehicleModelController@edit");
Route::post("vehilce/model/update/{id}", "Admin\VehicleModelController@update");
Route::post("vehilce/model/delete/{id}", "Admin\VehicleModelController@destroy");
Route::post("vehilce/model/store", "Admin\VehicleModelController@store");

Route::get("vehilce/type/create", "Admin\VehicleTypeController@create");
Route::get("vehilce/type", "Admin\VehicleTypeController@index");
Route::get("vehilce/type/edit/{id}", "Admin\VehicleTypeController@edit");
Route::post("vehilce/type/update/{id}", "Admin\VehicleTypeController@update");
Route::post("vehilce/type/delete/{id}", "Admin\VehicleTypeController@destroy");
Route::post("vehilce/type/store", "Admin\VehicleTypeController@store");

Route::get("vehilce/problems/create", "Admin\VehicleEmergencyController@create");
Route::get("vehilce/problems", "Admin\VehicleEmergencyController@index");
Route::get("vehilce/problems/edit/{id}", "Admin\VehicleEmergencyController@edit");
Route::post("vehilce/problems/update/{id}", "Admin\VehicleEmergencyController@update");
Route::post("vehilce/problems/delete/{id}", "Admin\VehicleEmergencyController@destroy");
Route::post("vehilce/problems/store", "Admin\VehicleEmergencyController@store");

// Customer Types
Route::get('customer_types/list','Admin\CustomerTypeController@index')->name('customer_types.list');
Route::get('customer_types/create','Admin\CustomerTypeController@create')->name('customer_types.create');
Route::get('customer_types/edit/{id}','Admin\CustomerTypeController@create')->name('customer_types.edit');
Route::post('customer_types/submit','Admin\CustomerTypeController@submit')->name('customer_types.submit');
Route::post('customer_types/delete/{id}','Admin\CustomerTypeController@delete')->name('customer_types.delete');
Route::post('customer_types/get_type_list','Admin\CustomerTypeController@getCustomeTypeList')->name('getCustomeTypeList');
Route::post('customer_types/status_change/{id}','Admin\CustomerTypeController@change_status')->name('customer_types.status_change');

//country
Route::get('countries/list','Admin\CountryController@index')->name('countries.list');
Route::get('countries/create','Admin\CountryController@create')->name('countries.create');
Route::get('countries/edit/{id}','Admin\CountryController@create')->name('countries.edit');
Route::post('countries/submit','Admin\CountryController@submit')->name('countries.submit');
Route::post('countries/delete/{id}','Admin\CountryController@delete')->name('countries.delete');
Route::post('countries/get_country_list','Admin\CountryController@getcountryList')->name('getcountryList');
Route::post('countries/status_change/{id}','Admin\CountryController@change_status')->name('countries.status_change');

//vehicle
Route::get('vehicles/list/{user_id}','Admin\UserVehicleController@index')->name('vehicles.list');
Route::get('vehicles/create/{user_id}','Admin\UserVehicleController@create')->name('vehicles.create');
Route::get('vehicles/edit/{user_id}/{id}','Admin\UserVehicleController@create')->name('vehicles.edit');
Route::post('vehicles/submit','Admin\UserVehicleController@submit')->name('vehicles.submit');
Route::post('vehicles/delete/{id}','Admin\UserVehicleController@delete')->name('vehicles.delete');
Route::post('vehicles/get_vehicles_list/{user_id}','Admin\UserVehicleController@getvehicleList')->name('getvehicleList');


//vehicle
Route::get('driver_booking_requests/list/{booking_id}','Admin\DriverBookingRequestController@index')->name('driver_booking_requests.list');
Route::get('driver_booking_requests/create/{booking_id}','Admin\DriverBookingRequestController@create')->name('driver_booking_requests.create');
Route::get('driver_booking_requests/edit/{booking_id}/{id}','Admin\DriverBookingRequestController@create')->name('driver_booking_requests.edit');
Route::post('driver_booking_requests/submit','Admin\DriverBookingRequestController@submit')->name('driver_booking_requests.submit');
Route::post('driver_booking_requests/assign_driver/{pickup_driver}/{id}/{booking_request_id}','Admin\DriverBookingRequestController@assign_driver')->name('driver_booking_requests.assign_driver');
Route::post('driver_booking_requests/get_requests_list/{booking_id}','Admin\DriverBookingRequestController@get_requests_list')->name('get_requests_list');

//langauges
Route::get('languages/list','Admin\LanguageController@index')->name('languages.list');
Route::get('languages/create','Admin\LanguageController@create')->name('languages.create');
Route::get('languages/edit/{id}','Admin\LanguageController@create')->name('languages.edit');
Route::post('languages/submit','Admin\LanguageController@submit')->name('languages.submit');
Route::post('languages/delete/{id}','Admin\LanguageController@delete')->name('languages.delete');
Route::post('languages/get_country_list','Admin\LanguageController@getlanguageList')->name('getlanguageList');
Route::post('languages/status_change/{id}','Admin\LanguageController@change_status')->name('languages.status_change');

//Catgory
Route::get('category/list','Admin\CategoryController@index')->name('category.list');
Route::get('category/create','Admin\CategoryController@create')->name('category.create');
Route::get('category/edit/{id}','Admin\CategoryController@create')->name('category.edit');
Route::post('category/submit','Admin\CategoryController@submit')->name('category.submit');
Route::post('category/delete/{id}','Admin\CategoryController@delete')->name('category.delete');
Route::post('category/get_brand_list','Admin\CategoryController@getCategoryList')->name('getCategoryList');
Route::post('category/status_change/{id}','Admin\CategoryController@change_status')->name('category.status_change');

//langauges
Route::get('malls/list','Admin\MallController@index')->name('malls.list');
Route::get('malls/create','Admin\MallController@create')->name('malls.create');
Route::get('malls/edit/{id}','Admin\MallController@create')->name('malls.edit');
Route::post('malls/submit','Admin\MallController@submit')->name('malls.submit');
Route::post('malls/delete/{id}','Admin\MallController@delete')->name('malls.delete');
Route::post('malls/get_mall_list','Admin\MallController@getmallList')->name('getmallList');
Route::post('malls/status_change/{id}','Admin\MallController@change_status')->name('malls.status_change');
Route::get('malls/zone/{id}','Admin\MallController@getZone')->name('malls.getZone');
//Users
Route::get('users/list','Admin\UserController@index')->name('users.list');
Route::post('users/get_user_list','Admin\UserController@getuserList')->name('getuserList');
Route::post('users/delete/{id}','Admin\UserController@delete')->name('users.delete');
Route::post('users/status_change/{id}','Admin\UserController@change_status')->name('users.status_change');
Route::get('users/create','Admin\UserController@create')->name('users.create');
Route::get('users/create/{id}','Admin\UserController@edit')->name('users.edit');
Route::get('users/view/{id}','Admin\UserController@view')->name('users.view');
Route::post('users/submit','Admin\UserController@submit')->name('users.submit');
Route::post('users/update','Admin\UserController@update')->name('users.update');

//Drivers
Route::get('drivers/list','Admin\DriverController@index')->name('drivers.list');
Route::post('drivers/get_user_list','Admin\DriverController@getdriversList')->name('getdriversList');
Route::post('drivers/delete/{id}','Admin\DriverController@delete')->name('drivers.delete');
Route::post('drivers/status_change/{id}','Admin\DriverController@change_status')->name('drivers.status_change');
Route::get('drivers/create','Admin\DriverController@create')->name('drivers.create');
Route::get('drivers/edit/{id}','Admin\DriverController@edit')->name('drivers.edit');
Route::get('drivers/view/{id}','Admin\DriverController@view')->name('drivers.view');
Route::post('drivers/delete/{id}','Admin\DriverController@delete')->name('drivers.delete');
Route::post('drivers/submit','Admin\DriverController@submit')->name('drivers.submit');
Route::post('drivers/update/{id}','Admin\DriverController@update')->name('drivers.update');

// Coupons
Route::get('coupons/list','Admin\CouponController@index')->name('coupons.list');
Route::get('coupons/create','Admin\CouponController@create')->name('coupons.create');
Route::get('coupons/edit/{id}','Admin\CouponController@create')->name('coupons.edit');
Route::post('coupons/create','Admin\CouponController@save')->name('coupons.save');
Route::post('coupons/get-list','Admin\CouponController@getList')->name('coupons.get-list');
Route::post('coupons/change-status/{id}','Admin\CouponController@changeStatus')->name('coupons.status_change');
Route::post('coupons/delete-coupon/{id}','Admin\CouponController@deleteCoupon')->name('coupons.delete-coupon');

//Bookings

Route::get('bookings/list_new','Admin\BookingController@index_new')->name('bookings.list.new');
Route::get('bookings/list_pickup_orders','Admin\BookingController@index_pickup_orders')->name('bookings.list.pickup_orders');
Route::get('bookings/list_delivery_orders','Admin\BookingController@index_delivery_orders')->name('bookings.list.delivery_orders');
Route::get('bookings/list_shipped','Admin\BookingController@index_shipped')->name('bookings.list.shipped');
Route::get('bookings/list_rejected','Admin\BookingController@index_rejected')->name('bookings.list.rejected');
Route::get('bookings/list_delivered','Admin\BookingController@index_delivered')->name('bookings.list.delivered');
Route::post('bookings/getdeliveredbookingList','Admin\BookingController@getdeliveredbookingList')->name('getdeliveredbookingList');

Route::post('bookings/get_new_booking_list/{status}','Admin\BookingController@getnewbookingList')->name('getnewbookingList');
Route::get('bookings/approve/{id}','Admin\BookingController@booking_approve')->name('booking.approve');
Route::get('bookings/reject/{id}','Admin\BookingController@booking_reject')->name('booking.reject');
Route::get('bookings/list_total','Admin\BookingController@index_total')->name('bookings.list.total');
Route::post('bookings/gettotalbookingList','Admin\BookingController@gettotalbookingList')->name('gettotalbookingList');



Route::get('bookings/list','Admin\BookingController@index')->name('bookings.list');
Route::post('bookings/get_booking_list','Admin\BookingController@getbookingList')->name('getbookingList');
Route::post('bookings/get_booking_qoutes/{id}','Admin\BookingController@getBookingQouteList')->name('getBookingQouteList');
Route::get('bookings/create','Admin\BookingController@create')->name('bookings.create');
Route::get('bookings/edit/{id}','Admin\BookingController@edit')->name('bookings.edit');
Route::get('bookings/view/{id}','Admin\BookingController@view')->name('bookings.view');
Route::post('bookings/store','Admin\BookingController@store')->name('bookings.store');
Route::post('bookings/update/{id}','Admin\BookingController@update')->name('bookings.update');
Route::post('bookings/get_drivers','Admin\BookingController@get_drivers')->name('get_drivers');
Route::get('bookings/qoutes/{id}/{type}','Admin\BookingController@booking_qoutes')->name('booking.qoutes');
Route::get('bookings/status/{id}/{status}','Admin\BookingController@change_status')->name('booking_status');
Route::post('bookings/delete/{id}','Admin\BookingController@delete')->name('bookings.delete');
Route::post('bookings/add_commission','Admin\BookingController@add_commission')->name('bookings.add.commission');
Route::post('bookings/assign_drvivers/{id}','Admin\BookingController@assign_drvivers')->name('bookings.assign.drvivers');
Route::get('bookings/payment/{id}/{status}','Admin\BookingController@payment_status')->name('payment_status');
Route::post('bookings/approve_qoutes','Admin\BookingController@approve_qoutes')->name('approve.qoutes');

Route::get('bookings/import','Admin\ImportBookingController@index')->name('bookings.import');

Route::get('bookings/download/csv','Admin\ImportBookingController@download_csv')->name('bookings.download.csv');

Route::post('bookings/import/csv','Admin\ImportBookingController@import')->name('bookings.import.csv');

Route::post('bookings/get_booking_charges','Admin\BookingController@get_booking_charges')->name('get.booking.charges');
Route::post('bookings/add_booking_charges','Admin\BookingController@add_booking_charges')->name('add.booking.charges');
Route::post('bookings/remove_booking_charges','Admin\BookingController@remove_booking_charges')->name('remove.booking.charges');

Route::get('bookings/create_new_request','Admin\BookingController@create_new_request')->name('admin.bookings.create_new_request');
//Route::post('bookings/create_new_request/store','Admin\BookingController@create_new_request_store')->name('admin.bookings.create_new_request_store');
Route::post('bookings/create_new_request/store','Admin\BookingController@update_existing_booking')->name('admin.bookings.create_new_request_store');
Route::post('bookings/create_new_request/get_costing','Admin\BookingController@create_new_request_get_costing')->name('admin.bookings.create_new_request_get_costing');
Route::post('bookings/create_new_request/get_list','Admin\BookingController@get_new_request_list')->name('admin.bookings.get_new_request_list');
Route::post('bookings/create_new_request/pickup_request_list','Admin\BookingController@pickup_request_list')->name('admin.bookings.pickup_request_list');
Route::post('bookings/create_new_request/delivery_request_list','Admin\BookingController@delivery_request_list')->name('admin.bookings.delivery_request_list');
Route::get('bookings/edit_request/{id}','Admin\BookingController@create_new_request')->name('admin.bookings.edit_request');
Route::get('bookings/view_request/{id}','Admin\BookingController@view_request')->name('admin.bookings.view_request');
Route::get('bookings/view_pickup_request/{id}','Admin\BookingController@view_pickup_request')->name('admin.bookings.view_pickup_request');
Route::get('bookings/view_delivery_request/{id}','Admin\BookingController@view_delivery_request')->name('admin.bookings.view_delivery_request');
Route::post('bookings/delete_image/{id}','Admin\BookingController@delete_image')->name('admin.bookings.delete_image');
Route::post('bookings/request_update','Admin\BookingController@request_update')->name('admin.bookings.request_update');

//Earnings
Route::get('earnings/list','Admin\EarningController@index')->name('earnings.list');
Route::post('bookings/get_earning_list/{from?}/{to?}','Admin\EarningController@getearningList')->name('getearningList');

//Reports
Route::get('reports/jobs_in_transit','Admin\ReportController@jobs_in_transit')->name('reports.jobs_in_transit');


Route::get('contact_us','Admin\ContactUsController@index')->name('contact_us');

//Customers
Route::get('customers/list','Admin\CustomerController@index')->name('customers.list');
Route::post('customers/get_user_list','Admin\CustomerController@getcustomerList')->name('getcustomerList');
Route::get('customers/delete/{id}','Admin\CustomerController@delete')->name('customers.delete');
Route::post('customers/status_change/{id}','Admin\CustomerController@change_status')->name('customers.status_change');
Route::get('customers/edit/{id}','Admin\CustomerController@edit')->name('customers.edit');
Route::get('customers/view/{id}','Admin\CustomerController@view')->name('customers.view');
Route::post('customers/update/{id}','Admin\CustomerController@update')->name('customers.update');

//Events
Route::get('events','Admin\EventController@index')->name('events');
Route::get('events/create','Admin\EventController@create')->name('events.create');
Route::get('events/edit/{id}','Admin\EventController@create')->name('events.edit');
Route::post('events/submit','Admin\EventController@submit')->name('events.submit');
Route::post('events/delete/{id}','Admin\EventController@delete')->name('events.delete');
Route::post('events/datatable','Admin\EventController@getEventsList')->name('events.datatable');
Route::post('events/status_change/{id}','Admin\EventController@change_status')->name('events.status_change');

//Products
Route::get('products','Admin\ProductController@index')->name('products');
Route::get('products/create','Admin\ProductController@create')->name('products.create');
Route::get('products/edit/{id}','Admin\ProductController@create')->name('products.edit');
Route::post('products/submit','Admin\ProductController@store')->name('products.submit');
Route::post('products/delete/{id}','Admin\ProductController@delete')->name('products.delete');

Route::post('products/datatable','Admin\ProductController@getProductList')->name('products.datatable');
Route::post('products/status_change/{id}','Admin\ProductController@change_status')->name('products.status_change');


//Catgory
Route::get('product-categories/list','Admin\ProductCategoryController@index')->name('product-categories.list');
Route::get('product-categories/create','Admin\ProductCategoryController@create')->name('product-categories.create');
Route::get('product-categories/edit/{id}','Admin\ProductCategoryController@create')->name('product-categories.edit');
Route::post('product-categories/submit','Admin\ProductCategoryController@submit')->name('product-categories.submit');
Route::post('product-categories/delete/{id}','Admin\ProductCategoryController@delete')->name('product-categories.delete');
Route::post('product-categories/datatable','Admin\ProductCategoryController@getCategoryList')->name('product-categories.dataTable');
Route::post('product-categories/status_change/{id}','Admin\ProductCategoryController@change_status')->name('product-categories.status_change');

//CMS Pages
Route::get('pages/list','Admin\PageController@index')->name('cms.pages.list');
Route::get('pages/create','Admin\PageController@create')->name('cms.pages.create');
Route::get('pages/edit/{id}','Admin\PageController@create')->name('cms.pages.edit');
Route::post('pages/submit','Admin\PageController@submit')->name('cms.pages.submit');
Route::post('pages/delete/{id}','Admin\PageController@delete')->name('cms.pages.delete');
Route::post('pages/datatable','Admin\PageController@getPagesList')->name('cms.pages.dataTable');
Route::post('pages/status_change/{id}','Admin\PageController@change_status')->name('cms.pages.status_change');
Route::get('pages/settings','Admin\PageController@settings')->name('cms.pages.settings');
Route::post('pages/settings/save','Admin\PageController@Settingssave')->name('cms.settings.save');
Route::get('pages/emirates','Admin\PageController@Emirates')->name('cms.pages.emirates');
Route::post('pages/emirates/save','Admin\PageController@EmiratesSave')->name('cms.emirates.save');

//Settings
Route::get('change-password','Admin\SettingController@index')->name('settings.change-password');
Route::post('change-password/submit','Admin\SettingController@changePassword')->name('settings.change-password.submit');

// company Types
Route::get('company/list','Admin\CompaniesController@index')->name('company.list');
Route::get('company/create','Admin\CompaniesController@create')->name('company.create');
Route::get('company/edit/{id}','Admin\CompaniesController@create')->name('company.edit');
Route::get('company/view/{id}','Admin\CompaniesController@view')->name('company.view');

Route::post('company/submit','Admin\CompaniesController@submit')->name('company.submit');
Route::post('company/delete/{id}','Admin\CompaniesController@delete')->name('company.delete');
Route::post('company/list','Admin\CompaniesController@getCompanyList')->name('getCompanyList');
Route::post('company/status_change/{id}','Admin\CompaniesController@change_status')->name('company.status_change');

Route::get('company/review/{id}','Admin\ReviewsController@company_view')->name('company.reviews');
Route::post('reviews/company_list/{id}','Admin\ReviewsController@getCompanyReviewList')->name('getCompanyReviewList');

Route::get('company/approve/{id}','Admin\CompaniesController@company_approve')->name('company.approve');
Route::get('company/reject/{id}','Admin\CompaniesController@company_reject')->name('company.reject');

Route::get('company/bookings/{id}/{status}','Admin\CompaniesController@company_bookings')->name('company.bookings');

Route::post('getCompanyBookingList/{id}/{status}','Admin\CompaniesController@getCompanyBookingList')->name('getCompanyBookingList');


// customres

Route::get('customers/lists/all','Admin\CustomerController@listCust')->name('customers.list.all');
Route::post('customers/get_list_total/al','Admin\CustomerController@getcustomerTotalList')->name('getcustomerTotalList');
Route::get('customers/insert/view','Admin\CustomerController@detailView')->name('customer.detail.view');
Route::post('customers/status_active/{id}','Admin\CustomerController@change_status_cus')->name('customers.status_active');
Route::get('customerss/create/data','Admin\CustomerController@createCus')->name('customers.create.data');
Route::post('customer_csv/submit','Admin\CustomerController@submitCsv')->name('customer_csv.submit');
Route::post('customers/insert','Admin\CustomerController@insert')->name('customer.insert');
Route::get('customers/edit/data/{id}','Admin\CustomerController@detailView')->name('customer.edit.data');
Route::post('customers/delete/{id}','Admin\CustomerController@delete')->name('customer.delete');
Route::GET('customers/detail/show/{id}','Admin\CustomerController@detailShow')->name('customer.view.data');
Route::get('/export_csv', 'Admin\CustomerController@exportCsv')->name('export.csv');


Route::get('customers/bookings/{id}/{status}','Admin\CustomerController@customer_bookings')->name('customer.bookings');

Route::post('getCustomerBookingList/{id}/{status}','Admin\CustomerController@getCustomerBookingList')->name('getCustomerBookingList');

Route::get('customers/pending_bookings/{id}','Admin\CustomerController@pending_bookings')->name('customer.bookings.pending');

Route::get('customers/rejected_bookings/{id}','Admin\CustomerController@rejected_bookings')->name('customer.bookings.rejected');

Route::post('getCustomernewbookingList/{id}/{status}','Admin\CustomerController@getCustomernewbookingList')->name('getCustomernewbookingList');


// Route::post('getCompanyBookingList/{id}/{status}','Admin\CompaniesController@getCompanyBookingList')->name('getCompanyBookingList');


// notification Types
Route::get('notifications/list','Admin\NotificationController@index')->name('notification.list');
Route::get('notifications/create','Admin\NotificationController@create')->name('notification.create');
Route::get('notifications/edit/{id}','Admin\NotificationController@edit')->name('notification.edit');
Route::post('notifications/delete/{id}','Admin\NotificationController@delete')->name('notification.delete');


Route::post('notifications/submit','Admin\NotificationController@submit')->name('notification.submit');
Route::post('notifications/update','Admin\NotificationController@update')->name('notification.update');
Route::post('notifications/list/data','Admin\NotificationController@getNotiList')->name('getNotiList');
Route::get('notifications/getListUser','Admin\NotificationController@getListUser')->name('getListUser');
Route::get('notifications/getSearchUser','Admin\NotificationController@getSearchUser')->name('getSearchUser');
Route::post('notifications/status_change/{id}','Admin\NotificationController@change_status')->name('notification.status_change');


//truck types
Route::get('truck_types/list','Admin\TruckTypeController@index')->name('truck_type.list');
Route::post('truck_types/list/show','Admin\TruckTypeController@getTruckTypeList')->name('getTruckTypeList');
Route::get('truck_types/create','Admin\TruckTypeController@create')->name('truck_type.create');
Route::get('truck_types/edit/{id}','Admin\TruckTypeController@create')->name('truck_type.edit');
Route::post('truck_types/submit','Admin\TruckTypeController@submit')->name('truck_type.submit');
Route::post('truck_types/delete/{id}','Admin\TruckTypeController@delete')->name('truck_type.delete');
Route::post('truck_types/status_change/{id}','Admin\TruckTypeController@change_status')->name('truck_type.status_change');

Route::get("faq", "Admin\FaqController@index");
Route::match(array('GET', 'POST'), 'faq/create', 'Admin\FaqController@create');
Route::get("faq/edit/{id}", "Admin\FaqController@edit");
Route::post("faq/update", "Admin\FaqController@update");
Route::delete("faq/delete/{id}", "Admin\FaqController@delete");
// map
Route::get('/map','MapController@showMap')->name('showMap');

Route::get('contact_details', 'Admin\PagesController@contact_details')->name('contact_details');
Route::post("contact_us_setting_store", "Admin\PagesController@contact_us_setting_store")->name('admin.contact_us_setting_store');


// deligates
Route::get("deligates", "Admin\DeligateController@index")->name('deligates.list');
Route::post("deligates/getdeligateList", "Admin\DeligateController@getdeligateList")->name('getdeligateList');
Route::get("deligate/create", "Admin\DeligateController@create")->name('deligates.create');
Route::post("deligates/change_status", "Admin\DeligateController@change_status")->name('deligates.change_status');
Route::get("deligates/edit/{id}", "Admin\DeligateController@edit")->name('deligates.edit');
Route::get("deligates/delete/{id}", "Admin\DeligateController@destroy")->name('deligates.destroy');
Route::post("save_deligate", "Admin\DeligateController@store")->name('deligates.store');



// reviews
Route::get('reviews/list','Admin\ReviewsController@index')->name('reviews.list');
// Route::get('reviews/create','Admin\ReviewsController@create')->name('reviews.create');
Route::get('reviews/edit/{id}','Admin\ReviewsController@edit')->name('reviews.edit');
// Route::post('company/submit','Admin\ReviewsController@submit')->name('company.submit');
Route::post('reviews/delete/{id}','Admin\ReviewsController@delete')->name('reviews.delete');
Route::post('reviews/list','Admin\ReviewsController@getReviewList')->name('getReviewList');
Route::post('reviews/status_change/{id}','Admin\ReviewsController@change_status')->name('reviews.status_change');
Route::post('reviews/update','Admin\ReviewsController@update')->name('reviews.update');
//


// wallet Types
Route::get('wallet/list','Admin\WalletController@index')->name('wallet.list');
Route::post('wallet/list','Admin\WalletController@getwalletList')->name('getwalletList');
Route::get('wallet/edit/{id}','Admin\WalletController@edit')->name('wallet.edit');
Route::post('wallet/update','Admin\WalletController@update')->name('wallet.update');
Route::get('wallet/add/{id}','Admin\WalletController@add')->name('wallet.add');
Route::post('wallet/update/amt','Admin\WalletController@updateamt')->name('wallet.add.amt');


// BlackLists
Route::get("blacklists", "Admin\BlackListController@index")->name('blacklists.list');
Route::post("blacklists/getblackList", "Admin\BlackListController@getblackList")->name('getblackList');
Route::get("blacklists/add/{id}", "Admin\BlackListController@add")->name('blacklists.add');
Route::get("blacklists/remove/{id}", "Admin\BlackListController@remove")->name('blacklists.remove');
Route::post("blacklists/remove_all", "Admin\BlackListController@remove_all")->name('remove.all.blacklists');
Route::post("blacklists/add_all", "Admin\BlackListController@add_all")->name('add.all.blacklists');

// shipping_methods
Route::get("shipping_methods", "Admin\ShippingMethodController@index")->name('shipping_methods.list');
Route::post("shipping_methods/getshipping_methodsList", "Admin\ShippingMethodController@getshipping_methodsList")->name('getshipping_methodsList');
Route::get("shipping_methods/create", "Admin\ShippingMethodController@create")->name('shipping_methods.create');
Route::post("shipping_methods/change_status", "Admin\ShippingMethodController@change_status")->name('shipping_methods.change_status');
Route::get("shipping_methods/edit/{id}", "Admin\ShippingMethodController@edit")->name('shipping_methods.edit');
Route::get("shipping_methods/delete/{id}", "Admin\ShippingMethodController@destroy")->name('shipping_methods.destroy');
Route::post("save_shipping_methods", "Admin\ShippingMethodController@store")->name('shipping_methods.store');


// categories
Route::get("categories", "Admin\CategoryController@index")->name('categories.list');
Route::post("categories/getdeligateList", "Admin\CategoryController@getcategoryList")->name('getcategoryList');
Route::get("categories/create", "Admin\CategoryController@create")->name('categories.create');
Route::post("categories/change_status", "Admin\CategoryController@change_status")->name('categories.change_status');
Route::get("categories/edit/{id}", "Admin\CategoryController@edit")->name('categories.edit');
Route::get("categories/delete/{id}", "Admin\CategoryController@destroy")->name('categories.destroy');
Route::post("save_category", "Admin\CategoryController@store")->name('categories.store');

// categories
Route::get("sizes", "Admin\SizeController@index")->name('sizes.list');
Route::post("sizes/getdeligateList", "Admin\SizeController@getSizeList")->name('getsizeList');
Route::get("sizes/create", "Admin\SizeController@create")->name('sizes.create');
Route::post("sizes/change_status", "Admin\SizeController@change_status")->name('sizes.change_status');
Route::get("sizes/edit/{id}", "Admin\SizeController@edit")->name('sizes.edit');
Route::get("sizes/delete/{id}", "Admin\SizeController@destroy")->name('sizes.destroy');
Route::post("save_size", "Admin\SizeController@store")->name('sizes.store');

// cares
Route::get("cares", "Admin\CareController@index")->name('cares.list');
Route::post("cares/getdeligateList", "Admin\CareController@getCareList")->name('getcareList');
Route::get("cares/create", "Admin\CareController@create")->name('cares.create');
Route::post("cares/change_status", "Admin\CareController@change_status")->name('cares.change_status');
Route::get("cares/edit/{id}", "Admin\CareController@edit")->name('cares.edit');
Route::get("cares/delete/{id}", "Admin\CareController@destroy")->name('cares.destroy');
Route::post("save_care", "Admin\CareController@store")->name('cares.store');

// costings
Route::get("costings", "Admin\CostingController@index")->name('costings.list');
Route::post("costings/getdeligateList", "Admin\CostingController@getCostingList")->name('getcostingList');
Route::get("costings/create", "Admin\CostingController@create")->name('costings.create');
Route::post("costings/change_status", "Admin\CostingController@change_status")->name('costings.change_status');
Route::get("costings/edit/{id}", "Admin\CostingController@edit")->name('costings.edit');
Route::get("costings/delete/{id}", "Admin\CostingController@destroy")->name('costings.destroy');
Route::post("save_cost", "Admin\CostingController@store")->name('costings.store');

Route::get("banners", "Admin\BannerController@index")->name('banners.list');
Route::match(array('GET', 'POST'), 'banner/create', 'Admin\BannerController@create');
Route::get("banner/edit/{id}", "Admin\BannerController@edit");
Route::post("banner/update", "Admin\BannerController@update");
Route::post("banner/delete/{id}", "Admin\BannerController@delete");
Route::get("banner/get_store/{id}", "Admin\BannerController@getstore");
Route::get("banner/get_product_by_store/{id}", "Admin\BannerController@getproductbystore");

