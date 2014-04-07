<?php 
Class kontrahenciController{
	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
		$this->Main->Models->LoadModel('dostawcy')
							->RunAction('test');
	}

	private function GetGroups(){
		foreach($this->Db->Select("select * from grupy ORDER BY id DESC") as $i){
			$grupyName[$i->id]= $i->nazwa;
		}
		return $grupyName;
	}
	public function kontrahenciEdit(){
		$out = new stdClass;
		$out->id = $this->Main->GetRouter()->params->id;
		if(isset($_POST['token'])){
			if($_POST['haslo'] !== ''){
				$_POST['haslo']=CreatePasswordWithSol($_POST['haslo']);
			}else{
				unset($_POST['haslo']);
			}
			$_POST['grupa']=json_encode($_POST['grupa']);
			$token = $_POST['token'];
			unset($_POST['token']);
			$this->Db->Update('kontrahenci',$_POST,"id='{$out->id}'");
			$out->smarty['js_code'].= "ShowAlertModalBox('Edycja danych dostawcy','Zmiana danych przebiegła pomyślnie.')";
		}
		
		
		$out->smarty[grupyX] = $this->GetGroups();
		$out->smarty['i'] = $this->Db->Select("select * from kontrahenci where id='{$out->id}'")->{0};
		$out->smarty['i']->grupa = json_decode($out->smarty['i']->grupa,true);
		$out->tpl = 'kontrahenciEdit.tpl';
		return $out;
	}
	public function kontrahenciDodaj(){
	pre($_POST);
		$out = new stdClass;
		if(isset($_POST['token'])){
			$_POST['grupa']=json_encode($_POST['grupa']);
			$_POST['data_add'] = time();
			$pass = $_POST['haslo'];
			$_POST['haslo'] = CreatePasswordWithSol($_POST['haslo']);
			$id = $this->Db->Insert('kontrahenci',$_POST,'id');
				$tfF=$this->Db->Select("select * from pliki WHERE id_tag='{$_POST[token]}' ORDER BY id DESC limit 1");
					if(count($tfF)){
						$tfF=$tfF->{0};
						$path = pathinfo($tfF->dir_file.$tfF->nazwa_pliku);
						rename($tfF->dir_file.$tfF->nazwa_pliku,$tfF->dir_file.$id.'.'.$path['extension']);
						$this->Db->Update('pliki',array('nazwa_pliku'=>$id.'.'.$path['extension'],'id_zapytania'=>$id,'data_upload'=>time()),"id='{$tfF->id}'");
						$this->Db->Update('kontrahenci',array('logotyp'=>$id.'.'.$path['extension']),"id='{$id}'");
						$out->smarty['imgKonttr'] = $id.'.'.$path['extension'];
						
					}
				$this->Main->eMail->SendEmail(array_merge($_POST,array(
					'id_mail'=>3,
					'to'=>$_POST['nazwa'],
					'haslo'=>$pass 
					)
				));
		$out->smarty['js_code']="ShowAlertModalBox('Dodawanie nowego dostawcy','Dodałem poprawnie nowego kontrahenta. <BR>Login: {$_POST['login']} <br>Hasło:{$pass}');";
		}
		$out->smarty[title] = 'dodaj';
		$out->smarty[grupyX] = $this->GetGroups();
		$out->smarty[token] = xhash();
		$out->tpl =  'kontrahenciDodaj.tpl';
		return $out;
	}


	public function kontrahenciLista(){
		$out = new stdClass;
		$out->smarty[title] = 'lista';
		$out->smarty[token] = xhash();
		$x=0;
		foreach($this->Db->Select("select a.*,b.nazwa_pliku, b.dir_file  from kontrahenci a LEFT JOIN pliki b ON a.token=b.id_tag ORDER BY a.id DESC") as $i){
			$out->smarty[listaK]->{$x}= $i;
			$out->smarty[listaK]->{$x}->adres = $i->ulica.' '.$i->nr_dom.'/'.$i->nr_lok.'<BR>'.$i->kod_pocztowy.' '.$i->miejscowosc;
				foreach($this->Db->Select("SELECT nazwa FROM grupy WHERE `id` IN(".implode(',',json_decode($i->grupa)).")") as $g){
					$out->smarty[listaK]->{$x}->grupaX.=li($g->nazwa);
				}
				$x++;
		}
			
		$out->tpl =  'kontrahenciLista.tpl';
			return $out;
	}


	public function kontrahenciGrupy(){
		$out = new stdClass;

		if(isset($_POST['nazwa']) && isset($_POST['opis']) && isset($_POST['aktywny'])){
			$this->Db->Insert('grupy',array('token'=>$_POST['token'],'nazwa'=>$_POST['nazwa'], 'opis'=>$_POST['opis'], 'data_add'=>time(),'aktywny'=>$_POST['aktywny']));
			$out->smarty[alert] = 'Dodałem poprawnie nową grupę';
		}
		$out->smarty[listGx] =$this->Db->Select("select * from kontrahenci ORDER BY id DESC");
		$out->smarty[listGrx] =$this->Db->Select("select * from grupy ORDER BY nazwa ASC");
		$out->smarty[token] = xhash();
		$out->smarty[title] = 'grupy';
		$out->tpl =  'kontrahenciGrupy.tpl';
		return $out;
	}


	public function kontrahenciIndex(){
	$out = new stdClass;
	$out->smarty[token] = xhash();
	$out->smarty[title] = '';
	$out->tpl =  'kontrahenciIndex.tpl';
		return $out;
	}
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
}