<?php
require_once("nucleo/browser/clases/objeto_cn.php");
class cnSipefco extends objeto_cn
{
	function cnSipefco($id,&$solicitud)
	{
		parent::objeto_cn($id, $solicitud);
	}
	//-------------------------------------------------------------------------------

	function procesar($id,&$solicitud)
	{
		//Buscar en las dependencias al "gestor";
	}
	//-------------------------------------------------------------------------------

}
?>