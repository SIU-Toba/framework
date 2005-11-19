<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_datos_relacion extends elemento_objeto
{
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function utilerias()
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
		return array_merge($iconos, parent::utilerias());	
	}		
}
?>