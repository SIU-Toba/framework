<?
require_once('modelo/lib/elemento_modelo.php');
require_once('modelo/instalacion.php');
require_once('modelo/procesos/instancia_exportador.php');
/**
	FALTA:
		- Falta un parametrizar en la instalacion si la base toba es independiente o adosada al negocio
			( se eliminan las tablas o la base en la regeneracion? )
*/
class instancia extends elemento_modelo
{
	const dir_datos_globales = 'global';
	const prefijo_dir_proyecto = 'p__';
	const archivo_datos = 'datos.sql';
	const archivo_logs = 'logs.sql';
	private $identificador;
	private $dir;
	private $db;
	
	public function __construct( $identificador )
	{
		parent::__construct();
		$this->identificador = $identificador;
		define('apex_pa_instancia', $this->identificador);
		$this->dir = $this->dir_raiz . '/instalacion/' . instalacion::instancia_prefijo . $this->identificador;
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (la carpeta '{$this->dir}' no existe)");
		} else {
			//Incluyo el archivo de parametros de la instancia
			require_once( $this->dir . '/info_instancia.php' );
		}
	}

	function get_db()
	{
		return dba::get_db('instancia');
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

	function get_parametros_db()
	{
		return dba::get_info_db_instancia();
	}
		
	function get_lista_proyectos()
	{
		$lista_proyectos = info_instancia::get_lista_proyectos();
		//ATENCION: temporal, hasta que el administrador se oficialice como proyecto
		if ( ! in_array( 'toba', $lista_proyectos ) ) {
			$lista_proyectos[] = 'toba';	
		}
		return $lista_proyectos;
	}
	
	function existe_proyecto( $proyecto )
	{
		$proyectos = $this->get_lista_proyectos();
		if ( in_array( $proyecto, $proyectos ) ) {
			return true;	
		}
		return false;
	}
	
	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	/**
	* Exportacion de TODO lo que hay en una instancia
	*/
	function exportar_full()
	{
		$this->exportar();
		foreach( $this->get_lista_proyectos() as $proyecto ) {
			$this->manejador_interface->titulo( "PROYECTO: $proyecto" );
			$proyecto = new proyecto( $this, $proyecto );
			$proyecto->set_manejador_interface( $this->manejador_interface );			
			$proyecto->exportar();
		}	
	}	

	/**
	* Exportacion de la informacion correspondiente a la instancia (no proyectos)
	*/
	function exportar()
	{
		try {
			$this->manejador_interface->titulo( "Exportando informacion de la INSTANCIA" );
			$exportador = new instancia_exportador( $this );
			$exportador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	//-----------------------------------------------------------
	//	IMPORTAR
	//-----------------------------------------------------------

	/**
	* Importacion completa de una instancia
	*/
	function importar()
	{
		// Existe la base?
		$base = info_instancia::get_base();
		if ( ! dba::existe_base_datos( $base ) ) {
			dba::crear_base_datos( $base );
		}
		//Inicio el proceso de carga
		try {
			$this->get_db()->abrir_transaccion();
			$this->get_db()->retrazar_constraints();
			$this->crear_modelo_datos_toba();
			$this->importar_proyectos();
			$this->get_db()->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$this->get_db()->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la inicializacion de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	/**
	* Inicializacion de instancias
	*/
	function crear_modelo_datos_toba()
	{	
		$this->crear_tablas();
		$this->cargar_datos_nucleo();
	}
	
	private function crear_tablas()
	{
		$this->manejador_interface->titulo('Creando tablas del sistema.');
		$directorio = nucleo::get_dir_ddl();
		$archivos = manejador_archivos::get_archivos_directorio( $directorio, '%.*\.sql%' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( 'Cargando: ' . $archivo );
			$this->get_db()->ejecutar_archivo( $archivo );
		}
	}
	
	private function cargar_datos_nucleo()
	{
		$this->manejador_interface->titulo('Cargando datos del nucleo.');
		$directorio = nucleo::get_dir_metadatos();
		$archivos = manejador_archivos::get_archivos_directorio( $directorio, '%.*\.sql%' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( 'Cargando: ' . $archivo );
			$this->get_db()->ejecutar_archivo( $archivo );
		}
	}

	private function importar_proyectos()
	{
		foreach( $this->get_lista_proyectos() as $proyecto ) {
			$this->manejador_interface->titulo( "PROYECTO: $proyecto" );
			$proyecto = new proyecto( $this, $proyecto );
			$proyecto->set_manejador_interface( $this->manejador_interface );			
			$proyecto->importar();
		}	
	}

	//-----------------------------------------------------------
	//	ELIMINAR
	//-----------------------------------------------------------

	/**
	* Eliminacion de la BASE de la instancia
	*/
	function eliminar()
	{
		$base = info_instancia::get_base();
		dba::borrar_base_datos( $base );
	}

	/**
	* Eliminacion de las tablas de la instancia
	*/
	function eliminar_tablas()
	{
	}
}
?>