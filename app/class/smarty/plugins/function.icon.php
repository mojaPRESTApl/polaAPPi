<?php
function smarty_function_icon($params, $template){
	$params2 = $params;
	unset($params2['src']);
	unset($params2['src0']);
	foreach($params2 as $key=>$p){
		$html.=$key.'="'.$p.'" ';
	}
	
	return '<img src="'.$params['src0'].$params['src'].'" '.$html.'/>';
}
?>