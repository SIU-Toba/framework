<?php 
require_once("contrib/lib/toba_nodo_basico.php");
require_once('configuracion/catalogo/catalogo_general.php');
require_once('configuracion/catalogo/catalogo_fuentes.php');
require_once('configuracion/catalogo/catalogo_perfiles.php');

class ci_catalogo extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- arbol ------------------------------------------------------------------------

	function evt__arbol__cambio_apertura($apertura)
	{
	}

	function evt__arbol__ver_propiedades($nodo)
	{
	}

	function conf__arbol($componente)
	{
		$nodo = new toba_nodo_basico('Parametros de previsualización');
		$nodo->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("items/instanciar.gif", false),
							'ayuda' => null ) );
		$nodo->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar los parametros de previsualizacion del proyecto',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '3287', array('menu'=>true, 'celda'=>'central') ),
				'target' => apex_frame_centro
		) );
		$arbol = array( $nodo,
						new catalogo_general(),
						new catalogo_fuentes(),
						new catalogo_perfiles );
		$componente->set_datos( $arbol );
		$componente->set_nivel_apertura(5);
	}
}

?>