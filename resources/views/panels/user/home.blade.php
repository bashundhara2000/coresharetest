@extends('layouts.main')

@section('head')

@stop

@section('content')
<section class="carouselsection  wow fadeInUp">
 <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
      <li data-target="#myCarousel" data-slide-to="3"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">

      <div class="item active">
        <img src="/assets/images/Security_4.jpg" alt="Los Angeles" style="width:100%;">
        <div class="carousel-caption">
        </div>
      </div>

      <div class="item">
        <img src="/assets/images/Security_2.jpg" alt="Chicago" style="width:100%;">
        <div class="carousel-caption">
        </div>
      </div>
    
      <div class="item">
        <img src="/assets/images/Security_3.jpg" alt="New York" style="width:100%;">
        <div class="carousel-caption">
        </div>
      </div>
 
      <div class="item">
        <img src="/assets/images/Security_1.jpg" alt="New York" style="width:100%;">
        <div class="carousel-caption">
        </div>
      </div>
 
    </div>

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
</section>
<section class="container storagelistsection  wow fadeInUp">
 <div class="row">
        <div class="section-header">
          <h3>Coreshare - Secure File Share</h3>
        </div>
  <div class="col-xs-12 col-sm-6">
   <p>Securely sharing your files over the cloud is easy now.
One destination to encrypt files and upload them to your preferred Cloud Storage Provider (Google Drive, Box, Dropbox OR Onedrive) and share it security to the ones you would like to share it with. Typically, key management is of paramount importance when you want to share an encrypted file to anyone. Coreshare does it for you with the patented Proxy Re-Encryption technology.
To get started, @if(!Auth::check())
<a href="/login" class=" waves-effect waves-light" style="color:#1570a6 !important">Click Here</a>
@else
<a href="" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addstorage" class=" waves-effect waves-light" style="color:#1570a6 !important">Click Here</a>
@endif   
</p>
  </div>
  <div class="col-xs-12 col-sm-6">
   <img src="/assets/images/architecture2.png" class="img img-responsive">
  </div>
 </div>
 <div class="row">
  <div class="col-xs-12 col-sm-6">
   <img src="/assets/images/architecture1.png" class="img img-responsive">
  </div>
  <div class="col-xs-12 col-sm-6 right6">
   <p>
    Encrypt your files and upload it to any of the public cloud storage providers and leave the worries of secure key transport to CORESHARE. 
   </p>
   <p>
    CORESHARE uses a patented Proxy Re-Encryption technique to share your secure file in a way that neither CORESHARE nor the Cloud Storage Provider could access your files in plain.
   </p>
   </div>
  </div>
 </div>
</section>
<section id="publicstorage" class="container section-bg wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container">

        <div class="section-header">
          <h3>Storages</h3>
          <p>Your employees want to use the best new technologies.
Let them.</p>
        </div>

        <div class="row contact-info">

          <div class="col-xs-6 col-sm-3">
           <div class="coreshare-technology-block-content">
            <img src="/assets/images/google_drive-active.png" alt="google" class="img img-responsive tech-block-icon">
           </div>
          </div>

	  <div class="col-xs-6 col-sm-3">
	   <div class="coreshare-technology-block-content">
	    <img src="/assets/images/coreshare_dropbox-active.png" alt="Dropbox" class="img img-responsive tech-block-icon">
	   </div>
          </div>

          <div class="col-xs-6 col-sm-3">
           <div class="coreshare-technology-block-content">
            <img src="/assets/images/coreshare_box-active.png" alt="box" class="img img-responsive tech-block-icon">
           </div>
          </div>
    
          <div class="col-xs-6 col-sm-3">
           <div class="coreshare-technology-block-content">
            <img src="/assets/images/coreshare_onedrive-active.png" alt="onedrive" class="img img-responsive tech-block-icon">
           </div>
          </div>

        </div>


      </div>
    </section>
@stop
