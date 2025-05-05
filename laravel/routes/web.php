<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BstFileController;
use App\Http\Controllers\StatisticsController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BstsController;
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
use App\Http\Controllers\Admin\JournalWordAbbreviationsController;
use App\Http\Controllers\Admin\TrainingItemsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\VonNamesController;

use App\Http\Controllers\Convert\ErrorReportController;
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
    Route::get('/examples', 'examples')->name('examples');
});

Route::controller(StatisticsController::class)->group(function () {
    Route::get('/statistics', 'index')->name('statistics');
    Route::get('/statsBibtex', 'bibtex')->name('statsBibtex');
    Route::get('/statsLanguages', 'languages')->name('statsLanguages');
    Route::get('/statsCrossref', 'crossref')->name('statsCrossref');
});

Route::controller(BstFileController::class)->group(function () {
    Route::get('/bsts', 'index')->name('bsts');
    Route::get('/searchBsts', 'index')->name('bsts.search');
});

Route::middleware('auth', 'noRequiredResponses')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::controller(ErrorReportController::class)->group(function () {
    Route::get('/errorReport/{id}', 'show')->name('errorReport');
    });

    Route::controller(CommentController::class)->group(function () {
        Route::get('/comment/{id}', 'show')->name('comment');
    });

    Route::controller(IndexController::class)->group(function () {
        Route::get('/requiredResponses', 'requiredResponses')->name('requiredResponses');
    });
});

