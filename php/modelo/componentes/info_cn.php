<?php
require_once('info_componente.php');

class info_cn extends info_componente
{
	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al controlador",
			'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'cn', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id ),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		return array_merge($iconos, parent::get_utilerias());	
	}		

	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
}
?>