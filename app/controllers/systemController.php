<?php
Class systemController{
	public static $SHOW_TPL = 1;
	public function __construct(){
		$this->__set('Main',Main::GetInst());
		$this->__set('Db',Main::GetInst()->Db);
	}
	
	public function __get($nazwa) {
		return $this->$nazwa;
	}
	public function __set($nazwa, $wartosc) {
		$this->$nazwa = $wartosc;
	}
	private function UtworzNowyBackupSql(){
		$dir = $this->Main->GetConfig()->dir->mysql_backup;
		$file = md5(md5(md5(md5(time())))).'.sql';
		require_once($this->Main->GetConfig()->dir->{'class'}.'/phpMyDumper.class.php');
		$Db = Db::GetInst();
		$dump = new phpMyDumper($this->Main->GetConfig()->db->nameDb,$Db->DBconnect(),$dir.'/'.$file,false);
		$dump->dropTable = true;
		$dump->createTable = true;
		$dump->tableData = true;
		$dump->expInsert = false;
		$dump->hexValue = false;
		$dump->phpMyAdmin = true;
		$dump->utf8 = true;
		$dump->autoincrement = true;
		$dump->doDump();
		return $file;
	}
	////data/uploader/[*:image].[*:format]
	public function getImgFromUploader_index(){
		$dir = $this->Main->config->dir->upload;
		$r = $this->Main->GetRouter()->params;
		$er=explode('/',$r->image);
		$dir = _DIR_ . $dir . '/';
		for($x=0;$x<count($er)-1;$x++){
			$dir.=$er[$x].'/';
		}
		$file = $er[$x++];
		$file.='.'.$r->format;
		
		pre($dir);
		pre($file);
		$o=new stdClass;
		self::$SHOW_TPL =0;
		return $o;
	
	}
	public function systemBackupDB(){
		$o=new stdClass;
		$o->smarty['nazwa_pliku'] = $this->UtworzNowyBackupSql();
		$o->smarty['src_pliku'] = $this->Main->GetConfig()->dir->mysql_backup.'/'.$o->smarty['nazwa_pliku'];
		$o->smarty['lista'] = $this->Main->Db->ShowAllTables();
		
		$o->tpl ='systemBackup_db.tpl';
		return $o;
	
	}
	public function systemApi(){
		$o=new stdClass;
		foreach(glob(_DIR_.'public/themes/backend/images/*.png')as $i){
			$o->smarty['filx'][] = str_replace(_DIR_,'',$i);
		}
		foreach(glob(_DIR_.'public/themes/backend/img/*.png')as $i){
			$o->smarty['filx'][] = str_replace(_DIR_,'',$i);
		}
		foreach(glob(_DIR_.'public/themes/backend/images/*.jpg')as $i){
			$o->smarty['filx'][] = str_replace(_DIR_,'',$i);
		}
		foreach(glob(_DIR_.'data/uploader/kontrahenci_grupy/*.jpg')as $i){
			$o->smarty['filx'][] = str_replace(_DIR_,'',$i);
		}
		$o->tpl ='system_api.tpl';
		return $o;
	
	}

}