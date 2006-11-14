<?php
require_once("nucleo/componentes/interface/interfaces.php");
require_once("modelo/consultas/dao_editores.php");
require_once("catalogo_fuentes_fuente.php");

class catalogo_fuentes implements toba_nodo_arbol
{
	protected $proyecto;
	protected $padre;
	
	function __construct($padre)
	{
		$this->padre = $padre;
		foreach( dao_editores::get_fuentes_datos() as $fuente ) {
			$this->estructura[] = new catalogo_fuentes_fuente( $this, $fuente['fuente_datos'] );
		}
	}
	
	function get_id()
	{
		return 'fuentes';
	}
	
	function get_nombre_corto()
	{
		return 'Fuentes de Datos';	
	}
	
	function get_nombre_largo()
	{
		return null;	
	}
	
	function get_info_extra()
	{
		return null;
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("objetos/datos_relacion.gif", false),
							'ayuda' => 'Administrar usuarios de la instancia' );		
		return $iconos;	
	}
	
	/**
	 * Arreglo de utilerias (similares a los iconos pero secundarios
	 * Formato de nodos y utilerias: array('imagen' => , 'ayuda' => ,  'vinculo' => )
	 */
	function get_utilerias()
	{
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("fuente_nueva.gif", false),
			'ayuda' => 'Editar fuente de datos',
			'vinculo' => toba::vinculador()->crear_vinculo( 'toba_editor', '/admin/datos/fuente', null, $opciones ),
			'target' => apex_frame_centro
		);
		return $utilerias;	
	}

	function get_padre()
	{
		return $this->padre;	
	}
	
	function tiene_hijos_cargados()
	{
		return true;	
	}
	
	function es_hoja()
	{
		return false;
	}
	
	function get_hijos()
	{
		return $this->estructura;
	}

	//¿El nodo tiene propiedades extra a mostrar?
	function tiene_propiedades()
	{
	}
}
?>