<?php

namespace AmazonReviewSuckerClassifierTool\Http\Controllers;

use AmazonReviewSuckerClassifierTool\Jobs\AmazonFetchingData;
use AmazonReviewSuckerClassifierTool\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  //still empty
     * @return void
     */
    public function __construct()
	{
        //
    }
    
    /**
     * Get all reviews by asin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllReview(Request $request)
    {
        $asin = $request->asin;
        try {
            $review = Review::where('asin', $asin)->get();
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            $newReview = Review::create($data);
            return response()->json([
				'status' => http_response_code(),
                'message' => 'Success',
				'data'	=> $newReview
			], 200);
        } catch (\Expection $e) {
    		return response()->json(['message' => $e->getMessage()]);
    	}
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
     * Remove the specified resource from storage by id or asin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            if ($request->has('id')) {
                $id = $request->id;
                $data = array('id' => $id);
            } else if ($request->has('asin')) {
                $asin = $request->asin;
                $data = array('asin' => $asin);
            }
            
            if (Review::where('asin', $asin)->delete()) {
                return response()->json([
                    'status' => http_response_code(),
                    'message' => 'Success',
                    'data'	=> $data
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
        $asin = $request->asin;
        dispatch((new AmazonFetchingData($asin)));
        // dispatch((new AmazonFetchingData($asin))->delay(60 * 5));
        try {
            $review = Review::where('asin', $asin)->get();
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
}
