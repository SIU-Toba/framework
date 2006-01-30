<?
require_once('modelo/procesos/exportador_instancia.php');

/**

	FALTA:
		- Control de que se referencia a una instancia VALIDA


	Esta clase seria la responsable de administrar una instancia

	Instancia:
	
		- Crear una nueva estructura
		- Validar (info_proyectos, info_bases_modulos)
		- Exportar la instancia
		- Importar la instancia
*/

class instancia
{
	const dir_datos_globales = 'global';
	const prefijo_dir_proyecto = 'p__';
	const archivo_datos = 'datos.sql';
	const archivo_logs = 'logs.sql';
	private $dir_raiz;
	private $nombre;
	private $interface_usuario;
	
	function __construct( $dir_raiz, $nombre )
	{
		$this->nombre = $nombre;
		$this->dir_raiz = $dir_raiz;
	}

	function set_interface_usuario( $interface_usuario )
	{
		$this->interface_usuario = $interface_usuario;
	}
		
	static function existe( $nombre )
	{
		if ( trim( $nombre ) == '' ) {
			throw new excepcion_toba("ATENCION: Es necesario definir la INSTANCIA de trabajo");	
		}
	}
			
	/**
	* Exportacion de instancias
	*/
	function exportar()
	{
		try {
			$exportador = new exportador_instancia( $this->dir_raiz, $this->nombre );
			$exportador->set_interface_usuario( $this->interface_usuario );
			$exportador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->interface_usuario->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->interface_usuario->mensaje( $e->getMessage() );
		}
	}

}
?>