<?php
Class System{
	
	/**
	 * Wysy³a ¿¹danie i konwertuje odpowiedŸ do postaci tablicy je¿eli podano format w jakim przyjdzie
	 *
	 * @param string $akcja Kod akcji, dostêpne: 'nowa','potwierdz','anuluj','status','typy'
	 * @param array $dane Tablica z danymi do wys³ania
	 * @param string $formatOdpowiedzi Format w jakim ma przyjœæ odpowiedŸ - 'xml' lub 'txt', domyœlnie puste
	 *
	 * @return array|string
	 */
	protected function wyslijZadanie($akcja, $dane, $formatOdpowiedzi = '')
	{
		$zadanie = curl_init();

		if ( ! $zadanie)
		{
			trigger_error('Inicjalizacja CURL nie powiodla sie', E_USER_WARNING);
			return;
		}

		curl_setopt_array($zadanie, array(
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $this->url($akcja, $formatOdpowiedzi),
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1, // zwracac to co przyszlo z serwera
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_CONNECTTIMEOUT => 6, // polaczenie moze zajac maksymalnie sekund
			CURLOPT_TIMEOUT => 7, //curl moze pracowac maksymalnie przez sekund
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => http_build_query($dane)
		));

		if ( ! $wynik = curl_exec($zadanie))
		{
			trigger_error('Blad CURL:'.curl_error($zadanie), E_USER_WARNING);
			return;
		}

		curl_close($zadanie);

		if ($formatOdpowiedzi == 'txt')
		{
			$wynik = $this->txt2array($wynik);
		}
		elseif ($formatOdpowiedzi == 'xml')
		{
			$wynik = $this->xml2array($xml);
		}

		return $wynik;
	}
	public static function GetImagesPro($id){
		return Db::GetInst()->Select("select * from produkty_zdjecia WHERE idp={$id}");
	}
	public static function ListaProduktow($where=null){
		$sql = "SELECT a.*,b.nazwa as kategoria_name FROM produkty a, produkty_kategorie b WHERE a.kategorie=b.id {$where}";	
		foreach(Db::GetInst()->Select($sql) as $i){
			$dane[$i->id] = $i;
			$dane[$i->id]->img = self::GetImagesPro($i->id);
		}
	return $dane;
	
	}
	public static function UsunZdjeciaProduktu($idp=null){
		if($idp !== null){
			$db = Db::GetInst();
			
			foreach($db->Select('select * from produkty_zdjecia where idp='.$idp) as $i){
				$file = $i->dirname.$i->filename;
				unlink($file);
				$db->Delete('produkty_zdjecia','id='.$i->id);
			}
		}
	}
	public static function ZrobObject($dane){
			foreach($dane as $i=>$k){
				$wynik->{$i} = $k;
			}
		return $wynik;
		
	}
	public static function ZrobTabliceSelect($dane,$ar=array()){
		if(count($dane)){
			foreach($dane as $i){
				$wynik[$i->{$ar[0]}] = $i->{$ar[1]};
			}
		return $wynik;
		}
	}
	public static function TestFTP($host,$port,$user,$pass,$pas=true,$folder='/',$sql=false){
		
		try {
			$ftp = new FTPClient();
			$ftp->connect($host,$sql,$port,30);
			$ftp->login($user, $pass);
			$ftp->passive($pas);
			if($folder !== '/'){
				$ftp->changeDirectory($folder);
			}
			return array('list'=>$ftp->listDirectory());
		} catch (Exception $e) {
			return array('error'=>$e);
		}
	
	}
	public static function GenerowaniePlikuZip($plik,$dir,$STARTdir=null,$chmod=null){
		$zip = new ZipArchive;
			if($zip->open($plik, ZipArchive::CREATE) === TRUE){
				self::DodajFolderDoPlikuZIP($dir,$zip,$STARTdir);
				$zip->setArchiveComment(date('d-m-Y G:i:s').' - Plik wygenerowano przy urzyciu oprogramowania MojaPresta.pl . Wszystkie prawa zastrze¿one! ');
			}
	}
	public static function DodajFolderDoPlikuZIP($dir, $zipArchive, $zipdir = ''){
		if(is_dir($dir)){
			if($dh = opendir($dir)){
				if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);
				while (($file = readdir($dh)) !== false){
					if(!is_file($dir . $file)){
						if( ($file !== ".") && ($file !== "..")){
							self::DodajFolderDoPlikuZIP($dir .'/'. $file . "/", $zipArchive, $zipdir .'/'. $file);
						}
					}else{
						$zipArchive->addFile($dir .'/'. $file, $zipdir .'/'. $file);  
					}
				}
			}
		}
	}
	public static function LaczenieTablic($tab,$tab2){
		if(is_array($tab) && is_array($tab2)){
			$x=0;
			foreach($tab as $k){
				$dane[$x][$k] = $tab2[$x];
			$x++;
			}
		return $dane;
		}
	}
	public static function ObiektDwuwymiarowy($tab){
		if(count($tab)){
			$wynik=new stdClass;
			foreach($tab as $i){
				$wynik->{$i} = $i;
			}
		}
		return $wynik;
	}
	
	public static function TablicaDwuwymiarowa($tab){
		if(count($tab)){
			foreach($tab as $i){
				$wynik[$i] = $i;
			}
		}
		return $wynik;
	}
	

}