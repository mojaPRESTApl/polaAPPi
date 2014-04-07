<?php
Class View{
	private static $oInstance = false;
	public static $Db = null;
	public function __construct(){
		self::$oInstance = $this;
		self::$Db = Main::GetInst()->Db;
		
	}
	public static function PobierzIDszablonuZAkcji($ida){
		return self::$Db->Select("SELECT * FROM kontrolery_akcje_podzial_projekty where ida='{$ida}'")->{0}->id_szablon;
	}
	public static function PobierzKonfiguracjeDlaSzablonu($id){
		$t = self::$Db->Select("SELECT * FROM konfiguracja where wartosc_pole='{$id}' and typ_pole='szablon'");
		foreach($t as $i){
			$dane[$i->keyx] = $i->valuex;
		}
		return $dane;
	}
	public static function PobierzDodatkowePolaSzablonu($id){
		$t = self::$Db->Select("SELECT * FROM system_szablony where id='{$id}'")->{0};
			foreach( explode('#',$t->vars) as $i){
				$dat=explode('=',$i);
				$dane[$dat[0]]= $dat[1];
			}
			return $dane;
		// pre($t);
	
	}
	public static function getInst(){
		if(self::$oInstance == false){
			self::$oInstance = new Main();
		}
		return self::$oInstance;
	}
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
}