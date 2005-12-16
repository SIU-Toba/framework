<?

interface definicion_toba
{
	//Indica que tablas conforman a un componente
	static function get_estructura(); 				
	//Devuelve la VISTA del componente utilizada en el ambiente
	static function get_vista_extendida($proyecto, $componente=null);	
	//Devuelve el PATH del runtime
	static function get_path_clase_runtime();	
	//Devuelve el nombre de la clase INFO a utilizar
	static function get_nombre_clase_info();
}
?>