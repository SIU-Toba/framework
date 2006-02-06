<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/instancia.php');
require_once('modelo/procesos/proyecto_exportador.php');
require_once('modelo/procesos/proyecto_compilador.php');
/**
*	Publica los servicios de la clase NUCLEO a la consola toba
*
*	FALTA:
*		- Control de que se referencia a un proyecto VALIDO
*/
class proyecto extends elemento_modelo
{
	private $instancia;				
	private $identificador;
	private $dir;

	public function __construct( instancia $instancia, $identificador )
	{
		parent::__construct();
		$this->instancia = $instancia;
		$this->identificador = $identificador;
		if ( $this->identificador == 'toba' ) {
			$this->dir = $this->dir_raiz . '/php/admin';	
		} else {
			$this->dir = $this->dir_raiz . '/proyectos/' . $this->identificador;	
		}
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' es invalido. (la carpeta '{$this->dir}' no existe)");
		} 
	}

	//-----------------------------------------------------------
	//	Informacion
	//-----------------------------------------------------------

	function get_id()
	{
		return $this->identificador;
	}
	
	function get_dir()
	{
		return $this->dir;	
	}

	function get_dir_componentes()
	{
		return $this->dir . '/metadatos/componentes';
	}
	
	function get_dir_tablas()
	{
		return $this->dir . '/metadatos/tablas';
	}

	function get_dir_componentes_compilados()
	{
		return $this->dir . '/metadatos_compilados/componentes';
	}

	//-----------------------------------------------------------
	//	Procesos generales
	//-----------------------------------------------------------

	function info()
	{
		/*
			Cuantas objetos hay, etc.
		*/	
	}

	//-----------------------------------------------------------
	//	IMPORTAR
	//-----------------------------------------------------------
	
	function importar( $transaccion = false )
	{
		if( ! $this->instancia->existe_proyecto( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual.");
		}
		try {
			$db = $this->instancia->get_db();
			if ( $transaccion ) $db->abrir_transaccion();
			if ( $transaccion ) $db->retrazar_constraints();
			$this->importar_tablas();
			$this->importar_componentes();
			if ( $transaccion ) $db->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			if ( $transaccion ) $db->abortar_transaccion();
			$this->manejador_interface->error( 'PROYECTO: Ha ocurrido un error durante la IMPORTACION.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
	
	private function importar_tablas()
	{
		$archivos = manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( 'Cargando: ' . $archivo );
			$this->instancia->get_db()->ejecutar_archivo( $archivo );
		}
	}
	
	private function importar_componentes()
	{
		$subdirs = manejador_archivos::get_subdirectorios( $this->get_dir_componentes() );
		foreach ( $subdirs as $dir ) {
			$this->manejador_interface->mensaje( 'Cargando: ' . $dir );
			$archivos = manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$this->instancia->get_db()->ejecutar_archivo( $archivo );
			}
		}
	}

	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	function exportar()
	{
		if( ! $this->instancia->existe_proyecto( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		try {
			$exportador = new proyecto_exportador( $this );
			$exportador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->mensaje( $e->getMessage() );
		}
	}

	//-----------------------------------------------------------
	//	COMPILAR
	//-----------------------------------------------------------

	function compilar()
	{
		try {
			$compilador = new proyecto_compilador( $this );
			$compilador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la compilacion.' );
			$this->manejador_interface->mensaje( $e->getMessage() );
		}
	}

	//-----------------------------------------------------------
	//	ELIMINAR
	//-----------------------------------------------------------

	function eliminar()
	{
		$this->manejador_interface->mensaje( 'Eliminando: ' . $this->identificador );
	}
}
?>