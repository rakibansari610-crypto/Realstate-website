<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\BrochureLeadController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ContactInquiresController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\GoyzerSyncController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadAssignController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadMaterialiseController;
use App\Http\Controllers\LeadOfferController;
use App\Http\Controllers\LeadTaskController;
use App\Http\Controllers\MailSettingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrgUserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartnerCommissionController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PaymentPlanController;
use App\Http\Controllers\ProjectAmenitiesController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMapPointController;
use App\Http\Controllers\ProjectMaterialController;
use App\Http\Controllers\ProjectMediaController;
use App\Http\Controllers\ProjectUnitController;
use App\Http\Controllers\SeoMetaController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StandMockupController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TypicalUnitsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitImageController;
use App\Http\Middleware\ResolveTenant;
use App\Models\Blog;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/debug-url', function () {
    return [
        'app_url'  => config('app.url'),
        'asset_url' => config('app.asset_url'),
        'asset'    => asset('frontend/css/styles.css'),
        'url'      => url('/'),
        'port'     => request()->getPort(),
        'host'     => request()->getHttpHost(),
    ];
});

Route::get('/create-storage-link', function () {
    $link = base_path('public/storage');
    $backup = base_path('public/storage_backup_old');

    if (file_exists($backup)) {
        return "Backup already exists at: " . $backup . " — pehle usse check/delete karein.";
    }

    if (rename($link, $backup)) {
        // Ab naya symlink banate hain
        $target = base_path('storage/app/public');
        if (symlink($target, $link)) {
            return "✅ Purana folder backup ho gaya: $backup <br>✅ Naya symlink ban gaya: $link";
        }
        return "⚠️ Backup ho gaya lekin symlink fail hui. Manually check karein.";
    }

    return "❌ Rename fail hua.";
});

$rootDomain = config('app.root_domain', parse_url(config('app.url'), PHP_URL_HOST));
Route::get('/', function () {
    $country = session('country', 'in'); // fallback

    return redirect($country, 301);
})->domain($rootDomain);
Route::domain($rootDomain)->group(function () {

    Route::get('/', function () {

        if (session()->has('country')) {
            // Change to 301
            return redirect(session('country'), 301);
        }

        $ip = request()->ip();

        if ($ip === '::1' || $ip === '127.0.0.1') {
            $ip = '103.76.253.131';
        }

        $location = geoip()->getLocation($ip);
        $country = strtolower($location->iso_code);

        $finalCountry = $country === 'in' ? 'in' : 'ae';
        session(['country' => $finalCountry]);

        // Change to 301
        return redirect($finalCountry, 301);
    });

    Auth::routes();
});

Route::domain('{tenant}.'.$rootDomain)
    ->middleware([\App\Http\Middleware\ResolveTenant::class])
    ->group(function () {
        // dd("1");
        Auth::routes();
        Route::redirect('/', '/login')->name('tenant.home');
    });

Route::get('/set-country/{country}', function ($country) {

    if (! in_array($country, ['in', 'ae'])) {
        return redirect('/', 301);
    }

    session([
        'country' => $country,
        'manual_selection' => true,
    ]);

    return redirect('/'.$country, 301);

})->name('set.country');
Route::get('/sitemap.xml', function () {

    return response()->view('sitemap.index')
        ->header('Content-Type', 'text/xml');

});
Route::get('/sitemap-static.xml', function () {

    return response()->view('sitemap.static')
        ->header('Content-Type', 'text/xml');

});
Route::get('/sitemap-in.xml', function () {
    $country = resolveSitemapCountry('in');

    abort_unless($country, 404);

    return response()->view('sitemap.country', [
        'country' => $country,
        'countrySlug' => 'in',
    ])->header('Content-Type', 'text/xml');
});

Route::get('/sitemap-ae.xml', function () {
    $country = resolveSitemapCountry('ae');

    abort_unless($country, 404);

    return response()->view('sitemap.country', [
        'country' => $country,
        'countrySlug' => 'ae',
    ])->header('Content-Type', 'text/xml');
});

