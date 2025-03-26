<?php
use App\Http\Controllers\RDTestController;
use App\Livewire\AudienceCustomerManager;
use App\Http\Controllers\OrderFileController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OptimizerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerStatusController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\CustomerFileController;
use App\Http\Controllers\Api\APIController;
use App\Http\Controllers\ReferencesController;
use App\Http\Controllers\WompiController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ActionTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\EmmailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTransactionController;
use App\Http\Controllers\MetaDataController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CustomerUnsubscribesController;
use App\Http\Controllers\BIController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});


Route::middleware('auth')->get('/faq', [HomeController::class, 'indexFaq'])->name('faq');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Customer Routes
Route::middleware('auth')->prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customers');
    Route::get('/create', [CustomerController::class, 'create']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::get('/{customer}/edit', [CustomerController::class, 'edit']);
    Route::post('/{customer}/update', [CustomerController::class, 'update']);
    Route::get('/{customer}/show', [CustomerController::class, 'show']);
    Route::get('/{customer}/show/{action}', [CustomerController::class, 'showAction']);
    Route::get('/{customer}/destroy', [CustomerController::class, 'destroy']);
    Route::post('/{customer}/action/store', [CustomerController::class, 'storeAction']);
    Route::post('/{customer}/action/sale', [CustomerController::class, 'saleAction']);
    Route::post('/{customer}/action/opportunity', [CustomerController::class, 'opportunityAction']);
    Route::post('/{customer}/action/poorly_rated', [CustomerController::class, 'poorlyRatedAction']);
    Route::post('/{customer}/action/pqr', [CustomerController::class, 'pqrAction']);
    Route::post('/{customer}/action/spare', [CustomerController::class, 'spareAction']);
    Route::post('/{customer}/action/mail', [CustomerController::class, 'storeMail']);
    Route::get('/{customer}/assignMe', [CustomerController::class, 'assignMe']);
    Route::post('/{customer}/audience', [CustomerController::class, 'storeAudience']);
    Route::get('/history/{customer}/show', [CustomerController::class, 'showHistory']);
    Route::get('/ajax/update_user', [CustomerController::class, 'updateAjax']);
    Route::get('/logistica', [CustomerController::class, 'newIndex']);
    Route::get('/{customer}/action/updateAjax', [CustomerController::class, 'updateAjaxStatus']);
    Route::get('/phase/{pid}', [CustomerController::class, 'getcustomers']);
    Route::get('/{pid}', [CustomerController::class, 'dragleads']);



    

});

// Optimizer Routes
Route::middleware('auth')->prefix('optimize/customers')->group(function () {
    Route::get('/findDuplicates', [OptimizerController::class, 'findDuplicates']);
    Route::get('/consolidateDuplicates', [OptimizerController::class, 'consolidateDuplicates']);
    Route::get('/findDuplicates/show', [OptimizerController::class, 'showDuplicates']);
    Route::get('/mergeDuplicates', [OptimizerController::class, 'mergeDuplicates_']);
});

// Lead Routes
Route::middleware('auth')->get('/leads', [CustomerController::class, 'leads'])->name('leads');
Route::middleware('auth')->get('/leads/excel', [CustomerController::class, 'excel']);
Route::middleware('auth')->get('/leads/change_status', [CustomerController::class, 'change_status']);

// Role Routes
Route::middleware('auth')->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles');
    Route::get('/create', [RoleController::class, 'create']);
    Route::post('/', [RoleController::class, 'store']);
    Route::post('/{role}/update', [RoleController::class, 'update']);
    Route::get('/{role}/edit', [RoleController::class, 'edit']);
    Route::get('/{role}/show', [RoleController::class, 'show']);
    Route::get('/{role}/destroy', [RoleController::class, 'destroy']);
});

Route::middleware('auth')->prefix('audiences')->group(function () {
    Route::get('/manage', AudienceCustomerManager::class);
    
    Route::get('/{audience}/manage', AudienceCustomerManager::class)->name('audiences.manage');

});



