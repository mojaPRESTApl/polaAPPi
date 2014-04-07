<?php
function smarty_function_modul($params, $template){
	return Controllers::GetInst()->UruchomModul($params);
}

?>