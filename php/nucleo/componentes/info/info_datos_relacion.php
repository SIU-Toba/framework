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
			'ayuda' => "Crear un objeto asociado al controlador",
			'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'datos_relacion', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id),
										false, false, null, true, "central")
		);
		return array_merge($iconos, parent::get_utilerias());	
	}		
}
?>