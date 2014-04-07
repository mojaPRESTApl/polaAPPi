<?php
function smarty_function_hook($params, $template){
	if(isset($params['data'])){
		// pre();
		foreach($params['data'] as $n){
			$outrp.=Controllers::GetInst()->UruchomModul(array('name'=>$n));
		}
		return $outrp;
	}

}
?>