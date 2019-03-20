@extends('layouts.main')

@section('head')
    {!! HTML::style('/assets/css/reset.css') !!}
@stop

@section('content')
<section  class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container loginside">

          <div class="omb_login">
        <div class="section-header">
        <h3 class="omb_authTitle">Set New Password</h3>
        </div>

        {!! Form::open(['url' => url('/password/email'), 'class' => 'form-signin' ] ) !!}

        @include('includes.status')

        <label for="inputEmail" class="sr-only">Email address</label>
        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email address', 'required', 'autofocus', 'id' => 'inputEmail' ]) !!}

        <br />
        <button class="btn btn-lg btn-primary btn-block" type="submit">Send me a reset link</button>

        {!! Form::close() !!}
</div>
</section>
@stop
