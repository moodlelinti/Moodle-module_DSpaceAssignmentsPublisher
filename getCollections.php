<?php
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/weblib.php');
function remoteFileExists($url) {
    //error_log("URL en cuestion ".$url);
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);


    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200) {
            $ret = true;   
        }
    }

    curl_close($curl);
    /*if($ret){error_log("BIENN!! hay un archivo para la URL");}
    else{error_log("No hay ningun archivo para la URl");}
    */
		return $ret;
}
try {

// Moodle_URL valida la dirección y extrae las partes

$url = new moodle_url($_POST["url"] . '/rest/collections/');
//error_log("mi url es:".$_POST["url"]);
/*$pad='';
 foreach ($_POST as $key => $value){
        error_log ($pad . "$key => $value");  
    } */
$url->remove_all_params();
//Valido que la dirección termine con /rest/collections
if (substr($url->get_path(true), -18,18) == '/rest/collections/') {
  //error_log("llegue a que la url termina en rest collections");
  //if (remoteFileExists($url->get_path(true))) {
    if (remoteFileExists($url)) {
     /*ACA SE ROMPIA, cambie la llamada del if
	http://dspace-dev.linti.unlp.edu.ar/rest/collections*/
     //error_log("verifique que la URL existe");  
     //Si la URL existe hago la petición
     //$ch = curl_init($url->get_path(true));
     $ch = curl_init($url);
     //remplaze para que se hiciera la peticion con la URL entera
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $output = curl_exec($ch);
     curl_close($ch);

     //Decodifico y me aseguro que sea un JSON válido
     $ret = json_encode($output);     
     if ($ret != null ) {       
       return $ret;       
     } 
  }
}
return false;
} catch (Exception $e) {
 return false;
}

