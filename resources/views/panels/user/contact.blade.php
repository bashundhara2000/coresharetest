@extends('layouts.main')

@section('head')

@stop

@section('content')

 <section id="contact" class="section-bg wow fadeInUp section-nh" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container">

        <div class="section-header">
          <h3>Contact Us</h3>
          <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque</p>
        </div>

        <div class="row contact-info">

          <div class="col-xs-12 col-sm-4">
            <div class="contact-address">
              <i class="fas fa-map-marker" aria-hidden="true"></i>
              <h3>Address</h3>
              <address>Jenkintown, Montgomery County, USA</address>
            </div>
          </div>

          <div class="col-xs-12 col-sm-4">
            <div class="contact-phone">
              <i class="ion-ios-telephone-outline"></i>
              <h3>Phone Number</h3>
              <p><a href="tel:+155895548855">+91 999 999 9999</a></p>
            </div>
          </div>

          <div class="col-xs-12 col-sm-4">
            <div class="contact-email">
              <i class="ion-ios-email-outline"></i>
              <h3>Email</h3>
              <p><a href="mailto:info@example.com">coreshare@gmail.com</a></p>
            </div>
          </div>

        </div>

      </div>
    </section>

@stop
