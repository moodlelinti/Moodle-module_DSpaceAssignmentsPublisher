<?php
global $CFG;
require_once("lib.php");
require_once($CFG->libdir.'/weblib.php');

class RetrieveCollections{
	private function sec_print($s) {
 		return htmlspecialchars(strip_tags($s), ENT_QUOTES);
	}
  private function sec_print_array($arr){
		foreach ($arr as &$act) {
			if(!is_array($act)){
		  	$act = sec_print($act);
			}
			else{ sec_print_array($act);}	
		}
			return $arr;
		}
	private function remoteFileExists($url) {
    error_log("URL en cuestion ".$url);
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
    return $ret;
	}
/*funcion por si se vuelve a usar campo de texto para la URL*/
/*function isValidURL($url){
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
}*/
	//funcion a rearmar, por ahi que sea publica?
	private function get_URL(){
   	global $CFG;
		if(isset($CFG->sword_select_repo)){
			$opt=$CFG->sword_select_repo;
		}
		else{
			$opt = 0; //toma el valor de produccion si no esta seteado.	
		}
		if($opt=='0'){ return "repositorio.info.unlp.edu.ar";}
		if($opt=='1'){return "dspace-dev.linti.unlp.edu.ar";}
		return "urlinvalida";	
	}
	private function get_URLHeader(){
		global $CFG;
		if(isset($CFG->sword_select_repo)){
			$opt=$CFG->sword_select_repo;
		}
		else{
			$opt = 0; //toma el valor de produccion si no esta seteado.	
		}
		if($opt==0){return "https://";}
		else return "http://";
	}

	public function getCollections(){
	if(get_URL()!="urlinvalida"){
		$url = new moodle_url(get_URLHeader().get_URL() . '/rest/collections/');
		error_log(get_URLHeader().get_URL() . '/rest/collections/');
		$url->remove_all_params();
		if (substr($url->get_path(true), -18,18) == '/rest/collections/') {
					if (remoteFileExists($url)) {
					  $ch = curl_init($url);
					  curl_setopt($ch, CURLOPT_HEADER, 0);
					  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //no usa salida estandar
					  $output = curl_exec($ch);
					  curl_close($ch);
					  $ret = json_encode(sec_print_array(json_decode($output,true)));					 
					  if ($ret != null ) {       
					  	return $ret;       
						}
					}
			}
		}
		return false;
	}
	public function hasCollections($URL){
		$url = new moodle_url($URL. '/rest/collections/');
		error_log($URL. '/rest/collections/');
		$url->remove_all_params();
		if (substr($url->get_path(true), -18,18) == '/rest/collections/') {
					if ($this->remoteFileExists($url)) {
					  return true;
					}
			}
		return false;
	}
}


