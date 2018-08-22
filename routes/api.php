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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/review','ReviewController@getAllReview');
Route::post('/review-amazon','ReviewController@scrapeReviewFromAmazon');
Route::get('/review/{id}','ReviewController@getReviewById');
Route::post('/review/create','ReviewController@store');
Route::put('/review/{id}','ReviewController@update');
Route::delete('/review','ReviewController@destroy');

Route::get('/test', function() {
    $asin = 'B003BEDQL2';
    $asin = 'B00ZDJ787Q';
    $params = '/?ie=UTF8&sortBy=recent&pageNumber=1';
    $crawler = Scrapper::request('GET', 'https://www.amazon.com/BLUE-Life-Protection-Dog-Food/product-reviews/B00ZDJ787Q/ref=cm_cr_getr_d_paging_btm_32?ie=UTF8&reviewerType=all_reviews&pageNumber=1');
    $reviews = $crawler->filter('div#cm_cr-review_list > div')->each(function($review) {
        if ($review->filter('div.review div.celwidget')->count() > 0) {
            $title = $review->filter('div.review div.celwidget div:nth-child(1) a.review-title')->text();
            $title_link = $review->filter('div.review div.celwidget div:nth-child(1) a.review-title')->attr('href');
            $score = $review->filter('div.review div.celwidget div:nth-child(1) a')->attr('title');
            $date = $review->filter('div.review div.celwidget div:nth-child(2) span.review-date')->text();
            
            list($dummy, $month, $day, $year) = explode(' ', $date);
            $newDate = $month . ' ' . $day . ' ' . $year;
            $date = date('d/m/Y', strtotime($newDate));
            
            $author = $review->filter('div.review div.celwidget div:nth-child(2) span.review-byline a.author')->text();
            $author_link = $review->filter('div.review div.celwidget div:nth-child(2) span.review-byline a.author')->attr('href');
            if ($review->filter('div.review div.celwidget div.review-comments span.review-comment-total')->count() > 0) {
                $number_of_comments = $review->filter('div.review div.celwidget div.review-comments span.review-comment-total')->text();
            } else {
                $number_of_comments = 0;
            }
            $photo = $review->filter('div.review div.celwidget div.review-data div.review-image-container')->count() > 0;
            $video = $review->filter('div.review div.celwidget div.review-data div.review-video-container')->count() > 0;
            $photos_or_video = $photo && $video;
            $verified = $review->filter('div.review div.celwidget div:nth-child(3) span.a-declarative')->count() > 0;
            return [
                'title' => $title,
                'title_link' => $title_link,
                'score' => (int)$score[0],
                'date' => $date,
                'author' => $author,
                'author_link' => $author_link,
                'number_of_comments' => $number_of_comments,
                'photos_or_video' => $photos_or_video,
                'verified' => $verified
            ];
        }
    });
    return response($reviews);
});

Route::get('/full-test/{asin}', function($asin) {
    $counter = 1;
    $isEndOfPage = False;
    $reviews = array();
    while ($counter < 3) {
        $params = "/?ie=UTF8&sortBy=recent&pageNumber={$counter}";
        $crawler = Scrapper::request('GET', 'https://www.amazon.com/product-reviews/' . $asin . $params);
        $reviews_page = $crawler->filter('div#cm_cr-review_list > div')->each(function($review) {
            if ($review->filter('div.review div.celwidget')->count() > 0) {
                $title = $review->filter('div.review div.celwidget div:nth-child(1) a.review-title')->text();
                $title_link = $review->filter('div.review div.celwidget div:nth-child(1) a.review-title')->attr('href');
                $score = $review->filter('div.review div.celwidget div:nth-child(1) a')->attr('title');
                $date = $review->filter('div.review div.celwidget div:nth-child(2) span.review-date')->text();
                $author = $review->filter('div.review div.celwidget div:nth-child(2) span.review-byline a.author')->text();
                $author_link = $review->filter('div.review div.celwidget div:nth-child(2) span.review-byline a.author')->attr('href');
                if ($review->filter('div.review div.celwidget div.review-comments span.review-comment-total')->count() > 0) {
                    $number_of_comments = $review->filter('div.review div.celwidget div.review-comments span.review-comment-total')->text();
                } else {
                    $number_of_comments = 0;
                }
                $photo = $review->filter('div.review div.celwidget div.review-data div.review-image-container')->count() > 0;
                $video = $review->filter('div.review div.celwidget div.review-data div.review-video-container')->count() > 0;
                $photos_or_video = $photo && $video;
                $verified = $review->filter('div.review div.celwidget div:nth-child(3) span.a-declarative')->count() > 0;
                $child_product = False;
                $child_asin = False;
                return [
                    'title' => $title,
                    'title_link' => $title_link,
                    'score' => $score,
                    'date' => $date,
                    'author' => $author,
                    'author_link' => $author_link,
                    'number_of_comments' => $number_of_comments,
                    'photos_or_video' => $photos_or_video,
                    'verified' => $verified,
                    'child_product' => $child_product,
                    'child_asin' => $child_asin
                ];
            }
        });
        $counter++;
        $reviews = array_merge($reviews, $reviews_page);
        sleep(rand(3,10));
    }
    return response($reviews);
});