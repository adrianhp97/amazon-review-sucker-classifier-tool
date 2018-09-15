@extends('layouts.base')

@section('content')
<div class="content-wrapper">
  <div class="content-body display-center">
    {{ Form::open(array('route' => array('review.scrape'), 'method' => 'post', 'id' => 'scrape-form', 'class' => 'col')) }}
      <div class="form-row">
        <div class="form-group col-md-12">
          {{ Form::label('asin', 'Asin') }}
          {{ Form::text('asin', '', array('class' => 'form-control')) }}
          @if ($errors->has('asin'))
            <span class="text-danger">{{ $errors->first('asin') }}</span>
          @endif
        </div>
      </div>
      {{ Form::submit('Submit', array('class' => 'btn btn-outline-primary')) }}
    {{ Form::close() }}
  </div>
</div>
@endsection
