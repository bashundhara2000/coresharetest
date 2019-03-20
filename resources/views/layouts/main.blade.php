<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>CoreShare</title>

    <meta name="description" content="A sample web application for google docs sharing">
    <meta name="author" content="Asquare">

    <!-- Google Fonts -->




  <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <!--link href="{{ asset('assets/lib/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"-->



  <!-- Libraries CSS Files -->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
  <link href="{{ asset('assets/lib/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/lib/animate/animate.min.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">



  <!-- Main Stylesheet File -->

  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/contextmenu.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.1.1/css/mdb.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries>
    <[if lt IE 9]>
    <![endif]-->

    @yield('head')

</head>

<body>

@include('partials.above-navbar-alert')
<header id="header1" class="">
    <!--Navbar-->
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
                    @if(Auth::check())
      <li class="nav-item visible-xs pull-right">
                        <a class="nav-link" href="{{ url('contactus') }}" id="contactphone"><i class="ion-ios-telephone-outline"></i></a>
      </li>
      <li class="dropdown visible-xs pull-right" onclick="messageNotification()">
                        <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false" id="sharenotify1"><i class="far fa-bell"></i><span class="badge">5</span></a>
                        <ul class="dropdown-menu">
                        <li class="notification_header_mobile">
                        </li>
                        <li id="sharemessages1">
                        </li>
                        <li>
                           <div class="col-xs-12 notification_bottom">
                              <a href="{{ url('sharemessage')  }}">See all notifications</a>
                           </div>
                         </li>
                        </ul>
          </li>
        @endif
          <a href="{{ route('public.home')  }}"><img src="{{ url('assets/images/Picture4.png')  }}" class="img img-responsive"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
                    @if(!Auth::check())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.home')  }}"><i class="fa fa-home"></i> Home</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.home')  }}"><i class="fa fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.home')  }}"> My Storage</a>
                    </li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="{{ route('user.home')  }}">Storage Control<span class="caret"></span></a>
		     <ul class="dropdown-menu">
		     <li><a href="" data-toggle="modal" data-target="#addstorage" data-backdrop="static" data-keyboard="false">Add Storage</a></li>
		     <li><a href="{{ url('existingstorage') }}">Existing Storage</a></li>
		     </ul>
		     </li>
                    @endif
      </ul>
      <ul class="nav navbar-nav navbar-right">
                    @if(!Auth::check())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('login') }}"><i class="fa fa-sign-in"></i> Login</a>
                    </li>
                    @else
                    <!--a href="#" class="btn btn-info btn-lg pull-right" id="uploadBtn" data-toggle="modal" data-target="#upload">
                       <span class="glyphicon glyphicon-cloud-upload"></span> Upload
                    </a-->
                    <li class="nav-item visible-sm visible-md visible-lg">
                        <a class="nav-link" href="{{ url('contactus') }}" id="contactphone"><i class="ion-ios-telephone-outline"></i></a>
                    </li>
                    <li class="dropdown hidden-xs" onclick="messageNotification()">
                        <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false" id="sharenotify"></a>
                        <ul class="dropdown-menu">
                        <li>
			 <div class="notification_header">
			 </div>
			</li>
                        <li id="sharemessages">
                        </li>
                        <li>
			   <div class="col-xs-12 notification_bottom">
			      <a href="{{ url('sharemessage')  }}">See all notifications</a>
			   </div> 
		         </li>
		        </ul>
                    </li>
                    <li class="dropdown"><a id="usertoggle" class="dropdown-toggle" data-toggle="dropdown" href="{{ route('user.home')  }}"><i class="fa fa-user"></i> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}<span class="caret"></span></a>
		     <ul class="dropdown-menu">
                     <li>
                        <a href="{{ url('settings')  }}"><i class="fa fa-user"></i> User Profile</a>
                     </li>
		     <li><a id="logout" href="{{ url('logout') }}"><i class="fa fa-sign-out"></i>logout</a></li>
		     </ul>
		     </li>

                    @endif
      </ul>
    </div>
  </div>
</nav> 
    <!--/.Navbar-->
</header>
<!--/Navigation-->
<!-- Modal when adding comment for upload -->
@if(Auth::check())

 <!--general  Modal -->
  <div class="modal fade" id="addstorage" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Storage</h4>
        </div>
        <div class="modal-body">
         <div class="form-group">
          <!--label for="storagename">Storage Name:</label-->
          <input type="text" class="form-control" id="storagename" value="" placeholder=" Enter a storage name" required>
         </div>
        <div class="form-group" id="addstoragediv">
         <label for="storagetype">Storage Type:</label>
         <!--select class="form-control" id="storagetype">
           <option>Google Drive</option>
           <option>Drop Box</option>
           <option>One Drive</option>
           <option>Box</option>
           <option>Amazon</option>
         </select-->
        </div>
         <label class="checkcontainer">
          <input type="checkbox" name="preferred" id="isPreferred"> Is Preferred Storage</input>
          <span class="checkmark"></span>
         </label>
        <button type="button" class="btn btn-primary btn3d" id="addstoragename" onclick="addStorage()">ADD STORAGE</button>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn3d" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>


  <div class="modal fade" id="passphraseModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Enter the coreshare password for {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
        </div>
          <form role="form" id="passphraseform">
             {{ csrf_field() }}
	    <div class="modal-body">
	      <p><input type='password' name="passphrase" data-rule-validatePassword="true" id ="passphrase" class="form-control custom-control" placeholder="password" minlength="1"></input></p>
	    </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary btn3d" onclick="setPassword(event);" id="passsubmit">Submit</button>
            </div>
	  </form>
      </div>
    </div>
  </div>
