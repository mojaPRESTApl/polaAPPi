<?php
/*
	************************************************************
	KLASA eMail - WYSYŁANIE ORAZ ZARZĄDZANIE WIADOMOŚCIAMI EMAIL
	DATA UTWORZENIA : 30-01-2014
	AUTOR : ADRIAN RYŚ
	************************************************************
	METODY:
		-SendEmail() - Wysyłanie wiadomości email
			DANE: 
				-id_mail
				-email
				-to
				-temat/submit
				-id_email
				-smarty_themes
				-smarty_file_context
				-context_string
*/
require_once(dirname(__FILE__).'/phpmailer/PHPMailerAutoload.php');
Class eMail{
	protected $config=null;
	private $Db=null;
	public function __construct(){
		$this->config_all = GetIniData(_CONFIG_INI_);
		$this->config = GetIniData(_CONFIG_INI_)->smtp_server;
		$this->Db=Db::GetInst(); 
	}
	public function CreateSmartyContext($id_email,$data){
		$iM=Main::getInst();
		$iM->SetSmartyData();
		$o = new StdClass;
		$e=$this->Db->Select("select * from szablony_email WHERE id='{$id_email}'");
		if(count($e)){
			$o->tpl_data = $e->{0};
		}
			else{
				$o->error = 'Brak wywoływanego szablonu email';
			}
			$data->temat = $o->tpl_data->tytul;
			foreach($data as $k=>$i){
				$iM->Smarty->assign($k,$i);
			}
			$o->context = $iM->Smarty->fetch(_DIR_ . $this->config_all->dir->email_themes .'/'. $o->tpl_data->file);
		return $o;
	}
	public function SendEmail($data){
		$xD=$this->CreateSmartyContext($data['id_mail'],$data);
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = $this->config->host;
		$mail->SMTPAuth = $this->config->smtp_auth;
		$mail->Username = $this->config->user;
		$mail->Password = $this->config->pass;
		$mail->From =  utf8_decode($this->config->email);
		$mail->FromName = utf8_decode($this->config->from);
		$mail->addAddress($data['email'], $data['to']);
		$mail->isHTML(true);
		$mail->CharSet = "UTF-8";
		$mail->Subject = mb_encode_mimeheader(((isset($data['temat'])) ? $data['temat'] : $xD->tpl_data->tytul),"UTF-8", "B", "\n");
			
		$mail->Body    = $xD->context;
		if($data['file'] && $data['file_name']){
			$mail->AddAttachment(_DIR_ . $data['file'],$data['file_name']);
		}
		// echo $xD->context;
		$out = new stdClass;
		if(!$mail->send()) { 
			$out->status = 0;
			echo $mail->ErrorInfo;
		}else{
			$out->status = 1;
		}
		$mail->ClearAddresses();
		if($data['file'] && $data['file_name']){
			$mail->ClearAttachments();
		}
		return $out;
	}
}