Route::get('/sitemap-{country}-projects.xml', function ($country) {
    $countryRow = resolveSitemapCountry($country);

    abort_unless($countryRow, 404);

    $projects = Project::query()
        ->where('projects.sale_status', '!=', 'out_of_stock')
        ->where('projects.country_id', $countryRow->id)
        ->get();

    return response()->view('sitemap.projects', [
        'projects' => $projects,
        'country' => $countryRow,
        'countrySlug' => strtolower($country) === 'uae' ? 'ae' : strtolower($country),
    ])->header('Content-Type', 'text/xml');
});

Route::get('/sitemap-{country}-properties.xml', function ($country) {
    $countryRow = resolveSitemapCountry($country);

    abort_unless($countryRow, 404);

    $properties = Unit::query()
        ->where('status', '1')
        ->where('country_id', $countryRow->id)
        ->get();

    return response()->view('sitemap.properties', [
        'properties' => $properties,
        'country' => $countryRow,
        'countrySlug' => strtolower($country) === 'uae' ? 'ae' : strtolower($country),
    ])->header('Content-Type', 'text/xml');
});

Route::get('/sitemap-{country}-pages.xml', function ($country) {
    $countryRow = resolveSitemapCountry($country);

    abort_unless($countryRow, 404);

    $countrySlug = strtolower($country) === 'uae' ? 'ae' : strtolower($country);
    $base = url('/'.$countrySlug);

    $urls = [
        $base.'/buy',
        $base.'/rent',
        // $base.'/projects',
        $base.'/project-lists',
        $base.'/developers',
        $base.'/properties',
    ];

    return response()->view('sitemap.pages', [
        'urls' => $urls,
        'country' => $countryRow,
        'countrySlug' => $countrySlug,
    ])->header('Content-Type', 'text/xml');
});

Route::get('/sitemap-{country}-news.xml', function ($country) {
    $countryRow = resolveSitemapCountry($country);

    abort_unless($countryRow, 404);

    $blogs = Blog::query()
        ->where('status', 'published')
        ->where('country_id', $countryRow->id)
        ->get();

    return response()->view('sitemap.news', [
        'blogs' => $blogs,
        'country' => $countryRow,
        'countrySlug' => strtolower($country) === 'uae' ? 'ae' : strtolower($country),
    ])->header('Content-Type', 'text/xml');
});
Route::get('/run-artisan', [ArtisanController::class, 'runCommand']);
Route::get('/support', [HomeController::class, 'support'])->name('support');
Route::post('support/store', [HomeController::class, 'supportStore'])->name('support-store');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-conditions', [HomeController::class, 'termsCondtions'])->name('terms-conditions');
Route::get('/contact-us', [HomeController::class, 'contactUs'])->name('contact-us');
Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about-us');
Route::get('/services', [HomeController::class, 'servicePage'])->name('services');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/search-location', [ProjectController::class, 'searchLocation'])->name('search.location');
Route::post('contact_us/enquiry/store',[ContactInquiresController::class, 'contactEnquiryStore'])->name('contact-us-enquiry-store');
Route::prefix('{country}')
    ->whereIn('country', ['in', 'ae'])   // only allow these codes
    ->middleware(['country.session'])
    ->group(function () {

        Route::get('/', [HomeController::class, 'landing'])->name('landing');

        // Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');

        Route::get('/news', [HomeController::class, 'news'])->name('news');
        Route::get('/news/search', [HomeController::class, 'news_search'])->name('news.search');
        Route::get('/buy', [HomeController::class, 'buyProperties'])->name('buy.properties');
        Route::get('/rent', [HomeController::class, 'rentProperties'])->name('rent.properties');

        Route::get('/properties/{type}', [HomeController::class, 'properties_type'])->name('properties');

        Route::get('properties', [HomeController::class, 'properties'])->name('properties_search');
        Route::get('property/{slug}', [HomeController::class, 'propertyDetails'])->name('property.details');

        // project created by Omprakash
        Route::get('project-lists', [ProjectController::class, 'project'])->name('project');
        Route::get('/projects/{slug}', [ProjectController::class, 'project_details'])->name('project-details');

        // all agetns
        Route::get('/agents', [HomeController::class, 'all_agents'])->name('all_agents');

        // developers
        Route::get('developers', [DeveloperController::class, 'developer'])->name('developers');
        Route::get('/ai/chat', [AiChatController::class, 'page'])->name('ai.chat.page');
        Route::post('/ai/chat/message', [AiChatController::class, 'message'])->name('ai.chat.message');
        Route::post('/ai/chat/lead', [AiChatController::class, 'store'])->name('ai.chat.lead');
        Route::middleware([ResolveTenant::class])->get('/pass/{token}', [HomeController::class, 'publicPass'])->name('public.pass');
        Route::get('/stands', [StandMockupController::class, 'index'])->name('stands.index');
        Route::post('/stands/mockup', [StandMockupController::class, 'generate'])->name('stands.generate');

        /*Route::middleware([ResolveTenant::class])->group(function () {
            //dd("4");
            Auth::routes();
        });*/
        Route::get('/reelly_projects', [HomeController::class, 'reellyProjects']);
        Route::get('/reelly/projects/{id}', [HomeController::class, 'showProject']);
        Route::get('/reelly/projects/{id}/units', [HomeController::class, 'projectUnits']);

        // contact inquireies front form
        Route::post('contact/inquiries/store', [ContactInquiresController::class, 'contactStore'])->name('contact.inquiries.store');

        Route::post('brochure/contact-store', [BrochureLeadController::class, 'brochureContactStore'])
            ->name('brochure.contact.store');

        Route::get('brochure/download', [BrochureLeadController::class, 'downloadBrochure'])->name('brochure.download');

        Route::get('news/{slug}', [HomeController::class, 'blog_details'])->name('news-details');
        Route::get('/search-blog', [BlogController::class, 'search'])->name('search.blog');

    });

