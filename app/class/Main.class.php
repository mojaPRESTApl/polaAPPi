<?php
function pre($d){	echo '<pre>';	print_r($d);	echo '</pre>';}
class Main{
	public static $errors=array();
	public static $view;
	public static $class;
	private static $oInstance = false;
	public static function GetConfig($file){
		$dir = _DIR_.'app/class/';
		require_once($dir .'Spyc.class.php');
		require_once(_DIR_ .'app/helpers/smarty_functions.php');
		return json_decode(json_encode(Spyc::YAMLLoad($file)));	
	}
	public function __construct(){
		$this->__set('config',self::GetConfig(_CONFIG_));
			if(count($this->config->app_class)){
				foreach($this->config->app_class as $classs=>$file){
					require_once( $this->config->dir->{'class'}. '/' . $file);
					self::$class->$classs = new $classs();
					$this->__set($classs,self::$class->$classs);
				}
			}
	}
	
	public function GetUrl(){
		return 'http://'.$_SERVER[HTTP_HOST].'/';
	}
	public function SetSmartyData2(){
		$sm = new stdclass;
		$this->Smarty->addTemplateDir($this->config->dir->themes);
		$sm->Router = $this->PolaRouter->GetRouterData();
		$this->Smarty->template_dir		= $this->config->dir->themes;
		$this->Smarty->compile_dir		= $this->config->dir->cache_dir;
		$this->Smarty->cache_dir		= $this->config->dir->cache_dir;
		// $sm->icons = new stdClass;
		
		foreach($this->config as $i=>$ii){
			$sm->konfiguracja->{$i}=$ii;
		}
		foreach(self::$view as $k=>$w){
			$sm->{$k}=$w;
		}
		// $sm->konfiguracja->typ_konta_front = array('1'=>'Osoba fizyczna','2'=>'Firma');
		// $sm->konfiguracja->tak_nie = array('1'=>'Tak','0'=>'Nie');
		// $sm->konfiguracja->statusy_zlecen = $this->config->statusy_zamowien->status;
		// $sm->konfiguracja->statusy_zlecen_color = $this->config->statusy_zamowien->color;
		// $t = $this->Db->Select("Select * FROM szablony_hooks WHERE id_szablon='{$this->kontroler->akcje->wyglad_tpl}'");
		// if(count($t)){
			// foreach($t as $i){
				// if($i->link_to_modules !== ''){
					// $sms[$i->smarty_var_name][]=$i->link_to_modules;
				// }
			// }
		// }
		
		foreach($theme as $k=>$w){
			$this->Smarty->assign($k,$w);
		}
		foreach($sm as $k=>$w){
			$this->Smarty->assign($k,$w);
		}
		foreach($sms as $k=>$w){
			$this->Smarty->assign($k,$w);
		}
		foreach(explode('/',$this->config->statusy_zamowien->jednostki_miary) as $i){
			$jd[$i] = $i;
		}
		$this->Smarty->assign('jednostki',$jd);
		$this->Smarty->assign('session',$_SESSION);
		
		return $sm;
	}
	

