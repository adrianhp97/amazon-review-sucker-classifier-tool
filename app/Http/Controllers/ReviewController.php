<?php

namespace AmazonReviewSuckerClassifierTool\Http\Controllers;

use AmazonReviewSuckerClassifierTool\Jobs\AmazonFetchingData;
use AmazonReviewSuckerClassifierTool\Review;
use AmazonReviewSuckerClassifierTool\Report;
use Illuminate\Http\Request;

use Session;
use Validator;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show form for request crawl reviews.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return view
     */
    public function requestPage(Request $request)
    {
    	return view('pages.request');

    }

    /**
     * Show all reviews by asin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return view
     */
    public function resultPage(Request $request, $report_id)
    {
        if (Report::find($report_id)) {
            $perPage = request('perPage', 5);
            $rat = array();
            if (request('rt1', 'true') == 'true') {
                array_push($rat, 1);
            }
            if (request('rt2', 'true') == 'true') {
                array_push($rat, 2);
            }
            if (request('rt3', 'true') == 'true') {
                array_push($rat, 3);
            }
            if (request('rt4', 'true') == 'true') {
                array_push($rat, 4);
            }
            if (request('rt5', 'true') == 'true') {
                array_push($rat, 5);
            }
            
            $verified = request('verified', '');
            $childAsin = request('child', '');

            $report = Report::find($report_id);
            $reviews = Review::where('report_id', $report_id)->get();
            $reviewsAll = Review::where('report_id', $report_id);
            $reviewsAll = $reviewsAll->whereIn('score', $rat);
            if ($verified != '') {
                $reviewsAll = $reviewsAll->where('verified', $verified);
            }
            $reviewsAll = $reviewsAll->where('child_asin', 'LIKE', '%' . $childAsin . '%');                     
            $reviewsAll = $reviewsAll->paginate(10);
            
            $rating = array(0, 0, 0, 0, 0, 0);
            $verified = array(0, 0, 0, 0, 0, 0);
            $unverified = array(0, 0, 0, 0, 0, 0);
            $totalRating = 0;
            foreach($reviews as $review) {
                if ($review->verified) {
                    $verified[$review->score]++;
                } else {
                    $unverified[$review->score]++;
                }
                $rating[$review->score]++;
                $totalRating += $review->score;
            }
            $totalReviews = Review::where('report_id', $report_id)->count();
            $avg = $totalReviews != 0 ? round($totalRating/$totalReviews, 2) : 0;
            $data = array(
                'report' => $report,
                'rating' => $rating,
                'totalRating' => $totalRating,
                'child_asin' => explode(",", $report->asin_variations),
                'reviews' => $reviewsAll,
                'verified' => $verified,
                'unverified' => $unverified,
                'avg' => $avg,
                'message' => Session::get('message')
            );
            return view('pages.result')
                ->with($data);
        } else {
            return redirect()->route('review.request')->with('alert', 'danger|' . trans('messages.token_invalid'));
        }
    }
    
    /**
     * Show form for tags page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return view
     */
    public function tagsPage(Request $request, $tag)
    {
    	$data = array(
            'reviews' => Review::where('tags', 'LIKE', '%' . $tag . '%')->paginate(10),
            'tag' => $tag,
            'message' => Session::get('message')
        );
        return view('pages.tags')
            ->with($data);
    }

    public function updateTag(Request $request, $id)
    {
        $data = $request->all();
        try {
            $review = Review::find($id);
            foreach ($data as $key => $value) {
                $review->$key = $value;
            }
            if ($review->save()) {
                return response()->json([
                    'status' => http_response_code(),
                    'message' => 'Success',
                    'data'	=> $review
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Review not found',
                    'data'		=> ""
                ], 404);
            }
        } catch (\Expection $e) {
    		return response()->json(['message' => $e->getMessage()]);
    	}
    }
    
    /**
     * Get all reviews by asin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllReview(Request $request)
    {
        $report_id = $request->report_id;
        $isReportDone = Report::find($report_id);
        try {
            $review = Review::where('report_id', $report_id)->get();
            if (!$review->isEmpty()) {
                return response()->json([
                    'status' => http_response_code(),
                    'message' => 'Success',
                    'data'	=> $review
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Review not found',
                    'data'		=> ""
                ], 404);
            }
        } catch (\Expection $e) {
    		return response()->json(['message' => $e->getMessage()]);
    	}
    }

     /**
     * Get reviews by id in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReviewById(Request $request, $id)
    {
        try {
            $review = Review::find($id);
            if (!empty($review)) {
                return response()->json([
                    'status' => http_response_code(),
                    'message' => 'Success',
                    'data'	=> $review
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Review not found',
                    'data'		=> ""
                ], 404);
            }
        } catch (\Expection $e) {
    		return response()->json(['message' => $e->getMessage()]);
    	}
    }

    /**
     * Scrape and store review from amazon asin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scrapeReviewFromAmazon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asin' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('review.request')
                        ->withErrors($validator)
                        ->withInput();
        }

        $asin = $request->asin;
        $report = Report::create($request->all());
        dispatch((new AmazonFetchingData($report->id, $asin))->delay(5));
        return redirect()->route('review.result', $report->id)->with('message', 'Your report is being generated. You can bookmark this page and return to it later.');;
    }
}
