<?php
class iController{
	protected $Main=false;
	protected $Db=false;
	
	public function __construct(){}
	public function RunAction($cl,$fn){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
		$this->__set('config',$this->Main->config);
		$this->config->routers=$this->Main->PolaRouter->GetRouterData();
		if($this->config->routers[target][kontroler]==''){
			$this->config->routers[target][kontroler]='indexController.php';
		}
		if(file_exists($this->config->dir->controllers.'/'.$this->config->routers[target][kontroler])){
		require_once($this->config->dir->controllers.'/'.$this->config->routers[target][kontroler]);
			$cls= new $cl();
			$cls->Main = $this->Main;
			$cls->Db = $this->Db;
			return $cls->{$fn}();
			
		}
	}
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}


}