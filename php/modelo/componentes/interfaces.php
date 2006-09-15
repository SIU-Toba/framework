<?

/**
 * Se comporta como un nodo dentro de un árbol
 * @package Objetos
 * @subpackage Ei
 */
interface recorrible_como_arbol
{
	function es_hoja();				//¿Posee algún hijo?
	function get_hijos();			 	//Arreglo con los hijos recorrible_como_arbol
	function get_padre();				//Padre recorrible_como_arbol
	function tiene_hijos_cargados();	//¿Los hijos del nodo estan cargados o cuando se requieran hay que ir a buscarlos al server?
	function tiene_propiedades();	//¿El nodo tiene propiedades extra a mostrar?
	function get_id();					//Forma de identificar al nodo
	function get_nombre_corto();			//Nombre corto del nodo
	function get_nombre_largo();			//Nombre largo sólo disponible para ayudas o vistas mas directas
	function get_info_extra();			//Información extra contextual a la situación actual del item
	function get_iconos(); 				//Arreglo de iconos asociados al nodo
	function get_utilerias();			//Arreglo de utilerias (similares a los iconos pero secundarios
	//Formato de nodos y utilerias:
	//	array('imagen' => , 'ayuda' => ,  'vinculo' => )
									
}

/**
 * Conoce como es la composicion interna de una clase del ambiente
 * @package Nucleo
 */
interface meta_clase
{
	//Generacion de codigo
	function get_plan_construccion_metodos();
	//Analisis de codigo
	function es_evento($metodo);
	function es_evento_predefinido($metodo);
	function es_evento_valido($metodo);
	function es_evento_sospechoso($metodo);
}
?>