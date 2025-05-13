<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnnouncementController;

// 首頁路由
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// 測試頁面（保留）
Route::get('/test-page', function () {
    $isLoggedIn = Auth::check();
    $user = $isLoggedIn ? Auth::user() : null;
    $roles = $isLoggedIn ? $user->roles()->pluck('name')->toArray() : [];
    
    return view('test-page', compact('isLoggedIn', 'user', 'roles'));
});

// 身份驗證路由（login, register等）
require __DIR__.'/auth.php';

// 儀表板
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 個人資料相關
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 活動相關
Route::get('/activities', [App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');
Route::get('/activities/{activity}', [App\Http\Controllers\ActivityController::class, 'show'])->name('activities.show');

// 公告相關
Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');

// 會員功能
Route::middleware(['auth'])->group(function () {
    Route::get('/my-activities', [App\Http\Controllers\MemberController::class, 'activities'])->name('member.activities');
    Route::post('/activities/{activity}/register', [App\Http\Controllers\ActivityController::class, 'register'])->name('activities.register');
    Route::delete('/activities/{activity}/unregister', [App\Http\Controllers\ActivityController::class, 'unregister'])->name('activities.unregister');
});

// 管理員後台路由 - Laravel 12 格式
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // 儀表板 - admin 和 super 都可使用
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->middleware('role:admin,super')
        ->name('dashboard');
    
    // 活動管理 - admin 和 super 可使用
    Route::resource('activities', App\Http\Controllers\Admin\ActivityController::class)
        ->middleware('role:admin,super');
    
    // 公告管理 - admin 和 super 可使用  
    Route::resource('announcements', App\Http\Controllers\Admin\AnnouncementController::class)
        ->middleware('role:admin,super');
    
    // 超級管理員專用功能
    Route::middleware('role:super')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class);
        Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/clear-cache', [App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
    });
});


// 搜尋功能
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/search/clear', [SearchController::class, 'clear'])->name('search.clear');
Route::post('/clear-search-logs', [SearchController::class, 'clearSearchLogs'])->name('clearSearchLogs');
Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
Route::get('/activities/{id}', [ActivityController::class, 'show'])->name('activities.show');
