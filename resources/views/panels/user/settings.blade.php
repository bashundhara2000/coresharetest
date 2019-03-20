@extends('layouts.main')

@section('head')

@stop

@section('content')

 <section id="contact" class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container">

        <div class="section-header">
          <h3>User Profiles</h3>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
   
   
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">{{Auth::user()->first_name . " " . Auth::user()->last_name}}</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="/assets/images/user-512.png" class="img-circle img-responsive"> </div>
                
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <td>Creation date:</td>
                        <td>{{Auth::user()->created_at}}</td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><a href="mailto:info@support.com">{{Auth::user()->email}}</a></td>
                      </tr>
                      <tr>
                        <td>Backup Master Key</td>
                        <td><a href="{{ url('downloadmasterkey')  }}" type="button" class="btn btn-primary btn-xs downloadmsk">Download</a></td>
                      </tr>
                      <tr>
                        <td>Delete Account</td>
                        <td><a href="{{ url('deleteaccount')  }}" type="button" class="btn btn-primary btn-xs downloadmsk">Delete</a></td>
                      </tr>
                      <tr>
                        <td>Recovery Tool</td>
                        <td><a href="#" type="button" class="btn btn-primary btn-xs downloadmsk">Download</a></td>
                      </tr>
                     
                    </tbody>
                  </table> 

        </div>

      </div>
    </section>

@stop
