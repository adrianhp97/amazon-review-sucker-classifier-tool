<?php

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

Route::prefix('review')->group(function() {
    Route::get('', 'ReviewController@requestPage')->name('review.request');
    Route::get('/report/{report_id}', 'ReviewController@resultPage')->name('review.result');
    Route::post('', 'ReviewController@scrapeReviewFromAmazon')->name('review.scrape');
});