// Site Routes
Route::get('/config', [SiteController::class, 'config']);
Route::get('/report', [SiteController::class, 'report']);

// User Routes
Route::middleware('auth')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users');
    Route::get('/create', [UserController::class, 'create']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{user}/edit', [UserController::class, 'edit']);
    Route::post('/{user}/edit', [UserController::class, 'edit']);
    Route::post('/{user}/update', [UserController::class, 'update']);
});

// Customer Status Routes
Route::middleware('auth')->prefix('customer_statuses')->group(function () {
    Route::get('/', [CustomerStatusController::class, 'index']);
    Route::get('/create', [CustomerStatusController::class, 'create']);
    Route::get('/{customer_status}', [CustomerStatusController::class, 'show']);
    Route::post('/', [CustomerStatusController::class, 'store']);
    Route::post('/{customer_status}/edit', [CustomerStatusController::class, 'edit']);
    Route::get('/{customer_status}/edit', [CustomerStatusController::class, 'edit']);
    Route::post('/{customer_status}/update', [CustomerStatusController::class, 'update']);
    Route::post('/{customer_status}/updateStatus', [CustomerStatusController::class, 'updateStatus']);
    Route::get('/{customer_status}/destroy', [CustomerStatusController::class, 'destroy']);
});

Route::get('/customers/{customer}/email/1', [CustomerController::class, 'mail']);
Route::get('/customers/{customer}/actions/createMail/{email}', [ActionController::class, 'trackEmail']);
Route::get('/customers/{customer}/actions/trackEmail/{email}', [ActionController::class, 'trackEmail']);

// File Routes
Route::middleware('auth')->prefix('customer_files')->group(function () {
    Route::post('/', [CustomerFileController::class, 'store']);
    Route::get('/{file}/delete', [CustomerFileController::class, 'delete']);
});

// API Routes
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/customers/saveCustomer', [APIController::class, 'saveApiWatoolbox']);
    
    Route::get('/customers/saveCheckout', [APIController::class, 'saveApiCheckout']);
    
    Route::get('/products/{id}/get', [APIController::class, 'getProduct']);
    Route::get('/products/get', [APIController::class, 'getProducts']);
    Route::get('/rd/saveCustomer', [APIController::class, 'saveFromRD']);
    Route::post('/rd/saveCustomer', [APIController::class, 'saveFromRD']);
    Route::get('/customers/save-calculate', [APIController::class, 'saveCustomerCalculate']);
    Route::post('/customers/referenceWompi', [APIController::class, 'getWompiReference']);
    Route::get('/histories/{id}/get', [APIController::class, 'getHistory']);
    Route::get('/actions/{id}/get', [APIController::class, 'getAction']);
    Route::get('/update_lead_score', [APIController::class, 'saveFromRD2']);
    Route::post('/update_lead_score', [APIController::class, 'saveFromRD2']);
});
Route::get('/products/{id}/show', [ProductController::class, 'show']);
Route::get('api/customers/saveCustomer', [APIController::class, 'saveApi'])->withoutMiddleware(['auth']);
Route::post('api/customers/update', [APIController::class, 'updateFromRD'])->withoutMiddleware(['auth'])->name('updateRD');

// References Routes
Route::middleware('auth')->prefix('references')->group(function () {
    Route::get('/', [ReferencesController::class, 'show']);
    Route::get('/{customer}/create', [ReferencesController::class, 'create']);
    Route::post('/', [ReferencesController::class, 'store']);
    Route::get('/{id}/destroy', [ReferencesController::class, 'destroy']);
    Route::get('/{id}/edit', [ReferencesController::class, 'edit']);
    Route::post('/{id}/update', [ReferencesController::class, 'update']);
    Route::get('/{id}/wompi_link', [ReferencesController::class, 'getLink']);
});

// Wompi Routes
Route::get('/wompi_link/{reference}', [WompiController::class, 'getLink']);

