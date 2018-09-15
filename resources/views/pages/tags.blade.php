@extends('layouts.base')

@section('content')
<div class="content-wrapper report">
  <div class="content-title">
    <h1 class="page-header">Amazon Review Report</h1>
  </div>
  <div class="content-body">
    <div class="review-result">
      <h3 class="page-header">Result: {{ $tag }}</h3>
        @if (count($reviews) > 0)
          @foreach ($reviews as $review)
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-10">
                  <h3 class="panel-title">{{ $review->title }}</h3>
                  {{ $review->desc }}
                </div>
                <div class="col-md-2">
                  <div>
                    @for ($idx = 1; $idx < 6; $idx++)
                      @if($idx <= $review->score)
                        <span class="fa fa-star checked"></span>
                      @else
                        <span class="fa fa-star"></span>
                      @endif
                    @endfor
                  </div>
                  <div>{{ $review->author }}</div>
                  <div>{{ $review->date }}</div>
                  <div>{{ $review->number_of_comments }} comments</div>
                  <div>{{ $review->vote }} helpful votes</div>
                </div>
              </div>
              <hr>
              <div class="tags-field"></div>
              <div>
                <label>Tags: </label>
                <input id="{{ $review->id }}" class="review-tags" name="tags" type="text" value="{{ $review->tags }}" data-role="tagsinput">
              </div>
            </div>
          </div>
          @endforeach
          <div class="pagination-wrapper">{{ $reviews->links() }}</div>
        @else
          <div>No review.</div>
        @endif
    </div>
  </div>
  <div class="content-footer"></div>
</div>


<script>
$(document).ready(function() {
	$('.review-tags').on('itemAdded', function(item, tag) {
    var tags = $(this).val();
    var url = '/review/update-tags/' + $(this).attr('id');
    $.ajax({
      type: "PUT",
      url: url,
      data: {
        tags: tags
      },
      headers: {
        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
      },
      success: function (result) {
        // console.log(result);
      }
    })
    let tagsArr = tags.split(",");
    let node = '';
    for (tag in tagsArr) {
      node += `
        <span class="label label-default">${tagsArr[tag]}</span>
      `
    }
    $(this).parent().parent().children('.tags-field').html(node);
  });

  $('.review-tags').on('itemRemoved', function(item, tag) {
    var tags = $(this).val();
    var url = '/review/update-tags/' + $(this).attr('id');
    $.ajax({
      type: "PUT",
      url: url,
      data: {
        tags: tags
      },
      headers: {
        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
      },
      success: function (result) {
        // console.log(result);
      }
    });
    let tagsArr = tags.split(",");
    let node = '';
    for (tag in tagsArr) {
      node += `
        <span class="label label-default">${tagsArr[tag]}</span>
      `
    }
    $(this).parent().parent().children('.tags-field').html(node);
  });

  $('.tags-field').on('click', 'span.label', function(event) {
    let tag = $(this).text();
    window.location.href = `/review/tags/${tag}`;
  })
});
</script>
@endsection
