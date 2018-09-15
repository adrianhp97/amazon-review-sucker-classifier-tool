@extends('layouts.base')

@section('content')
<div class="content-wrapper report">
  <div class="content-title">
    <h1 class="page-header">Amazon Review Report</h1>
  </div>
  <div class="content-body">
    @if (!empty($message))
      <div class="alert alert-success" role="alert">{{ $message }}</div>
    @endif
    <div class="detail-wrapper">
      <div class="row">
        <div class="col-md-4 filter">
          <span class="heading">{{ $report->asin }}</span>
          @for ($idx = 1; $idx < 6; $idx++)
            @if($idx <= round($avg))
              <span class="fa fa-star checked"></span>
            @else
              <span class="fa fa-star"></span>
            @endif
          @endfor
          <p>{{ $avg }} average based on <span id="total-review">{{ $report->verified + $report->unverified }}</span> reviews.</p>
          <hr class="hr-border">

          <div class="row">
            <div class="side">
              <div>5 star</div>
            </div>
            <div class="middle">
              <div class="bar-container">
                <div class="bar-5"></div>
              </div>
            </div>
            <div class="side right">
              <div>
                <span id="rating-5">{{ $rating[5] }}</span>
                (<span class="text-green">{{ $verified[5] }}</span> + <span class="text-red">{{ $unverified[5] }}</span>)
              </div>
            </div>
            <div class="side">
              <div>4 star</div>
            </div>
            <div class="middle">
              <div class="bar-container">
                <div class="bar-4"></div>
              </div>
            </div>
            <div class="side right">
              <div>
                <span id="rating-4">{{ $rating[4] }}</span>
                (<span class="text-green">{{ $verified[4] }}</span> + <span class="text-red">{{ $unverified[4] }}</span>)
              </div>
            </div>
            <div class="side">
              <div>3 star</div>
            </div>
            <div class="middle">
              <div class="bar-container">
                <div class="bar-3"></div>
              </div>
            </div>
            <div class="side right">
              <div>
                <span id="rating-3">{{ $rating[3] }}</span>
                (<span class="text-green">{{ $verified[3] }}</span> + <span class="text-red">{{ $unverified[3] }}</span>)
              </div>
            </div>
            <div class="side">
              <div>2 star</div>
            </div>
            <div class="middle">
              <div class="bar-container">
                <div class="bar-2"></div>
              </div>
            </div>
            <div class="side right">
              <div>
                <span id="rating-2">{{ $rating[2] }}</span>
                (<span class="text-green">{{ $verified[2] }}</span> + <span class="text-red">{{ $unverified[2] }}</span>)
              </div>
            </div>
            <div class="side">
              <div>1 star</div>
            </div>
            <div class="middle">
              <div class="bar-container">
                <div class="bar-1"></div>
              </div>
            </div>
            <div class="side right">
              <div>
                <span id="rating-1">{{ $rating[1] }}</span>
                (<span class="text-green">{{ $verified[1] }}</span> + <span class="text-red">{{ $unverified[1] }}</span>)
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div id="report-information" class="well-s">
            <div class="row">
              <div class="col-sm-4 font-weight-bold">Report Id</div>
              <div class="col-sm-8">{{ $report->id }}</div>
            </div>
            <div class="row">
              <div class="col-sm-4 font-weight-bold">Asin</div>
              <div class="col-sm-8"><a href="https://www.amazon.com/dp/{{$report->asin}}">{{ $report->asin }}</a></div>
            </div>
            <div class="row">
              <div class="col-sm-4 font-weight-bold">Request Date/Time</div>
              <div class="col-sm-8">{{ $report->created_at }}</div>
            </div>
            <div class="row">
              <div class="col-sm-4 font-weight-bold">Finish Date/Time</div>
              <div class="col-sm-8">{{ $report->updated_at }}</div>
            </div>
            <div class="row">
              <div class="col-sm-4 font-weight-bold">Total Review</div>
              <div class="col-sm-8">
                {{ $report->verified + $report->unverified }}
                (<span class="text-green">{{ $report->verified }}</span> + <span class="text-red">{{ $report->unverified }}</span>)
              </div>
            </div>
            <br>
            <form id="filter-form">
              <h4 class="page-header">Filter</h4>
              <div class="form-row">
                <div clss="form-group col-md-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="rt1" id="rt1" checked>
                    <label class="form-check-label" for="rt1">
                      Rating 1
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="rt2" id="rt2">
                    <label class="form-check-label" for="rt2">
                      Rating 2
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="rt3" id="rt3">
                    <label class="form-check-label" for="rt3">
                      Rating 3
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="rt4" id="rt4">
                    <label class="form-check-label" for="rt4">
                      Rating 4
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="rt5" id="rt5">
                    <label class="form-check-label" for="rt5">
                      Rating 5
                    </label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="verified">Verified</label>
                      <select class="form-control" id="verified">
                        <option value="">All</option>
                        <option value="1">Verified</option>
                        <option value="0">Unverified</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="child_asin">Child Asin</label>
                      <select class="form-control" id="child_asin">
                        <option value="">All</option>
                        @foreach ($child_asin as $child)
                          <option valude="{{ $child }}">{{ $child }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Filter</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="review-result">
      <h3 class="page-header">Result</h3>
      @if ($report->done)
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
          <div class="pagination-wrapper">{{ $reviews->appends(request()->except('page'))->links() }}</div>
        @else
          <div>No review.</div>
        @endif
      @else
        <div>Your request is currently in progress.</div>
      @endif
    </div>
  </div>
  <div class="content-footer"></div>
</div>

<script>
$(document).ready(function() {
  let avg1 = ($("#rating-1").text() / $("#total-review").text()) * 100;
  let avg2 = ($("#rating-2").text() / $("#total-review").text()) * 100;
  let avg3 = ($("#rating-3").text() / $("#total-review").text()) * 100;
  let avg4 = ($("#rating-4").text() / $("#total-review").text()) * 100;
  let avg5 = ($("#rating-5").text() / $("#total-review").text()) * 100;
  $(".bar-1").attr('style',  'width: ' + avg1 + '%');
  $(".bar-2").attr('style',  'width: ' + avg2 + '%');
  $(".bar-3").attr('style',  'width: ' + avg3 + '%');
  $(".bar-4").attr('style',  'width: ' + avg4 + '%');
  $(".bar-5").attr('style',  'width: ' + avg5 + '%');

  getUrlParams = function(params) {
    var result = new RegExp(params + "=([^&]*)", "i").exec(window.location.search); 
	  return result && unescape(result[1]) || ""; 
  };

  var params = {
    rt1: getUrlParams('rt1'),
    rt2: getUrlParams('rt2'),
    rt3: getUrlParams('rt3'),
    rt4: getUrlParams('rt4'),
    rt5: getUrlParams('rt5'),
    verified: getUrlParams('verified'),
    child: getUrlParams('child'),
    page: getUrlParams('page'),
  };
  
  $('#rt1').prop('checked', (params['rt1'] == 'true' || !params['rt1']) ? true : false);
  $('#rt2').prop('checked', (params['rt2'] == 'true' || !params['rt2']) ? true : false);
  $('#rt3').prop('checked', (params['rt3'] == 'true' || !params['rt3']) ? true : false);
  $('#rt4').prop('checked', (params['rt4'] == 'true' || !params['rt4']) ? true : false);
  $('#rt5').prop('checked', (params['rt5'] == 'true' || !params['rt5']) ? true : false);
  $('#verified').val(params['verified'] ? params['verified'] : '');
  $('#child_asin').val(params['child'] ? params['child'] : '');

  $('#filter-form').submit((event) => {
    event.preventDefault();
    params['rt1'] = $('#rt1').is(":checked");
    params['rt2'] = $('#rt2').is(":checked");
    params['rt3'] = $('#rt3').is(":checked");
    params['rt4'] = $('#rt4').is(":checked");
    params['rt5'] = $('#rt5').is(":checked");
    params['verified'] = $('#verified').val();
    params['child'] = $('#child_asin').val();
    params['page'] = 1;
    window.location = window.location.pathname + '?' + jQuery.param(params);
  });

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
