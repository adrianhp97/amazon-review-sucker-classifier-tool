<?php

namespace AmazonReviewSuckerClassifierTool\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Scrapper;
use Storage;
use AmazonReviewSuckerClassifierTool\Review;
use AmazonReviewSuckerClassifierTool\Report;

class AmazonFetchingData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $asin;
    protected $report_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($report_id, $asin)
    {
        $this->report_id = $report_id;
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
        $asinVariation = array();
        $verified = 0;
        $unverified = 0;
        foreach ($results as $review) {
            if (!array_key_exists('error', $review)) {
                $review['report_id'] = $this->report_id;
                $review['asin'] = $this->asin;
                if ($review['child_product']) {
                    if (!in_array($review['child_asin'], $asinVariation)) {
                        array_push($asinVariation, $review['child_asin']);
                    }
                }
                if ($review['verified']) {
                    $verified++;
                } else {
                    $unverified++;
                }
                $newReview = Review::create($review);
            }
        }
        $report = Report::find($this->report_id);
        $report->done = true;
        $report->asin_variations =  implode(",", $asinVariation);
        $report->verified = $verified;
        $report->unverified = $unverified;
        $report->save();
    }

    public function getReviewByAsin($asin)
	{
        $counter = 1;
        $reviews = array();
        $reviews_page = array(array('start' => True));
        while (!array_key_exists('error', $reviews_page[0])) {
            $params = "/?ie=UTF8&sortBy=recent&pageNumber={$counter}";
            $crawler = Scrapper::request('GET', 'https://www.amazon.com/product-reviews/' . $asin . $params);
            if ($crawler->filter('div#cm_cr-review_list > div')->count() > 0) {
                $reviews_page = $crawler->filter('div#cm_cr-review_list > div')->each(function($review, $isEndOfPage) {
                    if ($review->filter('.no-reviews-section')->count() > 0) {
                        return [
                            'error' => True
                        ];
                    }
                    if ($review->filter('div.review div.celwidget')->count() > 0) {
                        $title = $review->filter('div.review div.celwidget div:nth-child(1) a.review-title')->text();
                        $title_link = $review->filter('div.review div.celwidget div:nth-child(1) a.review-title')->attr('href');
                        $score = $review->filter('div.review div.celwidget div:nth-child(1) a')->attr('title');
                        if ($review->filter('div.review div.celwidget div.review-comments span.cr-vote span.cr-vote-text')->count() > 0) {
                            $vote = $review->filter('div.review div.celwidget div.review-comments span.cr-vote span.cr-vote-text')->text();
                            $pieces = explode(" ", $vote);
                            $vote = (int)$pieces[0];
                        } else {
                            $vote = 0;
                        }
                        $desc = $review->filter('div.review div.celwidget div.review-data span.review-text')->text();
                        $date = $review->filter('div.review div.celwidget div:nth-child(2) span.review-date')->text();
                        $author = $review->filter('div.review div.celwidget div:nth-child(2) span.review-byline a.author')->text();
                        $author_link = $review->filter('div.review div.celwidget div:nth-child(2) span.review-byline a.author')->attr('href');
                        if ($review->filter('div.review div.celwidget div.review-comments span.review-comment-total')->count() > 0) {
                            $number_of_comments = $review->filter('div.review div.celwidget div.review-comments span.review-comment-total')->text();
                            $pieces = explode(" ", $number_of_comments);
                            $number_of_comments = (int)$pieces[0];
                        } else {
                            $number_of_comments = 0;
                        }
                        $photo = $review->filter('div.review div.celwidget div.review-data div.review-image-container')->count() > 0;
                        $video = $review->filter('div.review div.celwidget div.review-data div.review-video-container')->count() > 0;
                        $photos_or_video = $photo && $video;
                        $isVerifiedExist = $review->filter('div.review div.celwidget div:nth-child(3) > span')->each(function($node, $i) {
                            return (strpos($node->text(), 'Verified') !== false);
                        });
                        $verified = false;
                        foreach ($isVerifiedExist as $ver) {
                            $verified |= $ver;
                        }           
                        $child_product = $review->filter('div.review div.celwidget div:nth-child(3) a.a-size-mini')->count() > 0;
                        if ($child_product) {
                            $mini_asin = $review->filter('div.review div.celwidget div:nth-child(3) a.a-size-mini')->attr('href');
                            preg_match("/((?<=product-reviews\/)[\w\d].*(?=\/)|$)/", $mini_asin, $c_asin);
                            $child_asin = $c_asin[0];
                        } else {
                            $child_asin = False;
                        }
                        return [
                            'title' => $title,
                            'title_link' => $title_link,
                            'desc' => $desc,
                            'score' => (int)$score[0],
                            'vote' => $vote,
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
                    return [
                        'error' => True
                    ];
                });
                $counter++;
            } else {
                $reviews_page = array(array(
                    'error' => True
                ));
            }
            $reviews = array_merge($reviews, $reviews_page);
            sleep(rand(3,10));
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