Route::middleware(['auth', 'verified', 'noRequiredResponses'])->group(function () {
    Route::controller(IndexController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/convertFile', 'convertFile')->name('convertFile');
        Route::post('/convertFile', 'convertFile')->name('convertFile');
        Route::get('/conversions', 'conversions')->name('conversions');
        Route::get('/showConversion/{id}/{flag?}', 'showConversion')->name('showConversion');
        Route::get('/downloadSource/{userFileId}', 'downloadSource')->name('downloadSource');
    });

    Route::controller(ErrorReportController::class)->group(function () {
        Route::get('/errorReports/{sortBy?}', 'index')->name('errorReports');
    });

    Route::resources([
        '/threads' => CommentController::class,
    ]);

    Route::controller(CommentController::class)->group(function () {
        Route::get('/comments/{sortBy?}', 'index')->name('comments');
    });

    Route::get('/downloadBibtex/{conversionId}', DownloadBibtexController::class)->name('conversion.downloadBibtex');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resources([
        '/admin/bsts' => BstsController::class,
        '/admin/itemTypes' => ItemTypesController::class,
        '/admin/itemFields' => ItemFieldsController::class,
        '/admin/examples' => ExamplesController::class,
        '/admin/vonNames' => VonNamesController::class,
        '/admin/publishers' => PublishersController::class,
        '/admin/cities' => CitiesController::class,
        '/admin/journalWordAbbreviations' => JournalWordAbbreviationsController::class,
        '/admin/names' => NamesController::class,
        '/admin/excludedWords' => ExcludedWordsController::class,
        '/admin/dictionaryNames' => DictionaryNamesController::class,
        '/admin/journals' => JournalsController::class,
    ]);

    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/index', 'index')->name('admin.index');
        Route::get('/admin/phpinfo', 'phpinfo')->name('admin.phpinfo');
        Route::get('/admin/addVersion', 'addVersion')->name('admin.addVersion');
        Route::get('/admin/addExistingStarts', 'addExistingStarts')->name('admin.journalWordAbbreviations.addExistingStarts');
    });

    Route::controller(ErrorReportController::class)->group(function () {
        Route::get('/convertErrorSource/{id}', 'convertSource')->name('convertErrorSource');
    });

    Route::controller(BstsController::class)->group(function () {
        Route::get('/admin/uncheckedBsts', 'unchecked')->name('admin.bsts.unchecked');
    });

    Route::controller(CitiesController::class)->group(function () {
        Route::get('/admin/uncheckedCities', 'unchecked')->name('admin.cities.unchecked');
    });

    Route::controller(PublishersController::class)->group(function () {
        Route::get('/admin/uncheckedPublishers', 'unchecked')->name('admin.publishers.unchecked');
    });

    Route::controller(JournalsController::class)->group(function () {
        Route::get('/admin/uncheckedJournals', 'unchecked')->name('admin.journals.unchecked');
    });

    Route::controller(JournalWordAbbreviationsController::class)->group(function () {
        Route::get('/admin/uncheckedJournalWordAbbreviations', 'unchecked')->name('admin.journalWordAbbreviations.unchecked');
        Route::get('/admin/populateJournalAbbreviations', 'populate')->name('admin.journalWordAbbreviations.populate');
    });

    Route::controller(ConversionAdminController::class)->group(function () {
        Route::get('/admin/showConversion/{conversionId}/{userId}/{style}/{page}', 'showConversion')->name('admin.showConversion');
        Route::get('/admin/convert/{fileId}/{itemSeparator?}', 'convert')->name('admin.convert');
        Route::get('/admin/conversions/{userId?}/{style?}', 'index')->name('admin.conversions');
        Route::get('/admin/formatExample/{outputId}', 'formatExample')->name('admin.formatExample');
        Route::get('/admin/downloadSource/{userFileId}', 'downloadSource')->name('admin.downloadSource');
        Route::delete('/admin/conversion/{conversionId}', 'destroy')->name('admin.conversion.destroy');
        Route::post('/admin/conversionExamined', 'examined')->name('admin.conversion.examined');
        Route::get('/admin/conversionUnexamined/{conversionId}', 'unexamined')->name('admin.conversion.unexamined');
        Route::post('/admin/searchConversions', 'search')->name('admin.search.conversions');
        Route::get('/admin/searchConversions', 'search')->name('admin.search.conversions');
    });

    Route::controller(ExampleFieldsController::class)->group(function () {
        Route::get('/admin/exampleFields/{fieldId}/editContent', 'editContent')->name('admin.exampleFields.editContent');
        Route::put('/admin/exampleFields/{fieldId}/updateContent', 'updateContent')->name('admin.exampleFields.updateContent');
    });
    
    Route::controller(ItemTypesController::class)->group(function () {
        Route::post('/admin/itemTypeField/add', 'add')->name('itemTypeField.add');
        Route::get('/admin/itemTypeField/remove/{itemField}/{itemTypeId}', 'remove')->name('itemTypeField.remove');
    });

    Route::controller(ExamplesController::class)->group(function () {
        Route::get('/admin/seedExamples', 'seed')->name('admin.examples.seed');
    });

    Route::controller(ExampleCheckController::class)->group(function () {
        Route::get('/admin/runExampleCheck/{reportType?}/{language?}/{detailsIfCorrect?}/{id?}/{charEncoding?}', 'runExampleCheck')->name('admin.examples.runCheck');
        Route::post('/admin/runExampleCheck', 'runExampleCheck')->name('admin.examples.runCheck');
    });

    Route::controller(TrainingItemsController::class)->group(function () {
        Route::get('/admin/trainingItems', 'index')->name('admin.trainingItems.index');
        Route::get('/admin/trainingItems/convert', 'convert')->name('admin.trainingItems.convert');
        Route::get('/admin/trainingItems/{id}/convert', 'convertItem')->name('admin.trainingItems.convertItem');
        Route::get('/admin/trainingItems/clean', 'clean')->name('admin.trainingItems.clean');
        Route::get('/admin/trainingItems/copy', 'copy')->name('admin.trainingItems.copy');
        Route::get('/admin/trainingItems/showLowercase', 'showLowercase')->name('admin.trainingItems.showLowercase');
    });

    Route::controller(UsersController::class)->group(function () {
        Route::get('/admin/users/{sortBy?}', 'index')->name('admin.users');
        Route::delete('/admin/users/{id}', 'destroy')->name('admin.user.destroy');
        Route::post('/admin/searchUsers', 'index')->name('admin.search.users');
    });
});

require __DIR__.'/auth.php';
