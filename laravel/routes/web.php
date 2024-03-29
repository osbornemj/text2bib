<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\ConversionAdminController;
use App\Http\Controllers\Admin\DictionaryNamesController;
use App\Http\Controllers\Admin\ExamplesController;
use App\Http\Controllers\Admin\ExampleCheckController;
use App\Http\Controllers\Admin\ExampleFieldsController;
use App\Http\Controllers\Admin\ExcludedWordsController;
use App\Http\Controllers\Admin\ItemFieldsController;
use App\Http\Controllers\Admin\ItemTypesController;
use App\Http\Controllers\Admin\JournalsController;
use App\Http\Controllers\Admin\NamesController;
use App\Http\Controllers\Admin\PublishersController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\VonNamesController;

use App\Http\Controllers\Convert\ErrorReportController;
use App\Http\Controllers\Convert\FileUploadController;
use App\Http\Controllers\Convert\DownloadBibtexController;

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
Route::controller(IndexController::class)->group(function () {
    Route::get('/', 'index')->name('public.index');
    Route::get('/about', 'about')->name('about');
});

Route::controller(StatisticsController::class)->group(function () {
    Route::get('/statistics', 'index')->name('statistics');
});

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::controller(IndexController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/convertFile', 'convertFile')->name('convertFile');
        Route::post('/convertFile', 'convertFile')->name('convertFile');
        Route::get('/conversions', 'conversions')->name('conversions');
        Route::get('/showConversion/{id}', 'showConversion')->name('showConversion');
        Route::get('/downloadSource/{userFileId}', 'downloadSource')->name('downloadSource');
    });

    Route::controller(ErrorReportController::class)->group(function () {
        Route::get('/errorReports', 'index')->name('errorReports');
        Route::get('/errorReport/{id}', 'show')->name('errorReport');
        Route::get('/convertErrorSource/{id}', 'convertSource')->name('convertErrorSource');
    });

    Route::resources([
        '/threads' => CommentController::class,
    ]);

    Route::controller(CommentController::class)->group(function () {
        Route::get('/comments', 'index')->name('comments');
        Route::get('/comment/{id}', 'show')->name('comment');
    });

    Route::get('/downloadBibtex/{conversionId}', DownloadBibtexController::class)->name('conversion.downloadBibtex');

    Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');
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
        '/admin/dictionaryNames' => DictionaryNamesController::class,
        '/admin/journals' => JournalsController::class,
    ]);

    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/index', 'index')->name('admin.index');
        Route::get('/admin/addVersion', 'addVersion')->name('admin.addVersion');
    });

    Route::controller(ConversionAdminController::class)->group(function () {
        Route::get('/admin/showConversion/{conversionId}/{page}', 'showConversion')->name('admin.showConversion');
        Route::get('/admin/convert/{fileId}/{itemSeparator?}', 'convert')->name('admin.convert');
        Route::get('/admin/conversions', 'index')->name('admin.conversions');
        Route::get('/admin/formatExample/{outputId}', 'formatExample')->name('admin.formatExample');
        Route::get('/admin/downloadSource/{userFileId}', 'downloadSource')->name('admin.downloadSource');
        Route::delete('/admin/conversion/{conversionId}', 'destroy')->name('admin.conversion.destroy');
        Route::get('/admin/conversionExamined/{conversionId}/{page}', 'examined')->name('admin.conversion.examined');
        Route::get('/admin/conversionUnexamined/{conversionId}', 'unexamined')->name('admin.conversion.unexamined');
    });

    Route::controller(ExampleFieldsController::class)->group(function () {
        Route::get('/admin/exampleFields/{fieldId}/editContent', 'editContent')->name('admin.exampleFields.editContent');
        Route::put('/admin/exampleFields/{fieldId}/updateContent', 'updateContent')->name('admin.exampleFields.updateContent');
    });
    
    Route::controller(ItemTypesController::class)->group(function () {
        Route::post('/admin/itemTypeField/add', 'add')->name('itemTypeField.add');
        Route::get('/admin/itemTypeField/remove/{itemField}/{itemTypeId}', 'remove')->name('itemTypeField.remove');
    });

    Route::get('/admin/runExampleCheck/{reportType?}/{language?}/{detailsIfCorrect?}/{id?}/{charEncoding?}', [ExampleCheckController::class, 'runExampleCheck'])->name('admin.examples.runCheck');
    Route::post('/admin/runExampleCheck', [ExampleCheckController::class, 'runExampleCheck'])->name('admin.examples.runCheck');

    Route::get('/admin/users', [UsersController::class, 'index'])->name('admin.users');
});

require __DIR__.'/auth.php';
