<?php
/*
	PODRĘCZNE PROSTE FUNKCJE/ MINI HELPERY
*/

function RunBootstrap(){	$dir = _DIR_.'app/class/';	require_once($dir .'Spyc.class.php');	}
function GetIniData($file){	return json_decode(json_encode(Spyc::YAMLLoad($file)));	}
function pre($d){	echo '<pre>';	print_r($d);	echo '</pre>';}
function CreatePasswordWithSol($pass){
	$sol = GetIniData(_CONFIG_INI_)->system->hash_sol;
	$pass = substr($sol,0,25) . $pass . substr($sol,25,50);
	return md5($pass);
}
function xhash(){
	return md5(md5(md5(time().GenRandom(128))));
}

function NumerLosowy($file){	
	$chars = "1234567890"; 
	$len = strlen($chars) - 1; 
	for($i =0; $i < 11; $i++) 
	{ 
	$random = rand(0, $len); 

	$output .=  $chars[$random]; 
	} 
	return $output;
} 
// function GetIniData($file){	
	// if(file_exists($file)){
		// $d = (object)parse_ini_file($file, true);
		// return jsonE($d);
	// }
// }
function alert($d){
		return '<h3>'.$d.'</h3>';
}
// function pre($d){
	// echo '<pre>';
			// print_r($d);
	// echo '</pre>';
// }
function li($d,$echo=null){
if($echo == null){
	return '<li>'.$d.'</li>';
	}else{
		echo '<li>'.$d.'</li>';
	}
}

function hr($d){
		return $d.'<hr>';
}
function css($d){
	return '<style>'.$d.'</style>';
}
function js($d){
	return '<script src="'.$dir.$d.'"></script>';
}

function GenRandom($howlong,$ever_strong=null){
if($ever_strong !==  null){
	$chars .= "ABCDEFGHIJKLMNOPRSTUWZYXQ"; 
}
else{
	$chars = "abcdefghijklmnoprstuwxyzq"; 
	$chars .= "ABCDEFGHIJKLMNOPRSTUWZYXQ"; 
	$chars .= "1234567890"; 
}
$pass = ""; 
$len = strlen($chars) - 1; 
for($i =0; $i < $howlong; $i++) 
  { 
   $random = rand(0, $len); 

       $output .=  $chars[$random]; 
   } 
return $output; 
}


function jsonD($d){
	return json_decode($d);
}
function jsonE($d){
	return json_decode(json_encode($d));
}
function AddClass($name,$dir=null){
	$cF= GetIniData(_CONFIG_INI_);
	$dir = $cF->dir->{'class'};
	$opt = array('.class.php','Class.php','.php');
		foreach($opt as $n){
			$file[] = $dir.'/'.$name.$n;
		}
		foreach($file as $Ff){
			if(file_exists($Ff)){
				require_once($Ff);
				if (class_exists($name)) {
					return new $name();
				}
			}
		}
}

function LoadController($name,$dir=null){
	$cF= GetIniData(_CONFIG_INI_);
	$dir = $cF->dir->{'controller'};
	$opt = array('Index.php','Controller.php','.php','Front.php','Admin.php');
		foreach($opt as $n){
			$file[] = $dir.'/'.$name.$n;
		}
		foreach($file as $Ff){
			if(file_exists($Ff)){
				require_once($Ff);
				if (class_exists($name)) {
					return new $name();
				}
			}
		}
}
