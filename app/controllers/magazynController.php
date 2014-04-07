<?php 
Class magazynController{

	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
	}
	public function magazynEdycja(){
	$out = new stdClass;
	$out->smarty['id'] = $this->Main->GetRouter()->params->id;
	if(count($_POST)){
		$dostawcy=$_POST['dostawcy'];	unset($_POST['dostawcy']);
		$token=$_POST['token'];	unset($_POST['token']);
		$this->Db->Update('magazyn',$_POST,"token='{$token}'");
		$this->Db->Delete('material_supplier',"material_id='{$out->smarty[id]}'");
			foreach($dostawcy as $d){
				$this->Db->Insert('material_supplier',array('material_id'=>$out->smarty[id],'supplier_id'=>$d));
			}
		$out->smarty['alert'] = 'Edycja produktu została zakończona sukcesem!';
	}
	
	foreach($this->Db->Select("select * from kontrahenci ORDER BY id DESC") as $i){
		$out->smarty[kontrahent][$i->id] = $i->nazwa;
	}
	foreach($this->Db->Select("select supplier_id from material_supplier WHERE material_id='{$out->smarty[id]}'") as $i){
		$_Sm[] = $i->supplier_id;
		$out->smarty[dost][] = $i->supplier_id;
	}
	
	$out->smarty['json_act'] = $_Sm;
	$out->smarty['json_act'] = json_encode($out->smarty['json_act']);
	$out->smarty[kontrahent2] = $out->smarty[kontrahent];
	$out->smarty[kontrahent][0] = '---Brak powiązania---';
	foreach($this->Db->Select("select * from kategorie_magazynu ORDER BY id DESC") as $i){
		$out->smarty[kategorie][$i->id] = $i->nazwa;
	}
	$out->smarty[users_list][0] = '---Brak opiekuna---';
	foreach($this->Db->Select("select * from users_admin WHERE aktywny=1") as $i){
		$out->smarty[users_list][$i->id] = $i->imie.' '.$i->nazwisko;
	}
	$out->smarty['i'] = $this->Db->Select("SELECT a.*,b.nazwa_pliku FROM magazyn a LEFT JOIN pliki b ON a.token=b.id_tag WHERE a.id='{$out->smarty[id]}'")->{0};
	$out->tpl = 'magazynEdit.tpl';
	$out->smarty[title] = 'zarzadzanie';
	return $out;
	
	
	}
	
	public function magazynKategorieProduktow(){
	$out = new stdClass;
	if(isset($_POST['add'])){
		$this->Db->Insert('kategorie_magazynu',
		array('nazwa'=>$_POST['nazwa'],'aktywna'=>$_POST['aktywna'],'data_add'=>time()));
		$out->smarty['alert'] ='Nowa kategoria została utworzona pomyślnie';
	}
	
	$out->smarty[listaK] = $this->Db->Select("select a.*,count(b.id) as IlesX from kategorie_magazynu a LEFT JOIN magazyn b ON a.id=b.kategoria GROUP BY a.id");
	$out->smarty[title] = 'kategorie-produktow';
	$out->tpl =  'magazynKategorieProduktow.tpl';
		return $out;
}


	public function magazynDodajProdukt(){
	$out = new stdClass;
	$out->smarty[token] = xhash();
	
	if(isset($_POST['token'])){
		$out->smarty[token] = $_POST['token'];
		if((int)$this->Db->Select("select count(id) as ile from magazyn where subiekt_tw_id='{$_POST[subiekt_tw_id]}'")->{0}->ile == 0){
		$idp = $this->Db->Insert('magazyn',
			array(
				'subiekt_tw_id'=>$_POST['subiekt_tw_id'],
				'nazwa'=>$_POST['nazwa'],
				'nazwa_atr'=>$_POST['nazwa_atr'],
				'kategoria'=>$_POST['kategoria'],
				'magazyn'=>$_POST['magazyn'],
				'min_quantity'=>$_POST['min_quantity'],
				'cena_nett_detal'=>$_POST['cena_nett_detal'],
				'cena_nett_zakup'=>$_POST['cena_nett_zakup'],
				'aktywny'=>$_POST['aktywny'],
				'opis'=>$_POST['opis'],
				'kontrahent'=>$_POST['kontrahent'],
				'uzytkownik_nadzorujacy'=>$_POST['uzytkownik_nadzorujacy'],
				'data_add'=>time(),
				'token'=>$_POST['token']
			),'id'
		);
				foreach($_POST['dostawcy'] as $i){
					$this->Db->Insert('material_supplier',array(
						'material_id'=>$idp,
						'supplier_id'=>$i						
					));
				}
			$out->smarty['alert'] = 'Produkt został wprowadzony poprawnie';
			
			$out->smarty[token] = xhash();
		}else{
			$out->smarty['alert'] = 'Taki produkt z takim indexem już istnieje';
			foreach($_POST as $k=>$c){
				$out->smarty['ix'][$k] = $c;
				
			}
		}
	}
	
	
	foreach($this->Db->Select("select * from kontrahenci ORDER BY id DESC") as $i){
		$out->smarty[kontrahent][$i->id] = $i->nazwa;
	}
	
	$out->smarty[kontrahent2] = $out->smarty[kontrahent];
	$out->smarty[kontrahent][0] = '---Brak powiązania---';
	foreach($this->Db->Select("select * from kategorie_magazynu ORDER BY id DESC") as $i){
		$out->smarty[kategorie][$i->id] = $i->nazwa;
	}
	$out->smarty[users_list][0] = '---Brak opiekuna---';
	foreach($this->Db->Select("select * from users_admin WHERE aktywny=1") as $i){
		$out->smarty[users_list][$i->id] = $i->imie.' '.$i->nazwisko;
	}
	$out->smarty[title] = 'dodaj-produkt';
	$out->tpl =  'magazynDodajProdukt.tpl';
		return $out;
	}

	public function magazynEdytujProdukt(){
	$out = new stdClass;
	$out->smarty[id] =$this->Main->GetRouter()->params->id;
	if(isset($_POST['token'])){
		$id = $out->smarty[id];
		$idp = $this->Db->Update('magazyn',
			array(
				'subiekt_tw_id'=>$_POST['subiekt_tw_id'],
				'nazwa'=>$_POST['nazwa'],
				'nazwa_atr'=>$_POST['nazwa_atr'],
				'kategoria'=>$_POST['kategoria'],
				'magazyn'=>$_POST['magazyn'],
				'min_quantity'=>$_POST['min_quantity'],
				'cena_nett_detal'=>$_POST['cena_nett_detal'],
				'cena_nett_zakup'=>$_POST['cena_nett_zakup'],
				'aktywny'=>$_POST['aktywny'],
				'opis'=>$_POST['opis'],
				'kontrahent'=>$_POST['kontrahent'],
				'uzytkownik_nadzorujacy'=>$_POST['uzytkownik_nadzorujacy'],
				'data_edit'=>time(),
				'token'=>$_POST['token']
				),"id='{$id}'"); 
				$this->Db->Delete('material_supplier',"material_id='{$out->smarty[id]}'");
				foreach($_POST['dostawcy'] as $i){
					$this->Db->Insert('material_supplier',array(
						'material_id'=>$out->smarty[id],
						'supplier_id'=>$i						
					));
				}
		$out->smarty['alert'] = 'Produkt został zmieniony poprawnie';	
	};		
	$out->smarty[title] = 'zapisz zmiany';	
	$out->smarty['id'] = $this->Main->GetRouter()->params->id;
	$out->smarty['i'] = $this->Db->Select("SELECT p.* FROM magazyn p WHERE p.id='{$this->Main->GetRouter()->params->id}'")->{0};
	
	foreach($this->Db->Select("select * from kontrahenci ORDER BY id DESC") as $i){
		$out->smarty[kontrahent][$i->id] = $i->nazwa;
	}
	
	$out->smarty[kontrahent2] = $out->smarty[kontrahent];
	$out->smarty[kontrahent][0] = '---Brak powiązania---';
	foreach($this->Db->Select("select * from kategorie_magazynu ORDER BY id DESC") as $i){
		$out->smarty[kategorie][$i->id] = $i->nazwa;
	}
	
	$out->smarty[users_list][0] = '---Brak opiekuna---';
	foreach($this->Db->Select("select * from users_admin WHERE aktywny=1") as $i){
		$out->smarty[users_list][$i->id] = $i->imie.' '.$i->nazwisko;
	}
	foreach($this->Db->Select("select * from material_supplier WHERE material_id='{$out->smarty['id']}'") as $i){
		$out->smarty[json_act][$i->supplier_id] = 1;
	}
		$out->smarty[json_act]=json_encode($out->smarty[json_act]);
	$out->tpl =  'magazynEdytujProdukt.tpl';
	return $out;
				
}

 public function magazynZarzadzanie(){
	$out = new stdClass;
	$sql = $this->Db->Select("select a.*,b.nazwa as kategoria_name from magazyn a, kategorie_magazynu b WHERE a.kategoria=b.id ORDER BY magazyn ASC");
	$x=0;
	foreach($sql as $i){
		$out->smarty['listPro']->{$x} = $i;
		$t=$this->Db->Select("Select imie,nazwisko FROM users_admin WHERE id='{$i->uzytkownik_nadzorujacy}'")->{0};
		$dostawcy_list=$this->Db->Select("select a.* FROM material_supplier b,kontrahenci a WHERE a.id=b.supplier_id and b.material_id='{$i->id}'");
		if(count($dostawcy_list)>0){
			$out->smarty['listPro']->{$x}->dostawcy_list='<ul>';
		foreach($dostawcy_list as $j){
			$out->smarty['listPro']->{$x}->dostawcy_list.=li($j->nazwa);
		}
			$out->smarty['listPro']->{$x}->dostawcy_list.='</ul>';
		}
		if($i->magazyn <= $i->min_quantity){
			$out->smarty['listPro']->{$x}->alert_magazyn_on =1;
		}else{
			$out->smarty['listPro']->{$x}->alert_magazyn_on =0;
		}
		$out->smarty['listPro']->{$x}->pmx = $t->imie.' '.$t->nazwisko;
		$out->smarty['listPro']->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$i->token}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
		
	$x++;
	}
	$out->smarty[title] = 'zarzadzanie';
	$out->tpl =  'magazynZarzadzanie.tpl';
		return $out;
	}
	
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
}