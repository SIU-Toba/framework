<?php

class toba_cn_info extends toba_componente_info
{
	static function get_tipo_abreviado()
	{
		return "CN";		
	}

	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un componente asociado al controlador",
			'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'toba_cn', 
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

	function get_molde_subclase($multilinea=false)
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar( new toba_codigo_metodo_php('ini') );
		$molde->agregar( new toba_codigo_metodo_php('evt__validar_datos') );
		$molde->agregar( new toba_codigo_metodo_php('evt__procesar_especifico') );
		return $molde;
	}
}
?>