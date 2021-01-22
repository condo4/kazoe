<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: KData.php
 * Description: Class to store all application data
 *
 * @author Fabien Proriol Copyright (C) 2009.
 *
 * @see The GNU Public License (GPL)
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

/* List of all standard environment

LANG            Normal language ask by client, like 'en', 'fr'
LANG_DEF        Default language of kazoe, all pages must exist in this language
LANGS           List of supported language with description
SKIN            Use skin directory
IDPP            Path variable (IDentity Path Parameter)
BASE            Static part for IDPP
SECTION         Dynamic part for IDPP, this is dynamic sub-section
PAGEPATH        Path of the page node
USER_ID         Key identifier in the kazoe_passwd table (-1 if nobody connected)
USER_LOGIN      Login of the user ("" if nobody connected)
USER_NAME       Name and First Name of the user
USER_EMAIL      Name and First Name of the user
HISTORY			Set in previous_page.
PAGEURL			Script page name, before url_rewrite

 */




class KData {
	private $pathroot;          // Path of the root of the website
	private $database;          // PDO object for all database access
	private $environment;       // DICT Array to store all environment
	private $lastquery;         // Latest SQL query user
	private $have_error;        // Error in the page
	private $cache;             // Cache is enable
	private $rights;            // XPath allow all user rights
	private $childs;            // List of user childs
	private $headers;           // Header accessor
	private $template;          // Template accessor
	private $logger;
	private $dbinited;

	public function __construct($source = "url"){
		$this->have_error = False;
		$this->environment = array();
		$this->init_language();
		$this->init_skin();
		$this->init_environment($source);
		$this->init_cache();
		$this->init_user();
		$this->init_rights();
		$this->previous = isset($_SESSION['previous_page'])?($_SESSION['previous_page']):($this->getBasePage());
		$this->headers = null;
		$this->template = null;
		$this->logger = new Logger($this);
		$this->logger->log_hit_into_database();
		$this->dbinited = false;
	}
	
	public function getPath($name)
	{
		switch($name) 
		{
			case "docroot":
				return $_SERVER['DOCUMENT_ROOT'];
			
		}
	}
	
	
	public function addJsScript($script, $referrerpolicy=""){
		if($this->headers != null && $this->template != null)
		{
			$XmlHead_script =  $this->template->createElement("script");
			$XmlHead_script->setAttribute('src',$script);
			$XmlHead_script->setAttribute('type','text/javascript');
			if( $referrerpolicy != "" ){
				$XmlHead_script->setAttribute('referrerpolicy',$referrerpolicy);
			}
			$this->headers->appendChild($XmlHead_script);
		}
	}
	
	public function setTemplate($h){
		$this->template = $h;
	}
	
	public function setHeaders($h){
		$this->headers = $h;
	}
	
	public function quit(){
		if($this->getEnv('HISTORY')){
			$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
		}
	}

	public function getBasePage(){
		return $this->getEnv('META').'-'.$this->getEnv('LANG').'-'.$this->getEnv('IDPP').'.html';
	}

	public function getRequestPage(){
		return str_replace('/','',$_SERVER["REQUEST_URI"])."<br />";
	}

	public function reload(){
		header('Location: '.$this->previous);
	}

	public function setError(){
		$this->have_error = True;
	}

