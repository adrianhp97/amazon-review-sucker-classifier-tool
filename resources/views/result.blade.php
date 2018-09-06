@extends('layouts.base')

@section('content')
<div class="content-wrapper">
  <div class="content-title">
    <h1>Amazon Return Reconciliation</h1>
  </div>
  <div class="content-body">
    @if (!empty($message))
      <div class="alert alert-success" role="alert">{{ $message }}</div>
    @endif
    <div id="report-information" class="well-s">
      <div class="row">
        <div class="col-sm-4 font-weight-bold">Report Id</div>
        <div class="col-sm-8">{{ $report->id }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4 font-weight-bold">Asin</div>
        <div class="col-sm-8">{{ $report->asin }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4 font-weight-bold">Request Date/Time</div>
        <div class="col-sm-8">{{ $report->created_at }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4 font-weight-bold">Scrape Date/Time End</div>
        <div class="col-sm-8">{{ $report->updated_at }}</div>
      </div>
    </div>
    <br>
    <h3>Result</h3>
    @if ($report->done)
      @if (count($reviews) > 0)
        @foreach ($reviews as $review)
          <div class="review-wrapper">
            <table>
              <tr>
                <td>Title</td>
                <td>:</td>
                <td>{{ $review->title }}</td>
              </tr>
              <tr>
                <td>Title Link</td>
                <td>:</td>
                <td>{{ $review->title_link }}</td>
              </tr>
              <tr>
                <td>Score</td>
                <td>:</td>
                <td>{{ $review->score }}</td>
              </tr>
              <tr>
                <td>Date</td>
                <td>:</td>
                <td>{{ $review->date }}</td>
              </tr>
              <tr>
                <td>Author</td>
                <td>:</td>
                <td>{{ $review->author }}</td>
              </tr>
              <tr>
                <td>Author Link</td>
                <td>:</td>
                <td>{{ $review->author_link }}</td>
              </tr>
              <tr>
                <td>Number of Comments</td>
                <td>:</td>
                <td>{{ $review->number_of_comments }}</td>
              </tr>
              <tr>
                <td>Photos or Video</td>
                <td>:</td>
                <td>{{ $review->photos_or_video }}</td>
              </tr>
              <tr>
                <td>Verified</td>
                <td>:</td>
                <td>{{ $review->verified }}</td>
              </tr>
              <tr>
                <td>Child Product</td>
                <td>:</td>
                <td>{{ $review->child_product }}</td>
              </tr>
              <tr>
                <td>Child Asin</td>
                <td>:</td>
                <td>{{ $review->child_asin }}</td>
              </tr>
            </table>
          </div>
        @endforeach
        <span class="page-showing">Showing {{$reviews->firstItem()}} to {{$reviews->lastItem()}} of {{$totalReviews}}</span>
        <nav>{{ $reviews->links() }}</nav>
      @else
        <div>No review.</div>
      @endif
    @else
      <div>Your request is currently in progress.</div>
    @endif

  </div>
  <div class="content-footer"></div>
</div>
@endsection