// Mail Routes
Route::get('/testMail', [SiteController::class, 'testMail']);
Route::get('/mail/send', [MailController::class, 'send']);
Route::get('/emails/store', [EmailController::class, 'store']);
Route::get('/emails/{email}/store', [EmailController::class, 'store']);
Route::get('/emails/send', [EmailController::class, 'send']);

// Email Routes
Route::middleware('auth')->prefix('emails')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('emails');
    Route::get('/create', [UserController::class, 'create']);
    Route::get('/{user}', [UserController::class, 'show']);
});

// Job Routes
Route::get('/jobs', [JobController::class, 'index']);

// Action Routes
Route::middleware('auth')->prefix('actions')->group(function () {
    Route::get('/', [ActionController::class, 'index'])->name('actions');
    Route::get('/{action}/show', [ActionController::class, 'show']);
    Route::get('/{action}/edit', [ActionController::class, 'edit']);
    Route::get('/{action}/update', [ActionController::class, 'update']);
    Route::get('/{action}/destroy', [ActionController::class, 'destroy']);
    Route::get('/schedule', [ActionController::class, 'schedule']);
});

// Pending Actions Routes
Route::middleware('auth')->prefix('pending_actions')->group(function () {
    Route::get('/', [ActionController::class, 'indexPending'])->name('pending_actions');
    Route::get('/paginacion', [ActionController::class, 'paginacion']);
    Route::get('/notifications', [ActionController::class, 'getPendingActions']);
});

// Action Type Routes
Route::middleware('auth')->prefix('action_type')->group(function () {
    Route::get('/', [ActionTypeController::class, 'index']);
    Route::post('/create', [ActionTypeController::class, 'store']);
    Route::get('/{id}/destroy', [ActionTypeController::class, 'destroy']);
    Route::get('/{id}/edit', [ActionTypeController::class, 'edit']);
    Route::post('/{id}/update', [ActionTypeController::class, 'update']);
    Route::get('/{id}/show', [ActionTypeController::class, 'show']);
});

// Report Routes
Route::middleware('auth')->prefix('reports')->group(function () {
    Route::get('/users', [ReportController::class, 'users']);
    Route::get('/users/customer/status', [ReportController::class, 'userCustomerStatus']);
    Route::get('/users/customer/actions', [ReportController::class, 'userCustomerActions']);
    Route::get('/customers_time', [ReportController::class, 'customersTime']);
    Route::get('/views/customers_followup', [ReportController::class, 'RFM_CustomersFollowups']);
    Route::get('/views/daily_customers_followup', [CustomerController::class, 'daily']);
    Route::get('/fm', [ReportController::class, 'RFM']);
});

// Dashboard Routes

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard/scroll_active', [ReportController::class, 'scrollActive']);
Route::get('/dashboard/scroll_inactive', [ReportController::class, 'scrollInactive']);
Route::get('/dashboard/scroll_inactive_without_user', [ReportController::class, 'scrollInactiveWithOutUser']);

// Order Routes
Route::middleware('auth')->prefix('orders')->group(function () {
    Route::post('/{id}/update', [OrderController::class, 'update']);
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/', [OrderController::class, 'index']);

    #Route::get('/{cid}/quotes/create', [OrderController::class, 'create'])->name('orders.create');
    #Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/{cid}/create', [OrderController::class, 'create'])->name('orders.create');
    #Route::get('/create/sid/{sid}', [OrderController::class, 'create'])->name('orders.create');
    
    Route::get('/{oid}/add/product', [OrderController::class, 'addProducts']);
    Route::get('/sid/{sid}', [OrderController::class, 'index']);
    Route::post('/search_customer', [OrderController::class, 'searchCustomer']);
    Route::get('/product/{pid}', [OrderController::class, 'orderProduct']);
    Route::post('/save', [OrderController::class, 'SaveOrder']);
    Route::get('/show/sid/{id}', [OrderController::class, 'showQuote']);
    
    Route::get('/product/{oid}/destroy', [OrderController::class, 'destroy']);
    Route::get('/{oid}/proforma', [OrderController::class, 'showProforma'])->name('orders.showProforma');
    Route::get('/{oid}/proforma_co', [OrderController::class, 'showProformaCO'])->name('orders.showProformaCO');
    Route::get('/sid/{sid}/report', [OrderController::class, 'indexReport']);
    Route::get('/{id}/show', [OrderController::class, 'show']);
    Route::get('/{id}/quote', [OrderController::class, 'quote']);
    Route::get('/{id}/edit', [OrderController::class, 'edit']);
    Route::get('/{id}/destroy', [OrderController::class, 'destroy']);
    Route::post('/payment/store', [OrderController::class, 'storePayment']);

    //Route::post('/storeproduct', [OrderController::class, 'storeProduct']);
    
});    


