<?php
require_once(toba_dir() . '/php/contrib/lib/toba_nodo_basico.php');

class catalogo_perfiles_grupo  extends toba_nodo_basico
{
	function __construct($padre, $id, $nombre)
	{
		parent::__construct($nombre, $padre);
		$this->id = $id;
		$this->nombre_largo = $nombre;
				
		$this->agregar_icono(array( 'imagen' => 	toba_recurso::imagen_toba('usuarios/usuario.gif', false),
							'ayuda' => null ));

		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$parametros = array(apex_hilo_qs_zona => toba_editor::get_proyecto_cargado() .apex_qs_separador. $this->id);

		$this->agregar_utileria(array(
			'imagen' => toba_recurso::imagen_toba('usuarios/permisos.gif', false),
			'ayuda' => 'Editar DERECHOS del grupo de acceso',
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(), '3278', $parametros, $opciones),
			'target' => apex_frame_centro
		));
		
		$this->agregar_utileria(array(
			'imagen' => toba_recurso::imagen_toba('usuarios/grupo.gif', false),
			'ayuda' => 'Editar el acceso a ITEMs del grupo de acceso',
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(), '3288', $parametros, $opciones),
			'target' => apex_frame_centro
		));
		
		$this->agregar_utileria(array(
			'imagen' => toba_recurso::imagen_toba('objetos/editar.gif', false),
			'ayuda' => 'Editar GRUPO de ACCESO',
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(), 1000261, $parametros, $opciones),
			'target' => apex_frame_centro
		));
	}
}
?>