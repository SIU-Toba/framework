<?
	
	global $argv;
	if(isset($argv[3])){
		include_once("nucleo/objeto.php");
		$objeto =& new objeto($argv[3],$this);		
		echo $objeto->dump_sql();
	}else{
		echo "No se especifico un objeto";
	}

?>