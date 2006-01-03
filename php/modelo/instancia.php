<?
require_once("includes.php");
require_once("dba.php");

/**
	Esta clase seria la responsable de administrar una instancia

	Instancia:
	
		- Crear una nueva estructura
		- Validar (info_proyectos, info_bases_modulos)
		- Exportar la instancia
		- Importar la instancia

*/
class instancia
{
	private $id;
	
	function __construct($identificador)
	{
		$this->id = $identificador;
	}
	
	/* Creacion de una instancia TOBA */
	
	function crear()
	{
		//dba::crear_base_datos("juan");
		//dba::borrar_base_datos("juan");
		if( dba::existe_base_datos("juan") ){
			echo "TODO OK";	
		}
	}

	function get_info()
	{
		$bases = array('toba2','toba_agentes','toba2','toba_agentes');
		foreach($bases as $base){
			$db = dba::get_db($base);
		}
	}

	
	
}
?>