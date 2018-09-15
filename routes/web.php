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

Auth::routes();

Route::get('/redirect', 'SocialAuthFacebookController@redirect');
Route::get('/callback', 'SocialAuthFacebookController@callback');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('review')->group(function() {
    Route::get('', 'ReviewController@requestPage')->name('review.request');
    Route::get('/report/{report_id}', 'ReviewController@resultPage')->name('review.result');
    Route::post('', 'ReviewController@scrapeReviewFromAmazon')->name('review.scrape');
    Route::put('/update-tags/{id}', 'ReviewController@updateTag')->name('review.tags.update');
    Route::get('/tags/{tag}', 'ReviewController@tagsPage')->name('review.tags');
});
