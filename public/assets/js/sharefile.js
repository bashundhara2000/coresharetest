$(document).on("click", ".fa-share-alt", function () {

     document.querySelector('#sharespinner').style.display='none';
     var fileId = $(this).data('id');
     var fileName = $(this).data('filename');
     var storage = $(this).data('storage');
     var url = $(this).data('href');
     console.log(fileName);
     $(".modal-body #url").val( url );
     $(".modal-body #fileId").val( fileId );
     $(".modal-body #fileName").val( fileName );
     $(".modal-body #storage").val( storage );
     // As pointed out in comments, 
     // it is superfluous to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});

var shareForm = document.forms.namedItem("shareinfo");
//shareForm.addEventListener('submit', function(ev) {
submitShareForm=function(ev){
	//start spinner 
     document.querySelector('#sharespinner').style.display='';
  var oOutput = document.querySelector("#shareModal"),
      oData = new FormData(shareForm);
      console.log(oData);

         $.get(oData.get('url'), function(data) {

                var plainText=data;
                try{ 

                var content = JSON.parse(data);
                console.log("content"+content);
                var cipher = content.cipher;
                console.log("cipher"+cipher);
                var cipherTextObj = object2SJCL(cipher);
                console.log("cipherTextObj"+cipherTextObj);

		//validate cipher for level
		if(cipherTextObj.level>1){

			// throw warning
			
		  oOutput.innerHTML = " This is a shared file. If you want to share it again download and re-upload it using Coreshare. Do you want Coreshare to do it?.<br \/>";

			return;
		}


 
      getUserPKI(oData.get('userId'),function(pkObj){
		console.log("pkObj"+pkObj);
			
		  var rekey_Keyobj = PRE.ReKeyGen(getCurrentUserSecretKey(), getCurrentUserPublicKey(), pkObj);
		console.dir("rekey_obj"+rekey_Keyobj);
		  rekey_Keyobj.RKi_j = rekey_Keyobj.RKi_j.toString();
		  rekey_Keyobj.V = rekey_Keyobj.V.toString();
		  rekey_Keyobj.W = rekey_Keyobj.W.toString();

		  var oReq = new XMLHttpRequest();
		  oReq.open("POST", "sharefile", true);
		  oReq.onload = function(oEvent) {

			//stop spinner
     			document.querySelector('#sharespinner').style.display='none';
		  if (oReq.status == 200) {
			//show success message
			oOutput.style.display='none';
			document.querySelector('.successMessage').style.display='block';
		  //var innerHtml = oOutput.innerHTML;
		  //oOutput.innerHTML = "Shared Successfully!";
		  setTimeout(function(){
				//oOutput.innerHTML=innerHtml;//reset form data here
				//bind event listeners
    				//bindAutoComplete(); 
				//shareForm.addEventListener('submit',submitShareForm,true);
				document.querySelector("#share").querySelector('.close').click();
				oOutput.style.display='block';
				document.querySelector('.successMessage').style.display='none';
				},3000); 
		  } else {
		  oOutput.innerHTML = "Error " + oReq.status + " occurred when trying to share your file.<br \/>";
		  }
		  };

      		  oData.append("reKey", JSON.stringify(rekey_Keyobj));
		  console.log(oData);
		  oReq.send(oData);
    });

		}catch(err){
			console.log("File is not encrypted by coreshare "+err);
		}


	});

  ev.preventDefault();
}
//, false);
shareForm.addEventListener('submit', submitShareForm,true);

shareToUser = function(){




}
