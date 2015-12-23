<?php
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/weblib.php');

function sec_print($s) {
 return htmlspecialchars(strip_tags($s), ENT_QUOTES);
}

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
/*funcion por si se vuelve a usar campo de texto para la URL*/
function isValidURL($url){
	$slicedURL= parse_url($url);
	foreach($slicedURL as $k=> $v){
		error_log($k. "     ".$v);
	}
	if(!isset($slicedURL["host"])){
		if($slicedURL["path"]=="dspace-dev.linti.unlp.edu.ar"){
			return true;
		}
	}
	else{
		if(($slicedURL["host"]=="dspace-dev.linti.unlp.edu.ar")&&(!isset($slicedURL["path"]))){
			return true;
		}
		else{
				if(isset($slicedURL["path"])){
					if($slicedURL["path"]=="/"){return true;}
				}
		}
	}

	if(!isset($slicedURL["host"])){
		if($slicedURL["path"]=="repositorio.info.unlp.edu.ar"){
			return true;
		}
	}
	else{
		if(($slicedURL["host"]=="repositorio.info.unlp.edu.ar")&&(!isset($slicedURL["path"]))){
			return true;
		}
		else{
				if($slicedURL["path"]=="/"){return true;}
		}
	}
	error_log("URL INVALIDA");
	return false;
}
function get_URL($url){
	if($url=='1'){return "repositorio.info.unlp.edu.ar";}
	if($url=='2'){return "dspace-dev.linti.unlp.edu.ar";}
	return "urlinvalida";	
}
try {
if(isloggedin()){
	// Moodle_URL valida la dirección y extrae las partes
	error_log("grande duilio". $_POST["url"]);
	if(get_URL($_POST["url"])!="urlinvalida"){
		$url = new moodle_url(get_URL($_POST["url"]) . '/rest/collections/');
		error_log("mi url es:".$url);
		/*$pad='';
		foreach ($_POST as $key => $value){		      
			error_log ($pad . "$key => $value");  
				 } */
		$url->remove_all_params();
		//Valido que la dirección termine con /rest/collections
		if (substr($url->get_path(true), -18,18) == '/rest/collections/') {
			//if (remoteFileExists($url->get_path(true))) {
					if (remoteFileExists($url)) {
					 error_log("verifique que la URL existe");  
					 //Si la URL existe hago la petición
					 $ch = curl_init($url);
					 //remplaze para que se hiciera la peticion con la URL entera				 
					 curl_setopt($ch, CURLOPT_HEADER, 0);
					 $output = curl_exec($ch);
					 curl_close($ch);

					 //Decodifico y me aseguro que sea un JSON válido	
					 $ret = sec_print(json_encode($output));     
					 if ($ret != null ) {       
							 return $ret;       
						} 
					}
			}
		}
		return false;
}	} catch (Exception $e) {
	 return false;

}

