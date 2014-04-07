<?php
Class ShopModule{
	public function __construct(){
		$this->name = 'ShopModule';
		$this->tab = 'frontend';
		$this->filename = 'shop.php';
		$this->classname = __CLASS__;
		$this->config  = Main::GetStaticConfig();
		$this->module_tpl_dir = $this->config->theme->dir.$this->config->theme->modules.'/';
		$this->Main = Main::GetInst();
		$this->Smarty = $this->Main->Smarty;
		$this->Db = $this->Main->Db;
	}
	public function BreadCrumbLinks(){
		return $this->Main->FetchSmarty($this->module_tpl_dir.'breadcrumb_links.tpl');
	}
	public function HomeKlocki(){
		return $this->Main->FetchSmarty($this->module_tpl_dir.'home_klocki.tpl');
	}
	public function MenuListLeft(){
	
		return $this->Main->FetchSmarty($this->module_tpl_dir.'menu_list_left_front.tpl');
	}
	public function HeaderTopNav(){
		$HeaderTopNav['koszyk'] = array(
			'suma_kosztow'=>(float)round(5.494,2),
			'ilosc_produktow'=>0
		);
		$HeaderTopNav['menu'] = array(
			'0'=>array(
				'id'=>'home',
				'active'=>0,
				'href'=>'start',
				'name'=>'Strona główna'
			),
			'1'=>array(
				'id'=>'ipsshop',
				'active'=>0,
				'href'=>'panel',
				'name'=>'Panel zarządzania'
			),
			'2'=>array(
				'id'=>'panel_klienta',
				'active'=>0,
				'href'=>'panel_klienta',
				'name'=>'Panel klienta'
			),
			'3'=>array(
				'id'=>'zamowienia',
				'active'=>0,
				'href'=>'sklep/zamowienia',
				'name'=>'Historia zamówień'
			)
		);
		$this->Main->Smarty->assign('HeaderTopNav',$HeaderTopNav);
		return $this->Main->FetchSmarty($this->module_tpl_dir.'header_top_nav.tpl');
	}
	public function HeaderMenu(){
		$HeaderMenu = $this->Db->Select("select * from produkty_kategorie where aktywny=1 and id_parent=0 ORDER BY sort ASC");
	
		$this->Main->Smarty->assign('HeaderMenu',$HeaderMenu);
		return $this->Main->FetchSmarty($this->module_tpl_dir.'header_top_menu.tpl');
	}
	public function HomeSlider(){
		return $this->Main->FetchSmarty($this->module_tpl_dir.'home_slider.tpl');
	}
	public function ProductsList(){
		$this->Main->Smarty->assign('proListModFront',System::ListaProduktow());
		return $this->Main->FetchSmarty($this->module_tpl_dir.'product_list.tpl');
	}
}