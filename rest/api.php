<?php
   
/*
                    ._,=.;===- ..su.                   
                    - `      .= -)4m;_                 
                   . _<=a,.. .  .. i)Yw,               
                   .wQQWQQQmawwwgQmQQa;!               
                   :QW#QQWWWWWmQWWWQWQ6s/              
                 . -4WQQQQQQQWQQQQQWQWQjm              
                   ammWB8BWWWWBWWBWWWWWQ?              
                . -QW#Y=-:=!Y#m#!=-+3$QW<.             
                vp]WWos=.=i_aWWk_i_vwmdQQL             
                jzd$QWWmgmQW5yQQQQQmWWQQo@             
                ~_QXmWWWWAm#VWEWmz3WWWWWQ(             
                 "<]mZYXadmL,:+nyQpmWjWf               
                   ~4Wmm6ss|laaX?YnWWQW`               
                    <4VBmWWBW##WQQQQWB'                
                    .c-*V$WWQQWWQWQQQ-.                
                 _.`,]g>=|)YYS$##QWWQ/ _,              
              _.''.]Qm3Qm%===udQWQQD]m ]Qw__,          
     . , ;-`       mQQmWWmmw>W#mW#P:Qm,-$Q@(+=;___;=;;;
. - -             _QQWQWmBm###mmmS<mQQf.+=++==++|+|=<i=
                  ]QQWQP"$Wmmm##XJ9QQQ[.|= -   .---=+!+
                  mQQQF`_u"^d$VX*+ )QWL--              
                  QQ9LwmQQc<QQwmawwc3W-                
                  QWwQQWWQ6jQWWQQWWQtW,   
                               
            Joaquín Ruiz, 2013 - jokiruiz.com
*/

//  Get parameter ($_GET or $_POST)
function getParameter($par,$default=null){
	if(isset($_GET[$par]) && strlen($_GET[$par]))
		return $_GET[$par];
	elseif(isset($_POST[$par]) && strlen($_POST[$par]))
		return $_POST[$par];
	else
		return $default;
}
	
class API  {

	public $data = "";
	
	const DB_SERVER = "db_server";
	const DB_USER = "db_user";
	const DB_PASSWORD = "db_passw";
	const DB = "db_name";
	
	private $db = NULL;
	
	public function __construct(){
	}

	//  Database connection 
	private function dbConnect(){
		$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
		if($this->db)
        {
			if (!mysql_select_db(self::DB))
			{
     			die('Could not select Database: '.mysql_error());
			}
        }
        if (!$this->db)
	    {
        	die('Could not connect to Database: '.mysql_error());
        }			
	}

	//	Encode array into JSON
	private function json($data){
		if(is_array($data)){
			if (getParameter('f')) {
				switch(getParameter('f')){
					case "json":
						return json_encode($data);
						break;
					case "pjson":
						if(defined(JSON_PRETTY_PRINT))
						{	
							return json_encode($data, JSON_PRETTY_PRINT);
						}
						else
						{	
							return json_encode($data);
						}
						break;						
				}
			}
			else return $data;
		}
	}

	//	Encode image in base 64
	function base64_encode_image ($filename=string,$filetype=string) {
		if ($filename) {
			$imgbinary = fread(fopen($filename, "r"), filesize($filename));
			return base64_encode($imgbinary);
		}
	}

	//	Initialite array.
	function createArray () {
		$fields = array();
		$fields["version"] = 1.0;
		$fields["author"] = "Joaquin Ruiz";
		return $fields;
	}

	// Public method for access api.
	public function processApi(){
		$words = explode("/",$_REQUEST['rquest']);
		$func = strtolower(trim(str_replace("/","",$words[0])));
		
 		if(strcmp("services",$func) == 0)
			$this->$func($words[1],$words[2]);	// you can set as many levels as you want
		else
			$this->response('',404);	// response would be "Page not found"
	}
	
