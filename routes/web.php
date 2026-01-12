<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Developer\InstitutionController as DeveloperInstitutionController;
use App\Http\Controllers\Developer\InmateController as DeveloperInmateController;
use App\Http\Controllers\Developer\UserController as DeveloperUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\NurseDashboardController;
use App\Http\Controllers\Admin\InmateController as AdminInmateController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Doctor\InmateController as DoctorInmateController;
use App\Http\Controllers\Nurse\InmateController as NurseInmateController;
use App\Http\Controllers\Doctor\MedicalRecordController;
use App\Http\Controllers\Doctor\LabTestController as DoctorLabTestController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Nurse\LabTestController as NurseLabTestController;
use App\Http\Controllers\Nurse\MedicationLogController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\Staff\InmateController as StaffInmateController;
use App\Http\Controllers\Staff\MedicationLogController as StaffMedicationLogController;
use App\Http\Controllers\GuardianDashboardController;
use App\Http\Controllers\GuardianMessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\GuardianController as DeveloperGuardianController;
use App\Http\Controllers\Admin\GuardianController as AdminGuardianController;
use App\Http\Controllers\Admin\ActionRequestController as AdminActionRequestController;
use App\Http\Controllers\Developer\ActionRequestController as DeveloperActionRequestController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\Developer\SupportTicketController as DeveloperSupportTicketController;
use App\Http\Controllers\SystemAdmin\SystemAdminDashboardController;
use App\Http\Controllers\SystemAdmin\InstitutionController as SystemAdminInstitutionController;
use App\Http\Controllers\Public\InstitutionController as PublicInstitutionController;
use App\Http\Controllers\Public\DonationController as PublicDonationController;
use App\Http\Controllers\SystemAdmin\InmateController as SystemAdminInmateController;
use App\Http\Controllers\SystemAdmin\UserController as SystemAdminUserController;
use App\Http\Controllers\SystemAdmin\GuardianController as SystemAdminGuardianController;
use App\Http\Controllers\Developer\DoctorController as DeveloperDoctorController;
use App\Http\Controllers\SystemAdmin\DoctorController as SystemAdminDoctorController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Developer\LocationController as DeveloperLocationController;
use App\Http\Controllers\Developer\BlockController as DeveloperBlockController;
use App\Http\Controllers\Admin\BlockController as AdminBlockController;
use App\Http\Controllers\SystemAdmin\BlockController as SystemAdminBlockController;
use App\Http\Controllers\SystemAdmin\MedicineController as SystemAdminMedicineController;
use App\Http\Controllers\SystemAdmin\InmatePaymentController as SystemAdminInmatePaymentController;
use App\Http\Controllers\SystemAdmin\InmateSearchController as SystemAdminInmateSearchController;
use App\Http\Controllers\Admin\MedicineController as AdminMedicineController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Public\BlogController as PublicBlogController;

// Public website pages (static, no app logic)
Route::get('/', function() {
    $galleryImages = \App\Models\GalleryImage::latest()->take(10)->get();
    return view('public.home', compact('galleryImages'));
})->name('home');
Route::view('/about', 'public.about')->name('about');
Route::view('/timeline', 'public.timeline')->name('timeline');
// Institutions (public)
// Institutions (public)
Route::get('/institutions', [PublicInstitutionController::class, 'index'])->name('institutions.index');
Route::get('/institutions/{id}', [PublicInstitutionController::class, 'show'])->name('institutions.show');

Route::get('/gallery', function() {
    $images = \App\Models\GalleryImage::latest()->get();
    return view('public.gallery', compact('images'));
})->name('gallery');
<<<<<<< HEAD
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Public\ContactController::class, 'store'])->name('contact.store');
Route::get('/donate', [PublicDonationController::class, 'index'])->name('donate');
Route::post('/donate', [PublicDonationController::class, 'store'])->name('donate.store');
=======
Route::view('/contact', 'public.contact')->name('contact');
Route::get('/donate', [PublicDonationController::class, 'index'])->name('donate');
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155

