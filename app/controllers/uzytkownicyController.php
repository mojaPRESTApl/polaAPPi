<?php 
Class uzytkownicyController{
	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',Main::GetInst()->Db);
	}	
	public function uzytkownicyEdycja(){
	$out = new stdClass;
	
	if(count($_POST) > 1){
		if($_POST['haslo'] !== ''){
			$_POST['haslo']=CreatePasswordWithSol($_POST['haslo']);
		}else{
			unset($_POST['haslo']);
		}
		$this->Db->Update('users_admin',$_POST,'id='.$this->Main->GetRouter()->params->id_user);
		$out->smarty['alert'] = 'Zmiany zostały zapisane poprawnie.';
	}
	
	$out->smarty['i'] = $this->Db->Select("select * from users_admin where id=".$this->Main->GetRouter()->params->id_user)->{0};
	$out->tpl='uzytkownicyEdycja.tpl';
		return $out;
}
	public function uzytkownicyDodaj(){
		$out = new stdClass;
		if($_POST['addAction']=='1'){
		unset($_POST['addAction']);
		if($this->Db->Select("select count(id) as id from users_admin WHERE login='{$_POST[login]}' or email='{$_POST[email]}'")->{0}->id > 0){
			$out->smarty['error_add'][] = 'Wprowadzony login lub adres email już istnieje w bazie danych';
		}
		$haslo = $_POST['haslo'];
		if(count($out->smarty['error_add']) > 0){
			$out->smarty['i'] =$_POST;
			$out->smarty['js_code'] = "ShowAlertModalBox('Dodawanie uzytkownika','Wystąpił błąd podczas dodawania użytkownika: <ul><li>".implode('</li><li>',$out->smarty[error_add])."</li></ul>')";
		}else{
			if($_POST['send_email'] == '1'){
				$this->Main->eMail->SendEmail(array(
					'id_mail'=>2,
					'email'=>$_POST['email'],
					'to'=>$_POST['login'],
					'login'=>$_POST['login'],
					'haslo'=>$_POST['haslo']
				));
			}
			$_POST['haslo'] = CreatePasswordWithSol($_POST[haslo]);
			unset($_POST['send_email']);
			unset($_POST['haslo2']);
			$_POST['data_add'] = time();
			$this->Db->Insert('users_admin',$_POST);
			$out->smarty['js_code'] = "ShowAlertModalBox('Dodawanie uzytkownika','Użytkownik {$_POST[imie]} {$_POST[nazwisko]} został utworzony poprawnie. ".(($_POST[send_email] == '1') ? "Dane logowania zostały wysłane na adres email: {$_POST[email]}" : '<BR>Login:'.$_POST[login].'<br>Hasło: '.$haslo)."');";
		}
		}
		$out->smarty[title] = 'dodaj';
		$out->tpl =  'uzytkownicyDodaj.tpl';
		return $out;
	}


	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
	public function uzytkownicyLista(){
	$out = new stdClass;
	$out->smarty[lista] = $this->Db->select("select * from users_admin");
	$out->tpl =  'uzytkownicyLista.tpl';
		return $out;
	}

}