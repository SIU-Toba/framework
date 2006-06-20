<?php

class cuadro_actividad_local extends objeto_ei_cuadro
{
	function modificar_vinculo_fila__seleccion($vinculo, $fila)
	{
		$vinculo->set_item(	$this->datos[$fila]['editor_proyecto'],
							$this->datos[$fila]['editor_item'] );
		$vinculo->agregar_opcion('menu',true);
		$vinculo->set_parametros( array( apex_hilo_qs_zona => $this->datos[$fila]['componente_proyecto'] . apex_qs_separador .
																$this->datos[$fila]['componente_id'] ));
	}
}
?>