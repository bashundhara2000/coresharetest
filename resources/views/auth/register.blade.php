@extends('layouts.main')

@section('head')
    {!! HTML::style('/assets/css/register.css') !!}
    {!! HTML::style('/assets/css/parsley.css') !!}
    {!! HTML::style('/assets/css/custom.css') !!}
@stop

@section('content')
     <section  class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container loginside">

      <div class="omb_login">
        <div class="section-header">
        <h3 class="omb_authTitle">Sign up</h3>
        </div>
                <div class="row omb_row-sm-offset-3 omb_socialButtons">
                   @include('partials.socials')
                </div>

                <div class="row omb_row-sm-offset-3 omb_loginOr">
                        <div class="col-xs-12 col-sm-8">
                                <hr class="omb_hrOr">
                                <span class="omb_spanOr">or</span>
                        </div>
                </div>

                <div class="row omb_row-sm-offset-3">
                        <div class="col-xs-12 col-sm-8">

        {!! Form::open(['url' => url('register'), 'class' => 'form-signin', 'data-parsley-validate' ] ) !!}

        @include('includes.errors')

        <div class="input-group">
         <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
        {!! Form::email('email', null, [
            'class'                         => 'form-control',
            'placeholder'                   => 'Email address',
            'required',
            'id'                            => 'inputEmail',
            'data-parsley-required-message' => 'Email is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-type'             => 'email'
        ]) !!}
        </div>
              <div style="height:15px;"></div>
       <div class="input-group">
         <span class="input-group-addon"><i class="fa fa-user"></i></span>
        {!! Form::text('first_name', null, [
            'class'                         => 'form-control',
            'placeholder'                   => 'First name',
            'required',
            'id'                            => 'inputFirstName',
            'data-parsley-required-message' => 'First Name is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-pattern'          => '/^[a-zA-Z]*$/',
            'data-parsley-minlength'        => '2',
            'data-parsley-maxlength'        => '32'
        ]) !!}
        </div>
              <div style="height:15px;"></div>

        <div class="input-group">
         <span class="input-group-addon"><i class="fa fa-user"></i></span>
        
        {!! Form::text('last_name', null, [
            'class'                         => 'form-control',
            'placeholder'                   => 'Last name',
            'required',
            'id'                            => 'inputLastName',
            'data-parsley-required-message' => 'Last Name is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-pattern'          => '/^[a-zA-Z]*$/',
            'data-parsley-minlength'        => '2',
            'data-parsley-maxlength'        => '32'
        ]) !!}
       </div>
               <div style="height:15px;"></div>

       <div class="input-group">
         <span class="input-group-addon"><i class="fa fa-key"></i></span>
       
        {!! Form::password('password', [
            'class'                         => 'form-control',
            'placeholder'                   => 'Password',
            'required',
            'id'                            => 'inputPassword',
            'data-parsley-required-message' => 'Password is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-minlength'        => '6',
            'data-parsley-maxlength'        => '20'
        ]) !!}
       </div>
              <div style="height:15px;"></div>

       <div class="input-group">
         <span class="input-group-addon"><i class="fa fa-key"></i></span>
        {!! Form::password('password_confirmation', [
            'class'                         => 'form-control',
            'placeholder'                   => 'Password confirmation',
            'required',
            'id'                            => 'inputPasswordConfirm',
            'data-parsley-required-message' => 'Password confirmation is required',
            'data-parsley-trigger'          => 'change focusout',
            'data-parsley-equalto'          => '#inputPassword',
            'data-parsley-equalto-message'  => 'Not same as Password',
        ]) !!}
         </div>
                <div style="height:15px;"></div>

        <div class="g-recaptcha" data-sitekey="{{ env('RE_CAP_SITE') }}"></div>
                <div style="height:15px;"></div>

        <button class="btn btn-lg btn-primary btn-block register-btn" type="submit">Register</button>



        {!! Form::close() !!}
 </div>
</section>

@stop

@section('footer')

    <script type="text/javascript">
        window.ParsleyConfig = {
            errorsWrapper: '<div></div>',
            errorTemplate: '<span class="error-text"></span>',
            classHandler: function (el) {
                return el.$element.closest('input');
            },
            successClass: 'valid',
            errorClass: 'invalid'
        };
    </script>

    {!! HTML::script('/assets/plugins/parsley.min.js') !!}

    <script src='https://www.google.com/recaptcha/api.js'></script>

@stop
