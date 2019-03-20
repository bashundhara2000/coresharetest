@extends('layouts.main')

@section('head')

@stop
@include('partials.status-panel')
@section('content')

 <section id="existstorage" class="section-bg section-nh  wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container">

        <div class="section-header">
          <h3>My Storages</h3>
          <p>Following are the storages you have linked with your Coreshare account. The files that are shared to you will be available in your preferred storage after you accept the share. </p>
        </div>

        <div class="row contact-info">
           <div class="settingspagecontainer">
    <div class="col-xs-12">
        <div class="col-xs-12 col-sm-12">
            <!-- Tab panes -->
                <div>
             <div class="col-xs-12 col-sm-12 existstoragecontain">
                <div class="" id="addstorages">
               </div>
                <div class="panel-body storageind">
               <span class="glyphicon glyphicon-star"></span><span>Prefered Storage</span>
               </div>

               </div>
            </div>
       </div>
   </div>
 </div>

        </div>

      </div>
    </section>

@stop
