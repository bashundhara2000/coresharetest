@extends('layouts.main')

@section('head')
    {!! HTML::style('/assets/css/reset-form.css') !!}
    {!! HTML::style('/assets/css/custom.css') !!}
@stop

@section('content')
 <section  class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container loginside">

          <div class="omb_login">
        <div class="section-header">
        <h3 class="omb_authTitle">Set New Password</h3>
        </div>

        {!! Form::open(['url' => url('/password/reset/'), 'class' => 'form-signin', 'method' => 'post' ] ) !!}

        @include('includes.errors')

        {{ csrf_field() }}

        <input type="hidden" name="token" value="{{ $token }}">


        <label for="inputEmail" class="sr-only">Email address</label>
        {!! Form::email('email', null, [
            'class'                         => 'form-control',
            'placeholder'                   => 'Email address',
            'required',
            'id'                            => 'inputEmail',
            'data-parsley-required-message' => 'Email is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-type'             => 'email',
            'autofocus'
        ]) !!}

        <label for="inputPassword" class="sr-only">Password</label>
        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'required',  'id' => 'inputPassword' ]) !!}


        <label for="inputPasswordConfirmation" class="sr-only">Password Confirmation</label>
        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Password confirmation', 'required',  'id' => 'inputPasswordConfirmation' ]) !!}


        <button class="btn btn-lg btn-primary btn-block" type="submit">Change</button>

        {!! Form::close() !!}
</div>
</section>
@stop
