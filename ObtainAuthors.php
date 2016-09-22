<?php
class AutorFetcher{
	
	
	private $file;
	public function __construct($fn)
	{
		$this->file= $fn;
	}
	
	
	public  function getAuthors(){
		$s=$this->file->get_content();
		$s = preg_replace('/\s+/', ' ', trim($s));
		$arr = explode(';', $s);
		return array_filter($arr);
	}
	
	
}
/*error_reporting(~0);
ini_set('display_errors', 1);
$myfile = fopen("autores.txt", "r") or die("Unable to open file!");
$s=fread($myfile,filesize("autores.txt"));
$st = preg_replace('/\s+/', ' ', trim($s));
var_dump($st);
fclose($myfile);*/
/*$fn = "autores.txt";
$aux = new AutorFetcher($fn);

var_dump($aux->getAuthors());*/