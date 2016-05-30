
function recuperarValores() 
{           
  entregasSeleccionadas = [];
  
  $('input[name="selectedusers"]:checked').each (function() {  
       
       entregasSeleccionadas.push($(this).val());       
      
  });
  
  return entregasSeleccionadas;
}

function enviar(course_id,assignment_id, swordid)
{
	
  submissions =recuperarValores();
  if (submissions.length>0) {
   //$("body").addClass("loading");
   user= document.getElementById("username").value;
	 password=document.getElementById("password").value;
	$.post( "sendToRepo.php",
    {id:course_id,
     assignment_id:assignment_id,
     submissions:submissions,
     swordid:swordid,
		 password:password,
		 user:user 
    },
     function(data, textStatus, jqXHR) {
           $("body").removeClass("loading");    
	  alert(data);	
	  location.reload(true);
     }
  );
  } else { 
      $.post("message.php", 
	     {
	         str:"non_selected"
	     },
	     function(data, textStatus, jqXHR) {         
	         alert(data);
              });          
  }
  
  
}
