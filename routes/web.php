<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/GetPrayerData',[AdminController::class,'GetPrayerData'])->name('getprayerdata');
Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('user')->name('user.')->group(function(){
  
    Route::middleware(['guest:web','PreventBackHistory'])->group(function(){
          Route::get('/sample',[UserController::class,'sample'])->name('sample');
          Route::view('/login','dashboard.user.login')->name('login');
          Route::view('/register','dashboard.user.register')->name('register');
          Route::post('/create',[UserController::class,'create'])->name('create');
          Route::post('/check',[UserController::class,'check'])->name('check');
    });

    Route::middleware(['auth:web','PreventBackHistory'])->group(function(){
          Route::view('/home','dashboard.user.home')->name('home');
          Route::post('/logout',[UserController::class,'logout'])->name('logout');
          Route::get('/add-new',[UserController::class,'add'])->name('add');
    });

});

Route::prefix('admin')->name('admin.')->group(function(){
   
    Route::middleware(['guest:admin','PreventBackHistory'])->group(function(){
          Route::view('/login','dashboard.admin.login')->name('login');
          Route::post('/loginPost',[AdminController::class,'loginpost'])->name('loginPost');

    });
    
    Route::middleware(['auth:admin','PreventBackHistory'])->group(function(){
        Route::get('/Dashboard',[AdminController::class,'dashboard'])->name('dashboard');
        Route::get('/ManageVersion/{pagenumber}',[AdminController::class,'ManageVersion'])->name('manageversion');
        Route::get('/ManageBooks/{pagenumber}',[AdminController::class,'ManageBooks'])->name('managebooks');
        Route::get('/ManageChapters/{pagenumber}',[AdminController::class,'ManageChapters'])->name('managechapters');
        Route::get('/EditVersions/{id}',[AdminController::class,'EditVersions'])->name('editversions');
        Route::get('/EditBooks/{id}',[AdminController::class,'EditBooks'])->name('editbooks');
        Route::get('/getVersions/{pagenumber}',[AdminController::class,'getVersions'])->name('getversions');
        Route::get('/AddVersions',[AdminController::class,'AddVersions'])->name('addversions');
        Route::get('/AddBooks',[AdminController::class,'AddBooks'])->name('addbooks');
        Route::get('/AddChapter',[AdminController::class,'AddChapter'])->name('addchapter');
        Route::post('/PickBooks',[AdminController::class,'PickBooks'])->name('pickbooks');
        Route::post('/AddVersionsPost',[AdminController::class,'AddVersionsPost'])->name('addversionpost');
        Route::post('/AddChapterPost',[AdminController::class,'AddChapterPost'])->name('addchapterpost');
        Route::post('/AddBooksPost',[AdminController::class,'AddBooksPost'])->name('addbookspost');
        Route::post('/UpdateVersionPost',[AdminController::class,'UpdateVersionPost'])->name('updateversionpost');
        Route::post('/UpdateBooksPost',[AdminController::class,'UpdateBooksPost'])->name('updatebookspost');
        Route::post('/DeleteVersionRecord',[AdminController::class,'DeleteVersionRecord'])->name('deleteversionrecord');
        Route::get('/logout',[ AdminController::class,'logout'])->name('logout');
    });

});

Route::prefix('doctor')->name('doctor.')->group(function(){

       Route::middleware(['guest:doctor','PreventBackHistory'])->group(function(){
            Route::view('/login','dashboard.doctor.login')->name('login');
            Route::view('/register','dashboard.doctor.register')->name('register');
            Route::post('/create',[DoctorController::class,'create'])->name('create');
            Route::post('/check',[DoctorController::class,'check'])->name('check');
       });

       Route::middleware(['auth:doctor','PreventBackHistory'])->group(function(){
            Route::view('/home','dashboard.doctor.home')->name('home');
            Route::post('logout',[DoctorController::class,'logout'])->name('logout');
       });

});
