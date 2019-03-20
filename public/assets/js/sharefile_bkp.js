$(document).on("click", ".fa-share-alt", function () {
     var fileId = $(this).data('id');
     var fileName = $(this).data('filename');
     var storage = $(this).data('storage');
     console.log(fileName);
     $(".modal-body #fileId").val( fileId );
     $(".modal-body #fileName").val( fileName );
     $(".modal-body #storage").val( storage );
     // As pointed out in comments, 
     // it is superfluous to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});

var shareForm = document.forms.namedItem("shareinfo");

submitShareForm= function(ev) {

  var oOutput = document.querySelector("#shareModal"),
      oData = new FormData(shareForm);
      console.log(oData);
 
      getUserPKI(oData.get('userId'),function(pkObj){
			
		  var rekey_Keyobj = PRE.ReKeyGen(getCurrentUserSecretKey(), getCurrentUserPublicKey(), pkObj);
		  rekey_Keyobj.RKi_j = rekey_Keyobj.RKi_j.toString();
		  rekey_Keyobj.V = rekey_Keyobj.V.toString();
		  rekey_Keyobj.W = rekey_Keyobj.W.toString();

		  var oReq = new XMLHttpRequest();
		  oReq.open("POST", "sharefile", true);
		  oReq.onload = function(oEvent) {
		  if (oReq.status == 200) {
		  var innerHtml = oOutput.innerHTML;
		  oOutput.innerHTML = "Shared Successfully!";
		  setTimeout(function(){
				oOutput.innerHTML=innerHtml;//reset form data here
				//bind event listeners
    				bindAutoComplete(); 
				shareForm.addEventListener('submit',submitShareForm(),true);
				document.querySelector("#share").querySelector('.close').click(); 
				},2000);
		  //redirectPageTo();
		  } else {
		  oOutput.innerHTML = "Error " + oReq.status + " occurred when trying to share your file.<br \/>";
		  }
		  };

      		  oData.append("reKey", JSON.stringify(rekey_Keyobj));
		  console.log(oData);
		  oReq.send(oData);
    });


  //ev.preventDefault();
}
$(function(){
shareForm.addEventListener('submit',submitShareForm(event),true);
});
shareToUser = function(){




}
