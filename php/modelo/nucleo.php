<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/procesos/nucleo_parser_ddl.php');
require_once('modelo/procesos/nucleo_exportador.php');

class nucleo extends elemento_modelo
{
	//------------------------------------------------
	// Informacion
	//------------------------------------------------
	
	static function get_dir_ddl()
	{
		return toba_dir() . '/php/modelo/ddl';
	}
	
	static function get_dir_estructura_db()
	{
		return toba_dir() . '/php/modelo/estructura_db';		
	}

	static function get_dir_metadatos()
	{
		return toba_dir() . '/php/modelo/metadatos';		
	}

	//------------------------------------------------
	// Procesos
	//------------------------------------------------

	/**
	*	Genera la informacion que describe el modelo de datos para todos los procesos toba
	*/
	function parsear_ddl()
	{
		try {
			$parser = new nucleo_parser_ddl( $this );
			$parser->procesar();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante el parseo.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	/*
	*	Exporta los metadatos correspondientes a las tablas maestras del sistema
	*/
	function exportar()
	{
		try {
			$exportador = new nucleo_exportador( $this );
			$exportador->procesar();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
}
?>