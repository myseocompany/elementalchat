<?php

use App\Http\Controllers\ConfigController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MetaDataController;
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

//Route::get( '/', function () {return view('home');});
Route::get( '/bi/newcustomers', 'BIController@newcustomers');
Route::get( '/bi/purchasefrequency', 'BIController@purchasefrequency');
Route::get( '/bi/averageTicket', 'BIController@averageTicket');

Route::get( '/', 'CustomerController@index')->name('home');

Route::get( '/home', 'CustomerController@index');

Route::get( '/demo', function(){return "hola";});


Auth::routes();

Route::get( '/customers', 'CustomerController@index')->name('customers');
Route::get( '/customers/phase/{pid}', 'CustomerController@indexPhase');

Route::get( '/customers/create', 'CustomerController@create');
Route::post('/customers', 'CustomerController@store');
Route::get( '/customers/{customer}/edit', 'CustomerController@edit');
Route::post('/customers/{customer}/update', 'CustomerController@update');
Route::get( '/customers/{customer}/show', 'CustomerController@show');
Route::get('/customers/{customer}/destroy', 'CustomerController@destroy');
//Route::post('/customers/{customer}/action/store', function(Request $request){dd($request);});
Route::post('/customers/{customer}/action/store', 'CustomerController@storeAction');
Route::post('/customers/{customer}/action/mail', 'CustomerController@storeMail');
Route::get( '/customers/{customer}/assignMe', 'CustomerController@assignMe');


Route::get( '/customers/json', 'CustomerController@indexJson');


Route::get('/customers/{cui}/unsubscribe', 'CustomerController@unsubscribe');
Route::get('/customers/{cui}/activate', 'CustomerController@activate');



Route::get( '/leads', 'CustomerController@leads')->name('leads');
Route::get( '/leads/excel', 'CustomerController@excel');
Route::get( '/leads/whatsapp', 'CustomerController@whatsapp');
Route::get( '/leads/daily_birthday', 'CustomerController@daily_birthday');
Route::get( '/leads/monthly_birthday', 'CustomerController@monthly_birthday');
Route::get( '/leads/change_status', 'CustomerController@change_status');

// Roles
Route::get( '/roles', 'RoleController@index')->name('roles');
Route::get( '/roles/create', 'RoleController@create');
Route::post('/roles', 'RoleController@store');
Route::post('/roles/{role}/update', 'RoleController@update');
Route::get( '/roles/{role}/edit', 'RoleController@edit');
Route::get( '/roles/{role}/show', 'RoleController@show');
Route::get( '/roles/{role}/destroy', 'RoleController@destroy');

Route::get('/config', 'SiteController@config');


// users
Route::get('/users', 'UserController@index')->name('users');
Route::get('/users/create', 'UserController@create');
Route::get('/users/{user}', 'UserController@show');
Route::post('/users', 'UserController@store');
Route::get('/users/{user}/edit', 'UserController@edit');
Route::post('/users/{user}/edit', 'UserController@edit');
Route::get('/users/{user}/show', 'UserController@show');
Route::post('/users/{user}/update', 'UserController@update');

// customer_statuses
Route::get('/customer_statuses', 'CustomerStatusController@index');
Route::get('/customer_statuses/create', 'CustomerStatusController@create');
Route::get('/customer_statuses/{customer_status}', 'CustomerStatusController@show');
Route::post('/customer_statuses', 'CustomerStatusController@store');
Route::post('/customer_statuses/{customer_status}/edit', 'CustomerStatusController@edit');
Route::get('/customer_statuses/{customer_status}/edit', 'CustomerStatusController@edit');
Route::post('/customer_statuses/{customer_status}/update', 'CustomerStatusController@update');
Route::post('/customer_statuses/{customer_status}/updateStatus', 'CustomerStatusController@updateStatus');
Route::get('/customer_statuses/{customer_status}/destroy', 'CustomerStatusController@destroy');

Route::get('/customers/{customer}/email/1', 'CustomerController@mail');
Route::get('/img/mailings/{email}/{customer}.jpg', 'ActionController@trackEmail');
Route::get('/actions/trackEmail/{customer}/{email}/pixel.png', 'ActionController@trackEmail');

Route::get('/customers/actions/trackEmail/', 'ActionController@trackEmailCode');

														
Route::get('/track/{cid}/{aid}/{sid}/{pid}', 'APIController@trackAction');
Route::get('/track_send/{cid}/{aid}/{sid}/{str}', 'APIController@trackWPAction');		
Route::get('/track/email/{cid}/{aid}/{sid}/{pid}', 'APIController@trackAction');


// file
Route::post('/customer_files', 'CustomerFileController@store');
Route::get('/customer_files/{file}/delete', 'CustomerFileController@delete');





// Mail
Route::get('/emails/test', 'EmailController@testMail');
Route::get('/mail/send', 'MailController@send');

