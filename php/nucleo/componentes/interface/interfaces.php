<?php

/**
 * Se comporta como un nodo dentro de un árbol
 * @package Componentes
 * @subpackage Eis
 */
interface toba_nodo_arbol
{
	/**
	 * ¿Posee algún hijo?
	 * @return boolean
	 */
	function es_hoja();
	
	/**
	 * Arreglo con los hijos toba_nodo_arbol
	 * @return array(toba_nodo_arbol)
	 */
	function get_hijos();
	
	/**
	 * Padre del nodo actual
	 * @return toba_nodo_arbol
	 */
	function get_padre();
	
	/**
	 * ¿Los hijos del nodo estan cargados o cuando se requieran hay que ir a buscarlos al server?
	 * @return boolean
	 */
	function tiene_hijos_cargados();
	
	
	function tiene_propiedades();	//¿El nodo tiene propiedades extra a mostrar?
	
	/**
	 * Forma de identificar univocamente al nodo
	 */
	function get_id();
	
	/**
	 * Nombre corto del nodo, utilizado para listados
	 * @return string
	 */
	function get_nombre_corto();
	
	/**
	 * Nombre largo sólo disponible para ayudas o vistas mas directas
	 * @return string 
	 */
	function get_nombre_largo();
	
	/**
	 * Información extra contextual a la situación actual del nodo
	 */
	function get_info_extra();
	
	/**
	 * Arreglo de iconos asociados al nodo
	 * Formato de nodos y utilerias: array('imagen' => , 'ayuda' => ,  'vinculo' => )
	 */
	function get_iconos();
	
	/**
	 * Arreglo de utilerias (similares a los iconos pero secundarios
	 * Formato de nodos y utilerias: array('imagen' => , 'ayuda' => ,  'vinculo' => )
	 */
	function get_utilerias();
									
}

interface toba_nodo_arbol_form extends toba_nodo_arbol 
{

	function get_input($id);
	
	function cargar_estado_post($id);

	function set_apertura($abierto);
	
	function get_apertura();
	
}


//-----------------------------------------------------------

/**
 * Representa un icono de utileria que se situa a un lado de los efs, que permite extender el comportamiento del mismo
 * 
 * @package Componentes
 * @subpackage Efs
 */
interface toba_ef_icono_utileria
{
	function get_html(toba_ef $ef);
}


interface toba_valida_datos
{
	function set_componente($componente);
	function validar_datos($datos);	
}


?>