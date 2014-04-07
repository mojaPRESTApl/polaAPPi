<?php
Class Controllers{
	private static $oInstance = false;
	Protected $Db;
    public function __construct() {}
	public function RunControllers(){
	
	}
	public static function getInst(){
	
		if(self::$oInstance == false){
			self::$oInstance = new Controllers();
			self::$oInstance->Db = Main::GetInst()->Db;
		}
		return self::$oInstance;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
	public function __get($nazwa) {
		if (array_key_exists($nazwa, $this->data)) {
			return $this->data[$nazwa];
		} else {
			return false;
		}
	}
	public function IncludeFunctionForSmarty($insts,$data){
		$this->__set('SmartyFunction',new $insts());
		$this->list_sf= $this->SmartyFunction->__GetSmartyFunction();
		foreach($this->list_sf as $n){
			if(method_exists($this->SmartyFunction,$n)){
			$this->SmartyFunction->$n();
			}
		}
	}
	public function ControllerActions($idc){
		return $this->Db->Select("SELECT * FROM kontrolery_akcje WHERE id='{$idc}'");
	}
	public function GetTemplates($idc){
		$th = $this->Db->Select("SELECT * FROM szablony WHERE id='{$idc}'");
		if(Count($th)){
			return $th->{0};
		}
	}
	public function ActionData($idc){
		$th = $this->Db->Select("SELECT * FROM kontrolery_akcje WHERE id='{$idc}'");
		if(Count($th)){
			return $th->{0};
		}else{
			return $this->Db->Select("SELECT * FROM kontrolery_akcje WHERE id='1'")->{0};
		}
	}
	public function ControllerData($idc){
		// $th = $this->Db->Select("SELECT * FROM kontrolery WHERE id='{$idc[kontroler]}'");
		// if($idc[akcja] !== null && isset($idc[akcja])){
			// $th->{0}->akcje = $this->ControllerActions($idc[akcja])->{0};
		// }else{
			// $th->{0}->akcje = $this->ControllerActions(0)->{0};
		// }
			// return $th->{0};
		
	}
	public function SzukajAkcjiPoNazwieFunkcji($Funkcja){
		$t = $this->Db->Select("SELECT * FROM kontrolery_akcje WHERE funkcja='{$Funkcja}' and idk='".Main::GetProject()->router->target['kontroler']."'");
		if(count($t)){
			return $t->{0};
		}
	}
	public function SprawdzRouterEtapowy(){
		if($this->daneAction[router_type] == 'post'){
			$tpl = $_POST[$this->daneAction['router_name']];
			if($tpl !== null){
				$name = $this->daneAction[etapy][$tpl];
				$t = $this->Db->Select("SELECT * FROM kontrolery_akcje_etapy WHERE nazwa='{$name}'");
					if(count($t)){
						return $t->{0};
					}else{
						return $this->Db->Select("SELECT * FROM kontrolery_akcje_etapy WHERE ida='{$ida}'")->{0};
					}
			}
		}
	}
	
	public function LadujModul($xp){
		if(isset($xp['id'])){
			$wherex[] ="`id`='{$xp['id']}'";
		}
		if(isset($xp['name'])){
			$wherex[] ="`nazwa`='{$xp['name']}'";
		}
		if(isset($xp['nazwa'])){
			$wherex[] ="`nazwa`='{$xp['nazwa']}'";
		}
		$wherex=((count($wherex)) ? 'WHERE '.implode(" and " ,$wherex) : '');
		$t= $this->Db->Select("SELECT * FROM moduly {$wherex}");
			if(count($t)){	
				$t=$t->{0};
				$file = GetIniData(_CONFIG_INI_)->dir->modules.'/'.$t->dir.'/'.$t->plik;
				require_once($file);
				$fj =(string)$t->nazwa.'Mod';
				$fjj_Class = new $fj;
				return $fjj_Class->indexAction();
			}
	}
	public function UruchomModul($p){
		if(isset($p['id'])){
			$wherex ="`id`='{$p['id']}'";
		}
		if(isset($p['name'])){
			$wherex ="`name`='{$p['name']}'";
		}
		if(isset($p['nazwa'])){
			$wherex ="`name`='{$p['nazwa']}'";
		}
		$t= $this->Db->Select("SELECT * FROM moduly WHERE {$wherex}");
		if(count($t)){
			$x = $this->Db->Select("select * from moduly_main WHERE id='".$t->{0}->id_m."'");
			if(count($x)){
				$x=$x->{0};
				$src=Main::GetInst()->config->dir->modules.'/'.$x->file;
				// return $src;
				if(!file_exists($src)){
						return 'Brak pliku:'.$src;
				}
				else{
					require_once($src);
					$nc = new $x->class_name;
					$ncc = $t->{0}->{'function'};
					return $nc->$ncc();
				}
			}else{
				return 'Brak modułu głównego';
			}
		}
		return;
			if(count($t)){
					$t=$t->{0};
					$src=GetIniData(_CONFIG_INI_)->dir->modules.'/'.$t->dir.'/'.$t->plik;
					if(!file_exists($src)){
						echo 'Brak pliku:'.$src;
					}else{
					return Main::FetchSmarty('moduly/'.$p[tpl],'Mod'.$t->nazwa,$this->LadujModul($p));
						
					}
				}
		}
	}
	
	
