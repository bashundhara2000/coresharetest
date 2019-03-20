@extends('layouts.main')

@section('head')

    <link rel="stylesheet" href="/assets/css/signin.css">
    <link rel="stylesheet" href="/assets/css/parsley.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
@stop

@section('content')
   <section  class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container loginside">

    <div class="omb_login">
        <div class="section-header">
    	<h3 class="omb_authTitle">Login or <a href="{{ url('register') }}">Sign up</a></h3>
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
        	         {!! Form::open(['url' => url('login'), 'class' => 'omb_loginForm', 'data-parsley-validate' ] ) !!}
                         @include('includes.status')
	<div class="input-group">
	 <span class="input-group-addon"><i class="fa fa-user"></i></span>
        {!! Form::email('email', null, [
            'class'                         => 'form-control',
            'placeholder'                   => 'Email address',
            'required',
            'id'                            => 'inputEmail',
            'data-parsley-type'             => 'email'
        ]) !!}
        </div>
        <div style="height:15px;"></div>
        <div class="input-group">
	 <span class="input-group-addon"><i class="fa fa-lock"></i></span>
        {!! Form::password('password', [
            'class'                         => 'form-control',
            'placeholder'                   => 'Password',
            'required',
            'id'                            => 'inputPassword',
            'data-parsley-minlength'        => '6',
            'data-parsley-maxlength'        => '20'
        ]) !!}

        </div>
        <div style="height:15px;"></div>
        <button class="btn btn-lg btn-primary btn-block login-btn" type="submit">Login</button>

    	</div>
		<div class="row col-xs-12 omb_row-sm-offset-3">
			<div class="col-xs-12 col-sm-3">
				<label class="checkbox">
                                        {!! Form::checkbox('remember', 1, null, ['id' => 'remember-me']) !!}
                                        <label for="remember-me">Remember me</label>
				</label>
			</div>
			<div class="col-xs-12 col-sm-3">
				<p class="omb_forgotPwd">
					<a href="{{ url('password/reset') }}">Forgot password?</a>
				</p>
			</div>
		</div>	    	
		</div>	    	
 		</div>	    	
 </div>
 </section>




        {!! Form::close() !!}

@stop

@section('footer')

    <script type="text/javascript">
        window.ParsleyConfig = {
            errorsWrapper: '<div></div>',
            errorTemplate: '<span class="error-text"></span>',
            classHandler: function (el) {
                return el.$element.closest('div');
            },
            successClass: 'valid',
            errorClass: 'invalid'
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.5.0/parsley.min.js"></script>

     <script src="/assets/js/theme.js"></script>
@stop
