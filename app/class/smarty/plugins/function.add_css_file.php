<?php
// smarty_function_modul(array(),'s');
function smarty_function_add_css_file($params, $template){
$Main = new Main;
// echo css($Main->PokazPlikCss($params[name]));

$dane = $Main->SetConfigToTpl();
$tresc = file_get_contents($dane[app][css_dir].'/'.$params[name]);
$tt= base64_decode($tresc);
echo $tt;
 // $handle = fopen('templates/xsystem/css/'.$params[name], 'w');
// fwrite($handle, $tt);
// echo xController::UruchomModulWygladuSektor($params);

// require_once($params[name]);
// pre($params);
// $main = new Main;
// return $main->LadujModulWidoku($params);
}
?>