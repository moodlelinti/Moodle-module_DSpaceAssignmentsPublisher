function getCollectionswithURL(){
 url = $("#id_s__sword_repo_url").val();
 //$("#id_url_selector").empty();
 $.ajax({
    type: "POST",
     url: "../mod/sword/isRepository.php",
     data: {url: url},
     success: function(bool) {
				alert("exito");       
				//$("#id_s__sword_repo_url").parent().append("exito");    
  	 } ,
     
     error: function(bool) {
   			alert("error")
          
     } 
  });
}
$(document).ready( function() { 
	$("#id_s__sword_repo_url").change( function() {
			//alert($("#id_s__sword_repo_url").val());
			url= $("#id_s__sword_repo_url").val();
			getCollectionswithURL();
	 }); 
});