Route::middleware('auth')->prefix('products')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
});
// Order Transaction Routes
Route::middleware('auth')->prefix('orders/transactions')->group(function () {
    Route::get('/{id}/destroy', [OrderTransactionController::class, 'destroy']);
    Route::get('/{tid}/edit', [OrderTransactionController::class, 'edit']);
    Route::post('/', [OrderTransactionController::class, 'store']);
    Route::post('/{tid}/update', [OrderTransactionController::class, 'update']);
});

// Product Routes
Route::middleware('auth')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products');
    Route::get('/show/{id}', [ProductController::class, 'show'])->name('product-show');
    Route::get('/create', [ProductController::class, 'create']);
    Route::post('/store', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'show']);
    Route::get('/{id}/edit', [ProductController::class, 'edit']);
    Route::post('/edit/{id}', [ProductController::class, 'update']);
});

// Import Routes
Route::get('/import', [ImportController::class, 'index']);

// Mail Routes
Route::get('/send-to-teacheable', [APIController::class, 'sendToTeacheable']);
Route::get('/send-mail', [MailController::class, 'send']);

Route::get('/wa/test', [WhatsAppAPIController::class, 'test']);

// Clientify Contacts Routes
Route::get('/contacts_clientify', [APIController::class, 'getContacts']);

// Track Send Routes
Route::get('/track_send/{cid}/{aid}/{sid}/{str}', [APIController::class, 'trackWPAction']);

// Survey Routes
Route::middleware('auth')->prefix('metadata')->group(function () {
    Route::get('/1/pollAll', [MetaDataController::class, 'poll']);
    Route::get('/1/pollAll/{id}', [MetaDataController::class, 'pollId']);
    Route::get('/{id}/create', [MetaDataController::class, 'createSurvey']);
    Route::post('/{id}/save', [MetaDataController::class, 'save']);
    Route::get('/{id}/datapolicy', [MetaDataController::class, 'datapolicy']);
    Route::post('/{id}/store', [MetaDataController::class, 'store']);
    Route::get('/2/{customer}', [MetaDataController::class, 'freeClass']);
    Route::post('/2/{customer}/update', [MetaDataController::class, 'freeClassUpdate']);
    Route::get('/2/{customer}/update', [MetaDataController::class, 'freeClassUpdate']);
    Route::get('/3/{customer}', [MetaDataController::class, 'schedule']);
    Route::post('/3/{customer}/update', [MetaDataController::class, 'freeClassUpdate']);
    Route::get('/3/{customer}/update', [MetaDataController::class, 'freeClassUpdate']);
    // Route::get('/{id}/create/poe/{cid}', [MetaDataController::class, 'createpoe']);
    // Route::post('/{id}/store/poe', [MetaDataController::class, 'storepoe']);
});

// Define the route without the 'auth' middleware
Route::get('metadata/{id}/create/poe/{cid}', [MetaDataController::class, 'createpoe'])->withoutMiddleware(['auth']);
Route::post('metadata/{id}/store/poe', [MetaDataController::class, 'storepoe'])->withoutMiddleware(['auth']);


