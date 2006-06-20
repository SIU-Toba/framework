<?
	include_once("nucleo/browser/interface/form.php");
	if($this->contexto['cargado_ok']){
		include_once("nucleo/objeto.php");
		$titere =& new objeto($this->contexto['elemento'],$this);		
		ei_centrar(form::textarea("sql",$titere->dump_sql(),25,60));
	}else{
		echo ei_mensaje("DUMP: No se explicito el objeto","error");
	}

?>