Route::get('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['role:super admin|org admin']], function () {

    Route::get('/su/dashboard', [DashboardController::class, 'su_dashboard'])->name('su.dashboard');

    // ORGANISATIONS
    Route::get('/su/organizations/get', [OrganizationController::class, 'get'])->name('su.org.get');
    Route::get('/su/organizations', [OrganizationController::class, 'index'])->name('su.org.index');
    Route::get('/su/organizations/create', [OrganizationController::class, 'create'])->name('su.org.create');
    Route::post('/su/organizations', [OrganizationController::class, 'store'])->name('su.org.store');
    Route::get('/su/organizations/{id}/edit', [OrganizationController::class, 'edit'])->name('su.org.edit');
    Route::put('/su/organizations/{id}', [OrganizationController::class, 'update'])->name('su.org.update');
    Route::delete('/su/organizations/{id}', [OrganizationController::class, 'destroy'])->name('su.org.destroy');

    Route::get('/su/organizations/{org}/branding', [BrandingController::class, 'edit'])->name('su.org.branding.edit');
    Route::put('/su/organizations/{org}/branding', [BrandingController::class, 'update'])->name('su.org.branding.update');
});
Route::group(['middleware' => ['role:property consultant']], function () {
    Route::get('/consultant/dashboard', [DashboardController::class, 'org_dashboard'])->name('cp.dashboard');
});
Route::group(['middleware' => ['role:freelancer']], function () {
    Route::get('/freelancer/dashboard', [DashboardController::class, 'org_dashboard'])->name('freelancer.dashboard');
});
Route::middleware([ResolveTenant::class, 'auth', 'role:super admin|data entry|channel partner|org admin|property consultant|freelancer|rm'])->group(function () {
    Route::post('/admin/goyzer/sync', [GoyzerSyncController::class, 'sync'])
        ->name('admin.goyzer.sync');
    Route::get('/privacy/policy', [PageController::class, 'index'])
        ->name('privacy.index');

    Route::get('/privacy/policy/edit', [PageController::class, 'edit'])
        ->name('privacy.edit');

    Route::post('/privacy/policy/update', [PageController::class, 'update'])->name('privacy.update');

    Route::get('/terms/conditions', [PageController::class, 'termsIndex'])
        ->name('terms.conditions.index');

    Route::get('/terms/conditions/edit', [PageController::class, 'termsEdit'])
        ->name('terms.conditions.edit');

    Route::post('/terms/conditions/update', [PageController::class, 'TermsUpdate'])->name('terms.conditions.update');

    Route::get('/support/index', [SupportController::class, 'index'])->name('org-support-index');
    Route::get('/support/get', [SupportController::class, 'get'])->name('org-support-get');
    Route::get('/support/delete/{id}', [SupportController::class, 'destroy'])->name('org-support-delete');

    // contact inquiries
    Route::get('contact/inquiry/get', [ContactInquiresController::class, 'contactGet'])->name('org-contact-get');
    Route::get('contact/inquiries/{type?}', [ContactInquiresController::class, 'contactIndex'])->name('org-contact-index');
    Route::get('contact/inquiries/delete/{id}', [ContactInquiresController::class, 'contactDestroy'])->name('org-contact-delete');

    // brochure inquiries
    Route::get('brochure/inquiries', [BrochureLeadController::class, 'index'])->name('org-brochure-index');

    Route::get('brochure/inquiries/get', [BrochureLeadController::class, 'get'])->name('org-brochure-get');
    Route::get('brochure/inquiries/delete/{id}', [BrochureLeadController::class, 'destroy'])->name('org-brochure-delete');

    // category
    Route::get('org/category', [CategoryController::class, 'index'])->name('org.category.index');
    Route::get('org/category/create', [CategoryController::class, 'create'])->name('org.category.create');
    Route::post('org/category/store', [CategoryController::class, 'store'])->name('org.category.store');
    Route::get('org/category/{id}/edit', [CategoryController::class, 'edit'])->name('org.category.edit');
    Route::post('org/category/update/{id}', [CategoryController::class, 'update'])->name('org.category.update');
    Route::delete('org/category/destroy/{id}', [CategoryController::class, 'destroy'])
        ->name('org.category.destroy');

    // country
    Route::get('org/country', [CountryController::class, 'index'])->name('org.country.index');
    Route::get('org/country/create', [CountryController::class, 'create'])->name('org.country.create');
    Route::post('org/country/store', [CountryController::class, 'store'])->name('org.country.store');
    Route::get('org/country/edit/{id}', [CountryController::class, 'edit'])->name('org.country.edit');
    Route::post('org/country/update/{id}', [CountryController::class, 'update'])->name('org.country.update');
    Route::delete('org/country/destroy/{id}', [CountryController::class, 'destroy'])->name('org.country.destroy');

    // sub_category
    Route::get('org/sub_category', [SubCategoryController::class, 'index'])->name('org.sub_category.index');
    Route::get('org/sub_category/create', [SubCategoryController::class, 'create'])->name('org.sub_category.create');
    Route::post('org/sub_category/store', [SubCategoryController::class, 'store'])->name('org.sub_category.store');
    Route::get('org/sub_category/{id}/edit', [SubCategoryController::class, 'edit'])->name('org.sub_category.edit');
    Route::post('org/sub_category/update/{id}', [SubCategoryController::class, 'update'])->name('org.sub_category.update');
    Route::delete('org/sub_category/destroy/{id}', [SubCategoryController::class, 'destroy'])
        ->name('org.sub_category.destroy');
    Route::get('org/subcategory.get', [SubCategoryController::class, 'getSubCategories'])->name('org.subcategory.get');

    Route::get('/cp/dashboard', [DashboardController::class, 'org_dashboard'])->name('cp.dashboard');
    Route::get('/org/dashboard', [DashboardController::class, 'org_dashboard'])->name('org.dashboard');

    // rm dashbaord
    Route::get('rm/dashboard', [DashboardController::class, 'org_dashboard'])->name('rm.dashboard');

    // SITES
    Route::get('/org/sites', [SiteController::class, 'index'])->name('org.sites.index');
    Route::get('/org/sites/create', [SiteController::class, 'create'])->name('org.sites.create');
    Route::post('/org/sites', [SiteController::class, 'store'])->name('org.sites.store');
    Route::get('/org/sites/{id}/edit', [SiteController::class, 'edit'])->name('org.sites.edit');
    Route::put('/org/sites/{id}', [SiteController::class, 'update'])->name('org.sites.update');
    Route::delete('/org/sites/{id}', [SiteController::class, 'destroy'])->name('org.sites.destroy');

    Route::get('/org/users', [OrgUserController::class, 'index'])->name('org.users.index');
    Route::get('/org/users/create', [OrgUserController::class, 'create'])->name('org.users.create');
    Route::post('/org/users', [OrgUserController::class, 'store'])->name('org.users.store');
    Route::get('/org/users/{id}/edit', [OrgUserController::class, 'edit'])->name('org.users.edit');
    Route::put('/org/users/{id}', [OrgUserController::class, 'update'])->name('org.users.update');
    Route::delete('/org/users/{id}', [OrgUserController::class, 'destroy'])->name('org.users.destroy');

    Route::get('/org/settings/mail', [MailSettingController::class, 'edit'])->name('org.mail.edit');
    Route::put('/org/settings/mail', [MailSettingController::class, 'update'])->name('org.mail.update');
    Route::post('/org/settings/mail/test', [MailSettingController::class, 'test'])->name('org.mail.test');

    Route::prefix('org-admin')->name('org.')->group(function () {
        Route::get('developer/get', [DeveloperController::class, 'get'])->name('developers.get');
        Route::get('developers/{developer}/view', [DeveloperController::class, 'edit'])->name('developers.view');
        Route::resource('developers', DeveloperController::class)
            ->parameters(['developers' => 'developer']);
    });

    Route::prefix('org-admin')->name('org.')->group(function () {

        Route::post('/projects/amenities/store', [ProjectAmenitiesController::class, 'storeAmenities'])->name('projects.amenities.store');

        Route::delete('/projects/amenities/destroy/{id}', [ProjectAmenitiesController::class, 'destroy'])->name('projects.amenities.destroy');

        Route::get('/get-subcategories/{category_id}/{project_id}', [ProjectController::class, 'getSubCategories'])
            ->name('org.get.subcategories');

        Route::get('org/project/subcategory.get', [ProjectAmenitiesController::class, 'getSubCategories'])->name('project.subcategory.get');
        Route::get('org/project/{project}/view', [ProjectController::class, 'edit'])->name('projects.view');
        Route::get('projects/get', [ProjectController::class, 'get'])->name('projects.get');
        Route::post('get/countriesdeveloper', [ProjectController::class, 'getCountriesDeveloper'])->name('get.countriesdeveloper');
        Route::get('get/unit_type/amenities', [ProjectController::class, 'get_unit_types_amenities'])->name('get.unit_type.amenities');

        Route::resource('projects', ProjectController::class);

        // typical units
        Route::get('typical_units/get', [TypicalUnitsController::class, 'get'])->name('typical.units.get');
        Route::post('typical_units/store', [TypicalUnitsController::class, 'store'])->name('typical.units.store');
        Route::get('typical_units/edit/{id}', [TypicalUnitsController::class, 'edit'])->name('typical.units.edit');
        Route::delete('typical_units/destroy/{typical}', [TypicalUnitsController::class, 'destroy'])->name('typical.units.destroy');
        Route::get('/get-room-types', [TypicalUnitsController::class, 'getRoomTypes'])->name('get-room-types');

        // project  media
        Route::post('project/media/store', [ProjectMediaController::class, 'store'])->name('project-media.store');
        Route::get('project/media/get', [ProjectMediaController::class, 'get'])->name('project-media.get');
        Route::get('project/media/edit/{id}', [ProjectMediaController::class, 'edit'])->name('project-media.edit');
        Route::delete('project/media/destroy/{media}', [ProjectMediaController::class, 'destroy'])->name('project-media.destroy');

        // payment plan
        Route::post('payment/plan/store', [PaymentPlanController::class, 'storePaymentPlan'])->name('payment-plan.store');
        Route::get('payment/plan/get', [PaymentPlanController::class, 'getPaymentPlan'])->name('payment-plan.get');
        Route::get('payment/plan/edit/{id}', [PaymentPlanController::class, 'editPlan'])->name('payment-plan.edit');
        Route::delete('payment/plan/destroy/{paymentPlan}', [PaymentPlanController::class, 'destroyPaymentPlan'])->name('payment-plan.destroy');

        // payment plan step
        Route::get('payment/plan/step/create/{id}', [PaymentPlanController::class, 'createStepPlan'])->name('payment-plan.step.create');
        Route::post('payment/plan/step/store', [PaymentPlanController::class, 'storePlanStep'])->name('payment-plan.steps.store');
        Route::get('payment/plan/step/get', [PaymentPlanController::class, 'getPlanStep'])->name('payment-plan.step.get');
        Route::get('payment/plan/step/edit/{id}', [PaymentPlanController::class, 'editPlanStep'])->name('payment-plan.step.edit');
        Route::post('payment-plan/steps/update/{plan}', [PaymentPlanController::class, 'updatePlanStep'])->name('payment-plan.steps.update');
        Route::delete('payment/plan/step/destroy/{paymentPlanStep}', [PaymentPlanController::class, 'destroyPlanStep'])->name('payment-plan.step.destroy');

        // near by plan
        Route::get('nearBy/plan/get', [ProjectMapPointController::class, 'get'])->name('near-by-plan.get');
        Route::post('nearBy/plan/store', [ProjectMapPointController::class, 'store'])->name('near-by-plan.store');
        Route::post('nearBy/plan/update/{id}', [ProjectMapPointController::class, 'update'])->name('near-by-plan.update');
        Route::get('nearBy/plan/edit/{id}', [ProjectMapPointController::class, 'edit'])->name('near-by-plan.edit');
        Route::delete('nearBy/plan/destroy/{nearBy}', [ProjectMapPointController::class, 'destroy'])->name('near-by-plan.destroy');

        // agent routes
        Route::get('agent/index', [AgentController::class, 'index'])->name('agent-index');
        Route::get('agent/create', [AgentController::class, 'create'])->name('agent-create');
        Route::post('agent/store/{id?}', [AgentController::class, 'store'])->name('agent-store');
        Route::get('agent/get', [AgentController::class, 'get'])->name('agent-get');

        Route::get('agent/edit/{id}', [AgentController::class, 'edit'])->name('agent-edit');
        Route::post('agent/update/{id}', [AgentController::class, 'update'])->name('agent-update');
        Route::delete('agent/destroy/{agent}', [AgentController::class, 'destroy'])->name('agent-destroy');

        // testimonial
        Route::get('testimonial/index', [TestimonialController::class, 'index'])->name('testimonial-index');
        Route::get('testimonial/get', [TestimonialController::class, 'get'])->name('testimonial-get');
        Route::get('testimonial/create', [TestimonialController::class, 'create'])->name('testimonial-create');
        Route::post('testimonial/store', [TestimonialController::class, 'store'])->name('testimonial-store');
        Route::get('testimonial/edit/{id}', [TestimonialController::class, 'edit'])->name('testimonial-edit');
        Route::post('testimonial/update/{id}', [TestimonialController::class, 'update'])->name('testimonial-update');
        Route::delete('testimonial/destroy/{id}', [TestimonialController::class, 'destroy'])->name('testimonial-destroy');
        Route::get('testimonial/profile/destroy/{id}', [TestimonialController::class, 'destroyImage'])->name('testimonial-profile-destroy');

        // blogs
        Route::get('blogs/index', [BlogController::class, 'index'])->name('blogs-index');
        Route::get('blogs/get', [BlogController::class, 'get'])->name('blogs-get');
        Route::get('blogs/create', [BlogController::class, 'create'])->name('blogs-create');
        Route::post('blogs/store', [BlogController::class, 'store'])->name('blogs-store');
        Route::get('blogs/edit/{id}', [BlogController::class, 'edit'])->name('blogs-edit');
        Route::post('blogs/update/{id}', [BlogController::class, 'update'])->name('blogs-update');
        Route::delete('blogs/destroy/{id}', [BlogController::class, 'destroy'])->name('blogs-destroy');

        // nested: units
        Route::get('projects/{project}/units', [ProjectUnitController::class, 'index'])->name('projects.units.index');
        Route::get('projects/{project}/units/create', [ProjectUnitController::class, 'create'])->name('projects.units.create');
        Route::post('projects/{project}/units', [ProjectUnitController::class, 'store'])->name('projects.units.store');
        Route::get('projects/{project}/units/{unit}/edit', [ProjectUnitController::class, 'edit'])->name('projects.units.edit');

        Route::put('projects/{project}/units/{unit}', [ProjectUnitController::class, 'update'])->name('projects.units.update');
        Route::delete('projects/{project}/units/{unit}', [ProjectUnitController::class, 'destroy'])->name('projects.units.destroy');

        // nested: marketing materials
        Route::get('projects/{project}/materials', [ProjectMaterialController::class, 'index'])->name('projects.materials.index');
        Route::post('projects/{project}/materials', [ProjectMaterialController::class, 'store'])->name('projects.materials.store');
        Route::delete('projects/{project}/materials/{material}', [ProjectMaterialController::class, 'destroy'])->name('projects.materials.destroy');

    });
    Route::prefix('org-admin')->name('org.')->group(function () {
        Route::get('leads/get', [LeadController::class, 'get'])->name('leads.get');
        Route::get('leads/{lead}/view', [LeadController::class, 'edit'])->name('leads.view');
        Route::resource('leads', LeadController::class)->parameters(['leads' => 'lead']);

        // Ajax: units for project (for dependent dropdown)
        Route::get('projects/{project}/units/options', [LeadController::class, 'unitsForProject'])
            ->name('projects.units.options');

    });
    Route::get('/leads/{lead}/tasks', [LeadTaskController::class, 'index'])
        ->name('leads.tasks.index');

    Route::get('/leads/{lead}/tasks/store', [LeadTaskController::class, 'store'])
        ->name('leads.tasks.store');

    // leads offers
    Route::get('/leads/{lead}/offers', [LeadOfferController::class, 'index'])->name('leads.offers.index');
    Route::get('/leads/{lead}/offers/store', [LeadOfferController::class, 'store'])->name('leads.offers.store');

    Route::prefix('org-admin')->name('org.')->group(function () {
        // Admin-only: assign & reassign
        Route::get('leads/{lead}/assign', [LeadAssignController::class, 'edit'])->name('leads.assign');
        Route::put('leads/{lead}/assign', [LeadAssignController::class, 'update'])->name('leads.assign.update');

        // Admin-only: materialise to customer
        Route::get('leads/{lead}/materialise', [LeadMaterialiseController::class, 'create'])->name('leads.materialise');
        Route::post('leads/{lead}/materialise', [LeadMaterialiseController::class, 'store'])->name('leads.materialise.store');
    });
    Route::prefix('org-admin')->name('org.')->group(function () {

        Route::get('partners/get', [PartnerController::class, 'get'])->name('partners.get');
        Route::resource('partners', PartnerController::class)
            ->parameters(['partners' => 'partner']);

        // Per-project commissions for a partner
        Route::get('partners/{partner}/commissions', [PartnerCommissionController::class, 'index'])
            ->name('partners.commissions.index');
        Route::post('partners/{partner}/commissions', [PartnerCommissionController::class, 'store'])
            ->name('partners.commissions.store');
        Route::delete('partners/{partner}/commissions/{commission}', [PartnerCommissionController::class, 'destroy'])
            ->name('partners.commissions.destroy');

        /** LEADS: Admin-only actions (assign / materialise) */
        Route::get('leads/{lead}/assign', [LeadAssignController::class, 'edit'])
            ->name('leads.assign');
        Route::put('leads/{lead}/assign', [LeadAssignController::class, 'update'])
            ->name('leads.assign.update');

        Route::get('leads/{lead}/materialise', [LeadMaterialiseController::class, 'create'])
            ->name('leads.materialise');
        Route::post('leads/{lead}/materialise', [LeadMaterialiseController::class, 'store'])
            ->name('leads.materialise.store');
        Route::get('seo-meta/index', [SeoMetaController::class, 'index'])->name('seo-meta.index');
        Route::get('seo-meta/create', [SeoMetaController::class, 'create'])->name('seo-meta.create');
        Route::get('seo-meta/get', [SeoMetaController::class, 'get'])->name('seo-meta.get');
        Route::post('seo-meta/store', [SeoMetaController::class, 'store'])->name('seo-meta.store');
        Route::get('seo-meta/edit/{id}', [SeoMetaController::class, 'edit'])->name('seo-meta.edit');
        Route::post('seo-meta/update', [SeoMetaController::class, 'update'])->name('seo-meta.update');
        Route::delete('seo-meta/destroy/{id}', [SeoMetaController::class, 'destroy'])->name('seo-meta.destroy');

         /** Header Menus Management */
        Route::get('menus/index/{editId?}', [MenuController::class, 'index'])->name('menus.index');
        Route::get('menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::get('menus/get', [MenuController::class, 'get'])->name('menus.get');
        Route::post('menus/store', [MenuController::class, 'store'])->name('menus.store');
        Route::post('menus/save-order', [MenuController::class, 'saveOrder'])->name('menus.save_order');
        Route::get('menus/edit/{id}', [MenuController::class, 'edit'])->name('menus.edit');
        Route::post('menus/update/{id}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('menus/destroy/{id}', [MenuController::class, 'destroy'])->name('menus.destroy');
    });
    

    Route::get('units', [UnitController::class, 'index'])->name('units.index');
    Route::get('units/get', [UnitController::class, 'get'])->name('units.get');
    Route::get('units/create', [UnitController::class, 'create'])->name('units.create');
    Route::post('units', [UnitController::class, 'store'])->name('units.store');
    Route::get('units/edit/{unit}', [UnitController::class, 'edit'])->name('units.edit');
    Route::get('units/view/{unit}', [UnitController::class, 'edit'])->name('units.view');
    Route::post('units/{unit}', [UnitController::class, 'update'])->name('units.update');
    Route::delete('units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

    Route::post('units/{unit}/images', [UnitImageController::class, 'store'])
        ->name('units.images.store');

    Route::put('units/{unit}/images/{image}', [UnitImageController::class, 'update'])
        ->name('units.images.update');

    Route::delete('units/{unit}/images/{image}', [UnitImageController::class, 'destroy'])
        ->name('units.images.destroy');

    Route::get('/get-projects-by-developer', [UnitController::class, 'getProjectsByDeveloper'])
        ->name('getProjectsByDeveloper');
    Route::get('/get-sub-types', [UnitController::class, 'getSubTypes'])->name('get-sub-types');

    Route::prefix('taxonomies/{module}')->middleware(['auth'])->group(function () {
        Route::get('/', [TaxonomyController::class, 'index'])->name('taxonomies.index');
        Route::get('/create', [TaxonomyController::class, 'create'])->name('taxonomies.create');
        Route::post('/store', [TaxonomyController::class, 'store'])->name('taxonomies.store');
        Route::get('/{category}/edit', [TaxonomyController::class, 'edit'])->name('taxonomies.edit');
        Route::put('/{category}', [TaxonomyController::class, 'update'])->name('taxonomies.update');
        Route::delete('/{category}', [TaxonomyController::class, 'destroy'])->name('taxonomies.destroy');
        Route::get('/get', [TaxonomyController::class, 'get'])->name('taxonomies.get');
    });
});
Route::group(['middleware' => [ResolveTenant::class, 'auth', 'role:employee']], function () {

    Route::get('/employee/dashboard', [DashboardController::class, 'employee_dashboard'])->name('employee.dashboard');
});
Route::group(['middleware' => [ResolveTenant::class, 'auth', 'role:receptionist']], function () {
    Route::get('/receptionist/dashboard', [DashboardController::class, 'receptionist_dashboard'])->name('receptionist.dashboard');
});
Route::middleware([ResolveTenant::class, 'auth', 'role:super admin|site admin|receptionist|employee'])->group(function () {});

Route::middleware([ResolveTenant::class, 'auth'])->group(function () {
    Route::get('change/password', [ChangePasswordController::class, 'change_password'])->name('change.password');
    Route::post('reset/password/store', [ChangePasswordController::class, 'change_password_store'])->name('reset-password-store');

    Route::get('reset/password/{id}', [ChangePasswordController::class, 'reset_password'])->name('partner-reset-password');
    Route::post('partnerreset/password/store', [ChangePasswordController::class, 'reset_password_store'])->name('partner-reset-password-store');

});