	/****************** rest/services *******************/
	private function services($id1,$id2)
	{	
		$answer = $this->createArray();
		if (!isset($id1)){ 		// services description
			$answer["description"]="Generic API rest for public github";
			$answer["services"][0]["name"]="service1";
			$answer["services"][0]["description"]="description";
			$answer["services"][1]["name"]="service2";
			$answer["services"][1]["description"]="description";
		}
		else {
			$func = strtolower(trim(str_replace("/","",$id1)));
			if (method_exists($this,$func) > 0)
				$answer = $this->$func($id2);
			else
				$this->response('',404);
		}
		$this->response($answer, 200);
	}
	
	/****************** rest/services/service1 *******************/
	private function service1($id2)
	{	
		$answer = $this->createArray();
		if (!isset($id2)){		// service1 description
			$answer["services"]["name"]="service1";
			$answer["services"]["description"]="description";
			$answer["services"]["fields"][0]["name"]="text";
			$answer["services"]["fields"][1]["name"]="db";
		}
		else {
			$func = strtolower(trim(str_replace("/","",$id2)));
			if (method_exists($this,$func) > 0)
				$answer = $this->$func();
			else
				$this->response('',404);
		}
		$this->response($answer, 200);
	}
	
	/****************** rest/services/service2 *******************/
	private function service2($id2)
	{	
		$answer = $this->createArray();
		if (!isset($id2)){		// service2 description
			$answer["services"]["name"]="service2";
			$answer["services"]["description"]="description";
			$answer["services"]["fields"][0]["name"]="image";
		}
		else {
			$func = strtolower(trim(str_replace("/","",$id2)));
			if (method_exists($this,$func) > 0)
				$answer = $this->$func();
			else
				$this->response('',404);
		}
		$this->response($answer, 200);
	}

	/****************** rest/services/service1/text *******************/
	private function text()
	{	
		$answer = $this->createArray();
		if (getParameter("name")){
			$answer["name"]=getParameter("name");
			if (file_exists(getParameter("name")))
			{
				$file = file_get_contents(getParameter("name"), true);
				$answer["content"]=$file;
			}
			else $answer["err"]="file ".getParameter("name")." does not exist";
		}
		else $answer["err"]="you have to set a name";
		
		$this->response($answer, 200);
	}

	/****************** rest/services/service1/db *******************/
	private function db()
	{	
		$answer = $this->createArray();
		if (getParameter("query")){
			$answer["query"]=getParameter("query");
			$sql = mysql_query(getParameter("query"), $this->db);
			while($row = mysql_fetch_array($sql))
			{
				$answer[]["result1"]=$row["result1"];
				$answer[]["result2"]=$row["result1"];
				$answer[]["result3"]=$row["result1"];
			}
		}
		else $answer["err"]="you have to set a query";
		
		$this->response($answer, 200);
	}

	/****************** rest/services/service2/image *******************/
	private function image()
	{	
		$answer = $this->createArray();
		if (getParameter("name") && getParameter("type")){
			$answer["name"]=getParameter("name");
			$answer["type"]=getParameter("type");
			if (file_exists(getParameter("name")))
			{
				$imgsrc = $this->base64_encode_image(getParameter("name"), getParameter("type"));
				$this->responseImg(base64_decode($imgsrc),200);
			}
			else $answer["err"]="image ".getParameter("name")." does not exist";
		}
		else $answer["err"]="you have to set a name and a type";
		
		$this->response($answer, 200);
	}


	public function response($data,$status)
	{
		$this->_code = ($status)?$status:200;
		$this->_content_type = "text/plain;charset=utf-8";
		$this->set_headers();
		$json = $this->json($data);
		print($json);
		exit();
	}
	public function responseImg($data,$status){
		$this->_code = ($status)?$status:200;
		$this->set_headersImg();
		echo $data;
		exit;
	}
	protected function set_headers()
	{
		header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		header("Content-Type:".$this->_content_type);
	}
	private function set_headersImg(){
		header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		header("Content-Type: image/png");
	}
	private function get_status_message(){
		$status = array(
			100 => 'Continue',  
			101 => 'Switching Protocols',  
			200 => 'OK',
			201 => 'Created',  
			202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'No Content',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
			406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported');
		return ($status[$this->_code])?$status[$this->_code]:$status[500];
	}
}
	
// Initiate Library
if( getParameter('debug') )
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	print "<pre>";
}
date_default_timezone_set('Europe/Madrid');
$api = new API;
$api->processApi();

?>