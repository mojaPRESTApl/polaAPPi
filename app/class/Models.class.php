<?php
Class Models{

	public $Main,$inst,$Db,$config;
	
	public function __construct(){
		$this->__set('config',GetIniData(_CONFIG_INI_));
	}
	public function LoadModel($name){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
		if(file_exists($file = _DIR_ . $this->config->dir->model.'/'.$name.'.class.php')){
			require_once($file);
			$inst = (string)ucfirst($name).'_model';
			$this->inst = new $inst;
			return $this->inst;
		}
	}
	public function RunAction($act){
		return $this->{$act}();
	}
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
}