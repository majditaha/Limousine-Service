<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('client_config', 'ClientConfigController@get');
Route::get('pages/{alias}', 'PagesController@get');
Route::get('messages/reviews', 'MessagesController@getReviews');
Route::get('agreement', 'PagesController@agreement');

Route::group(['middleware' => ['auth:api']], function() {
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function() {
        //
        // All admin related endpoints go here
        //
        Route::resource('cities', 'CitiesController');
        Route::resource('disciplines', 'DisciplinesController');
        Route::resource('menu_items', 'MenuItemsController');

        Route::get('messages/{message}/history', 'MessagesController@getHistory');
        Route::resource('messages', 'MessagesController');

        Route::resource('pages', 'PagesController');
        Route::resource('practices', 'PracticesController');
        Route::resource('schools', 'SchoolsController');
        Route::resource('sections', 'SectionsController');
        Route::resource('variants', 'VariantsController');
        Route::resource('settings', 'SettingsController', [
            'except' => ['destroy', 'store', 'show'],
        ]);
        Route::resource('subtypes', 'SubtypesController');
        Route::resource('theories', 'TheoriesController');
        Route::resource('transactions', 'TransactionsController', [
            'only' => ['index'],
        ]);
        Route::resource('users', 'UsersController');
    });

    Route::group(['prefix' => 'teacher', 'namespace' => 'Teacher'], function() {
        //
        // All teacher related endpoints go here
        //

        Route::post('messages/{message}/answer', 'MessagesController@answer');
        Route::put('messages/{message}/mark_read', 'MessagesController@markRead');
        Route::put('messages/{message}/mark_taken', 'MessagesController@markTaken');
        Route::resource('messages', 'MessagesController', [
            'except' => ['update', 'destroy'],
        ]);

        Route::get('statistics', 'StatisticsController@get');
    });

    Route::group(['prefix' => 'account'], function() {
        //
        // Actions with authenticated user's account
        //
        Route::put('/', 'AccountController@update');
        Route::put('/desired_hours', 'AccountController@setDesiredHours');
        Route::put('/finish_theory/{theory}', 'AccountController@setTheoryFinished');
        Route::put('/finish_practice/{practice}', 'AccountController@setPracticeFinished');
        Route::put('/finish_section/{section}', 'AccountController@setSectionFinished');
        Route::post('/presence', 'AccountController@logPresence');
        Route::get('/available_resources', 'AccountController@getAvailableResources');
        Route::post('/add_money', 'AccountController@addMoney');
        Route::post('/purchase_plan/{plan}', 'AccountController@purchasePlan');
    });

    // All simple user related endpoints go here
    Route::resource('cities', 'CitiesController', [
        'only' => ['index'],
    ]);

    Route::resource('plans', 'PlansController', [
        'only' => ['index'],
    ]);

    Route::resource('disciplines', 'DisciplinesController', [
        'only' => ['index', 'show'],
    ]);

    Route::resource('variants', 'VariantsController', [
        'only' => ['show'],
    ]);

    Route::get('practices/trainings/{section}', 'PracticesController@getTrainings');
    Route::get('practices/smart', 'PracticesController@getSmart');

    Route::get('sections/{section}', 'SectionsController@show');
    Route::get('sections/{section}/drop_progress', 'SectionsController@dropProgress');
    Route::get('sections/finished/{discipline}', 'SectionsController@getFinished');
    Route::get('sections/latest_in_training/{discipline}', 'SectionsController@getLatestInTraining');

    Route::put('messages/{message}/rating', 'MessagesController@setRating');
    Route::put('messages/{message}/mark_read', 'MessagesController@markRead');
    Route::resource('messages', 'MessagesController', [
        'except' => ['update', 'destroy'],
    ]);
});
