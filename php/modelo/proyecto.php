<?php
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('nucleo/lib/reflexion/clase_datos.php');
require_once('nucleo/lib/manejador_archivos.php');
require_once('modelo/estructura_db/tablas_proyecto.php');


class proyecto
{
	protected $interface;			// Objeto que maneja la salida de la interface
	protected $dir_raiz;			// Directorio RAIZ
	private $nombre;				// 	Id del proyecto
	private $instancia;				//	Referencia a la instancia en la que se esta trabajando

	public function __contruct($directorio_raiz, $nombre, $instancia)
	{
		$this->dir_raiz = $directorio_raiz;		
		$this->identificador = $nombre;
		$this->instancia = $instancia;
	}

	function set_interface_usuario( $objeto )
	{
		$this->interface = $objeto;
	}
	
	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------


	//-----------------------------------------------------------
	//	COMPILAR
	//-----------------------------------------------------------

}
?>