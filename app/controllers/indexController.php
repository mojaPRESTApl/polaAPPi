<?php 
class indexController extends iController{
	public function indexStart(){
		$out=new stdClass;
		$out->tpl = $this->Main->config->routers[target][tpl];
		return $out;
	}
}