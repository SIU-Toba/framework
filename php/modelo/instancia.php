<?
require_once('modelo/lib/elemento_modelo.php');
require_once('modelo/instalacion.php');
require_once('modelo/procesos/instancia_exportador.php');

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

class instancia extends elemento_modelo
{
	const dir_datos_globales = 'global';
	const prefijo_dir_proyecto = 'p__';
	const archivo_datos = 'datos.sql';
	const archivo_logs = 'logs.sql';
	private $identificador;
	private $dir;
	
	public function __construct( $directorio_raiz, $identificador )
	{
		parent::__construct( $directorio_raiz );
		$this->identificador = $identificador;
		$this->dir = $this->dir_raiz . '/instalacion/' . instalacion::instancia_prefijo . $this->identificador;
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("Exportador de Instancia: la carpeta '{$this->dir}' no existe");
		} else {
			//Incluyo el archivo de parametros de la instancia
			require_once( $this->dir . '/info_instancia.php' );
		}
	}

	static function existe( $nombre )
	{
		if ( trim( $nombre ) == '' ) {
			throw new excepcion_toba("ATENCION: Es necesario definir la INSTANCIA de trabajo");	
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
	
	function get_lista_proyectos()
	{
		$lista_proyectos = info_instancia::get_lista_proyectos();
		//ATENCION: temporal, hasta que el administrador se oficialice como proyecto
		if ( ! in_array( 'toba', $this->lista_proyectos ) ) {
			$lista_proyectos[] = 'toba';	
		}
		return $lista_proyectos;
	}
	
	function get_id_db()
	{
		return info_instancia::get_base();		
	}	
	
	function info_db()
	{
		
	}

	//-----------------------------------------------------------
	//	Procesos
	//-----------------------------------------------------------

	/**
	* Exportacion de instancias
	*/
	function exportar()
	{
		try {
			$exportador = new instancia_exportador( $this );
			$exportador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	/**
	* Eliminacion de instancias
	*/
	function eliminar()
	{
			
	}

	/**
	* Inicializacion de instancias
	*/
	function inicializar()
	{	
		/*
		try {
			$db->abrir_transaccion();
			$db->retrazar_constraints();
			$this->crear_base();
			$this->crear_tablas();
			$this->desactivar_constraints();
			$this->cargar_proyectos();
			$this->cargar_datos_instancia();
			$db->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$db->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la inicializacion de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
		*/	
	}
}
?>