@endif
<!-- Modal when adding comment for upload -->
  <div class="modal fade" id="registerPassphraseModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Password Creation</h4>
        </div>
            <div class="modal-body">
          <div class="alert alert-info col-xs-12">
           <strong>INFO!</strong> CORESHARE REQUIRES A PASSWORD TO MAINTAIN SECRACY. ENTER THE CORESHARE PASSWORD AND REMEMBER IT FOR YOUR FUTURE USE!
          </div>
          <form role="form" id="registerpassphraseform">
             {{ csrf_field() }}
	      <div class="form-group">
	      <input type="password" name="newpassphrase" class="form-control" id="newpassphrase" placeholder="Create a Password" minlength="6" required>
	      </div>
	      <div class="form-group">
	      <input type="password" class="form-control" name="confirmpassphrase" data-rule-checksamepassword="true" id="confirmpassphrase" minlength="6" placeholder="Confirm a Password" required>
	      </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" onclick="registerPassword(event)" >Submit</button>
              <button type="button" data-toggle="modal" data-target="#exitcoreshare" class="btn btn-danger">Exit</button>
            </div>
          </form>
      </div>
    </div>
  </div>


 <!-- Modal -->
  <div class="modal fade" id="exitcoreshare" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        </div>
        <div class="modal-body">
@if (isset($userprovider))
   
          <p>You are logged into {{ $userprovider }} account as {{Auth::user()->first_name . " " . Auth::user()->last_name}}.</p>
          @if ( $userprovider == 'google')
          <p>To logout <a href="https://gmail.com" target="_blank">click here</p>
          @endif
           @if ( $userprovider == 'facebook')
          <p>To logout <a href="https://facebook.com" target="_blank">click here</p>
          @endif
           @if ( $userprovider == 'twitter')
          <p>To logout <a href="https://twitter.com" target="_blank">click here</p>
          @endif
           @if ( $userprovider == 'github')
          <p>To logout <a href="https://github.com" target="_blank">click here</p>
          @endif
        @if (!$userprovider)
         <p>You are logged into {{Auth::user()->first_name . " " . Auth::user()->last_name}}.</p>
         <p>To logout <a href="{{ url('logout') }}" target="_blank">click here</p>
        @endif
       @endif

        </div>
        <div class="modal-footer">
           <a type="button" href="{{ url('logout') }}" class="btn btn-default">Logout</a>
        </div>
      </div>
      
    </div>
  </div>


<!-- Share Modal -->
<div id="share" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4>Share files</h4>
      </div>
      <div class="alert alert-success successMessage" style="display:none">
        <strong>Success!</strong> Shared Successfully.
      </div>
      <div class="modal-body" id="shareModal">
      <form enctype="" method="post" name="shareinfo">
    {{ csrf_field() }}
       <div class="form-group">
      </div>
       <div class="form-group">
        <input type="hidden" name="url" class="form-control" id="url">
        <input type="hidden" name="fileId" class="form-control" id="fileId">
        <input type="hidden" name="storage" class="form-control" id="storage">
        <input type="hidden" name="fileName" class="form-control" id="fileName">
        <label for="pwd">Share to</label>
        <input type="text" name="email" class="form-control ui-autocomplete-input" autocomplete="off" id="shareto">
        <input name="userId" type="hidden" id="shareUserId">
      </div>
       <div class="form-group">
         <input type="submit" class="btn btn-sm btn-info" value="Share" />
	 <i id="sharespinner" class="fa fa-refresh fa-spin" style="font-size:24px"></i>
      </div>
      </form>

      <div></div>


      </div>
      <div class="modal-footer">
        <button type="button" onclick="redirectPageTo()"  class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



<div class="modal fade" id="showmessage" tabindex="-1" role="dialog" aria-labelledby="showmessage" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="alert alert-warning">
             <a href="#" class="close" data-dismiss="modal" aria-label="close" title="close">×</a>
             <strong>Warning!</strong> Prefered Storage can't delete
        </div>
      </div>
    </div>
  </div>
</div>


