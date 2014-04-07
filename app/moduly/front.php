<?php
Class FrontModules{
	public function __construct(){
		$this->Main = Main::GetInst();
		$this->Smarty = $this->Main->Smarty;
		$this->Db = $this->Main->Db;
	}
	public function HookTopFront(){
		return $this->Main->FetchSmarty('mod/belka_top.tpl');
	}
	public function HookLeftFront(){
		$this->Main->Smarty->assign('prodx',Main::GetProducts());
		return $this->Main->FetchSmarty('mod/left.tpl');
	}
	public function menuFront(){}
	public function MenuUser(){}
	public function modulNavBar(){
		
		return $this->Main->FetchSmarty('mod/nav_bar.tpl');
	}
	
	public function menuLeft(){
		$data = json_decode(file_get_contents(dirname(__FILE__).'/menu_top.json'), true);
		$data2 = array(
			array(
				'url'=>'panel_crm',
				'name'=>'Start',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/kategorie_produktow',
				'name'=>'Kategorie',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/produkty',
				'name'=>'Produkty',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/klienci',
				'name'=>'Klienci',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/zamowienia',
				'name'=>'Zamówienia',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/projekty',
				'name'=>'Projekty',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/zadania',
				'name'=>'Zadania',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/materialy',
				'name'=>'Materiały i pliki',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/wyceny',
				'name'=>'Wyceny',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'', 
			),array(
				'url'=>'panel_crm/plan-pracy',
				'name'=>'Plan Pracy',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'name'=>'Raport prac',
				'url'=>'panel_crm/raporty',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'name'=>'Wyloguj',
				'url'=>'panel_crm/wyloguj',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			)
		);
		$this->Main->Smarty->assign('MenuLeft',$data);
		return $this->Main->FetchSmarty('mod/menu_left.tpl');
	}
	
	public function MenuTop(){
		$data = array(
			array(
				'url'=>'panel_crm',
				'name'=>'Start',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/kategorie_produktow',
				'name'=>'Kategorie',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/produkty',
				'name'=>'Produkty',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/klienci',
				'name'=>'Klienci',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/zamowienia',
				'name'=>'Zamówienia',
				'active'=>'0',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/projekty',
				'name'=>'Projekty',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/zadania',
				'name'=>'Zadania',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/materialy',
				'name'=>'Materiały i pliki',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/wyceny',
				'name'=>'Wyceny',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'url'=>'panel_crm/plan-pracy',
				'name'=>'Plan Pracy',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'name'=>'Raport prac',
				'url'=>'panel_crm/raporty',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			),array(
				'name'=>'Wyloguj',
				'url'=>'panel_crm/wyloguj',
				'active'=>'',
				'id'=>'',
				'class'=>'',
				'icon'=>'',
			)
		);
		$this->Main->Smarty->assign('MenuTop',$data);
		return $this->Main->FetchSmarty('mod/menu_top.tpl','MenuTop',$data);
	}
}