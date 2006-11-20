<?php
require_once("contrib/lib/toba_nodo_basico.php");
require_once("modelo/consultas/dao_editores.php");
require_once("catalogo_fuentes_fuente.php");

class catalogo_fuentes extends toba_nodo_basico
{
	protected $hijos = array();

	function __construct()
	{
		parent::__construct('Fuente de Datos');		

		$this->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("solic_consola.gif", false),
							'ayuda' => 'Administrar fuentes de datos' ) );
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';

		$this->agregar_utileria( array(
			'imagen' => toba_recurso::imagen_toba("ml/agregar.gif", false),
			'ayuda' => 'Crear FUENTE de DATOS',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/datos/fuente', null, $opciones ),
			'target' => apex_frame_centro
		) );
		
		//HIJOS
		foreach( dao_editores::get_fuentes_datos() as $fuente ) {
			$hijos[] = new catalogo_fuentes_fuente( $fuente['fuente_datos'], $this );
		}
		$this->set_hijos($hijos);
	}
}
?>