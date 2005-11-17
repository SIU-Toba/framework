<?

/**
 * Se comporta como un nodo dentro de un árbol
 * @package Objetos
 * @subpackage Ei
 */
interface recorrible_como_arbol
{
	public function es_hoja();				//¿Posee algún hijo?
	public function hijos();			 	//Arreglo con los hijos recorrible_como_arbol
	public function tiene_propiedades();	//¿El nodo tiene propiedades extra a mostrar?
	public function id();					//Forma de identificar al nodo
	public function nombre_corto();			//Nombre corto del nodo
	public function nombre_largo();			//Nombre largo sólo disponible para ayudas o vistas mas directas
	public function iconos(); 				//Arreglo de iconos asociados al nodo
	public function utilerias();			//Arreglo de utilerias (similares a los iconos pero secundarios
	//Formato de nodos y utilerias:
	//	array('imagen' => , 'ayuda' => ,  'vinculo' => )
									
}

/**
 * @package Objetos
 * @subpackage Ei
 */
interface ei
{
	public function inicializar();				//Inicializa al elemento
    public function agregar_observador();		//Se le agrega un observador al EI
    public function eliminar_observador();		//Se le extrae un observador al EI
    public function procesar_eventos();			//Sele pide al EI que procese informacion
	//public function recuperar_interaccion();	//El elemento recupera la interaccion con el cliente
    //public function validar_estado();			//Valida su estado interno
}

/**
 * Conoce como es la composicion interna de una clase del ambiente
 * @package Nucleo
 */
interface meta_clase
{
	//Generacion de codigo
	public function set_nivel_comentarios($nivel);
	public function generar_constructor();
	public function generar_metodos_basicos();
	public function generar_eventos($nivel);
	//Analisis de codigo
	public function es_evento($metodo);
	public function es_evento_predefinido($metodo);
	public function es_evento_valido($metodo);
	public function es_evento_sospechoso($metodo);
}
?>