	/*
		* User managment
		*/
	private function init_user(){
		$USERID =   (isset($_SESSION["userid"]))?($_SESSION["userid"]):(-1);
		$USER =     (isset($_SESSION["user"]))?($_SESSION["user"]):("");

		if($USER != ""){
			$sql = $this->db_query(
				"SELECT name || ' ' || firstname AS name, email  FROM kazoe_users WHERE _passwd = :LOGIN",
				array(
					'LOGIN'         => $USER
				)
			);
			if (!$sql->execute()) throw new Exception($this->db_error($sql));

			$line = $sql->fetch();
			$USERNAME = $line['name'];
			$EMAIL = $line['email'];
		}
		else 
		{
			$USERNAME = "";
			$EMAIL = "";
		}
		if($USERID != -1) $this->disable_cache();
		$this->setEnv('USER_ID',$USERID);
		$this->setEnv('USER_LOGIN',$USER);
		$this->setEnv('USER_NAME',$USERNAME);
		$this->setEnv('USER_EMAIL',$EMAIL);
		
		if($USERID != -1)
		{
			$tree = array();
			$sql = $this->db_query("SELECT kazoe_passwd.id as id, kazoe_passwd._owner as parent, kazoe_passwd.login as login FROM kazoe_users JOIN kazoe_passwd ON kazoe_users._passwd = kazoe_passwd.login ORDER BY kazoe_passwd._owner DESC");
			if (!$sql->execute()) throw new Exception($this->db_error($sql));
			$keys = $sql->fetch();
			while($keys){
				$node = array('id' => $keys['id'], 'parent' => $keys['parent'], 'login' => $keys['login']);
				$tree[$keys['id']] = $node;
				$keys = $sql->fetch();
			}
			
			/* find current log user */
			$subpath = "";
			$u = $USERID;
			while($u != NULL)
			{
				$subpath = $tree[$u]['login']."/".$subpath;
				$u = $tree[$u]['parent'];
			}
			
			$this->setenv("SUBFOLDER", $this->getenv("PAGEPATH")."/__local_img__/".$subpath);
			$this->setenv("THUNBFOLDER", $this->getPath("docroot").'/kazoe/lib/tinymce/plugins/filemanager/thumbs/'.$this->getenv("PAGEPATH")."/__local_img__/".$subpath);
			
			if(!is_dir($this->getenv("SUBFOLDER")))
			{
				@mkdir($this->getenv("SUBFOLDER"),0777,true);
			}
			
			if(!is_dir($this->getenv("THUNBFOLDER")))
			{
				@mkdir($this->getenv("THUNBFOLDER"),0777,true);
			}
		}
	}

	/*
	* Environment managment
	*/
	private function init_environment($source){
		if($source != "url")
		{
			$tabs = preg_split("/-/",str_replace(".html","",substr($source,strrpos($source,"/")+1)));
			if(count($tabs) >= 3)
			{
				$_SESSION["METAPAGE"] = $source;
			}
			else
			{
				if(isset($_SESSION["METAPAGE"]))
				{
					$source = $_SESSION["METAPAGE"];
					$tabs = preg_split("/-/",str_replace(".html","",substr($source,strrpos($source,"/")+1)));
				}
			}
			if(count($tabs) >= 3)
			{
				$this->setEnv('IDPP',$tabs[2]);
				$this->setEnv('META',$tabs[0]);
			}
			else
			{
				$this->setEnv('IDPP',"");
				$this->setEnv('META',$this->getConfig("default_meta"));
			}
		}
		else
		{
			$this->setEnv('IDPP',(isset($_GET["idpp"]))?($_GET["idpp"]):(""));
			$this->setEnv('META',(isset($_GET["meta"]))?($_GET["meta"]):($this->getConfig("default_meta")));
		}
		
		$this->setEnv('HISTORY',True);
		$this->setEnv('QUERY',(isset($_REQUEST["query"]))?($_REQUEST["query"]):(""));
		
		$page = (isset($_GET["page"]))?($_GET["page"]):(0);
		if(!is_numeric($page)) $page = 0;
		$this->setEnv('PAGE',$page);
		
		$key = ((isset($_REQUEST["key"]) and ($_REQUEST["key"] != ""))?($_REQUEST["key"]):(0));
		if(!is_numeric($key)) $key = 0;
		$this->setEnv('KEY', $key);
		
		$ROOTPATH = 'root/'.$this->getEnv('META').'/';
		$REQUEST  = $this->getEnv('IDPP');
		$PAGEPATH = str_replace('//','/',$ROOTPATH.str_replace('.','/',$REQUEST));

		$olddir = getcwd();
		chdir($_SERVER['DOCUMENT_ROOT']);
		if(is_dir($PAGEPATH)){
			$BASE = $REQUEST;
			$SECTION = '';
		}
		else{
			$BASE = $REQUEST;
			$SECTION = '';
			$PAGEPATH = str_replace('//','/',$ROOTPATH.str_replace('.','/',$BASE));
			while(!is_dir($PAGEPATH)){
				$explode_idpp = preg_split('/\./',$BASE);
				if($SECTION == '') $SECTION = array_pop($explode_idpp);
				else $SECTION = array_pop($explode_idpp).'.'.$SECTION;
				$BASE = join('.',$explode_idpp);
				$PAGEPATH = str_replace('//','/',$ROOTPATH.str_replace('.','/',$BASE));
			}
		}
		
		if(is_file($PAGEPATH."/Xternal.xml"))
		{
			$arr = preg_split('/\./',$SECTION);
			$fsection = array_shift($arr);
			$SECTION = join('.',$arr);
			$externals = new DOMDocument('1.0');
			$externals->Load($PAGEPATH."/Xternal.xml");
			$xpath = new DOMXpath($externals);
			foreach($xpath->query('//node') as $node) {
				if($node->getAttribute('id') == $fsection)
				{
					$mod = $node->getAttribute('module');
					$ROOTPATH = 'kazoe/mods/'.$mod.'/';
					$REQUEST = str_replace('.','/',$SECTION);
					$PAGEPATH = str_replace('//','/',$ROOTPATH.str_replace('.','/',$REQUEST));

					$BASE = $REQUEST;
					$SECTION = '';
					break;
				}
			}
		}
		chdir($olddir);
		
		$this->setEnv('PAGEPATH',   $PAGEPATH);
		$this->setEnv('PAGEURL',    $this->getBasePage());
		$this->setEnv('BASE',       $BASE);
		$this->setEnv('SECTION',    $SECTION);		
		$this->setEnv('APPTABLE',   $this->getAppTable($this->getEnv('BASE')));
	}
	
