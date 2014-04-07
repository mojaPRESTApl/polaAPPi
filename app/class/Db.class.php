<?php
/*
	KLASA OBSŁUGI MYSQL
	GetInst - uchwyt instancji 
	DBconnect() - połączenie z Db
	Insert($tabela, $dane)	- Dodawanie rekordu
	Select() - Pobieranie danych
	Delete() - Usuwanie rekordu
	Update() - Aktualizacja
	Query()  - Wykonanie zapytania sql
	ClearTable() - Czyszczenie tabeli
	GetTableData() - Pobieranie pól wybranej tabelki
	ShowAllTables() - Wyświetlanie wszystkich tabel w bazie danych
*/
class Db{
protected $connect=null;
static private $instDB; 
static private $oInstance=null; 
	public function __construct($db_date=null){
		if(!self::$instDB) { 
		   self::$instDB = Db::DBconnect();
		}
		$this->connect = self::$instDB;
	}
	public function DBconnect(){
		$db_date=Main::GetConfig(_CONFIG_)->db;
		$this->connect = mysql_connect($db_date->host, $db_date->user, $db_date->pass);
		if(!$this->connect){
			Main::$errors[] = 'Błąd połączenia z hostem bazy danych';
		} else{
			$this->db_select = mysql_select_db($db_date->nameDb,$this->connect);
			if(!$this->db_select){
				Main::$errors[] = 'Nie znaleziono bazy danych';
			}else{
				mysql_query('SET CHARSET utf8');
				return $this->connect;
			}
	}
	}
	public function Select($sql,$obj=false){
		if($sql !== ''){
			$zap = $this->Query($sql);
			$x=0;
			$wyn_obj = new stdClass;
				while($t = mysql_fetch_assoc($zap)){
					$wyn[$x] = $t;
					$wyn_obj->{$x} = (object)$t;
					$x++;
				}
			if(count($wyn)){
				if($obj==false){
					return $wyn_obj;
				}else{
					return $wyn;
				}
			}
		}
	}
	private function SecureData($data){
		if(is_array($data)){
			foreach($data as $key=>$val){
				if(!is_array($data[$key])){
					$data[$key] = mysql_real_escape_string($data[$key], $this->connect);
				}
			}
		}else{
			$data = mysql_real_escape_string($data, $this->connect);
		}
		return $data;
	}
	public function Update($table, $set, $where= null){
		
		$set 		= $this->SecureData($set);
		$Query = "UPDATE `{$table}` SET ";

		foreach($set as $key=>$value){
			if(in_array($key, $exclude)){
				continue;
			}
			$Query .= "`{$key}` = '{$value}', ";
		}

		$Query = substr($Query, 0, -2);
			if($where !== null){
					$Query .= ' WHERE '.$where;
			}
		return $this->Query($Query);
	}
	public function Delete($tab, $where=null){
		$sql="DELETE FROM {$tab}";
			if($where !== null){
				$sql.=" WHERE {$where}";
			}
		$this->Query($sql);
	}
	public function Insert($table_name, $form_data,$jey=null){
		$fields = array_keys($form_data);
		$sql = "INSERT INTO `{$table_name}`
		(`".implode('`,`', $fields)."`)
		VALUES('".implode("','", $form_data)."')";
		$this->Query($sql);
			$id= mysql_insert_id();
			if($jey !== null){
				return $this->Select("select {$jey} from {$table_name} ORDER BY {$jey} DESC LIMIT 1")->{0}->{$jey};
			}
	}
	public function Query($sql){
		return mysql_query($sql);
	}
	public function ClearTable($tab){
		$this->Query("TRUNCATE TABLE {$tab};");
	}
	public function GetTableData($tab){
		$t=$this->Select("DESCRIBE `{$tab}`");
		foreach($t as $i=>$j){
			$_data->{$j->Field} = $j;
		}
		return $_data;
	}
	public function ShowAllTables(){
		$t = $this->Select("SHOW TABLES;");
		$x=0;
			foreach($t as $j=>$i){
				foreach($i as $k=>$v){
					$_row->{$x} = $v;
					$x++;
				}
			}
			return $_row;
	}
	public static function getInst(){
		if(self::$oInstance == false){
			self::$oInstance = new Db();
		}
		return self::$oInstance;
	}
	// public function __destruct(){
		// mysql_close($this->connect);
	// }
}