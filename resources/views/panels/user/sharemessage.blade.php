@extends('layouts.main')

@section('content')

@include('partials.status-panel')
  <section  class="section-bg section-nh wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
      <div class="container">

        <div class="section-header">
          <h3>Share Messages</h3>
        </div>


                <div id="sharePanel" class="container">
                 <div class="col-xs-12">
                   <span>coreshare want to share</span>
                   <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">ACCEPT <span class="badge">+</span></button>
                   <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">IGNORE <span class="badge">-</span></button>
                 </div>
                 </div>

   </div>
 </section>
  <!-- Group Modal -->
  <div class="modal fade" id="addgroup" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Groups</h4>
        </div>
        <div class="modal-body">
         <div class="form-group">
          <label for="groupname">Group Name:</label>
          <input type="text" class="form-control" id="groupname" value="">
         </div>
        <button type="button" class="btn btn-primary" id="addgroupname" onclick="addgroupname()">ADD</button> 
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

  <!--whitelist  Modal -->
  <div class="modal fade" id="addwhitelist" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Whitelist</h4>
        </div>
        <div class="modal-body">
         <div class="form-group">
          <label for="whitelistemail">Email:</label>
          <input type="text" class="form-control" id="whitelistemail" value="">
         </div>
        <button type="button" class="btn btn-primary" id="addwhitelist" onclick="addwhitelist()">ADD</button>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>

  <!--blocklist  Modal -->
  <div class="modal fade" id="addblacklist" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Blacklist</h4>
        </div>
        <div class="modal-body">
         <div class="form-group">
          <label for="blacklistemail">Email:</label>
          <input type="text" class="form-control" id="blacklistemail" value="">
         </div>
        <button type="button" class="btn btn-primary" id="addblocklist" onclick="addblocklist()">ADD</button>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
@stop
