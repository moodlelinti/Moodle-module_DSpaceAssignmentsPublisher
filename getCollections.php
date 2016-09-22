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
		  	$act = $this->sec_print($act);
			}
			else{ $this->sec_print_array($act);}	
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
	private function restQuerry($url){
		//$url->remove_all_params();
		if ($this->remoteFileExists($url)) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //no usa salida estandar
			$output = curl_exec($ch);
			curl_close($ch);
			$ret =$this->sec_print_array(json_decode($output,true));					 
			return $ret;	
		}
		else{
			error_log("hubo un error ejecutando la consulta REST con la URL : ".$url);
			return -1;
		}
	}
	//funcion a rearmar, por ahi que sea publica?
	public function get_URL($aux=null){
   	global $CFG;
		$opt;
		if(!is_null($aux)){
			$opt = $aux;
		}
		else{
			if(isset($CFG->sword_select_repo)){
				$opt=$CFG->sword_select_repo;
			}
			else{
				$opt = 0; //toma el valor de produccion si no esta seteado.	
			}
		}
		if($opt=='0'){
			if(isset($CFG->sword_prod_url)){
					return $CFG->sword_prod_url;
			}
			else{ 
					error_log("La URL de produccion fue solicitada pero no tiene ningun valor");
					return "URL invalida";
			}
		}
		if($opt=='1'){
		if(isset($CFG->sword_dev_url)){
					return $CFG->sword_dev_url;
			}
			else{ 
					error_log("La URL de desarrollo fue solicitada pero no tiene ningun valor");
					return "URL invalida";
			}
		}
	}

	public function getCollections(){
	if($this->get_URL()!="urlinvalida"){
		$url = new moodle_url($this->get_URL() . '/rest/collections/');
		error_log($this->get_URL() . '/rest/collections/');
		$url->remove_all_params();
		if (substr($url->get_path(true), -18,18) == '/rest/collections/') {
					if ($this->remoteFileExists($url)) {
					  $ch = curl_init($url);
					  curl_setopt($ch, CURLOPT_HEADER, 0);
					  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //no usa salida estandar
					  $output = curl_exec($ch);
					  curl_close($ch);
					  $ret = json_encode($this->sec_print_array(json_decode($output,true)));					 
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
	private function getCollectionWithHandle($collection_handle,$url){
		$collections = $this->restQuerry($url.'/rest/collections/');
		if($collections!=-1){		
			foreach($collections as $col){
				//error_log(var_dump($col));
				if(strtolower($col['handle'])==strtolower($collection_handle)){
					return $col;		
				}
			}
		}
	return -1;
}
	
	public function collectionHasItem($collection_handle, $item){
	$url= $this->get_URL();
	$encontrado = false;
	$collection= $this->getCollectionWithHandle($collection_handle,$url);
	if( $collection!= -1){
		//collection link identifica a la coleccion.
		$colWithItems = $this->restQuerry($url.$collection['link']."?expand=items");		
		//items son los items en la coleccion actual
		$items = $colWithItems["items"];		
		//error_log(var_dump($items));


		foreach($items as $it){
			error_log("iterando por los items");
			$item_completo= $this->restQuerry($url.$it['link']."?expand=all");
			//error_log(var_dump($item_completo));
			$meta = $item_completo["metadata"];
			foreach($meta as $m){
				if($m["key"]=="dc.contributor.author")
					$autor= $m["value"];			
			if(($item_completo["name"]== $item["name"])&&($autor==$item["author"])){
					$encontrado = true;break;
			}
			}			
		}
		error_log("termine de iterar");
		return $encontrado;	
	}
		return false;
	}
}


