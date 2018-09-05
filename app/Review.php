<?php

namespace AmazonReviewSuckerClassifierTool;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reviews';

    protected $fillable = [
        'report_id',
        'asin',
        'title',
        'title_link',
        'score',
        'date',
        'author',
        'author_link',
        'number_of_comments',
        'photos_or_video',
        'verified',
        'child_product',
        'child_asin',
    ];
}
