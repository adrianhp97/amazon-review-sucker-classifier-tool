@extends('layouts.base')

@section('content')
<div class="content-wrapper">
  <div class="content-body display-center">
    {{ Form::open(array('route' => array('review.scrape'), 'method' => 'post', 'id' => 'scrape-form', 'class' => 'col')) }}
      <div class="form-row">
        <div class="form-group col-md-12">
            <a href="{{url('/redirect')}}" class="btn btn-primary">Login with Facebook</a>
        </div>
      </div>
    {{ Form::close() }}
  </div>
</div>
@endsection
