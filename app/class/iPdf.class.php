<?php
Class iPdf{
    /**
     * 
     * 
     * 
     * @return <type>
     */
    public function __construct(){
		require_once(dirname(__FILE__).'/mpdf/mpdf.php');
		$this->config = GetIniData(_CONFIG_INI_);
		$this->Db=Db::GetInst(); 
	}
    /**
     * 
     * 
     * @param string $plik 
     * @param string $data 
     * @param array  $opt=array(email,send_email)
     * 
     * @return <type>
     */
	  
	public function GenerowaniePDF($plik,$data,$opt){
		$mpdf=new mPDF();
		$mpdf->mirroMargins = true;
		$mpdf->SetCreator($this->config->system->title);
		$mpdf->SetProtection(array());
		$mpdf->CSSselectMedia = 'screen';
		$mpdf->WriteHTML(file_get_contents(_DIR_ . $this->config->pdf->css_file ),1);
		$mpdf->WriteHTML($data);
		$mpdf->Output(_DIR_ . $plik);
		return _DIR_ . $plik;
	}
}