<?php
require_once('modelo/procesos/exportador_proyecto.php');
require_once('modelo/procesos/compilador_proyecto.php');

/*
	FALTA:
		- Control de que se referencia a un proyecto VALIDO

*/
class proyecto
{
	private $instancia;				
	private $dir_raiz;				
	private $nombre;				
	private $interface_usuario;				

	public function __construct( $directorio_raiz, $instancia, $nombre )
	{
		$this->dir_raiz = $directorio_raiz;		
		$this->instancia = $instancia;
		$this->nombre = $nombre;
	}

	function set_interface_usuario( $interface_usuario )
	{
		$this->interface_usuario = $interface_usuario;
	}
	
	static function existe( $nombre )
	{
		
	}
	
	static function nombre_valido( $nombre )
	{
		if ( trim( $nombre ) == '' ) {
			throw new excepcion_toba("ATENCION: Es necesario definir el proyecto sobre el cual se va a trabajar");	
		}
	}
	
	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	function exportar()
	{
		try {
			$exportador = new exportador_proyecto( $this->dir_raiz, $this->instancia, $this->nombre );
			$exportador->set_interface_usuario( $this->interface_usuario );
			$exportador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->interface_usuario->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->interface_usuario->mensaje( $e->getMessage() );
		}
	}

	//-----------------------------------------------------------
	//	COMPILAR
	//-----------------------------------------------------------

	function compilar()
	{
		try {
			$compilador = new compilador_proyecto( $this->dir_raiz, $this->instancia, $this->nombre );
			$compilador->set_interface_usuario( $this->interface_usuario );
			$compilador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->interface_usuario->error( 'Ha ocurrido un error durante la compilacion.' );
			$this->interface_usuario->mensaje( $e->getMessage() );
		}
	}
}
?>