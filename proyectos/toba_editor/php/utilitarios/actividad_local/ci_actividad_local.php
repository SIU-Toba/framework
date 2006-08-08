<?php
require_once('modelo/consultas/dao_editores.php'); 
//--------------------------------------------------------------------
class ci_actividad_local extends objeto_ci
{
	function conf__cuadro()
	{
		return dao_editores::get_log_modificacion_componentes();
	}
}
?>