	public function getAppTable($idpp){
		$config = new DOMDocument('1.0');
		$config->Load($_SERVER['DOCUMENT_ROOT']."/root/etc/database.xml");
		$xpath = new DOMXpath($config);
		$node = $xpath->query('/database/apptables/table[@idpp=\''.$idpp.'\']');
		if($node->length == 1){
			return $node->item(0)->nodeValue;
		}
		else return '';
	}

	private function add_uchild($user){
		$sql = $this->db_query(
			"SELECT id FROM kazoe_passwd WHERE _owner=:ID",
			array(
				'ID'         => $user
			)
		);
		if (!$sql->execute()) throw new Exception($this->db_error($sql));
		while($line = $sql->fetch()){
			array_push($this->childs, $line['id']);
			$this->add_uchild($line['id']);
		}
	}

	public function setEnv($name,$value){
		$this->environment[$name] = $value;
	}

	public function getEnv($name){
		if(array_key_exists($name,$this->environment)){
			return $this->environment[$name];
		}
		else {
			return "";
		}
	}

	public function isEnv($name){
		if(array_key_exists($name,$this->environment)){
			return True;
		}
		else {
			return False;
		}
	}

	/*
	* Database Managment
	*/
	private function init_database(){
		if($this->dbinited)
			return;
		$config = new DOMDocument('1.0');
		$config->Load($_SERVER['DOCUMENT_ROOT']."/root/etc/database.xml");
		$this->db_transation = False;
		foreach($config->firstChild->childNodes as $contents) {
			if($contents->nodeName != "config"){
				continue;
			}
			$condition = $contents->getAttribute('test');
			if($condition){
				eval('$test = ('.$condition.');');
				if(!$test) continue;
			}
			$connector = $contents->getElementsByTagName("connector")->item(0)->nodeValue;
			switch($connector){
				case 'postgresql':
					$host       = $contents->getElementsByTagName("host")->item(0)->nodeValue;
					$base       = $contents->getElementsByTagName("base")->item(0)->nodeValue;
					$login      = $contents->getElementsByTagName("login")->item(0)->nodeValue;
					$password   = $contents->getElementsByTagName("password")->item(0)->nodeValue;
					$this->database = new PDO('pgsql:host='.$host.' port=5432 dbname='.$base.' user='.$login.' password='.$password);
					break;
				default:
					throw new Exception("DB Connector ".$connector." unknown.");
			}
			break;
		}
		$this->dbinited = true;
	}

	public function db_beginTransaction(){
		$this->init_database();
		$this->db_transation = True;
		return $this->database->beginTransaction();
	}

	public function db_rollBack(){
		$this->init_database();
		if($this->db_transation){
			$this->db_transation = False;
			return $this->database->rollBack();
		}
		return 0;
	}

	public function db_commit(){
		$this->init_database();
		$this->db_transation = False;
		return $this->database->commit();
	}

