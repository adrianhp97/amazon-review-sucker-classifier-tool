<?php

namespace AmazonReviewSuckerClassifierTool\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Scrapper;
use AmazonReviewSuckerClassifierTool\Review;

class AmazonFetchingData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $asin;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($asin)
    {
        $this->asin = $asin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $results = $this->getReviewByAsin($this->asin);
        foreach ($results as $review) {
            if ($review != null) {
                $review['asin'] = $this->asin;
                $newReview = Review::create($review);
            }
        }
    }

    public function getReviewByAsin($asin)
	{
        $counter = 1;
        $isEndOfPage = False;
        $reviews = array();
        while (!$isEndOfPage) {
            $params = "/?ie=UTF8&sortBy=recent&pageNumber={$counter}";
            $crawler = Scrapper::request('GET', 'https://www.amazon.com/product-reviews/' . $asin . $params);
            if ($crawler->filter('div#cm_cr-review_list > div')->count() > 0) {
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
                            'score' => (int)$score[0],
                            'date' => $this->stringToDate($date),
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
            } else {
                $isEndOfPage = True;
            }
        }
        return $reviews;
    }
    
    public function stringToDate($text)
    {
        list($dummy, $month, $day, $year) = explode(' ', $text);
        $newDate = $month . ' ' . $day . ' ' . $year;
        return date('Y-m-d', strtotime($newDate));
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        //
    }
}
