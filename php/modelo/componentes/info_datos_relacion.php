<?php
require_once('info_componente.php');

class info_datos_relacion extends info_componente
{
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear una nueva tabla asociada a la relación",
			'vinculo' => toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'datos_relacion', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		return array_merge($iconos, parent::get_utilerias());	
	}		
}
?>