	public function db_query($query,$param=array()){
		$this->init_database();
		$query = str_replace(":SCHILD","'".$this->getEnv('SCHILD')."'",$query);
		$query = str_replace(":{meta}",$this->getEnv('META'),$query);
		$query = str_replace(":{apptable}",$this->getEnv('APPTABLE'),$query);
		$query = str_replace(":{section}","'".$this->getEnv('SECTION')."'",$query);
		$this->lastquery = $query;
		$pdo_statement = $this->database->prepare($query);
		if($param===False) return $pdo_statement;
		$regex = "(:[A-Z][A-Z_]*)";
		$key_need = array();
		preg_match_all($regex ,$query,$key_need);
		foreach($key_need[0] as $key){
			if(strlen($key) > 0){
				$key = str_replace(":","",$key);
				if(array_key_exists($key,$param)){
					$pdo_statement->bindParam(':'.$key,$param[$key]);
				}
				elseif($this->isEnv($key)){
					$val = $this->getEnv($key);
					$pdo_statement->bindParam(':'.$key,$val);
				}
				elseif(array_key_exists($key,$_POST)){
					$pdo_statement->bindParam(':'.$key,$_POST[$key]);
				}
				elseif(array_key_exists(strtolower($key),$_POST)){
					$pdo_statement->bindParam(':'.$key,$_POST[strtolower($key)]);
				}
				else throw new Exception("Database prepare query, KEY '".$key."' NOT FOUND !!!");
			}
		}
		return $pdo_statement;
	}

	public function db_error($pdos){
		$this->init_database();
		$this->db_rollBack();
		$err = $pdos->errorInfo();
		return "Database error SQLSTATE(".$err[0]."/".$err[1].") ".$err[2]."\nSQL:".$this->lastquery;
	}

	/*
		* Cache Managment
		*/
	private function init_cache(){
		$PAGE_NAME = rtrim(trim($_SERVER['REQUEST_URI']),"/");
		$PAGE_OPTS = strpos($PAGE_NAME,'?');
		
		if(($PAGE_OPTS != null) || ($this->getEnv('QUERY') != null) ) $this->cache = false;
		else  $this->cache = ($this->getConfig("cache") == "enable");
	}

	public function getPageName(){
		if($this->getEnv('IDPP') == '') return $this->getEnv('META').'-'.$this->getEnv('LANG').'-__index.html';
		$uri = rtrim($_SERVER['REQUEST_URI'],"/");
		$tab = preg_split("/\//",$uri);
		$res = trim(array_pop($tab));
		return $res;
	}