Route::get('/emails/store', 'EmailController@store');
Route::get('/emails/{email}/store', 'EmailController@storeEmail');
Route::get('/emails/send', 'EmailController@send');
Route::get('/emails/getCustomersZeroActions', 'EmailController@getCustomersZeroActions'); 

Route::get('/emails', 'EmailController@index')->name('emails');
Route::get('/emails/create', 'EmailController@create');
Route::get('/emails/{email}/show', 'EmailController@show');
Route::get('/emails/{email}/edit', 'EmailController@edit');
Route::post('/emails/{email}/update', 'EmailController@update');
Route::post('/emails', 'EmailController@store');
Route::get('/emails/{email}/storeAudience', 'EmailController@storeAudience');

Route::get('/emails/{email}/destroy', 'EmailController@destroy');


Route::get('/jobs', 'JobController@index');


//Action
Route::get('/actions', 'ActionController@index')->name('actions');
Route::get('/actions/{action}/show', 'ActionController@show');
Route::get('/actions/{action}/edit', 'ActionController@edit');
Route::get('/actions/{action}/update', 'ActionController@update');
Route::get('/actions/{action}/complete', 'CustomerController@updateAction');

Route::get('/actions/{action}/destroy', 'ActionController@destroy');

//Action Types
Route::get('/action_type', 'ActionTypeController@index');
Route::post('/action_type/create', 'ActionTypeController@store');
Route::get('/action_type/{id}/destroy', 'ActionTypeController@destroy');
Route::get('/action_type/{id}/edit', 'ActionTypeController@edit');
Route::post('/action_type/{id}/update', 'ActionTypeController@update');
Route::get('/action_type/{id}/show', 'ActionTypeController@show');
Route::get('/action_type/{action}', 'ActionTypeController@getActionTypes');

//NPS
Route::get('/nps/create/{id}/{cid}', 'App\Http\Controllers\MetaDataController@createNps')->name('nps.create');
Route::get('/nps/thanks')->name('nps.thanks');
Route::get('/metadata/{id}/create/nps/{cid}', 'MetaDataController@createNPS');
Route::get('/nps/{phone}', 'MetaDataController@findNPS');
Route::post('/metadata/{id}/store/nps', 'MetaDataController@storeNPS');



Route::get('/api/customers/save', 'APIController@saveApi');

Route::get('/api/customers/save_pabbly', 'APIController@saveAPICutomerPabbly');

Route::get('/api/customers/save-calculate', 'APIController@saveCustomerCalculate');
Route::get('/api/opendialog', 'APIController@opendialog');
Route::post('/api/opendialog', 'APIController@opendialog');
Route::get('/api/customers/saveCustomer', 'APIController@saveApi');

Route::post('/api/customers/saveCustomer', 'APIController@saveApi');

// Reports
Route::get('/reports/users/customer/status', 'ReportController@userCustomerStatus');
Route::get('/reports/users/customer/actions', 'ReportController@userCustomerActions');
Route::get('/reports/customers_time', 'ReportController@customersTime');




// Audience
Route::get('/audiences', 'AudienceController@index');
Route::get('/audiences/{id}/show', 'AudienceController@show');
Route::get('/audiences/{id}/whatsapp', 'AudienceController@whatsapp');

Route::get('/audiences/{id}/send', 'AudienceController@send');
Route::get('/audiences/create', 'AudienceController@create');
Route::post('/audiences', 'AudienceController@store');
Route::get('/audiences/{id}/customers', 'AudienceController@createCustomers');
Route::post('/audiences/{id}/customers', 'AudienceController@storeCustomers');
Route::get('/audiences/{id}/customers/{cid}/destroy', 'AudienceController@destroyCustomer');

Route::get('/audiences/{id}/campaign/{cid}/show', 'AudienceController@whatsapp');
Route::get('/audiences/{id}/campaign/{cid}/show_rpa', 'AudienceController@showRpa');

// CAMPAIGN
Route::get('/campaigns', 'CampaignController@index');
Route::get('/campaigns/{id}/show', 'CampaignController@show');
Route::get('/campaigns/{id}/edit', 'CampaignController@edit');
Route::post('/campaigns/{id}/update', 'CampaignController@update');
Route::get('/campaigns/{id}/send', 'CampaignController@send');
Route::get('/campaigns/create', 'CampaignController@create');
Route::post('/campaigns', 'CampaignController@store');


//CAMPAIGN MESSAGES
Route::post('/campaigns/message/{mid}/delete', 'CampaignController@destroyMessage');
Route::post('/campaigns/message/{mid}/update', 'CampaignController@updateMessage');
Route::post('/campaigns/message/store', 'CampaignController@storeMessage');

Route::get('/campaigns/{mid}/update', 'CampaignController@updateCampaign');




