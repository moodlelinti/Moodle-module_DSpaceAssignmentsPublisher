function correctURL(val){
alert(val.val());
 if(val.slice(-1)=="/"){
		val.val(url.substring(0,url.length-1));
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

function actualizar(){
	//comboboxcontainer
	 containerCombo = $("#admin-sword_select_repo");
	//combobox
	combo = $("#id_s__sword_select_repo");
	//produccion text container
	containerProd = $("#admin-sword_prod_url");
	//produccion text 
	text = $("#id_s__sword_repo_url");	
	//desarrollo text container
	containerDev = $("#admin-sword_dev_url");
	//dev text 
	text = $("#id_s__sword_dev_url");
		

	if(combo.val()==0){
		containerDev.hide();
		containerProd.show();
	}
	if(combo.val()==1){
		containerProd.hide();
		containerDev.show();
	}
}

$(document).ready( function() {

	actualizar();
	combo = $("#id_s__sword_select_repo");
	combo.change(actualizar);
	text = $("#id_s__sword_prod_url");
	text.change(function(){
		text = $("#id_s__sword_prod_url").val();
	 	if(text.slice(-1)=="/"){
			alert("recorto");
			$("#id_s__sword_prod_url").val(text.substring(0,text.length-1));
 		}
	}	
	);
	text2 = $("#id_s__sword_dev_url");	
	text2.change(function(){
		text = $("#id_s__sword_dev_url").val();
	 	if(text.slice(-1)=="/"){
			$("#id_s__sword_dev_url").val(text.substring(0,text.length-1));
 		}
	}	
	);
	 }); 