	public function SetSmartyData(){
		$sm = new stdclass;
		$this->__IncludingClasses();
		foreach($this->config->smarty_functions as $s_name=>$s_func){
			$this->Smarty->unregisterPlugin("function",$s_name);
			$this->Smarty->registerPlugin("function",$s_name, $s_func);
		}
		
		$sm->Router = $this->PolaRouter->GetRouterData();
		
		$this->Smarty->addTemplateDir($this->config->dir->themes);
		$sm->url = $this->GetUrl();
		$sm->css = $sm->url.$this->config->dir->themes->css;
		$sm->js = $sm->url.$this->config->dir->themes.'/js/';
		$sm->img = $sm->url.$this->config->dir->themes.'/img/';
		// $this->Smarty->addTemplateDir($theme->dir);
		$this->Smarty->template_dir		= $this->config->dir->themes;
		$this->Smarty->compile_dir		= $this->config->dir->templates_cache;
		$this->Smarty->cache_dir		= $this->config->dir->cache_dir;
		$sm->icons = new stdClass;
		$sm->icons->edit = $sm->img.'edit.png';
		$sm->debug_info->kontroler = $this->kontroler;
		
		foreach($this->GetConfig() as $i=>$ii){
			$sm->konfiguracja->{$i}=$ii;
		}
		foreach(self::$view as $k=>$w){
			$sm->{$k}=$w;
		}
		pre($this->config);
		// $sm->konfiguracja->typ_konta_front = array('1'=>'Osoba fizyczna','2'=>'Firma');
		// $sm->konfiguracja->tak_nie = array('1'=>'Tak','0'=>'Nie');
		// $sm->konfiguracja->statusy_zlecen = $this->config->statusy_zamowien->status;
		// $sm->konfiguracja->statusy_zlecen_color = $this->config->statusy_zamowien->color;
		// $t = $this->Db->Select("Select * FROM szablony_hooks WHERE id_szablon='{$this->kontroler->akcje->wyglad_tpl}'");
		if(count($t)){
			foreach($t as $i){
				if($i->link_to_modules !== ''){
					$sms[$i->smarty_var_name][]=$i->link_to_modules;
				}
			}
		}
		
		foreach($theme as $k=>$w){
			$this->Smarty->assign($k,$w);
		}
		foreach($sm as $k=>$w){
			$this->Smarty->assign($k,$w);
		}
		foreach($sms as $k=>$w){
			$this->Smarty->assign($k,$w);
		}
		foreach(explode('/',$this->config->statusy_zamowien->jednostki_miary) as $i){
			$jd[$i] = $i;
		
		}
		$this->Smarty->assign('jednostki',$jd);
		$this->Smarty->assign('session',$_SESSION);
		return $sm;
	}
	
public function __IncludingClasses(){
		$this->config->routers = $this->PolaRouter->GetRouterData();
		// $this->kontroler = Controllers::GetInst()->ControllerData($this->config->routers[target]);
	}

	public Static function GetStaticConfig(){
		$Main = Main::GetInst();
		$Main->config->theme =  $Main->Db->Select("select * from `szablony` where id='{$Main->kontroler->akcje->wyglad_tpl}'")->{0};
		return $Main->config;
	}
	public static function GetRouter(){
		$PolaRouter = new PolaRouter;
		$t = $PolaRouter->GetRouterData();
			foreach($t as $k=>$w){
				$tt->{$k} = (object)$w;
			}
			return $tt; 
	}
	public function AddSmartyObject(){
		
	}
	public function Cat($id){
		return $this->Db->Select("select name from category where id='{$id}'")->{0}->name;
	}
	
	
	public function RunAJAX(){
		$func = 'Ajax_'.$_REQUEST['action'];
		$ts=$this->iController->RunAction('indexController',$func);
		echo json_encode($ts);
	}
	public function StartSystem(){
		$this->config->routers = $this->PolaRouter->GetRouterData();
		$this->SetSmartyData();
		if($this->config->routers[target][kontroler]){
			$t = $this->iController->RunAction($this->config->routers[target][class_name],$this->config->routers[target]['akcja']);
			if(isset($t->smarty)){
				foreach($t->smarty as $k=>$v){
						$this->Smarty->assign($k,$v);
				}
			}
			$this->Smarty->assign('MAIN',$this->Smarty->fetch($t->tpl));
			return $this->Smarty->fetch('index.tpl');
			
		}
	}
	public static function AV($key,$val){
		if(!isset(self::$view->$key)){
			self::$view->$key = $val;
		}
	}
	public function FetchSmarty($file,$dname=null, $data=null){
		$this->SetSmartyData();
		$themes_ = $this->Smarty->getTemplateDir();
		$i=$themes_[0];	$isset=0;
		foreach($themes_ as $i){
			if(!file_exists($i.$file)){
				$error[] = 'Brak pliku widoku. Lokalizacja: '.$i.$file;
			}else{
				$isset++;
			}
		}
		if($isset > 0){
		if(count($data)){
			if($data !==null || $data !== ''){
				$this->Smarty->assign($dname, $data);
			}else{
				$this->Smarty->assign($dname);
			}
		}
		return $this->Smarty->fetch($file);
		}else{
			foreach($error as $nds){
				$this->error(4,$nds);
				$this->Smarty->assign('errors',self::$errors);
			}
				$this->Smarty->addTemplateDir('public/themes/system/');
				return $this->Smarty->fetch('public/themes/system/errors.tpl');
		}
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
	
	public function Error($id, $text){
		self::$errors[$id] = $text;
	}
	
}

?>