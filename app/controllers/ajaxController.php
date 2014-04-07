<?php
class ajaxController{
	public static $SHOW_TPL = 0;
	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
	}
	public function Ajax_ShowMeesageBox(){
		$t= $this->Db->Select("select * FROM wiadomosci_alerty WHERE id='{$_POST[id]}'")->{0};
		$this->Main->Smarty->assign('l',$t);
		$this->Main->SetSmartyData();
		echo $this->Main->Smarty->fetch('ajax/wiadomosc_modal.tpl');
	}
	public function Ajax_UsunUser(){
		$this->Db->Delete('users_admin',"id='{$_POST[id]}'");
		echo "Uzytkownik został usunięty poprawnie	";
	
	}
	public function Ajax_UsunZapytanie(){
		$this->Db->Delete('zapytania_ofertowe',"id='{$_POST[id]}'");
	}
	public function Ajax_ShowEditFormKategorie_editAction(){
		unset($_POST['call']);
		$this->Db->Update('kategorie_magazynu',$_POST, "id='{$_POST[id]}'");
		
	}
	public function Ajax_GetSortProductsForZap($idz){
		$t2=$this->Db->Select("select a.ilosc,a.jm,a.id_pro,m.nazwa as prpdS,a.token as TokenProdutk_A,m.* from zapytania_ofertowe_produkty a LEFT JOIN magazyn m ON m.id=a.id_pro WHERE a.id_zapytanie='{$_POST[id]}' ORDER BY a.id_pro ASC");
		
		foreach($t2 as $x=> $i){
			$target[$x] = $i->id_pro;
			$target2[$x] = $i->prpdS;
		}
		
		$t=$this->Db->Select("select a.*,b.nazwa as dostawca_nazwa from oferty a LEFT JOIN kontrahenci b ON a.idd=b.id WHERE a.idz='{$idz}' group by a.id");
		if(count($t)){
			foreach($t as $o){
				$data['off'][$o->id] = [$o->idd,$o->dostawca_nazwa,date('d-m-Y',(int)$o->date_add),date('d-m-Y',(int)$o->czas_realizacji),$o->koszt_dostawy];
				foreach($target as $id){
					$data['off'][$o->id][] = $this->Db->Select('select * from oferty_produkty where id_pro='.$id.' and id_order='.$o->id)->{0}->cena;
				}
				
			}
		}
		
		foreach($target as $id=>$z){
			$data['pro'][$id] = $this->Db->Select('select * from oferty_produkty where id_pro='.$id.' and id_order='.$data[off][0])->{0};
		}
		$data['sort']=$target;
		$data['sort2']=$target2;
		return $data;
		
	
	}
	public function Ajax_PobierzListeOfertZapytaniaModal(){
		// return $this->Ajax_GetSortProductsForZap(1);
		$this->Main->SetSmartyData();
		$o=new stdClass;
		$x=0;
		$t=$this->Db->Select("select a.*,b.nazwa as dostawca_nazwa from oferty a LEFT JOIN kontrahenci b ON a.idd=b.id WHERE a.idz='{$_POST[id]}' group by a.idd");
		// pre($t);
		if(count($t) == 0){
			$this->Main->Smarty->assign('ajax_alert' ,'<h2>Brak dodanych ofert!</h2>');
		}else{
		
			foreach($this->Db->Select("select a.*,m.nazwa from oferty_produkty a, magazyn m where m.id=a.id_pro and a.id_order='{$_POST[id]}' order by a.id_pro ASC") as $xsw){
				$danep->{$xsw->id_pro} = $xsw->nazwa;
			}
			foreach($t as $p){
				$dane->{$x} = $p;
				$dane->{$x}->data1 = date('d-m-Y',(int)$p->date_add);
				$dane->{$x}->data2 = date('d-m-Y',(int)$p->czas_realizacji);
				$dane->{$x}->suma_pro = $this->Db->Select("select sum(cena_all) as suma from oferty_produkty where id_order='{$p->id}'")->{0}->suma + $p->koszt_dostawy;
				
				foreach($this->Db->Select("select * from oferty_produkty  where id_order='{$p->id}' order by id_pro ASC") as $xsw){
					$dane->{$x}->produkty[] = $xsw->cena;
				}
				
				$x++;
			}
			// $t2=$this->Db->Select("select a.ilosc,a.jm,a.id_pro,a.token as TokenProdutk_A,m.* from zapytania_ofertowe_produkty a LEFT JOIN magazyn m ON m.id=a.id_pro WHERE a.id_zapytanie='{$_POST[id]}' ORDER BY a.id_pro ASC");
			// pre($t2);
			// foreach($t2 as $ixx){
				// $o->Tav->{$x} = $ixx;
				// $o->Tav->{$x}->produkty = $this->Db->Select("select a.*,b.nazwa as NazwaProd from oferty_produkty a LEFT JOIN magazyn b ON b.id=a.id_pro WHERE a.id_order='{$ixx->id}' ORDER BY a.id_pro ASC");
				// $x++;
			// }
			$this->Main->Smarty->assign('qq' ,$danep);
			$this->Main->Smarty->assign('q' ,$dane);
			$this->Main->Smarty->assign('ajax_produkty' ,$t2);
		}
			// $this->Db->Select("select * from ")
			// $this->Main->Smarty->assign('id_zap' ,$_POST[id]);
			// $this->Main->Smarty->assign('q' ,$this->Ajax_GetSortProductsForZap($_POST[id]));
			echo $this->Main->Smarty->fetch('ajax/listaOfert.tpl');
	}
	public function Ajax_ShowEditFormKategorie(){
		$this->Main->SetSmartyData();
		$this->Main->Smarty->assign('x',$this->Db->Select("SELECT * FROM kategorie_magazynu WHERE id='{$_POST[id]}'")->{0});
		echo $this->Main->Smarty->fetch('ajax/magazynEdycjaKategorii.tpl');
	
	
	}
	public function Ajax_GetMessageBoxJSON(){
	// $dd[] = array(
							// 'temat'=>'',
							// 'text'=>'',
							// 'link'=>'',
							// 'type'=>'',
							// 'icon'=>'',
							// 'data_add'=>'',
							// 'hook'=>'');
		$t= $this->Db->Select("SELECT a.*,a.id as IdWiadomosci,a.date_add as data_wyslania,b.* FROM wiadomosci_alerty a LEFT JOIN kontrahenci b ON a.idd=b.id ORDER BY a.id asc");
			foreach($t as $k){
				$dd[$k->IdWiadomosci] = array(
							'temat'=>$k->temat,
							'text'=>$k->text,
							'link'=>'ShowMeesageBox(\''.$k->IdWiadomosci.'\')',
							'type'=>$k->type,
							'icon'=>$this->Main->config->typy_wiadomosci_img->{$k->type},
							'data_add'=>$k->data_wyslania,
							'hook'=>$k->hook);
			}
		$t = $this->Db->Select("SELECT a.id as id_pro, a.*,b.nazwa as kategoria_name ,a.id as GlskID, c.nazwa_pliku FROM magazyn a LEFT JOIN kategorie_magazynu b ON a.kategoria=b.id LEFT JOIN pliki c ON c.id_tag=a.token WHERE a.magazyn<=a.min_quantity ORDER BY id DESC");
		foreach($t as $k){
			$ddd[$k->GlskID] = $k;
		}
		echo json_encode(array('box1'=>$dd,'box2'=>$ddd));
	
	
	}
	public function Ajax_Get_Product_data_From_Partner_offer(){
	
	
	
	}
	public function Ajax_PobierzListeProdID(){
		$this->Main->SetSmartyData();
		$t=$this->Db->Select("SELECT x.*,a.*,p.nazwa_pliku FROM magazyn a LEFT JOIN pliki p ON p.id_tag=a.token LEFT JOIN zapytania_ofertowe_produkty x ON a.id=x.id_pro WHERE a.id IN(SELECT id_pro FROM zapytania_ofertowe_produkty WHERE id_zapytanie='{$_POST[id]}') GROUP BY a.id");
		$this->Main->Smarty->assign('x_pro',$t);
		echo $this->Main->Smarty->fetch('ajax/ofertyProduktyLista.tpl');
	}
	public function Ajax_PobierzListeProd(){
		$_smarty = '{tabela klasa=\'table table-bordered miniTab\' tr="ID,Nazwa,Zapotrzebowanie (ilosc),Jednostka miary" array="id_PRO,nazwa,ilosc,jm" dane=$x_pro typ=1}'; 
		$t = $this->Db->Select("SELECT i.ilosc,i.cena,i.cena_all,a.subiekt_tw_id , pi.jm, a.nazwa,a.id as id_PRO,p.nazwa_pliku,p.dir_file,  a.token as token_PRO FROM magazyn a 
		LEFT JOIN oferty_produkty i ON a.id=i.id_pro 
		LEFT JOIN pliki p ON a.token=p.id_tag 
		LEFT JOIN zapytania_ofertowe_produkty pi ON pi.id_pro=i.id_pro 
			WHERE i.id_order='{$_POST[token]}'");
			// pre($_POST);
		// $t= $this->Db->Select("select a.*,b.* from zapytania_ofertowe_produkty a,magazyn b WHERE a.id_zapytanie='{$_POST[token]}' and a.id_pro=b.id");
		$this->Main->SetSmartyData();
		$this->Main->Smarty->assign('x_pro',$t);
		echo $this->Main->Smarty->fetch('string:'.$_smarty);
		
	}
	private function Ajax_BlokadaSkladaniaOfert(){
		$this->Db->Update('zapytania_ofertowe',array('blokada_ofert'=>$_POST['val']),"id='{$_POST[id]}'");
		echo 'Zapisałem poprawnie';
	
	}
	private function Ajax_UsunProdukt(){
		$this->Db->Delete('magazyn',"id='{$_POST['id']}'");
	}
	private function Ajax_UsunKontrahenta(){
		$this->Db->Delete('kontrahenci',"id='{$_POST['id']}'");
	}
	private function Ajax_UsunKategorie(){
		$this->Db->Delete('kategorie_magazynu',"id='{$_POST['id']}'");
	}
	public function AjaxCallApp(){
		$out = new stdClass;
			$nfunc = 'Ajax_'.$_POST['call'];
			$this->$nfunc();
	}
	public function AjaxCall(){
		// echo time();
	
	}
	public function UploadifyIndex(){
		$out = new stdClass;
		$out->status = 1;
		$out->dir = _DIR_.$_POST['upload_dir'];
		if(!is_dir($out->dir)){
			mkdir($out->dir,0777);
		}
		$tempFile   = $_FILES['Filedata']['tmp_name'];
		$uploadDir  = $out->dir;
		$targetFile = $uploadDir . $_FILES['Filedata']['name'];
		
		$info = pathinfo($_FILES['Filedata']['name']);
			if(isset($_POST['token'])){
				$tag = $_POST['token'];
			}
			else{
				$tag = xhash();
			}
			if($_POST[nazwa_pliku] !== null){
				$new_name= $_POST[nazwa_pliku].'.'.$info['extension'];
			}else{
				$new_name =$tag.'.'.$info['extension'];
			}
			move_uploaded_file($tempFile, $uploadDir . $new_name);
			
			if(!isset($_POST[grupowanie]) || $_POST[grupowanie] == ''){
				$this->Main->UnsetAllFilesForToken($_POST[token]);
			}else{
				$this->Db->Delete('pliki',"nazwa_pliku='{$new_name}' and id_tag='{$tag}' and grupowanie='{$_POST[grupowanie]}'");
			}
			$id=$this->Db->Insert('pliki',array('nazwa_pliku'=>$new_name,'id_tag'=>$tag,'data_dodania'=>time(),'data_upload'=>time(),'dir_file'=>$_POST['upload_dir'],'id_zapytania'=>$_POST['id_zapytania'],'grupowanie'=>$_POST['grupowanie'],'opis'=>$_POST[opis]),'id');
			
			// $tag=$_POST['token'];
		
			// if($_POST[nazwa_pliku] !== null){
				// $new_name= $_POST[nazwa_pliku].'.'.$info['extension'];
			// }else{
				// $new_name =$_POST['token'].'.'.$info['extension'];
				// $new_name =$tag.'.'.$info['extension'];
			// }
			// move_uploaded_file($tempFile, $uploadDir . $new_name);
			
			// $this->Main->UnsetAllFilesForToken($tag);
			
			// $id=$this->Db->Insert('pliki',array('nazwa_pliku'=>$new_name,'id_tag'=>$tag,'data_dodania'=>time(),'data_upload'=>time(),'dir_file'=>$_POST['upload_dir']),'id');
		
		unset($out);
		$out['id'] = $id;
		$out['tag'] = $tag;
		
		if(isset($_POST['tabela'])){
			$tss=explode('|',$_POST['tabela']);
			$this->Db->Update($tss[0],array($tss[1]=>$new_name),'id='.$tss[2]);
		}
		
		return $out;
	}
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
}