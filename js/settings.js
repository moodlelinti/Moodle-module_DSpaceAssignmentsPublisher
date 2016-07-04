function correctURL(){
 url = $("#id_s__sword_repo_url").val();
 if(url.slice(-1)=="/"){
		$("#id_s__sword_repo_url").val(url.substring(0,url.length-1));
 }
}
/*function getCollectionswithURL(){
 url = $("#id_s__sword_repo_url").val();
 //$("#id_url_selector").empty();
 //chequear que la URL tenga forma de URL antes de llamar por ajax
 /*$.ajax({
    type: "POST",
     url: "../mod/sword/isRepository.php",
     data: {url: url},
     success: function(bool) {
				//alert("exito");
				$("#result").remove();       
				$("#id_s__sword_repo_url").parent().parent().append("<div id='result'><img src='../mod/sword/pix/tick.png'>   La URL corresponde a un repositorio válido</div>");    
  	 } ,
     
     error: function(bool) {
   			//alert("error")
				$("#result").remove();
        $("#id_s__sword_repo_url").parent().parent().append("<div id='result'><img src='../mod/sword/pix/x.png'>  La URL ingresada no corresponde a un repositorio válido</div>");  
     } 
  });
*/
//Cambio en vez de usar ajax solo valida que la URL sea una URL bien formada???





$(document).ready( function() { 
	$("#id_s__sword_repo_url").change( function() {
			//alert($("#id_s__sword_repo_url").val());
			url= $("#id_s__sword_repo_url").val();
			correctURL();
	 }); 
});



