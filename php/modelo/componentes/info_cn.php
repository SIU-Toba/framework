<?php
require_once('info_componente.php');

class info_cn extends info_componente
{
	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al controlador",
			'vinculo' => toba::get_vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
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

	function generar_metodos()
	{
		$metodos = parent::generar_metodos();
		$metodos[] = "\t".
'function mantener_estado_sesion()
	!#c2//Declarar todas aquellas propiedades de la clase que se desean persistir automáticamente
	!#c2//entre los distintos pedidos de página en forma de variables de sesión.
	{
		$propiedades = parent::mantener_estado_sesion();
		!#c1//$propiedades[] = \'propiedad_a_persistir\';
		return $propiedades;
	}
';
		return $this->filtrar_comentarios($metodos);
	}
}
?>