// Blog (public, dynamic)
Route::get('/blog', [PublicBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [PublicBlogController::class, 'show'])->name('blog.show');

Route::get('/dashboard', [DashboardController::class,'index'])->middleware(['auth','verified'])->name('dashboard');

// Developer routes
Route::middleware(['auth','verified','role:developer'])->prefix('developer')->name('developer.')->group(function() {
    Route::get('/dashboard', [DeveloperDashboardController::class,'index'])->name('dashboard');
    Route::resource('institutions', DeveloperInstitutionController::class);
    Route::resource('inmates', DeveloperInmateController::class);
    Route::resource('users', DeveloperUserController::class); // includes show for profile view
    Route::post('users/{user}/toggle-bug-reporting', [DeveloperUserController::class,'toggleBugReporting'])->name('users.toggle-bug-reporting');
    Route::patch('users/{user}/promote', [DeveloperUserController::class,'promoteToSystemAdmin'])->name('users.promote');
    Route::resource('guardians', DeveloperGuardianController::class)->except(['show']);
    Route::get('requests', [DeveloperActionRequestController::class,'index'])->name('requests.index');
    Route::patch('requests/{actionRequest}', [DeveloperActionRequestController::class,'update'])->name('requests.update');
    Route::get('tickets', [DeveloperSupportTicketController::class,'index'])->name('tickets.index');
    Route::get('tickets/{ticket}', [DeveloperSupportTicketController::class,'show'])->name('tickets.show');
    Route::post('tickets/{ticket}/reply', [DeveloperSupportTicketController::class,'reply'])->name('tickets.reply');
    // Removed settings/bug-access routes after consolidation
    // Doctors assignment (top user)
    Route::get('doctors', [DeveloperDoctorController::class,'index'])->name('doctors.index');
    Route::get('doctors/{doctor}', [DeveloperDoctorController::class,'show'])->name('doctors.show');
    Route::post('doctors/{doctor}/assignments', [DeveloperDoctorController::class,'saveAssignments'])->name('doctors.assignments');
    // Allocation management
    Route::get('allocation', [DeveloperBlockController::class,'index'])->name('allocation.index');
    Route::post('allocation', [DeveloperLocationController::class,'store'])->name('allocation.store');
    Route::put('allocation/{location}', [DeveloperLocationController::class,'update'])->name('allocation.update');
    Route::delete('allocation/{location}', [DeveloperLocationController::class,'destroy'])->name('allocation.destroy');
    // Blocks management (developer)
    Route::get('blocks', [DeveloperBlockController::class,'index'])->name('blocks.index');
    Route::post('blocks', [DeveloperBlockController::class,'store'])->name('blocks.store');
    Route::put('blocks/{block}', [DeveloperBlockController::class,'update'])->name('blocks.update');
    Route::delete('blocks/{block}', [DeveloperBlockController::class,'destroy'])->name('blocks.destroy');
    Route::get('blocks/{block}/locations', [DeveloperBlockController::class,'locations'])->name('blocks.locations');
    Route::post('blocks/{block}/locations', [DeveloperBlockController::class,'storeLocation'])->name('blocks.locations.store');
});

// Admin educational records route (inmates under standard admin role if exists)
Route::middleware(['auth','verified','role:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::post('inmates/{inmate}/educational-records', [\App\Http\Controllers\Admin\EducationalRecordController::class,'store'])->name('educational-records.store');
    Route::post('inmates/{inmate}/case-log', [\App\Http\Controllers\Admin\CaseLogEntryController::class,'store'])->name('case-log.store');
    Route::post('inmates/{inmate}/geriatric-care', [\App\Http\Controllers\Admin\GeriatricCarePlanController::class,'storeOrUpdate'])->name('geriatric-care.save');
});

// System Admin routes (global management excluding developer-only features)
Route::middleware(['auth','verified','role:system_admin'])->prefix('system-admin')->name('system_admin.')->group(function(){
    Route::get('/dashboard', [SystemAdminDashboardController::class,'index'])->name('dashboard');
    Route::resource('institutions', SystemAdminInstitutionController::class);
    // Institution profile AJAX tabs
    Route::get('institutions/{institution}/tabs/overview', [SystemAdminInstitutionController::class,'show'])->name('institutions.tabs.overview');
    Route::get('institutions/{institution}/tabs/users', [SystemAdminInstitutionController::class,'tabUsers'])->name('institutions.tabs.users');
    Route::get('institutions/{institution}/tabs/inmates', [SystemAdminInstitutionController::class,'tabInmates'])->name('institutions.tabs.inmates');
    Route::get('institutions/{institution}/tabs/donations', [SystemAdminInstitutionController::class,'tabDonations'])->name('institutions.tabs.donations');
    Route::post('institutions/{institution}/donations', [SystemAdminInstitutionController::class,'updateDonationSettings'])->name('institutions.donations.update');
    Route::get('institutions/{institution}/tabs/settings', [SystemAdminInstitutionController::class,'tabSettings'])->name('institutions.tabs.settings');
    Route::resource('inmates', SystemAdminInmateController::class);
    Route::get('inmates/{inmate}/report', [SystemAdminInmateController::class,'downloadReport'])->name('inmates.report');
    Route::get('inmates-search', SystemAdminInmateSearchController::class)->name('inmates.search');
    // Payments
    Route::get('payments', [SystemAdminInmatePaymentController::class,'index'])->name('payments.index');
<<<<<<< HEAD
    Route::get('payments/{payment}/receipt', [SystemAdminInmatePaymentController::class,'downloadReceipt'])->name('payments.receipt');
    Route::get('payments-report', [SystemAdminInmatePaymentController::class,'downloadReport'])->name('payments.report');
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
    Route::post('inmates/{inmate}/payments', [SystemAdminInmatePaymentController::class,'storeForInmate'])->name('inmates.payments.store');
    Route::resource('users', SystemAdminUserController::class);
    Route::post('users/{user}/toggle-bug-reporting', [SystemAdminUserController::class,'toggleBugReporting'])->name('users.toggle-bug-reporting');
    // Guardians (now with profile/show page)
    Route::resource('guardians', SystemAdminGuardianController::class);
    // Settings - medication windows
    Route::get('settings/medication-windows', [\App\Http\Controllers\SystemAdmin\SettingsController::class,'medicationWindows'])->name('settings.medication-windows');
    Route::post('settings/medication-windows', [\App\Http\Controllers\SystemAdmin\SettingsController::class,'saveMedicationWindows'])->name('settings.medication-windows.save');
    // Doctors assignment (top user)
    Route::get('doctors', [SystemAdminDoctorController::class,'index'])->name('doctors.index');
    Route::get('doctors/{doctor}', [SystemAdminDoctorController::class,'show'])->name('doctors.show');
    Route::post('doctors/{doctor}/assignments', [SystemAdminDoctorController::class,'saveAssignments'])->name('doctors.assignments');
    Route::get('doctors/{doctor}/feed', [SystemAdminDoctorController::class,'feed'])->name('doctors.feed');
    Route::post('doctors/{doctor}/emergency', [SystemAdminDoctorController::class,'scheduleEmergency'])->name('doctors.emergency');
    // Allocation dashboard alias
    Route::get('allocation', [SystemAdminBlockController::class,'index'])->name('allocation.index');
    // Locations API for institution-wide lookup (used by Assign Room modal)
    Route::get('allocation/api/institutions/{institution}/locations', [SystemAdminBlockController::class,'apiLocationsByInstitution'])->name('allocation.api.locations');
    // Blocks management (system admin)
    Route::get('blocks', [SystemAdminBlockController::class,'index'])->name('blocks.index');
    Route::post('blocks', [SystemAdminBlockController::class,'store'])->name('blocks.store');
    Route::put('blocks/{block}', [SystemAdminBlockController::class,'update'])->name('blocks.update');
    Route::delete('blocks/{block}', [SystemAdminBlockController::class,'destroy'])->name('blocks.destroy');
    Route::get('blocks/{block}/locations', [SystemAdminBlockController::class,'locations'])->name('blocks.locations');
    Route::post('blocks/{block}/locations', [SystemAdminBlockController::class,'storeLocation'])->name('blocks.locations.store');
    // Update an individual location (status etc.)
    Route::put('locations/{location}', [SystemAdminBlockController::class,'updateLocation'])->name('locations.update');
    Route::delete('locations/{location}', [SystemAdminBlockController::class,'destroyLocation'])->name('locations.destroy');
    // Inmates lookup for Allocate modal
    Route::get('allocation/api/institutions/{institution}/inmates', [SystemAdminBlockController::class,'apiInmatesByInstitution'])->name('allocation.api.inmates');
    // Medicines (global) — Inventory (stock/catalog)
    Route::get('medicines', [SystemAdminMedicineController::class,'index'])->name('medicines.index');
    // Live medications and attendance logs (global)
    Route::get('medicines/live', [SystemAdminMedicineController::class,'live'])->name('medicines.live');
    Route::get('medicines/logs', [SystemAdminMedicineController::class,'logs'])->name('medicines.logs');
    // Log a medication administration (system admin)
    Route::post('medications/log', [SystemAdminMedicineController::class,'logMedication'])->name('medications.log');
    // Medicines intake monitoring UI (separate from inventory)
    Route::get('medications', function(){
        return view('system_admin.medications.index');
    })->name('medications.index');
    Route::post('medicines', [SystemAdminMedicineController::class,'store'])->name('medicines.store');
    Route::put('medicines/{medicine}', [SystemAdminMedicineController::class,'update'])->name('medicines.update');
    Route::post('medicines/{medicine}/deactivate', [SystemAdminMedicineController::class,'deactivate'])->name('medicines.deactivate');
    Route::post('medicines/{medicine}/activate', [SystemAdminMedicineController::class,'activate'])->name('medicines.activate');
    Route::delete('medicines/{medicine}', [SystemAdminMedicineController::class,'destroy'])->name('medicines.destroy');
    Route::get('medicines/needs', [SystemAdminMedicineController::class,'needs'])->name('medicines.needs');
    Route::get('medicines/availability', [SystemAdminMedicineController::class,'availability'])->name('medicines.availability');
    Route::get('medicines/usage', [SystemAdminMedicineController::class,'usage'])->name('medicines.usage');
    Route::get('medicines/uncatalogued', [SystemAdminMedicineController::class,'uncatalogued'])->name('medicines.uncatalogued');
    Route::get('medicines/assignees', [SystemAdminMedicineController::class,'assignees'])->name('medicines.assignees');
    Route::get('medicines/low-stock', [SystemAdminMedicineController::class,'lowStock'])->name('medicines.low-stock');
    Route::get('medicines/history', [SystemAdminMedicineController::class,'history'])->name('medicines.history');
    // Medication attendance report (global)
    Route::get('medications/report', [\App\Http\Controllers\SystemAdmin\MedicationReportController::class,'index'])->name('medications.report');
    // Reports
    Route::get('medicines/reports/stock', [SystemAdminMedicineController::class,'reportStock'])->name('medicines.reports.stock');
    Route::get('medicines/reports/prescriptions', [SystemAdminMedicineController::class,'reportPrescriptions'])->name('medicines.reports.prescriptions');
    Route::get('medicines/reports/usage-trends', [SystemAdminMedicineController::class,'reportUsageTrends'])->name('medicines.reports.usage-trends');
    // Inmate location assignment
    Route::post('inmates/{inmate}/assign-location', [SystemAdminInmateController::class,'assignLocation'])->name('inmates.assign-location');
    Route::post('inmates/{inmate}/upload-file', [SystemAdminInmateController::class,'uploadFile'])->name('inmates.upload-file');
    Route::post('inmates/{inmate}/documents', [SystemAdminInmateController::class,'storeDocument'])->name('inmates.documents.store');
    // Toggle guardian sharing for a document
    Route::post('inmates/{inmate}/documents/{document}/toggle-share', [SystemAdminInmateController::class,'toggleDocumentShare'])->name('inmates.documents.toggle-share');
    // Guardian messages (reply within guardian show)
    Route::post('guardians/{guardian}/messages', [\App\Http\Controllers\SystemAdmin\GuardianController::class,'replyMessage'])->name('guardians.messages.reply');
    // Blog Management
    Route::resource('blogs', AdminBlogController::class);
    // Gallery Management
    Route::resource('gallery', \App\Http\Controllers\SystemAdmin\GalleryController::class)->except(['create','edit','show','update']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Doctor routes (read only inmates)
Route::middleware(['auth','verified','role:doctor'])->prefix('doctor')->name('doctor.')->group(function(){
    Route::get('/dashboard', [DoctorDashboardController::class,'index'])->name('dashboard');
    Route::resource('inmates', DoctorInmateController::class)->only(['index','show']);
    Route::post('inmates/{inmate}/medical-records', [MedicalRecordController::class,'store'])->name('inmates.medical-records.store');
    // Medications (doctor can add/update)
    Route::post('inmates/{inmate}/medications', [\App\Http\Controllers\Doctor\MedicationController::class,'store'])->name('inmates.medications.store');
    Route::put('medications/{medication}', [\App\Http\Controllers\Doctor\MedicationController::class,'update'])->name('medications.update');
    // Lab tests ordering & updating
    Route::get('lab-tests', [DoctorLabTestController::class,'index'])->name('lab-tests.index');
    Route::get('inmates/{inmate}/lab-tests/create', [DoctorLabTestController::class,'create'])->name('lab-tests.create');
    Route::post('inmates/{inmate}/lab-tests', [DoctorLabTestController::class,'store'])->name('lab-tests.store');
    Route::get('lab-tests/{labTest}/edit', [DoctorLabTestController::class,'edit'])->name('lab-tests.edit');
    Route::get('lab-tests/{labTest}', [DoctorLabTestController::class,'show'])->name('lab-tests.show');
    Route::put('lab-tests/{labTest}', [DoctorLabTestController::class,'update'])->name('lab-tests.update');
    // Appointments (calendar + JSON feed CRUD via ajax)
    Route::get('appointments', [DoctorAppointmentController::class,'index'])->name('appointments.index');
    Route::get('appointments/feed', [DoctorAppointmentController::class,'feed'])->name('appointments.feed');
    Route::post('appointments', [DoctorAppointmentController::class,'store'])->name('appointments.store');
    Route::patch('appointments/{appointment}', [DoctorAppointmentController::class,'update'])->name('appointments.update');
    Route::delete('appointments/{appointment}', [DoctorAppointmentController::class,'destroy'])->name('appointments.destroy');
    Route::post('inmates/{inmate}/therapy-logs', [\App\Http\Controllers\Doctor\TherapySessionLogController::class,'store'])->name('therapy-logs.store');
    Route::post('inmates/{inmate}/counseling-notes', [\App\Http\Controllers\Doctor\CounselingProgressNoteController::class,'store'])->name('counseling-notes.store');
});

// Nurse routes (read only inmates)
Route::middleware(['auth','verified','role:nurse'])->prefix('nurse')->name('nurse.')->group(function(){
    Route::get('/dashboard', [NurseDashboardController::class,'index'])->name('dashboard');
    Route::resource('inmates', NurseInmateController::class)->only(['index','show']);
    Route::get('lab-tests', [NurseLabTestController::class,'index'])->name('lab-tests.index');
    Route::post('medical-records/{medicalRecord}/log', [MedicationLogController::class,'store'])->name('medical-records.log');
    // Medications schedule (today's meds) and quick logging
    Route::get('medications/schedule', [\App\Http\Controllers\Nurse\MedicationScheduleController::class,'index'])->name('meds.schedule');
    Route::post('medications/log', [\App\Http\Controllers\Nurse\MedicationScheduleController::class,'log'])->name('meds.log');
    // Nurse view & update lab test status when in progress/completed (result upload)
    Route::get('lab-tests/{labTest}', [NurseLabTestController::class,'show'])->name('lab-tests.show');
    Route::patch('lab-tests/{labTest}', [NurseLabTestController::class,'partialUpdate'])->name('lab-tests.partial-update');
    // Nurse examinations (report to doctor)
    Route::post('inmates/{inmate}/examinations', [\App\Http\Controllers\Nurse\ExaminationController::class,'store'])->name('inmates.examinations.store');
});

require __DIR__.'/auth.php';

// Admin dedicated prefixed routes
Route::middleware(['auth','verified','role:admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');
    Route::resource('inmates', AdminInmateController::class);
    Route::post('inmates/{inmate}/assign-doctor', [AdminInmateController::class,'assignDoctor'])->name('inmates.assign-doctor');
    Route::post('inmates/{inmate}/transfer-doctor', [AdminInmateController::class,'transferDoctor'])->name('inmates.transfer-doctor');
    Route::post('inmates/{inmate}/assign-location', [AdminInmateController::class,'assignLocation'])->name('inmates.assign-location');
    Route::post('inmates/{inmate}/upload-file', [AdminInmateController::class,'uploadFile'])->name('inmates.upload-file');
    Route::post('inmates/{inmate}/documents', [AdminInmateController::class,'storeDocument'])->name('inmates.documents.store');
    // Allocation API for cascading selects
    Route::get('allocation/api/blocks/{block}/types', function(\App\Models\Block $block){
        abort_unless($block->institution_id === auth()->user()->institution_id, 403);
        $types = \App\Models\Location::where('block_id',$block->id)->select('type')->distinct()->pluck('type');
        return response()->json($types);
    })->name('allocation.api.types');
    Route::get('allocation/api/blocks/{block}/types/{type}/numbers', function(\App\Models\Block $block, string $type){
        abort_unless($block->institution_id === auth()->user()->institution_id, 403);
        $numbers = \App\Models\Location::where('block_id',$block->id)->where('type',$type)->orderBy('number')->get(['id','number']);
        return response()->json($numbers);
    })->name('allocation.api.numbers');
    Route::post('inmates/{inmate}/assign-doctor', [AdminInmateController::class,'assignDoctor'])->name('inmates.assign-doctor');
    // Admin users and institutions routes - use existing controllers to avoid duplicating logic
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('institutions', DeveloperInstitutionController::class);
    Route::resource('staff', AdminStaffController::class);
    // Doctors management and schedule
    Route::get('doctors', [\App\Http\Controllers\Admin\DoctorController::class,'index'])->name('doctors.index');
    Route::get('doctors/{doctor}', [\App\Http\Controllers\Admin\DoctorController::class,'show'])->name('doctors.show');
    Route::post('doctors/{doctor}/assignments', [\App\Http\Controllers\Admin\DoctorController::class,'saveAssignments'])->name('doctors.assignments');
    Route::get('doctors/{doctor}/feed', [\App\Http\Controllers\Admin\DoctorController::class,'feed'])->name('doctors.feed');
    Route::post('doctors/{doctor}/emergency', [\App\Http\Controllers\Admin\DoctorController::class,'scheduleEmergency'])->name('doctors.emergency');
    Route::resource('guardians', AdminGuardianController::class);
    Route::get('requests', [AdminActionRequestController::class,'index'])->name('requests.index');
    Route::post('requests', [AdminActionRequestController::class,'store'])->name('requests.store');
    // Allocation management
    // Blocks (hierarchical)
    Route::get('allocation', [AdminBlockController::class,'index'])->name('allocation.index');
    Route::post('blocks', [AdminBlockController::class,'store'])->name('blocks.store');
    Route::put('blocks/{block}', [AdminBlockController::class,'update'])->name('blocks.update');
    Route::delete('blocks/{block}', [AdminBlockController::class,'destroy'])->name('blocks.destroy');
    Route::get('blocks', fn() => redirect()->route('admin.allocation.index'))->name('blocks.index');
    // Locations within a block
    Route::get('blocks/{block}/locations', [AdminBlockController::class,'locations'])->name('blocks.locations');
    Route::post('blocks/{block}/locations', [AdminBlockController::class,'storeLocation'])->name('blocks.locations.store');
    // Update an individual location (status etc.)
    Route::put('locations/{location}', [AdminBlockController::class,'updateLocation'])->name('locations.update');
    Route::delete('locations/{location}', [AdminBlockController::class,'destroyLocation'])->name('locations.destroy');
    // Allocation APIs for admin (locations + inmates by institution)
    Route::get('allocation/api/institutions/{institution}/locations', [AdminBlockController::class,'apiLocationsByInstitution'])->name('allocation.api.locations');
    Route::get('allocation/api/institutions/{institution}/inmates', [AdminBlockController::class,'apiInmatesByInstitution'])->name('allocation.api.inmates');
    // Medicines (institution-scoped) — Inventory (stock/catalog)
    Route::get('medicines', [AdminMedicineController::class,'index'])->name('medicines.index');
    // Live medications and attendance logs (institution-scoped)
    Route::get('medicines/live', [AdminMedicineController::class,'live'])->name('medicines.live');
    Route::get('medicines/logs', [AdminMedicineController::class,'logs'])->name('medicines.logs');
    // Log a medication administration (admin)
    Route::post('medications/log', [AdminMedicineController::class,'logMedication'])->name('medications.log');
    // Medicines intake monitoring UI (separate from inventory)
    Route::get('medications', function(){
        return view('admin.medications.index');
    })->name('medications.index');
    Route::post('medicines', [AdminMedicineController::class,'store'])->name('medicines.store');
    Route::put('medicines/{inventory}', [AdminMedicineController::class,'update'])->name('medicines.update');
    Route::delete('medicines/{inventory}', [AdminMedicineController::class,'destroy'])->name('medicines.destroy');
    // AJAX tabs
    Route::get('medicines/tabs/inventory', [AdminMedicineController::class,'tabInventory'])->name('medicines.tabs.inventory');
    Route::get('medicines/tabs/catalog', [AdminMedicineController::class,'tabCatalog'])->name('medicines.tabs.catalog');
    Route::get('medicines/usage', [AdminMedicineController::class,'usage'])->name('medicines.usage');
    Route::get('medicines/uncatalogued', [AdminMedicineController::class,'uncatalogued'])->name('medicines.uncatalogued');
    Route::get('medicines/assignees', [AdminMedicineController::class,'assignees'])->name('medicines.assignees');
    Route::get('medicines/low-stock', [AdminMedicineController::class,'lowStock'])->name('medicines.low-stock');
    Route::get('medicines/history', [AdminMedicineController::class,'history'])->name('medicines.history');
    // Medication attendance report
    Route::get('medications/report', [\App\Http\Controllers\Admin\MedicationReportController::class,'index'])->name('medications.report');
    Route::post('medicines/{inventory}/notify', [AdminMedicineController::class,'notify'])->name('medicines.notify');
    // Reports
    Route::get('medicines/reports/stock', [AdminMedicineController::class,'reportStock'])->name('medicines.reports.stock');
    Route::get('medicines/reports/prescriptions', [AdminMedicineController::class,'reportPrescriptions'])->name('medicines.reports.prescriptions');
    Route::get('medicines/reports/usage-trends', [AdminMedicineController::class,'reportUsageTrends'])->name('medicines.reports.usage-trends');
    // Guardian messages (admin side)
    Route::get('guardian-messages', [\App\Http\Controllers\Admin\GuardianMessageController::class,'index'])->name('guardian-messages.index');
    Route::get('guardian-messages/{guardian}', [\App\Http\Controllers\Admin\GuardianMessageController::class,'show'])->name('guardian-messages.show');
    Route::post('guardian-messages/{guardian}', [\App\Http\Controllers\Admin\GuardianMessageController::class,'reply'])->name('guardian-messages.reply');
    // Toggle guardian sharing for a document
    Route::post('inmates/{inmate}/documents/{document}/toggle-share', [AdminInmateController::class,'toggleDocumentShare'])->name('inmates.documents.toggle-share');
    // Blog Management
    Route::resource('blogs', AdminBlogController::class);
    // Gallery Management
    Route::resource('gallery', \App\Http\Controllers\Admin\GalleryController::class)->except(['create','edit','show','update']);
});

// Staff routes
Route::middleware(['auth','verified','role:staff'])->prefix('staff')->name('staff.')->group(function(){
    Route::get('/dashboard', [StaffDashboardController::class,'index'])->name('dashboard');
    Route::get('inmates', [StaffInmateController::class,'index'])->name('inmates.index');
    // Define 'create' before the catch-all '{inmate}' route to avoid 404 on /inmates/create
    Route::get('inmates/create', [StaffInmateController::class,'create'])->name('inmates.create');
    Route::get('inmates/{inmate}', function(\App\Models\Inmate $inmate){
        abort_unless($inmate->institution_id === auth()->user()->institution_id, 403);
        return view('staff.inmates.show', compact('inmate'));
    })->name('inmates.show');
    Route::post('inmates', [StaffInmateController::class,'store'])->name('inmates.store');
    Route::post('medical-records/{medicalRecord}/log', [StaffMedicationLogController::class,'store'])->name('medical-records.log');
    // Medications schedule (today) and quick logging
    Route::get('medications/schedule', [\App\Http\Controllers\Staff\MedicationScheduleController::class,'index'])->name('meds.schedule');
    Route::post('medications/log', [\App\Http\Controllers\Staff\MedicationScheduleController::class,'log'])->name('meds.log');
    // Staff examinations (report to doctor)
    Route::post('inmates/{inmate}/examinations', [\App\Http\Controllers\Staff\ExaminationController::class,'store'])->name('inmates.examinations.store');
    // Staff rooms API (for room picker)
    Route::get('allocation/api/locations', [\App\Http\Controllers\Staff\BlockController::class,'apiLocationsForInstitution'])->name('allocation.api.locations');
    // Staff Lab Tests: list and update results similar to Nurse
    Route::get('lab-tests', [\App\Http\Controllers\Staff\LabTestController::class,'index'])->name('lab-tests.index');
    Route::get('lab-tests/{labTest}', [\App\Http\Controllers\Staff\LabTestController::class,'show'])->name('lab-tests.show');
    Route::patch('lab-tests/{labTest}', [\App\Http\Controllers\Staff\LabTestController::class,'partialUpdate'])->name('lab-tests.partial-update');
    // Staff allocation (assign/transfer only)
    Route::get('inmates/{inmate}/allocation', [\App\Http\Controllers\Staff\AllocationController::class,'edit'])->name('allocation.edit');
    Route::put('inmates/{inmate}/allocation', [\App\Http\Controllers\Staff\AllocationController::class,'update'])->name('allocation.update');
});

// Guardian routes
Route::middleware(['auth','verified','role:guardian'])->prefix('guardian')->name('guardian.')->group(function(){
    Route::get('/dashboard', [GuardianDashboardController::class,'index'])->name('dashboard');
    Route::post('/messages', [GuardianMessageController::class,'send'])->name('messages.send');
});

// Bug report submission route (auth users; gating can be improved later)
Route::middleware(['auth','verified'])->group(function(){
    Route::get('/my/tickets', [SupportTicketController::class,'index'])->name('tickets.index.user');
    Route::post('/my/tickets', [SupportTicketController::class,'store'])->name('tickets.store');
    Route::get('/my/tickets/{ticket}', [SupportTicketController::class,'show'])->name('tickets.show');
    Route::post('/my/tickets/{ticket}/reply', [SupportTicketController::class,'reply'])->name('tickets.reply');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class,'index'])->name('notifications.index');
    Route::get('/notifications/feed', [\App\Http\Controllers\NotificationController::class,'feed'])->name('notifications.feed');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class,'markAllRead'])->name('notifications.mark-all');
    Route::post('/notifications/{notification}/mark-read', function($notification){
        $n = \Illuminate\Support\Facades\Auth::user()->notifications()->where('id',$notification)->firstOrFail();
        $n->markAsRead();
        return response()->json(['ok'=>true]);
    })->name('notifications.mark-read');
});