Route::get('/products', 'ProductController@index')->name('products.index');
Route::get('/products/public', 'ProductController@indexPublic');
Route::get('/products/{id}/show', 'ProductController@show');
Route::get('/products/{id}/edit', 'ProductController@edit');
Route::post('/products/{id}/update', 'ProductController@update');

Route::get('/products/{id}/destroy', 'ProductController@destroy');
Route::get('/products/create', 'ProductController@create');

Route::post('/products', 'ProductController@store');

Route::get('/products/import', 'ProductController@import');
Route::post('/products/bulk_store', 'ProductController@bulkStore');


Route::get('/orders/sid/{sid}', 'OrderController@index');
Route::get('/orders/{id}/show', 'OrderController@show');
Route::get('/orders/{id}/quote', 'OrderController@quote');
Route::get('/orders/{id}/edit', 'OrderController@edit');
Route::post('/orders/{id}/update', 'OrderController@update');
Route::post('/orders/{id}/updateProducts', 'OrderController@updateProducts');
Route::get('/orders/{id}/delete', 'OrderController@delete');

Route::get('/orders/{cid}/create', 'OrderController@create')->name('orders.create');
Route::post('/orders/', 'OrderController@store'); // guarda la orden
Route::get('/orders/', 'OrderController@index');

Route::get('/orders/{oid}/add/product', 'OrderController@addProducts');
Route::get('/order/product/{oid}/destroy', 'OrderController@destroy');
Route::post('/order/products/store', 'OrderController@storeProduct');
Route::post('/order/payment/store', 'OrderController@storePayment');


Route::get('/orders/transactions/{id}/destroy', 'OrderTransactionController@destroy');
//Route::get('/orders/transactions', 'OrderTransactionController@create');
Route::get('/orders/transactions/{tid}/edit', 'OrderTransactionController@edit');
Route::post('/orders/transactions/', 'OrderTransactionController@store');
Route::post('/orders/transactions/{tid}/update', 'OrderTransactionController@update');


Route::get('/settings', 'ConfigController@index');
Route::get('/settings/{id}/edit', 'ConfigController@edit');
Route::post('/settings/{id}/update', 'ConfigController@update');


Route::get('/product_types', 'ProductTypesController@index');
Route::get('/product_types/create', 'ProductTypesController@create');
Route::post('/product_types', 'ProductTypesController@store');
Route::get('/product_types/{id}/destroy', 'ProductTypesController@destroy');
Route::get('/product_types/{id}/edit', 'ProductTypesController@edit');
Route::post('/product_types/{id}/update', 'ProductTypesController@update');


Route::get('/customers/metadata/{phone}/create/{cid}', 'MetadataController@createMetadata');
Route::get('/customers/metadata/{id}/show/{cid}', 'MetadataController@showMetadata');
Route::post('/customers/metadata/{id}/save', 'MetadataController@saveMetadata');


/* Ruleta de premios */
Route::get('/roulette', 'RouletteController@index');
// Route::post('/roulette_', 'RouletteController@store');
// Route::get('/roulette_/play/store', 'RouletteController@play');


// Route::get('/roulette', 'RouletteController@trick');

// Route::get('/roulette/retail', 'RouletteController@create');
// Route::post('/roulette/retail', 'RouletteController@store');
// Route::get('/roulette/retail/play', 'RouletteController@retailPlay');


// Route::get('/roulette/ig', 'RouletteController@igPlay');
// Route::post('/roulette/ig', 'RouletteController@igPlay');


Route::get('/customer_unsubscribes', 'CustomerUnsubscribesController@index');
Route::post('/customer_unsubscribes', 'CustomerUnsubscribesController@save');
Route::get('/customer_unsubscribes/{id}/destroy', 'CustomerUnsubscribesController@destroy');
Route::get('/customer_unsubscribes/{id}/edit', 'CustomerUnsubscribesController@edit');
Route::post('/customer_unsubscribes/{id}/update', 'CustomerUnsubscribesController@update');


Route::get('/optimize/customers/consolidateDuplicates/', 'OptimizerController@consolidateDuplicates');
Route::get('/optimize/customers/findDuplicates/', 'OptimizerController@findDuplicates');
Route::get('/optimize/customers/findDuplicates/show', 'OptimizerController@showDuplicates');
Route::get('/optimize/customers/mergeDuplicates/', 'OptimizerController@mergeDuplicates_');




Route::get('/clear-config', 'ConfigController@clearConfig');

Route::get('/landing', 'LandingController@productList');
Route::get('/landing/all', 'LandingController@allProductList');
Route::post('/send-order', 'LandingController@sendOrder');



Route::post('/order-files/store', 'OrderFileController@store')->name('order-files.store');
Route::post('/order-files/delete/{id}', 'OrderFileController@delete')->name('order-files.delete');