	private function uncache_file($pattern){
		/* Dir loop */
		if ($skin_handle = opendir($_SERVER['DOCUMENT_ROOT'].'/var/cache')) {
			while($Entry = @readdir($skin_handle)) {
				if(is_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache/'.$Entry)&& $Entry != '.' && $Entry != '..') {
					if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/var/cache/'.$Entry)) {
						while (false !== ($file = readdir($handle))) {
							if ($file != "." && $file != "..") {
								if(preg_match($pattern,$file)) unlink($_SERVER['DOCUMENT_ROOT'].'/var/cache/'.$Entry.'/'.$file);
							}
						}
						closedir($handle);
					}
				}
			}
			closedir($skin_handle);
		}
	}

	public function uncache($pattern='auto'){
		if($pattern=='auto'){
			$pattern = '/'.str_replace('html','*',$this->getBasePage()).'/';

			$xpath = new DOMXpath($this->getEnv('PAGEROOTCONF'));
			$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
			$xpath->registerNamespace('xp','http://kazoe.org.free.fr/xsd/Xpages.xsd');
			$caches = $xpath->query('//xp:cache');
			foreach($caches as $cache)
			{
				$this->uncache_file($cache->nodeValue);
			}
		}
		if($pattern=='all'){
			$pattern = '/.*/';
		}

		$this->uncache_file($pattern);
	}

	public function disable_cache(){
		$this->cache = false;
	}

	public function getCache(){
		return $this->cache;
	}

	/*
	* Language managment
	*/
	private function init_language(){
		$config = new DOMDocument('1.0');
		$config->Load($_SERVER['DOCUMENT_ROOT']."/root/etc/language.xml");
		$xpath = new DOMXpath($config);
		$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
		$lang = array();
		$this->setEnv('LANG_DEF',$this->getConfig('default_language'));
		foreach($xpath->query('//lang') as $contents) {
			$flag = strtolower($contents->getAttribute("flag"));
			$lang[$flag] = array();
			foreach($xpath->query("//lang[@flag='".$flag."']/desc") as $desc){
				$lang[$flag][$desc->getAttributeNS('http://www.w3.org/XML/1998/namespace',"lang")] = $desc->nodeValue;
			}
		}
		$this->setEnv('LANGS',$lang);
		$ask_lang = (isset($_REQUEST["lang"]))?($_REQUEST["lang"]):($this->getConfig("default_language"));
		$clang = $this->getBestLang($ask_lang);
		$this->setEnv('LANG',$clang);
	}

	public function isLang($key){
		$lang = $this->getEnv('LANGS');
		if(array_key_exists(strtolower($key),$lang)){
			return True;
		}
		else {
			return False;
		}
	}

	public function getBestLang($key){
		if($this->isLang(strtolower($key))){
			return strtolower($key);
		}
		else {
			return $this->getEnv('LANG_DEF');
		}
	}

	public function getLangDesc($key){
		$lang = $this->getEnv('LANGS');
		if(array_key_exists(strtolower($key),$lang)){
			$lg = $lang[strtolower($key)];
			if(array_key_exists($this->getEnv('LANG'),$lg))
				return $lg[$this->getEnv('LANG')];
			else
				return $lg[$this->getEnv('LANG_DEF')];
		}
		else {
			throw new Exception("Language ".strtolower($key)." don't exist");
		}
	}

	public function getNbLang(){
		return count($this->getEnv('LANGS'));
	}

	public function getLangKey($id){
		$i = 0;
		foreach ($this->getEnv('LANGS') as $key => $desc) {
			if($id == $i) return $key;
			$i = $i + 1;
		}
		throw new Exception("Language ".str($id)." don't exist");
	}

	/*
	* Message managment
	*/
	private function getTextFromFile($mid,$file,$lang){
		if(file_exists($file)){
			$config = new DOMDocument('1.0');
			$config->Load($file);
			$xpath = new DOMXpath($config);
			$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
			$node = $xpath->query('/messages/private[@idpp=\''.$this->getEnv('BASE').'\']/msg[@xml:lang=\''.$lang.'\'][@id=\''.$mid.'\']');
			if($node->length == 1){
				return $node->item(0)->nodeValue;
			}
			$node = $xpath->query('/messages/msg[@xml:lang=\''.$lang.'\'][@id=\''.$mid.'\']');
			if($node->length == 1){
				return $node->item(0)->nodeValue;
			}
			else return "";
		}
		else return "";	
	}

	public function getText($mid,$errorOneFail=true){
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT']."/root/etc/i18n.xml",$this->getEnv('LANG'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT'].'/skin/'.$this->getEnv('SKIN').'/i18n.xml',$this->getEnv('LANG'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$this->getEnv('PAGEPATH').'/i18n.xml',$this->getEnv('LANG'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$this->getEnv('MODPATH' ).'/i18n.xml',$this->getEnv('LANG'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT']."/kazoe/bin/i18n.xml",$this->getEnv('LANG'));
		if($res != "") return $res;

		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT']."/root/etc/i18n.xml",$this->getEnv('LANG_DEF'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT'].'/skin/'.$this->getEnv('SKIN').'/i18n.xml',$this->getEnv('LANG_DEF'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$this->getEnv('PAGEPATH').'/i18n.xml',$this->getEnv('LANG_DEF'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$this->getEnv('MODPATH' ).'/i18n.xml',$this->getEnv('LANG_DEF'));
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT']."/kazoe/bin/i18n.xml",$this->getEnv('LANG_DEF'));
		if($res != "") return $res;

		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT']."/root/etc/i18n.xml","en");
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT'].'/skin/'.$this->getEnv('SKIN').'/i18n.xml',"en");
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$this->getEnv('PAGEPATH').'/i18n.xml',"en");
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$this->getEnv('MODPATH' ).'/i18n.xml',"en");
		if($res != "") return $res;
		$res = $this->getTextFromFile($mid,$_SERVER['DOCUMENT_ROOT']."/kazoe/bin/i18n.xml","en");
		if($res != "") return $res;

		if($errorOneFail) throw new Exception("Unknown message \"".$mid."\"");
		return "";
	}

	public function getConfig($query){
		$config = new DOMDocument('1.0');
		$config->Load($_SERVER['DOCUMENT_ROOT']."/root/etc/configurations.xml");
		$xpath = new DOMXpath($config);
		$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
		$node = $xpath->query($query);
		if($node->length == 1){
			return $node->item(0)->nodeValue;
		}
		else {
			return "";
		}
	}

	/*
		* Skin Managment
		*/
	private function init_skin(){
		$doc = new DomDocument();
		$doc->load($this->getPath('docroot').'/root/etc/skin.xml');
		$this->skinconf = new DOMXpath($doc);
		$this->skinconf->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
		
		$conf =  $this->skinconf->query("/skins/defaults/@id");
		$skin_def = $conf->item(0)->value;
		$skin = $skin_def;
		
		if(isset($_REQUEST["skin"]))
		{
			$skin = $_REQUEST["skin"];
		}
		elseif(isset($_SESSION["skin"]))
		{
			$skin = $_SESSION["skin"];
		}
		if($skin != $skin_def)
		{
			if(!is_dir($this->getPath('docroot').'/skin/'.$skin))
			{
				$skin = $skin_def;
			}
		}
		$this->setEnv('SKIN',$skin);
		$_SESSION["skin"] = $skin;
	}

	/*
		* Path accessors
		*/
	public function getSkinPath(){
		return $this->getPath('docroot').'/skin/'.$this->getEnv('SKIN');
	}

	/*
		* Rights managment
		*/
	private function init_rights(){
		$RIGHT = "";
		if($this->getEnv('USER_LOGIN')==$this->getConfig('admin_login')){
			$doc = new DomDocument();
			$udoc = new DomDocument();
			$doc->load($this->getPath('docroot').'/root/etc/autorizations.xml');
			$RIGHT = new DOMXpath($doc);
		}
		elseif($this->getEnv('USER_LOGIN') != ""){
			$sql = $this->db_query("SELECT _properties FROM kazoe_passwd WHERE login=:USER_LOGIN");
			if (!$sql->execute()) throw new Exception($this->db_error($sql));
			$line = $sql->fetch();
			if(!$line){
				throw new Exception("User doesn't exist ".$this->getEnv('USER_LOGIN'));
			}
			$xmlauth = $line['_properties'];
			$doc = new DomDocument();
			$udoc = new DomDocument();
			$doc->loadXML($xmlauth);
			$xpath = new DOMXpath($doc);
			$user_node = $xpath->query('/properties/authorization')->item(0);
			if($user_node){
				$node = $udoc->importNode($user_node,True);
				$udoc->appendChild($node);
				$RIGHT = new DOMXpath($udoc);
			}
			else{
				$udoc->loadXML("<null />");
				$RIGHT = new DOMXpath($udoc);
			}
		}
		else{
			$doc = new DomDocument();
			$doc->loadXML("<null />");
			$RIGHT = new DOMXpath($doc);
		}
		$this->rights = $RIGHT;

		$this->childs = array();
		array_push($this->childs, $this->getEnv('USER_ID'));
		if($this->getEnv('USER_ID') != -1)
		{
			$this->add_uchild($this->getEnv('USER_ID'));
		}
		$this->setEnv('SCHILD','{'.join(",",$this->childs).'}');
	}

	public function getRight($query){
		$query = str_replace(":{base}","'".$this->getEnv('BASE')."'",$query);
		return $this->rights->query($query);
	}

	public function canDo($query){
		$query = str_replace(":{base}",$this->getEnv('BASE'),$query);
		if($this->getEnv('USER_LOGIN')==$this->getConfig('admin_login')) return True;
		if ($this->getRight($query)->length > 0){
			return True;
		}
		else {
			return False;
		}
	}
	
	public function canEdit($filename){
		$file = str_replace("root/","",$filename);
		$query = "static[@filename='$file']";
		if ($this->getRight($query)->length > 0){
			return True;
		}
		else {
			return False;
		}
	}

	public function xquery($xobj,$query,$default='####RAISE_EXCEPTION####'){
		$res = $xobj->query($query);
		if($res->length == 1){
			return $res->item(0)->nodeValue;
		}
		else{
			if($default == '####RAISE_EXCEPTION####'){
				throw new Exception("Query without result:".$query);
			}
			else return $default;
		}
	}
	
	public function getPostText($id,$striptags=true){
		if($striptags){
			return strip_tags($_POST[$id]);
		}
		return $_POST[$id];
	}
	
	public function getPostDate($id){
		$year  = $this->getPostText($id."_year");
		$month = $this->getPostText($id."_month");
		$day   = $this->getPostText($id."_day");
		return ($year."-".$month."-".$day);
	}
   
};

function GetKText($mid)
{
	global $Kz;
	return $Kz->getText($mid);
}

function GetKEnv($mid)
{
	global $Kz;
	return $Kz->getEnv($mid);
}

function GetKLangName($lang)
{
	global $Kz;
	return $Kz->getLangDesc($lang);
}

?>
