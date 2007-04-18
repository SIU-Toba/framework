<?php
require_once(toba_dir() . '/php/contrib/lib/toba_nodo_basico.php');

class catalogo_fuentes_fuente extends toba_nodo_basico
{
	protected $datos;
	
	function __construct($id, $padre)
	{
		$this->id = $id;
		$this->datos = toba_info_editores::get_info_fuente_datos($this->id);
		parent::__construct($this->datos['fuente_datos'], $padre);
		$this->nombre_largo = $this->datos['descripcion'];
		
		$this->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("objetos/datos_relacion.gif", false),
									'ayuda' => null ) );
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$parametros = array( apex_hilo_qs_zona => $this->datos['proyecto'] .apex_qs_separador. $this->id);

		$this->agregar_utileria(  array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Editar fuente de datos',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/datos/fuente', $parametros, $opciones ),
			'target' => apex_frame_centro
		) );
	}
}
?>