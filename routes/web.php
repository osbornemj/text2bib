<?php

use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\ConversionAdminController;
use App\Http\Controllers\Admin\ExamplesController;
use App\Http\Controllers\Admin\ExampleCheckController;
use App\Http\Controllers\Admin\ExampleFieldsController;
use App\Http\Controllers\Admin\ExcludedWordsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ItemFieldsController;
use App\Http\Controllers\Admin\ItemTypesController;
use App\Http\Controllers\Admin\NamesController;
use App\Http\Controllers\Admin\PublishersController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\VonNamesController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Convert\ErrorReportController;
use App\Http\Controllers\Convert\FileUploadController;
use App\Http\Controllers\Convert\ConversionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [IndexController::class, 'index'])->name('public.index');

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    // TEST
    //Route::get('/process', [ConversionController::class, 'process'])->name('process');
    //
    Route::get('/dashboard', [IndexController::class, 'index'])->name('dashboard');
    Route::get('/convertFile', [IndexController::class, 'convertFile'])->name('convertFile');
    Route::post('/convertFile', [IndexController::class, 'convertFile'])->name('convertFile');
    Route::get('/errorReports', [ErrorReportController::class, 'index'])->name('errorReports');
    Route::get('/errorReport/{id}', [ErrorReportController::class, 'show'])->name('errorReport');
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');
    Route::get('/about', [IndexController::class, 'about'])->name('about');

    Route::controller(ConversionController::class)->group(function () {
        Route::get('/convert/{conversionId}/{userFileId?}/{itemSeparator?}', 'convert')->name('file.convert');
        Route::get('/redo/{id}', 'redo')->name('redo');
    //    Route::get('/convertIncremental/{conversionId}/{index?}', 'convertIncremental')->name('file.convertIncremental');
    //    Route::post('/addToBibtex/{conversionId}', 'addToBibtex')->name('file.addToBibtex');
        Route::post('/addOutput/{conversionId}', 'addOutput')->name('conversion.addOutput');
        Route::get('/showBibtex/{conversionId}', 'showBibtex')->name('conversion.showBibtex');
        Route::get('/encodingError/{conversionId}', 'encodingError')->name('conversion.encodingError');
        Route::get('/itemSeparatorError/{conversionId}', 'itemSeparatorError')->name('conversion.itemSeparatorError');
        Route::get('/downloadBibtex/{conversionId}', 'downloadBibtex')->name('conversion.downloadBibtex');
    });
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resources([
        '/admin/itemTypes' => ItemTypesController::class,
        '/admin/itemFields' => ItemFieldsController::class,
        '/admin/examples' => ExamplesController::class,
        '/admin/vonNames' => VonNamesController::class,
        '/admin/publishers' => PublishersController::class,
        '/admin/cities' => CitiesController::class,
        '/admin/names' => NamesController::class,
        '/admin/excludedWords' => ExcludedWordsController::class,
    ]);

    Route::get('/admin/index', [AdminController::class, 'index'])->name('admin.index');

    Route::controller(ConversionAdminController::class)->group(function () {
        Route::get('/admin/showConversion/{conversionId}', 'showConversion')->name('admin.showConversion');
        Route::get('/admin/convert/{fileId}/{itemSeparator?}', 'convert')->name('admin.convert');
        Route::get('/admin/conversions', 'index')->name('admin.conversions');
        Route::get('/admin/formatExample/{outputId}', 'formatExample')->name('admin.formatExample');
    });

    Route::controller(ExampleFieldsController::class)->group(function () {
        Route::get('/admin/exampleFields/{fieldId}/editContent', 'editContent')->name('admin.exampleFields.editContent');
        Route::put('/admin/exampleFields/{fieldId}/updateContent', 'updateContent')->name('admin.exampleFields.updateContent');
    });
    
    Route::controller(ItemTypesController::class)->group(function () {
        Route::post('/admin/itemTypeField/add', 'add')->name('itemTypeField.add');
        Route::get('/admin/itemTypeField/remove/{itemField}/{itemTypeId}', 'remove')->name('itemTypeField.remove');
    });

    Route::get('/admin/runExampleCheck/{verbose?}/{showDetailsIfCorrect?}/{id?}/{charEncoding?}', [ExampleCheckController::class, 'runExampleCheck'])->name('admin.examples.runCheck');

    Route::get('/admin/users', [UsersController::class, 'index'])->name('admin.users');
});

require __DIR__.'/auth.php';
