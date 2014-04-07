<?php
Class dostawcaController{
	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
	}
	public function dostawcaZlecenia(){
		$out = new stdClass;
		$out->smarty['dostListaZapytan'] = $this->Db->Select("SELECT z.*,z.data_add as i_data_add,z.nazwa as NazwaZapytania, i.*,i.token as TokenDost , count(o.id) as CzyJestOferta FROM zapytania_ofertowe z LEFT JOIN zapytania_ofertowe_dostawcy i ON i.idz=z.id LEFT JOIN oferty o ON o.idz=i.idz WHERE i.idd='{$_SESSION[dostawca_user_id]}' GROUP BY z.id");
		$out->tpl = 'dostawcaZlecenia.tpl';
		return $out;
	
	}

	public function dostawcaUstawienia(){
		
	
	}
	public function dostawcaRealizacje(){
			$out= new stdClass;
			$out->tpl = 'dostawcaRealizacje.tpl';
			return $out;
	
	}

	public function dostawcaUploaderIndex(){
		$o = new stdClass;
		$o->smarty['id_hash'] = $this->Main->GetRouter()->params->id;
		foreach($this->Db->Select("select * from pliki where id_tag='{$o->smarty[id_hash]}'") as $i){
			$o->smarty['pliki'][$i->grupowanie] = $i;
		}
		
		$o->tpl = 'oferta_uploader.tpl';
		return $o;
	}

	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}


}