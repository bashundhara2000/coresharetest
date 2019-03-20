@extends('layouts.main')

@section('content')

@include('partials.status-panel')
 <div class="settingspagecontainer">
    <div class="col-xs-12">
        <div class="col-xs-12 col-sm-12">
            <!-- Tab panes -->
                <div>
             <div class="col-xs-12 col-sm-12">
                <div class="panel panel-default">
                 <div class="well">
                    <h4 class="col-xs-6"> Storage: </h4>
                   <span class="col-xs-6">
                    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addstorage">ADD <span class="badge">+</span></button></span>
                 </div>
               <div class="panel-body" id="addstorages">
               </div>
             </div>
               <div class="panel panel-default">
               <div class="panel-heading">
               <h4>Share Messages:</h4>
               </div>
                <div id="sharePanel" class="panel-body">
                 <div class="col-xs-12">
                   <span>coreshare want to share</span>
                   <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">ACCEPT <span class="badge">+</span></button>
                   <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">IGNORE <span class="badge">-</span></button>
                 </div>
                 </div>
                 </div>

               </div>
            </div>
       </div>
   </div>
 </div>

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
