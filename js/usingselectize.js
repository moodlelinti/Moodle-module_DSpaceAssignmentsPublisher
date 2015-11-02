
var $select;
var selectize;
var handler = function() {alert(selectize.getValue()); };

$(document).ready( function() {
  $select= $('#id_programminglanguage').selectize({
	allowEmptyOption: true,
	create: false
	});
// selectize = $select[0].selectize;
//alert (selectize.getValue());
//selectize.on("blur",handler);
//selectize.onchange =function(){alert("hola2");}

//$('#id_submitbutton2').onclick(alert(selectize.getValue()));
//$('#id_submitbutton').onclick(alert(selectize.getValue()));		



});
