@extends('layouts.base')

@section('content')
<div class="content-wrapper">
  <div class="content-title">
    <h1>Amazon Review Request</h1>
  </div>
  <div class="content-body">
    {{ Form::open(array('route' => array('review.scrape'), 'method' => 'post', 'id' => 'scrape-form')) }}
      <div class="form-row">
        <div class="form-group col-md-4">
          {{ Form::label('asin', 'Asin') }}
          {{ Form::text('asin', '', array('class' => 'form-control')) }}
        </div>
      </div>
      {{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}
  </div>
  <div class="content-footer"></div>
</div>
@endsection
