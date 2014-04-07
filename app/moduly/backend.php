<?php
Class BackendModules extends Modules{
	public $list_function = array();
	public function __construct(){
		$this->Main = Main::GetInst();
		$this->list_function=array('MenuUser');
	}
	public function ProfilUserInfo(){
		if(isset($_SESSION['user_id'])){
			$t = $this->Main->getInst()->Db->Select("select * from users_admin WHERE id='{$_SESSION[user_id]}'")->{0};
			$d[__FUNCTION__] = array(
				'user_info'=>array(
					'avatar'=>'avatar/40x40/avatar5.png',
					'login'=>$t->login,
					'nazwa'=>$t->imie.' '.$t->nazwisko,
				),
				'wiadomosci'=>array(
					'ilosc'=>'0',
					'wiadomosci'=>array(),
				)
			);
		}elseif(isset($_SESSION[dostawca_user_id])){
			$t = $this->Main->getInst()->Db->Select("select * from kontrahenci WHERE id='{$_SESSION[dostawca_user_id]}'")->{0};
			$t->logotyp = 'data/uploader/kontrahenci/'.$t->logotyp;
			$d[__FUNCTION__] = array(
				'dostawca_info'=>$t
			);
			
			
		
		}
		return $d;
		
	}
	public function MenuUser(){
	if(isset($_SESSION['user_id'])){
		$d[__FUNCTION__] = array(array(
			'id'=>'x_start',
			'icon'=>'iconfa-home',
			'text'=>'Start',
			'id_akcji'=>'1',
			'children_id'=>array('1','2','3'),
			// 'children'=>array(
				// array(
					// 'id'=>'x_panel2',
					// 'icon'=>'iconfugue16-home',
					// 'text'=>'Panel główny',
					// 'id_akcji'=>'1'
				// ),
				// array(
					// 'id'=>'x_panel1',
					// 'icon'=>'iconfugue16-chart',
					// 'text'=>'Statystyki',
					// 'id_akcji'=>'2'
				// ),
				// array(
					// 'id'=>'x_panel3',
					// 'icon'=>'iconfugue16-information-button',
					// 'text'=>'Informacje',
					// 'id_akcji'=>'3'
				// ),
				
			// )
		),array(
			'id'=>'x_zapytania',
			'icon'=>'iconfa-share',
			'text'=>'Zapytania ofertowe',
			'id_akcji'=>'19','children_id'=>array('4','5','6','37','41'),
			'children'=>array(
				array(
					'id'=>'x_zapytania3',
					'icon'=>'iconfugue16-application-monitor',
					'text'=>'Zarządzanie zapytaniami',
					'id_akcji'=>'6'
				),	
				array(
					'id'=>'x_zapytania2',
					'icon'=>'iconfugue16-application-form',
					'text'=>'Lista Odpowiedzi',
					'id_akcji'=>'5'
				),	
				array(
					'id'=>'x_zapytania1',
					'icon'=>' iconfugue16-plus-circle',
					'text'=>'Dodaj nowe zapytanie',
					'id_akcji'=>'4'
				),
			)
		),array(
			'id'=>'x_uzytkownicy',
			'icon'=>'iconfa-user',
			'text'=>'Użytkownicy',
			'id_akcji'=>'18',
			'children_id'=>array('7','9','26','24'),
			'children'=>array(
				array(
					'id'=>'x_user1',
					'icon'=>' iconfugue16-user--plus',
					'text'=>'Dodaj nowego użytkownika',
					'id_akcji'=>'7'
				),
				array(
					'id'=>'x_user2',
					'icon'=>' iconfugue16-table',
					'text'=>'Lista użytkowników',
					'id_akcji'=>'9'
				),	
				// array(
					// 'id'=>'x_user3',
					// 'icon'=>' iconfugue16-clock',
					// 'text'=>'Logi',
					// 'id_akcji'=>'8'
				// )	
			)
		),array(
			'id'=>'x_kontrahenci',
			'icon'=>'iconfa-truck',
			'text'=>'Dostawcy towaru',
			'id_akcji'=>'17','children_id'=>array('10','11','12','30'),
			'children'=>array(
				array(
					'id'=>'x_user',
					'icon'=>' iconfugue16-user--plus',
					'text'=>'Dodaj nowego dostawcę',
					'id_akcji'=>'10'
				),
				array(
					'id'=>'x_user',
					'icon'=>' iconfugue16-table',
					'text'=>'Lista dostawców',
					'id_akcji'=>'11'
				),	
				array(
					'id'=>'x_user',
					'icon'=>'iconfugue16-star',
					'text'=>'Grupy dostawców',
					'id_akcji'=>'12'
				),	
			)
		),array(
			'id'=>'x_pro',
			'icon'=>' iconfa-th',
			'text'=>'Magazyn',
			'id_akcji'=>'magazyn','children_id'=>array('15','14','13','38'),
			'children'=>array(
				array(
					'id'=>'x_user',
					'icon'=>' iconfugue16-table',
					'text'=>'Zarządzaj magazynem',
					'id_akcji'=>'15'
				),
				array(
					'id'=>'x_user',
					'icon'=>'iconfugue16-box--plus',
					'text'=>'Dodaj produkt',
					'id_akcji'=>'14'
				),
				array(
					'id'=>'x_user',
					'icon'=>' iconfugue16-category',
					'text'=>'Kategorie produktów',
					'id_akcji'=>'13'
				)
			)
			
			
		),
		array(
			'id'=>'d_ustawienia',
			'icon'=>'iconfa-cogs',
			'text'=>'Ustawienia',
			'id_akcji'=>'39',
			'children_id'=>array('39','21','25'),
			'children'=>array(
				array(
					'id'=>'x_user',
					'icon'=>'iconfa-cogs',
					'text'=>'Konfiguracja systemu',
					'id_akcji'=>'39'
				),
				array(
					'id'=>'x_user',
					'icon'=>' iconfugue16-database--plus',
					'text'=>'Kopia bazy danych',
					'id_akcji'=>'21'
				),
				array(
					'id'=>'x_user',
					'icon'=>' iconfugue16-picture',
					'text'=>'Materiały',
					'id_akcji'=>'25'
				)	
				
			)),array(
			'id'=>'x_wyloguj',
			'icon'=>'iconfa-off',
			'text'=>'Wyloguj',
			'id_akcji'=>'16'
		));
		
	}
		else{
			$d[__FUNCTION__] = array(
		array(
			'id'=>'d_start',
			'icon'=>'iconfa-home',
			'text'=>'Start',
			'id_akcji'=>'1',
			'children_id'=>array('1')
				
			),
		array(
			'id'=>'d_zapytania',
			'icon'=>'iconfa-table',
			'text'=>'Twoje wyceny',
			'id_akcji'=>'32',
			'children_id'=>array('32','36')
				
			),
		array(
			'id'=>'d_realizacje',
			'icon'=>'iconfa-calendar',
			'text'=>'Lista zleceń',
			'id_akcji'=>'34',
			'children_id'=>array('34')
				
			),
		array(
			'id'=>'x_wyloguj',
			'icon'=>'iconfa-off',
			'text'=>'Wyloguj',
			'id_akcji'=>'16'
		));
		}
		return $d;
	}
	

}