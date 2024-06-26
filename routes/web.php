<?php
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\CheckAdmin;

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\JobApplicationController;
use App\Http\Controllers\admin\JobController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\JobsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');

Route::get('/forgot-password', [AccountController::class, 'forgotPassword'])->name('account.forgotPassword');
Route::post('/process-forgot-password', [AccountController::class, 'processForgotPassword'])->name('account.processForgotPassword');
Route::get('/reset-password/{token}', [AccountController::class, 'resetPassword'])->name('account.resetPassword');
Route::post('/process-reset-password', [AccountController::class, 'processResetPassword'])->name('account.processResetPassword');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->middleware(CheckAdmin::class)->name('admin.dashboard');
Route::get('/admin/users', [UserController::class, 'index'])->middleware(CheckAdmin::class)->name('admin.users');
Route::get('/admin/users/{id}', [UserController::class, 'edit'])->middleware(CheckAdmin::class)->name('admin.users.edit');
Route::post('/admin/users/{id}', [UserController::class, 'update'])->middleware(CheckAdmin::class)->name('admin.users.update');
Route::delete('/admin/users', [UserController::class, 'destroy'])->middleware(CheckAdmin::class)->name('admin.users.destroy');
Route::get('/admin/jobs', [JobController::class, 'index'])->middleware(CheckAdmin::class)->name('admin.jobs');
Route::get('/admin/jobs/{id}', [JobController::class, 'edit'])->middleware(CheckAdmin::class)->name('admin.jobs.edit');
Route::match(['get', 'post'], '/admin/jobs/{id}', [JobController::class, 'update'])->middleware(CheckAdmin::class)->name('admin.jobs.update');
Route::delete('/admin/users', [JobController::class, 'destroy'])->middleware(CheckAdmin::class)->name('admin.jobs.destroy');
Route::get('/admin/job-applications', [JobApplicationController::class, 'index'])->middleware(CheckAdmin::class)->name('admin.jobApplications');
Route::delete('/admin/job-applications', [JobApplicationController::class, 'destroy'])->middleware(CheckAdmin::class)->name('admin.jobApplications.destroy');

Route::get('/jobs/edit/{id}',[JobController::class,'edit'])->name('admin.jobs.edit');
Route::put('/jobs/{id}',[JobController::class,'update'])->name('admin.jobs.update');
Route::delete('/jobs',[JobController::class,'destroy'])->name('admin.jobs.destroy');



Route::get('/jobs/detail/{id}', [JobsController::class, 'detail'])->name('jobDetail');
Route::post('/jobs/apply-job', [JobsController::class, 'applyJob'])->name('applyJob');
Route::post('/jobs/save-job', [JobsController::class, 'saveJob'])->name('saveJob');

// Routes for visitors
Route::get('/account/register', [AccountController::class, 'registration'])->name('account.registration');
Route::post('/account/process-register', [AccountController::class, 'proccessRegistration'])->name('account.proccessRegistration');
Route::get('/account/login', [AccountController::class, 'login'])->name('account.login');
Route::post('/account/authentificate', [AccountController::class, 'authentificate'])->name('account.authentificate');

// Routes for authenticated users
Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
Route::match(['get', 'post'], '/account/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
Route::get('/account/logout', [AccountController::class, 'logout'])->name('account.logout');
Route::post('/account/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');
Route::get('/account/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
Route::post('/account/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
Route::get('/account/my-jobs', [AccountController::class, 'myJobs'])->name('account.myJobs');
Route::get('/account/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
Route::post('/account/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
Route::post('/account/delete-job/', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
Route::get('/account/my-job-applications', [AccountController::class, 'myJobApplications'])->name('account.myJobApplications');
Route::post('/account/remove-job-application', [AccountController::class, 'removeJobs'])->name('account.removeJobs');
Route::get('/account/saved-jobs', [AccountController::class, 'savedJobs'])->name('account.savedJobs');
Route::post('/account/remove-saved-job', [AccountController::class, 'removeSavedJob'])->name('account.removeSavedJob');
Route::post('/account/update-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');
