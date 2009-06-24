<?php
require_once('objetos_toba/ci_eventos.php');
class ci_eventos_cuadro extends ci_eventos
{
	//-----------------------------------------------------------------------------------
	//---- eventos_lista ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__eventos_lista__modificacion($datos)
	{
		$multiples = 0;
		foreach($datos as $evt){
			$multiples += isset($evt['es_seleccion_multiple']) ? $evt['es_seleccion_multiple'] : 0;
		}
		if ($multiples > '1') {
			throw new toba_error_def('Solo puede existir un evento de múltiples registros por cuadro');
		}else{
			parent::evt__eventos_lista__modificacion($datos);
		}
	}

}
?>