<!--Upload Modal -->
<div id="upload" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" onclick="myFunction()" class="close" data-dismiss="modal">&times;</button>
          <h4>Select files from your computer</h4>
        <div class="alert alert-success uploadsuccessMessage" style="display:none">
          <strong>Success!</strong>Uploaded Successfully.
        </div>
      </div>

      <div class="modal-body" id="uploadModal">
      <form enctype="multipart/form-data" method="post" name="fileinfo">
    {{ csrf_field() }}
    <div class="fileinput fileinput-new" data-provides="fileinput">
     <label>File to stash:</label>
     <span class="btn btn-primary btn-md btn-file"><input type="file" id="file" name="file" required/></span>
   </div>
    <div class="form-group">
      <label>Choose an Encryption Algorithm to encrypt the file (AES-128 is the default):</label>
      <select id = "Encryption" class="form-control" name="Encryption">
        <option value="AES-128">AES-128</option>
        <option value="AES-192">AES-192</option>
        <option value="AES-256">AES-256</option>
        <!--option value="IDEA">IDEA</option-->
        <option value="Blowfish">Blowfish</option>
      </select>
    </div>
    <div class="form-group" id="uploadstoragediv">
      <label>Choose the storage :</label>
      <!--select id="uploadstorage" class="form-control" name="uploadstorage">
        <option value="googledrive">GOOGLE DRIVE</option>
        <option value="dropbox">DROP BOX</option>
        <option value="box">BOX</option>
        <option value="onedrive">ONE DRIVE</option>
      </select-->
    </div>

      <input type="submit" class="btn btn-primary btn-md" value="Upload" />
      <i id="uploadspinner" class="fa fa-refresh fa-spin" style="font-size:24px"></i> 
      </form>

      
      <div></div>



      </div>
      <div class="modal-footer">
        <button type="button" onclick="redirectPageTo()" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<section id="spinner">
    <div id="overlay"> 
        <i class="fa fa-spinner fa-spin spin-big"></i>
    </div>
</section>


<main id="main-grid">
<div class="container-fluid">

    @yield('content')

</div> <!-- /container -->
</main>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script-->


 <!-- JavaScript Libraries -->

<script src="/assets/lib/jquery/jquery.min.js"></script>

  <script src="/assets/lib/jquery/jquery-migrate.min.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.1.1/js/mdb.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="/assets/js/ie10-viewport-bug-workaround.js"></script>
<script type="text/javascript" src="/assets/js/sjcl.js"></script>
<script type="text/javascript" src="/assets/js/core/sha256.js"></script>
<script type="text/javascript" src="/assets/js/core/bn.js"></script>
<script type="text/javascript" src="/assets/js/core/cbc.js"></script>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/fileupload.js"></script>
<script src="/assets/js/sharefile.js"></script-->

  <script src="/assets/lib/easing/easing.min.js"></script>

  <script src="/assets/lib/superfish/hoverIntent.js"></script>

  <script src="/assets/lib/superfish/superfish.min.js"></script>

  <script src="/assets/lib/wow/wow.min.js"></script>

  <script src="/assets/lib/waypoints/waypoints.min.js"></script>

  <script src="/assets/lib/counterup/counterup.min.js"></script>

  <script src="/assets/lib/owlcarousel/owl.carousel.min.js"></script>

  <script src="/assets/lib/isotope/isotope.pkgd.min.js"></script>

  <script src="/assets/lib/lightbox/js/lightbox.min.js"></script>

  <script src="/assets/lib/touchSwipe/jquery.touchSwipe.min.js"></script>

  <!-- Contact Form JavaScript File -->

  



  <!-- Template Main Javascript File -->
  <script src="/assets/js/theme.js"></script>



@yield('footer')

<footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6 footer-info">
            <h3>CoreShare</h3>
            <p>Coreshare uses the industry best cryptographic algorithms to encrypt user files. The keys used to encrypt user files that are uploaded to the cloud storage are not known to both Coreshare and the cloud storage provider, hence offering full security to user data. </p>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Useful Links</h4>
            <ul>
              <li><i class="ion-ios-arrow-right"></i> <a href="#">Home</a></li>
              <li><i class="ion-ios-arrow-right"></i> <a href="#">About us</a></li>
              <li><i class="ion-ios-arrow-right"></i> <a href="#">Services</a></li>
              <li><i class="ion-ios-arrow-right"></i> <a href="#">Terms of service</a></li>
              <li><i class="ion-ios-arrow-right"></i> <a href="#">Privacy policy</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-contact">
            <h4>Contact Us</h4>
            <p>
              CoreShare, <br>                            
              Coracias LLC, Philadelphia, US.  <br>
            </p>
            <div class="social-links">
              <a href="#" class="twitter"><i class="fa fa-twitter"></i></a>
              <a href="#" class="facebook"><i class="fa fa-facebook"></i></a>
              <a href="#" class="instagram"><i class="fa fa-instagram"></i></a>
              <a href="#" class="google-plus"><i class="fa fa-google-plus"></i></a>
              <a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a>
            </div>

          </div>

          <div class="col-lg-3 col-md-6 footer-newsletter">
            <h4>Our Newsletter</h4>
            <p>Coreshare presents its first unofficial demo on 23rd June 2018.</p>
          </div>

        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
        © Copyright <strong>CoreShare</strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!--
          All the links in the footer should remain intact.
          You can delete the links only if you purchased the pro version.
          Licensing information: https://bootstrapmade.com/license/
          Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=BizPage
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        -->
      </div>
    </div>
  </footer>
</body>
</html>
