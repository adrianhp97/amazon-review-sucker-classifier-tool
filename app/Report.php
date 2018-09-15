<?php

namespace AmazonReviewSuckerClassifierTool;

use AmazonReviewSuckerClassifierTool\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use Uuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'asin',
        'asin_variations',
        'verified',
        'unverified',
        'done',
    ];
}
