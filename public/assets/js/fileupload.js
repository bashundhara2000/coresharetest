//if (!("TextDecoder" in window))
//	alert("Sorry, this browser does not support TextDecoder...");
document.querySelector('#uploadspinner').style.display='none';
uploadForm.addEventListener('submit', function(ev) {
console.log("Going to upload file 1");
  readBlob();
  //fileTest()
  ev.preventDefault();
  return;
	/*
  var oOutput = document.querySelector("#uploadModal"),
      oData = new FormData(uploadForm);

  oData.append("CustomField", "This is some extra data");

  var oReq = new XMLHttpRequest();
  oReq.open("POST", "uploadfile", true);
  oReq.onload = function(oEvent) {
    if (oReq.status == 200) {
      oOutput.innerHTML = "Uploaded!";
    } else {
      oOutput.innerHTML = "Error " + oReq.status + " occurred when trying to upload your file.<br \/>";
    }
  };

  oReq.send(oData);
  */
  ev.preventDefault();
}, false);

function selectEncFunction()
{
  var x = document.getElementById('Encryption');
  alert(x.options[x.selectedIndex].value);
}
function selectUploadStorage()
{
  var y = document.getElementById('uploadstorage');
  alert(y.options[y.selectedIndex].value);
}
function redirectPageTo()
{
  window.location.href = '/user';
}
$(document).ready(function(){
    $("#usertoggle").click(function(){
        $(".usershow").slideToggle('slow');
    });
});


$(function(){
  var hash = window.location.hash;
  hash && $('ul.nav a[href="' + hash + '"]').tab('show');

  $('.nav-tabs a').click(function (e) {
    $(this).tab('show');
    var scrollmem = $('body').scrollTop() || $('html').scrollTop();
    window.location.hash = this.hash;
    $('html,body').scrollTop(scrollmem);
  });
});

function addgroupname() {
    $('.addgroup').empty();
    var nameValue = document.getElementById("groupname").value;
    var btn = document.createElement("BUTTON");
    var t = document.createTextNode(nameValue);
    btn.appendChild(t);
    $('.addgroup').append(btn);
}

function readBlob(opt_startByte, opt_stopByte) {
	
    var files = document.getElementById('file').files;
    if (!files.length) {
      console.log('Please select a file!');
      return;
    }
   document.querySelector('#uploadspinner').style.display='';
    var file = files[0];
    var start = parseInt(opt_startByte) || 0;
    var stop = parseInt(opt_stopByte) || file.size - 1;
	console.log(file);
    var reader = new FileReader();

    // If we use onloadend, we need to check the readyState.
    reader.onloadend = function(evt) {
      if (evt.target.readyState == FileReader.DONE) { // DONE == 2
	console.log("File reader completed");
	var hexString = evt.target.result.hexEncode();
	console.log(hexString);
			//var bytes = new Uint8Array(evt.target.result);
			//var bits = toBitArrayCodec(bytes);
			//var base64bits = sjcl.codec.base64.fromBits(bits); // added
	encryptAndUpload(evt.target.result,file.name,file.type);
	//call the encrypt and upload function here
        /*document.getElementById('byte_content').textContent = evt.target.result;
        document.getElementById('byte_range').textContent = 
            ['Read bytes: ', start + 1, ' - ', stop + 1,
             ' of ', file.size, ' byte file'].join('');
      */
      }
    };

    var blob = file.slice(start, stop + 1);
    //reader.readAsBinaryString(blob);
	reader.readAsDataURL(blob);
  }

function fileTest(){
    var files = document.getElementById('file').files;
    if (!files.length) {
      console.log('Please select a file!');
      return;
    }
    var file = files[0];

	//var file = new Blob(['hello world']); // your file
	var fr = new FileReader();
	fr.addEventListener('load', function () {
			console.log(fr.result);
			var bytes = new Uint8Array(fr.result);
			var bits = toBitArrayCodec(bytes);
			var base64bits = sjcl.codec.base64.fromBits(bits); // added
			encryptAndUpload(base64bits,file.name,file.type);
			});
	fr.readAsArrayBuffer(file);

}


/*
var bytes = new Uint8Array(reader.result);
var bits = toBitArrayCodec(bytes);
var base64bits = sjcl.codec.base64.fromBits(bits); // added
var crypt = sjcl.encrypt("aaaaa", base64bits);

var base64decrypt = sjcl.decrypt("aaaaa", crypt);
var decrypt = sjcl.codec.base64.toBits(base64decrypt); // added
var byteNumbers = fromBitArrayCodec(decrypt);
var byteArray = new Uint8Array(byteNumbers);

*/
