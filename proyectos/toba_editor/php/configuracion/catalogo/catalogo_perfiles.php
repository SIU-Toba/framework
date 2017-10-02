<?php
require_once(toba_dir() . '/php/contrib/lib/toba_nodo_basico.php');
require_once('catalogo_perfiles_grupo.php');

class catalogo_perfiles extends toba_nodo_basico
{
	function __construct()
	{
		parent::__construct('Grupos de Acceso');
		foreach (toba_info_permisos::get_perfiles_funcionales() as $grupo) {
			$hijos[] = new catalogo_perfiles_grupo( $this, $grupo['usuario_grupo_acc'], $grupo['nombre'] );
		}
		$this->set_hijos($hijos);
		$this->agregar_icono(array( 'imagen' => 	toba_recurso::imagen_toba('nucleo/preferencias.gif', false),
							'ayuda' => 'Administrar GRUPOS de ACCESO' ));
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		
		$this->agregar_utileria(array(
			'imagen' => toba_recurso::imagen_toba('nucleo/agregar.gif', false),
			'ayuda' => 'Crear un nuevo grupo de acceso',
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(), 1000261, null, $opciones),
			'target' => apex_frame_centro
		));
	}
}
?>