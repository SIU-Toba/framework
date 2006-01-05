<?
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
	const prefijo_dir_instancia = 'i__';
	const prefijo_dir_proyecto = 'p__';
	const archivo_datos = 'datos.sql';
	const archivo_logs = 'logs.sql';
	private $id;
	
	function __construct($identificador)
	{
		$this->id = $identificador;
	}
	
	/* Creacion de una instancia TOBA */
	
	static function crear($id, $base, $proyectos)
	{
		
		
		
	}
	
}
?>