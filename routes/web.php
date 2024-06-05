<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RolespermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MaindashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignElementController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\ScheduleCampaign;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\UnipileController;

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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/auth/linkedin/redirect', function () {
    return Socialite::driver('linkedin-openid')->redirect();
});

Route::get('/auth/linkedin/callback', function () {
    $user = Socialite::driver('linkedin-openid')->stateless()->user();
    // echo $user->token;
    // dd($user);

    $data = [
        'title' => 'Setting'
    ];

    return view('dashboard-account', compact('data', 'user'));

    // return redirect('/dashboard');
});

// Route::get('linkedin/login', [SocialController::class, 'provider'])->name('linked.login');
// Route::get('linkedin/callback', [SocialController::class, 'providerCallback'])->name('linked.user');


Route::get('/team-rolesandpermission', [RolespermissionController::class, 'rolespermission']);
Route::get('/roles-and-permission-setting', [SettingController::class, 'settingrolespermission']);
Route::get('/report', [ReportController::class, 'report'])->name('dash-reports');
Route::get('/message', [MessageController::class, 'message'])->name('dash-messages');
Route::get('/contacts', [ContactController::class, 'contact']);
Route::get('/integration', [IntegrationController::class, 'integration'])->name('dash-integrations');
Route::get('/feature-suggestion', [FeatureController::class, 'featuresuggestions'])->name('dash-feature-suggestions');

Route::get('/', [HomeController::class, 'home']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/pricing', [HomeController::class, 'pricing']);
Route::get('/faq', [HomeController::class, 'faq']);
Route::get('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logoutUser'])->name('logout-user');
Route::get('/register', [RegisterController::class, 'register']);
Route::post('/register-user', [RegisterController::class, 'registerUser'])->name('register-user');
Route::get('/dashboard', [DasboardController::class, 'dashboard'])->name('dashobardz');
Route::get('/blacklist', [BlacklistController::class, 'blacklist']);
Route::get('/team', [TeamController::class, 'team']);
Route::get('/invoice', [InvoiceController::class, 'invoice']);
// Route::get('/rolesandpermission',[RolespermissionController::class,'rolespermission']);
Route::get('/setting', [SettingController::class, 'setting'])->name('dash-settings');
Route::get('/accdashboard', [MaindashboardController::class, 'maindasboard'])->name('acc_dash');
Route::post('/check-credentials', [LoginController::class, 'checkCredentials'])->name('checkCredentials');

Route::controller(StripePaymentController::class)->group(function () {
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});

Route::get('/campaign', [CampaignController::class, 'campaign'])->name('campaigns');
Route::get('/campaign/createcampaign', [CampaignController::class, 'campaigncreate'])->name('campaigncreate');
Route::post('/campaign/campaigninfo', [CampaignController::class, 'campaigninfo'])->name('campaigninfo');
Route::post('/campaign/createcampaignfromscratch', [CampaignController::class, 'fromscratch'])->name('createcampaignfromscratch');
Route::get('/campaign/getcampaignelementbyslug/{slug}', [CampaignElementController::class, 'campaignElement'])->name('getcampaignelementbyslug');
Route::post('/campaign/createCampaign', [CampaignElementController::class, 'createCampaign'])->name('createCampaign');
Route::get('/campaign/getPropertyDatatype/{id}/{element_slug}', [PropertiesController::class, 'getPropertyDatatype'])->name('getPropertyDatatype');
Route::get('/campaign/campaignDetails/{campaign_id}', [CampaignController::class, 'getCampaignDetails'])->name('campaignDetails');
Route::get('/campaign/changeCampaignStatus/{campaign_id}', [CampaignController::class, 'changeCampaignStatus'])->name('changeCampaignStatus');
Route::get('/campaign/{campaign_id}', [CampaignController::class, 'deleteCampaign'])->name('deleteCampaign');
Route::get('/campaign/archive/{campaign_id}', [CampaignController::class, 'archiveCampaign'])->name('archiveCampaign');
Route::get('/filterCampaign/{filter}/{search}', [CampaignController::class, 'filterCampaign'])->name('filterCampaign');
Route::post('/createSchedule', [ScheduleCampaign::class, 'createSchedule'])->name('createSchedule');
// Route::get('/campaign/scheduleDays/{schedule_id}', [ScheduleCampaign::class, 'scheduleDays'])->name('scheduleDays');
Route::get('/filterSchedule/{search}', [ScheduleCampaign::class, 'filterSchedule'])->name('filterSchedule');
Route::get('/getElements/{campaign_id}', [CampaignElementController::class, 'getElements'])->name('getElements');
Route::get('/campaign/editcampaign/{campaign_id}', [CampaignController::class, 'editCampaign'])->name('editCampaign');
Route::post('/campaign/editCampaignInfo/{campaign_id}', [CampaignController::class, 'editCampaignInfo'])->name('editCampaignInfo');
Route::post('/campaign/editCampaignSequence/{campaign_id}', [CampaignController::class, 'editCampaignSequence'])->name('editCampaignSequence');
Route::get('/campaign/getcampaignelementbyid/{element_id}', [CampaignElementController::class, 'getcampaignelementbyid'])->name('getcampaignelementbyid');
Route::post('/campaign/updateCampaign/{campaign_id}', [CampaignController::class, 'updateCampaign'])->name('updateCampaign');
Route::post('/import_csv', [CsvController::class, 'import_csv'])->name('import_csv');
Route::get('/campaign/getPropertyRequired/{id}', [PropertiesController::class, 'getPropertyRequired'])->name('getPropertyRequired');

Route::get('/leads', [LeadsController::class, 'leads'])->name('dash-leads');
Route::get('/leads/getLeadsByCampaign/{id}/{search}', [LeadsController::class, 'getLeadsByCampaign'])->name('getLeadsByCampaign');
Route::post('/leads/sendLeadsToEmail', [LeadsController::class, 'sendLeadsToEmail'])->name('sendLeadsToEmail');
Route::get('/leads/getLeadsCountByCampaign/{id}', [LeadsController::class, 'getLeadsCountByCampaign'])->name('getLeadsCountByCampaign');

Route::get('/get_relations', [UnipileController::class, 'get_relations'])->name('getAllRelations');

Route::match(['get', 'post'], '/unipile-callback', [UnipileController::class, 'handleCallback']);

