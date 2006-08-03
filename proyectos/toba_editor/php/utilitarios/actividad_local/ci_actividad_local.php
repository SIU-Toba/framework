<?php
require_once('modelo/consultas/dao_editores.php'); 
//--------------------------------------------------------------------
class ci_actividad_local extends objeto_ci
{
	function evt__cuadro__carga()
	{
		return dao_editores::get_log_modificacion_componentes();
	}
}
?>