// Audience Routes
Route::middleware('auth')->prefix('audiences')->group(function () {
    Route::get('/', [AudienceController::class, 'index']);
    Route::get('/{id}/show', [AudienceController::class, 'show']);
    Route::get('/{id}/whatsapp', [AudienceController::class, 'whatsapp']);
    Route::get('/{id}/send', [AudienceController::class, 'send']);
    Route::get('/create', [AudienceController::class, 'create']);
    Route::post('/', [AudienceController::class, 'store']);
    Route::get('/{id}/customers', [AudienceController::class, 'createCustomers']);
    Route::post('/{id}/customers', [AudienceController::class, 'storeCustomers']);
    Route::get('/{id}/customers/{cid}/destroy', [AudienceController::class, 'destroyCustomer']);
    Route::get('/{id}/campaign/{cid}/show', [AudienceController::class, 'whatsapp']);
    Route::get('/{id}/campaign/{cid}/show_rpa', [AudienceController::class, 'showRpa']);
});

// Campaign Routes
Route::middleware('auth')->prefix('campaigns')->group(function () {
    Route::get('/', [CampaignController::class, 'index']);
    Route::get('/{id}/show', [CampaignController::class, 'show']);
    Route::get('/{id}/edit', [CampaignController::class, 'edit']);
    Route::post('/{id}/update', [CampaignController::class, 'update']);
    Route::get('/{id}/send', [CampaignController::class, 'send']);
    Route::get('/create', [CampaignController::class, 'create']);
    Route::post('/', [CampaignController::class, 'store']);
    Route::post('/message/{mid}/delete', [CampaignController::class, 'destroyMessage']);
    Route::post('/message/{mid}/update', [CampaignController::class, 'updateMessage']);
    Route::post('/message/store', [CampaignController::class, 'storeMessage']);
    Route::get('/{mid}/update', [CampaignController::class, 'updateCampaign']);
    Route::get('/{id}', [CampaignController::class, 'getMessages']);
    Route::get('/{id}/getPhone/setMessage/{message}', [CampaignController::class, 'getPhone']);
});

// Callback Routes
Route::post('/auth/callback', [APIController::class, 'callBack']);
Route::get('/auth/callback', [APIController::class, 'callBack']);

// Reports
Route::get('/reports/fm', [ReportController::class, 'RFM']);

Route::get('/watoolbox/test', function(){

    return view('test');
});

Route::get( '/bi/newcustomers', [BIController::class, 'newcustomers']);
Route::get( '/bi/purchasefrequency', [BIController::class, 'purchasefrequency']);
Route::get( '/bi/averageTicket', [BIController::class, 'averageTicket']);

Route::get('/customers_unsubscribe', [CustomerUnsubscribesController::class, 'index']);
Route::post('/customers_unsubscribe', [CustomerUnsubscribesController::class, 'save']);
Route::get('/customers_unsubscribe/{id}/destroy', [CustomerUnsubscribesController::class, 'destroy']);
Route::get('/customers_unsubscribe/{id}/edit', [CustomerUnsubscribesController::class, 'edit']);
Route::post('/customers_unsubscribe/{id}/update', [CustomerUnsubscribesController::class, 'update']);



Route::get('/reset_password', function(){
    // Generar un hash de prueba
    $hash = Hash::make('myseo2025');
    echo $hash."<br>";

    $contraseñaPlana = "myseo2025"; // La contraseña a verificar
    $usuario = User::find(8);

    if (!$usuario) {
        echo "Usuario con ID 8 no encontrado.";
        return;
    }

    $hashAlmacenado = $usuario->password;

    if (Hash::check($contraseñaPlana, $hashAlmacenado)) {
        echo "El hash es correcto.";
    } else {
        echo "El hash no coincide.";
    }

});

Route::get('/rdtest', [RDTestController::class, 'test']);
Route::post('/rdtest', [RDTestController::class, 'handleRequest'])->name('rdtest.post');

// Rutas para subir y eliminar archivos de órdenes
Route::post('/order-files/store', [OrderFileController::class, 'store'])->name('order-files.store');
Route::post('/order-files/delete/{id}', [OrderFileController::class, 'delete'])->name('order-files.delete');
