<?php
Class zapytania_ofertoweController{
	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',$this->Main->Db);
	}
	public function wyborOfertyIndex(){
		$o= new stdClass;
		$o->smarty['j'] = $this->Db->Select("select a.id as id_zsaf, a.*,b.nazwa as dostawca_nazwa,b.id as ASfjas ,b.* from oferty a LEFT JOIN kontrahenci b ON a.idd=b.id WHERE a.idz='{$this->Main->GetRouter()->params->id}' group by a.id")->{0};
		$o->smarty['i'] = $this->Db->Select("select * from zapytania_ofertowe where id='{$this->Main->GetRouter()->params->id}'")->{0};
		$o->smarty['o'] = $this->Db->Select('select * from oferty_produkty where id_order='.$o->smarty['j']->id_zsaf);
		$o->smarty['op'] =  $this->Db->Select("select pliki.nazwa_pliku,a.*,b.nazwa as NazwaProd from oferty_produkty a LEFT JOIN magazyn b ON b.id=a.id_pro LEFT JOIN pliki ON b.token = pliki.id_tag WHERE a.id_order='{$this->Main->GetRouter()->params->id2}' group by a.id_pro");
		$o->smarty['roz'] = $this->Main->GetRouter()->params;
		
		if(isset($_POST['id']) && isset($_POST['id2']) && isset($_POST['action'])){
			$token=xhash();
			$id = $this->Db->Insert('zamowienia',array('ido' => $o->smarty[roz]->id2,'idd' => $o->smarty['j']->ASfjas,'data_add' => time(),'status' => '2','termin_dostawy'=>'','token' => $token,'towar_odebrano' => NULL,'zakonczone' => NULL),'id');
			$this->Db->Update('oferty',array(
				'wybrana_oferta'=>1,
				'id_order'=>$id,
				'status'=>2,
				'blokada'=>1,
				'data_wyboru_oferty'=>time()
				),"id={$o->smarty['roz']->id2} and idz={$o->smarty['roz']->id}");
			$this->Main->SetSmartyData();
			foreach($o->smarty as $k=>$w){
				$this->Main->Smarty->assign($k,$w);
			}
			$pdf_cont = $this->Main->Smarty->fetch($this->Main->config->dir->pdf_themes . '/'.$this->Db->Select("SELECT file FROM szablony_pdf WHERE type='new_order'")->{0}->file);
			$dile = $this->Main->iPdf->GenerowaniePDF($this->Main->config->dir->pdf_generator.'/'.$token.'.pdf',$pdf_cont,$pdf);
			$this->Main->eMail->SendEmail(array(
				'id_mail'=>4,'email'=>$o->smarty['j']->email,'to'=>$o->smarty['j']->nazwa,
				'file'=>$dile,
				'file_name'=>$token.'.pdf','token'=>$token
			));
			$o->smarty[plik_pdf] = $token.'.pdf';
			$o->tpl = 'wyborOferty_potwierdzenie.tpl';
		}else{
			$o->tpl = 'wyborOferty.tpl';
		}
		if((int)$this->Db->Select("select count(id) as ile from oferty where id='{$o->smarty[roz]->id2}' and idz='{$this->Main->GetRouter()->params->id}' and wybrana_oferta=1")->{0}->ile > 0){
			$o->tpl = 'wyborOferty_potwierdzenie.tpl';
		}
		return $o;
		
	
	
	
	
	}
	public function GetProForRequest($id){
	$sql = $this->Db->Select("SELECT pi.ilosc, a.id as id_PRodkd, a.subiekt_tw_id , pi.jm, a.nazwa,a.id as id_PRO,p.nazwa_pliku,p.dir_file,  a.token as token_PRO FROM magazyn a LEFT JOIN pliki p ON a.token=p.id_tag 
		LEFT JOIN zapytania_ofertowe_produkty pi ON pi.id_pro=a.id 
			WHERE pi.id_zapytanie='{$id}'");
				/*			LISTOWANIE WSZYSTKICH PRODUKTÓW Z ZAPYTANIA 	*/
				$x=0;
				foreach($sql as $i){
					$listPro->{$x} = $i;
					$listPro->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$i->token_PRO}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
				$x++;
				}
		return $listPro;
	
	}
	public function GetOfferts($id){
		return $this->Db->Select("select * from oferty WHERE idz='{$id}'");
		}
	public function GetOffertsForTargetMain($id){
		$t=new StdClass;
		$t= $this->Db->Select("select * from oferty WHERE idz='{$id}'")->{0};
		$t->products = $this->Db->Select("select * from oferty_produkty WHERE id_order='{$t->id}'");
		return $t;
	}
	public function zapytania_ofertoweOdpowiedzi(){
		$out = new stdClass;
		$x=0;
		foreach($this->Db->Select("SELECT * FROM zapytania_ofertowe ORDER BY id DESC") as $i){
			$out->smarty[listaZap]->{$x} = $i;
			$out->smarty[listaZap]->{$x}->ilosc_zgloszen = $this->GetOfferts($i->id);
			$out->smarty[listaZap]->{$x}->ilosc_zgloszen_count = count($out->smarty[listaZap]->{$x}->ilosc_zgloszen );
			
			$out->smarty[listaZap]->{$x}->oferty = $this->GetOffertsForTargetMain($i->id);
			$out->smarty[listaZap]->{$x}->color = $this->Main->config->statusy_zamowien->color[$i->status];
			$out->smarty[listaZap]->{$x}->status_text = $this->Main->config->statusy_zamowien->status[$i->status];
			$x++;
		}
		// pre($out);
		
		$out->tpl =  'zapytania_ofertoweOdpowiedzi.tpl';
		return $out;
	}
    /**
     * 
	 *			EDYCJA ZAPYTANIA OFERTOWEGO 
     * 
     * @return <type>
     */
	
	
	public function zapytania_ofertoweEdycja(){
		$out = new stdClass;
		$out->smarty[title] = 'edycja';	
		$out->smarty['id_zap'] = $this->Main->GetRouter()->params->id;
		$out->smarty['Oferta'] = $this->Db->Select("SELECT z.*,z.token as Tksa,spdf.name as PdfSzablon FROM zapytania_ofertowe z LEFT JOIN szablony_pdf spdf ON z.szablon_pdf=spdf.id WHERE z.id='{$this->Main->GetRouter()->params->id}'")->{0};
 		foreach($xi = $this->Db->Select("SELECT k.* , zod.* FROM zapytania_ofertowe_dostawcy zod LEFT JOIN kontrahenci k ON k.id=zod.idd WHERE `idz`='{$out->smarty[Oferta]->id}'") as $x=> $i){
				$out->smarty['dS']->{$x} = $i;
				
			$x=0;
			foreach($sql as $i){
				$out->smarty['listPro']->{$x} = $i;
				$out->smarty['listPro']->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$i->xTokens}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
				
			$x++;
			}
			
		}
		$out->smarty['pSO'] = $this->Db->Select("SELECT p.nazwa_pliku, a.*,magazyn.token as xTokens ,magazyn.id as id_PRodkd,magazyn.nazwa as nazwa_prodsk ,a.ilosc as ileTrza, k.nazwa as kategoriss FROM `zapytania_ofertowe_produkty` a LEFT OUTER JOIN magazyn ON a.id_pro=magazyn.id LEFT OUTER JOIN kategorie_magazynu k ON magazyn.kategoria=k.id LEFT JOIN pliki p ON p.id_tag=magazyn.token WHERE a.id_zapytanie='".$this->Main->GetRouter()->params->id."'");
		$out->tpl =  'zapytania_edycja.tpl';
		return $out;
	}
	public function GetID_Offer_For_UserToken($token=null){ 
		if($token==null)
			$token = $this->Main->GetRouter()->params->token;
		
		return $this->Db->Select("select a.token as ToksA,a.idz as ID_Zapytania,b.id as ID_Oferty_dostawcy  FROM zapytania_ofertowe_dostawcy a LEFT JOIN oferty b ON a.idz=b.id WHERE a.token='{$token}' ");
		
	
	}
	public function GetAllSupplier(){
		foreach($this->Db->Select("select id,nazwa FROM kontrahenci WHERE aktywny='1'") as $i){
			$dan[$i->id] = $i->nazwa;
		}
		return $dan;
	}
	public function ShowErrorSite($txt){
		$out=new stdClass;
		$out->tpl = '404_error.tpl';
		$out->smarty['txt'] = $txt;
		return $out;
	
	}
	/**
     * 	GENEROWANIE WPROWADZANIA OFERTY PRZEZ 
     * 	KONTRAHENTA
     * 	public zapytania_ofertoweOutLink()
     * @return <type>
     */
	
	public function zapytania_ofertoweOutLink(){
		$out= new stdClass;
		$out->smarty['Tokenx']= $this->Main->GetRouter()->params->token;
		if(!isset($_SESSION['dostawca_user_id'])){
			// session_destroy();
			$_SESSION['last_oferta_token'] = $out->smarty['Tokenx'];
			header("Location: ".Main::GetUrl());
			// return false;
		}
		/*				CZY PARTNER JEST ZALOGOWANY i czy to jego token		*/
		if(isset($_SESSION['dostawca_user_id']) && count($xi = $this->Db->Select("SELECT a.*,b.nazwa as SDj FROM zapytania_ofertowe_dostawcy a,zapytania_ofertowe b WHERE `a`.`token`='{$out->smarty[Tokenx]}' and a.`idd`='{$_SESSION[dostawca_user_id]}' and b.id=a.idz"))){
		
				/*				WPROWADZANIE DANYCH / EDYCJA DANYCH 		*/
	if(isset($_POST['token'])){
		// if($this->Db->Select("SELECT count(id) as ile FROM oferty_produkty WHERE id_order=(SELECT id FROM oferty WHERE token='{$out->smarty[Tokenx]}'")->{0}->ile == 0){
		/*		JEZELI DOSTAWCA DODAJE PIERWSZY RAZ  	*/
	$date = new DateTime($_POST[data_dostarczenia]);
	$idPO = $this->Db->Insert('oferty',$pdf->offer = array('idd'=>$_SESSION[dostawca_user_id],'token'=>$xi->{0}->token,'date_add'=>time(),'idz'=>$xi->{0}->idz,'koszt_dostawy'=>$_POST[koszt_dostawy],'czas_realizacji'=>$date->format('U'),'status'=>1,'blokada'=>1,'notatki_1'=>$_POST[notatka]),'id');
		$pdf->offer->time_normal = $_POST[data_dostarczenia];
		// $pdf->offer->time_normal = $pdf->offer->time_normal->format('d-m-Y');
		for($x=0;$x<count($_POST[pro_on]);$x++){
			$this->Db->Insert('oferty_produkty',array('id_order'=>$idPO,'id_pro'=>$_POST['pro_on'][$x],'ilosc'=>$_POST['ilosc'][$x],'cena'=>$_POST['cena_netto1'][$x],'cena_all'=>$_POST['cena_netto2'][$x]));
		}
	$pdf->zapyt=$this->Db->Select("select * from zapytania_ofertowe where id='".$xi->{0}->idz."'")->{0};
	$pro = $this->Db->Select("SELECT i.ilosc,i.cena,i.cena_all,a.nazwa,a.id as id_PRO,p.nazwa_pliku,p.dir_file,  a.token as token_PRO FROM magazyn a 
		LEFT JOIN oferty_produkty i ON a.id=i.id_pro 
		LEFT JOIN pliki p ON a.token=p.id_tag 
			WHERE i.id_order='{$idPO}'");
	/*		INFOEMACJA W BOXIE INFORMACYJNYM 		*/
	$usParnData = $this->Db->Select('select a.* from kontrahenci a where a.id='.$_SESSION[dostawca_user_id])->{0};
	$this->Db->Insert('wiadomosci_alerty',array('temat'=>'Nowa wycena zapytania ofertowego','text'=>'Kontrahent <b>'.$usParnData->nazwa.'</b> uzupełnił wycenę zapytania ofertowego <b>'.$xi->SDj.'</b>','date_add'=>time(),'type'=>'add_new_price','idd'=>$_SESSION[dostawca_user_id]));
    /**
     *		GENEROWANIE PDF'A I WYSYŁANIE NA EMAIL
     *
     */
	
	$token=xhash();
	$Main = Main::getInst();
	$pdf_smarty = $Main->Smarty;
	$Main->SetSmartyData();
	$pdf->user = $this->Db->Select("SELECT * FROM `kontrahenci` a LEFT OUTER JOIN pliki ON a.token = pliki.id_tag WHERE a.id='{$_SESSION[dostawca_user_id]}'")->{0};
	$pdf->token_off = $token;
	
	$pdf_smarty->assign('pdf',$pdf);
	$pdf->listPro=$pro;
	foreach($pdf as $k=>$w){
		$pdf_smarty->assign($k,$w);
	}
	Main::getInst()->SetSmartyData();
	$pdf_cont = $pdf_smarty->fetch(
		$this->Main->config->dir->pdf_themes.'/'.
		$this->Db->Select("select file from szablony_pdf where id=4")->{0}->file);
	$dile = $this->Main->iPdf->GenerowaniePDF($this->Main->config->dir->pdf_generator.'/'.$token.'.pdf',$pdf_cont,$pdf);
	$this->Main->eMail->SendEmail(array(
		'id_mail'=>5,'email'=>$pdf->user->email,'to'=>$pdf->user->nazwa,
		'file'=>$dile,
		'pdf'=>$pdf,
		'file_name'=>$token.'.pdf','token'=>$token
	));
	$out->smarty['alert_text'] = 'Wycena została zapisana poprawnie. Potwierdzenie wraz z linkiem do pliku pdf znajduje się w wiadomości email wysłanej na twój adres. Pozdrawiamy!';
		// }
		
		$out->tpl = 'dostawca_oferta.tpl';
		
	}
				/*			LISTING I WYŚWIETLANIE WPROWADZONYCH DANYCH		*/
				$xi=$xi->{0};
				$out->smarty['iSx']=$this->Main->GetZapytanieData($xi->idz);
				$out->smarty['iSo']=$this->Db->Select("select * from oferty WHERE token='{$out->smarty[Tokenx]}'")->{0};
				// pre($out->smarty['iSo']);
				$sql = $this->Db->Select("SELECT od.cena as cena_ret, od.cena_all as cena_all_ret ,  a.*,magazyn.token as xTokens ,magazyn.id as id_PRodkd,magazyn.nazwa as nazwa_prodsk ,k.nazwa as kategoriss , a.ilosc as ileTrza FROM `zapytania_ofertowe_produkty` a LEFT OUTER JOIN magazyn ON a.id_pro=magazyn.id LEFT OUTER JOIN kategorie_magazynu k ON magazyn.kategoria=k.id LEFT JOIN oferty_produkty od ON od.id_pro=magazyn.id  WHERE  a.id_zapytanie='{$xi->idz}' and od.id_order='{$out->smarty[iSo]->id}' group by od.id_pro");
				
				$sql = $this->Db->Select("SELECT i.ilosc,i.cena,i.cena_all,a.subiekt_tw_id , pi.jm, a.nazwa,a.id as id_PRO,p.nazwa_pliku,p.dir_file,  a.token as token_PRO FROM magazyn a 
		LEFT JOIN oferty_produkty i ON a.id=i.id_pro 
		LEFT JOIN pliki p ON a.token=p.id_tag 
		LEFT JOIN zapytania_ofertowe_produkty pi ON pi.id_pro=i.id_pro 
			WHERE i.id_order='{$out->smarty[iSo]->id}'");
				/*			LISTOWANIE WSZYSTKICH PRODUKTÓW Z ZAPYTANIA 	*/
				$x=0;
				foreach($sql as $i){
					$out->smarty['listPro']->{$x} = $i;
					$out->smarty['listPro']->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$i->xTokens}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
				$x++;
				}
				
					$Pdid = $this->Db->Select("select * FROM oferty_produkty WHERE id_order=(SELECT id FROM oferty WHERE token='{$out->smarty['Tokenx']}')");
					foreach($Pdid as $xiZ){
						$out->smarty['dane_prod_value'][$xiZ->id_pro] = array('ilosc'=>$xiZ->ilosc, 'cena'=>$xiZ->cena,'cena_all'=>$xiZ->cena_all);
					}
					
		if($t=$this->Db->Select("SELECT count(id) as ile FROM oferty_produkty WHERE id_order=(SELECT b.id FROM oferty b WHERE b.token LIKE '%".$this->Main->GetRouter()->params->token."%') GROUP BY oferty_produkty.id")->{0}->ile == 0){
			// echo $t;
			// return $out;
			$out->tpl = 'zapytanie_front.tpl';
		}else{
			$out->tpl = 'dostawca_oferta.tpl';
			// return $out;
		}
			
			}else{
				return $this->ShowErrorSite('Podałeś błedny link lub indentyfikator nie jest przydzielony do twojego konta');
			}
			if(count($out->smarty[listPro]) == 0){
					$out->smarty[listPro] = $this->GetProForRequest($xi->idz);
				}
		return $out;
	
	}
	private function GetSupplierList($ids,$act=null){
	if($act == 1){
		$act = " AND aktywny=1";
	}
	$x=0;
	$out=new stdClass;
	foreach($this->Db->Select("select * from kontrahenci WHERE id IN(".$ids.") {$act} ORDER BY id DESC") as $i){
		$out->smarty[listaK]->{$x}= $i;
		$out->smarty[listaK]->{$x}->adres = $i->ulica.' '.$i->nr_dom.'/'.$i->nr_lok.'<BR>'.$i->kod_pocztowy.' '.$i->miejscowosc;
			foreach($this->Db->Select("SELECT nazwa FROM grupy WHERE `id` IN(".implode(',',json_decode($i->grupa)).")") as $g){
				$out->smarty[listaK]->{$x}->grupaX.=li($g->nazwa);
			}
			$x++;
	}
	return $out->smarty[listaK];
	}
	private function GetProductsList($ids){
	$sql = $this->Db->Select("select a.*,b.nazwa as kategoria_name from magazyn a, kategorie_magazynu b WHERE a.kategoria=b.id AND a.id IN(".$ids.")");
	// pre("select a.*,b.nazwa as kategoria_name from magazyn a, kategorie_magazynu b WHERE a.kategoria=b.id AND b.id IN(".$ids.")");
	$x=0;
	
	foreach($sql as $i){
		$listPro->{$x} = $i;
		$t=$this->Db->Select("Select imie,nazwisko FROM users_admin WHERE id='{$i->uzytkownik_nadzorujacy}'")->{0};
		$dostawcy_list=$this->Db->Select("select a.* FROM material_supplier b,kontrahenci a WHERE a.id=b.supplier_id and b.material_id='{$i->id}'");
		if(count($dostawcy_list)>0){
			$listPro->{$x}->dostawcy_list='<ul>';
		foreach($dostawcy_list as $j){
			$listPro->{$x}->dostawcy_list.=li($j->nazwa);
		}
			$listPro->{$x}->dostawcy_list.='</ul>';
		}
		$listPro->{$x}->pmx = $t->imie.' '.$t->nazwisko;
		$listPro->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$i->token}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
		
	$x++;
	}
	return $listPro;
	}
	
	
	
	public function zapytania_ofertoweNoweZapytanie(){
		return $this->{'zapytania_ofertoweNoweZapytanie_krok'.((!isset($_POST[krok])) ? 1 : $_POST[krok]+1)}();
	}
	/**
	*	GENEROWANIE PLIKÓW PDF 
	*	WYSYŁANIE ICH NA EMAIL
	*	ZAPIS OFERTY
	*
	**/
	public function zapytania_ofertoweNoweZapytanie_krok4(){
		$out = new stdClass;
		$out->pdf_list = Main::GetPdfOffertTemplates();
		$out->smarty[token] = ((!isset($_POST[token])) ? xhash() : $_POST[token]);
		$out->smarty[krok] = ((!isset($_POST[krok])) ? 1 : (int)$_POST[krok]+1);
		$Termin = new DateTime($_POST['termin_end']);
		$Termin2 = new DateTime($_POST['termin']);
		
		$ido = $this->Db->Insert('zapytania_ofertowe',array(
			'nazwa'=>$_POST['nazwa'],
			'data_add'=>time(),
			'termin_skladania_ofert'=>$Termin->format('U'),
			'termin_dostarczenia'=>$Termin2->format('U'),
			'token'=>$_POST['token'],
			'szablon_pdf'=>$_POST['pdf_theme'],
			'status'=>1,
			'aktywne'=>1,
			'id_user'=>$_SESSION['user_id']
		),'id');
		$pdf = new StdClass;
		for($x=0;$x<count($_POST['pro']);$x++){
			$pdf->pro->{$x} =  $this->Db->Select("SELECT *,a.opis as opis_produktu, a.id as id_prod FROM `magazyn` a LEFT OUTER JOIN pliki ON a.token = pliki.id_tag WHERE a.id='{$_POST[pro][$x]}'")->{0};
			$pdf->pro->{$x}->jedn = $_POST['jedn'][$x];
			$pdf->pro->{$x}->ilosc = $_POST['ilosc'][$x];
			$this->Db->Insert('zapytania_ofertowe_produkty',array(
				'id_zapytanie'=>$ido,
				'id_pro'=>$_POST[pro][$x],
				'ilosc'=>$_POST['ilosc'][$x],
				'jm'=>$_POST['jedn'][$x],
				'token'=>$_POST['token']
			));
			$out->smarty['pdf_prod']->{$x} = $pdf->pro->{$x};
			$out->smarty['pdf_prod']->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$pdf->pro->{$x}->token}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
		}
		
		foreach($_POST as $k=>$w){
			if(!is_array($w)){
				$pdf->{$k} = $w;
			}
		}
        /**
         *	GENEROWANIE PDF DLA KAŻDEGO DOSTAWCY
         *	WYSYŁANIE NA EMAIL ZAPYTANIA
         *	ZAPIS DO BAZY DANYCH 
         *
         *
         */
		
		foreach($_POST['dost'] as $i){
			unset($pdf_smarty);
			$token=xhash();
			$Main = Main::getInst();
			$pdf_smarty = $Main->Smarty;
			$Main->SetSmartyData();
			$pdf->user = $this->Db->Select("SELECT * FROM `kontrahenci` a LEFT OUTER JOIN pliki ON a.token = pliki.id_tag WHERE a.id='{$i}'")->{0};
			$pdf->token_off = $token;
			$pdf_smarty->assign('pdf',$pdf);
			foreach($pdf as $k=>$w){
				$pdf_smarty->assign($k,$w);
			}
			$pdf_cont = $pdf_smarty->fetch($out->pdf_list[$_POST['pdf_theme']]->file);
			$dile = $this->Main->iPdf->GenerowaniePDF($this->Main->config->dir->pdf_generator.'/'.$token.'.pdf',$pdf_cont,$pdf);
			$this->Main->eMail->SendEmail(array(
				'id_mail'=>1,'email'=>$pdf->user->email,'to'=>$pdf->user->nazwa,
				'file'=>$dile,
				'file_name'=>$token.'.pdf','token'=>$token
			));
			$out->smarty['send_pdf'][$i]->user =$pdf->user;
			$out->smarty['send_pdf'][$i]->name_file = _DIR_ . $this->Main->config->dir->pdf_generator.'/'.$token.'.pdf';
			$out->smarty['send_pdf'][$i]->token = $token;
			
			$this->Db->Insert('zapytania_ofertowe_dostawcy',array(
				'idz'=>$ido,
				'idd'=>$i,
				'token'=>$token,
				'status'=>'1',
				'email'=>$pdf->user->email,
				'data_add'=>time(),
				'pdf'=>$token.'.pdf'
			));
		} 
		foreach($_POST as $k=>$c){
			$out->smarty[$k] = $c;
		}
		$out->smarty[pro] = $this->GetProductsList($_POST['pro']);
		$out->smarty[theme] = $out->pdf_list[$_POST['pdf_theme']];
		$out->tpl =  'zapytania_ofertoweNoweZapytanie4.tpl';
		
		
		return $out;
	
	}
	public function zapytania_ofertoweNoweZapytanie_krok3(){
	if(count($_POST['dost'])){
		$out = new stdClass;
		$out->smarty[token] = ((!isset($_POST[token])) ? xhash() : $_POST[token]);
		$out->smarty[krok] = ((!isset($_POST[krok])) ? 1 : (int)$_POST[krok]+1);
		$out->smarty[pro] = $this->GetProductsList($_POST['pro']);
		$out->smarty[data] = date('d-m-Y');
		$out->smarty[pdf_list] = Main::GetPdfOffertTemplates(true);
		$out->smarty[listaK] = $this->GetSupplierList(implode(',',$_POST['dost']),1);
		
		$out->tpl = 'zapytania_ofertoweNoweZapytanie3.tpl';
		return $out;
		}else{
			return $this->zapytania_ofertoweNoweZapytanie_krok2();	
		}
	}
	public function zapytania_ofertoweNoweZapytanie_krok2(){
	if(Count($_POST['pro'])){
		$out = new stdClass;
		$out->smarty[token] = ((!isset($_POST[token])) ? xhash() : $_POST[token]);
		$out->smarty[krok] = ((!isset($_POST[krok])) ? 1 : (int)$_POST[krok]+1);
		$out->smarty[pro] = implode(',',$_POST['pro']);
		$x=0;
			foreach($this->Db->Select("select * from kontrahenci WHERE aktywny=1 ORDER BY id DESC") as $i){
	$out->smarty[listaK]->{$x}= $i;
	$out->smarty[listaK]->{$x}->adres = $i->ulica.' '.$i->nr_dom.'/'.$i->nr_lok.'<BR>'.$i->kod_pocztowy.' '.$i->miejscowosc;
		foreach($this->Db->Select("SELECT nazwa FROM grupy WHERE `id` IN(".implode(',',json_decode($i->grupa)).")") as $g){
			$out->smarty[listaK]->{$x}->grupaX.=li($g->nazwa);
		}
		$x++;
}
	
		
		
		$out->tpl = 'zapytania_ofertoweNoweZapytanie2.tpl';
		return $out;
	}else{
		return $this->zapytania_ofertoweNoweZapytanie();
	}
	}
	public function zapytania_ofertoweNoweZapytanie_krok1(){
	$out = new stdClass;
	$out->smarty[token] = ((!isset($_POST[token])) ? xhash() : $_POST[token]);
	$out->smarty[krok] = ((!isset($_POST[krok])) ? 1 : (int)$_POST[krok]+1);
	$sql = $this->Db->Select("select a.*,b.nazwa as kategoria_name from magazyn a, kategorie_magazynu b WHERE a.kategoria=b.id and a.aktywny=1 ORDER BY b.nazwa ASC");
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
			$out->smarty['listPro']->{$x}->pmx = $t->imie.' '.$t->nazwisko;
			$out->smarty['listPro']->{$x}->imgx = '<img src="'.Main::GetUrl().'data/uploader/produkty/'.$this->Db->Select("Select nazwa_pliku,opis FROM pliki WHERE id_tag='{$i->token}' ORDER by id ASC")->{0}->nazwa_pliku.'" class="img_pro_list img-polaroid"/>';
			
		$x++;
		}
	$out->smarty[title] = 'nowe-zapytanie';
	$out->tpl =  'zapytania_ofertoweNoweZapytanie.tpl';
		return $out;
}

	// public function zapytania_ofertoweOdpowiedzi(){
		// $out = new stdClass;
		// $out->smarty[title] = 'odpowiedzi';
		// $out->tpl =  'zapytania_ofertoweOdpowiedzi.tpl';
		// return $out;
	// }


 public function zapytania_ofertoweZarzadzanie(){
	$out = new stdClass;
	$dost = $this->GetAllSupplier();
	$x=0;
	foreach($this->Db->Select("SELECT * FROM zapytania_ofertowe ORDER BY id DESC") as $i){
		$dkd->{$x} = $i;
		$dkd->{$x}->data_add = date('d-m-Y G:i:s',(int)$i->data_add);
		foreach($this->Db->Select("SELECT a.id_pro,b.nazwa FROM zapytania_ofertowe_produkty a,magazyn b WHERE a.id_zapytanie='{$i->id}' and a.id_pro=b.id") as $kd){
			$dkd->{$x}->prodS.= li($kd->nazwa);
		}
		foreach($this->Db->Select("SELECT idd FROM zapytania_ofertowe_dostawcy WHERE idz='{$i->id}'") as $k){
			$dkd->{$x}->dostawcyX.=li($dost[(int)$k->idd]);
		}
		$x++;
	}
	$out->smarty[listaZap] = $dkd;
	$out->smarty[title] = 'zarzadzanie';
	$out->tpl =  'zapytania_ofertoweZarzadzanie.tpl';
		return $out;
}

	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
}