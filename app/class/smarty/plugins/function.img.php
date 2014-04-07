<?php
function smarty_function_img($params, $template){
	foreach($params as $key=>$p){
		$html.=$key.'="'.$p.'" ';
	}
	return '<img '.$html.'/>';
}
?>