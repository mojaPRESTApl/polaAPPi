<?php
Class Modules extends Main{
	
	public function __construct(){
		// parent::__construct();
	}
	public function ListaModulowView(){}
	public function UruchomModul($p){
		$t = $this->Db->Select("SELECT * FROM moduly WHERE nazwa='{$p['name']}'");
		if(count($t)){
			$t = $t->{0};
			$file = parent::GetProject()->konfiguracja->dir_modules . $t->dir . '/' . $t->plik;
			if(file_exists($file)){
				$tt = require_once($file);
				pre($tt);
				
			}
		}
	}
}