<?php

class toba_modelo_catalogo
{
	private $instalacion;				// Instalacion
	private $instancia;					// Array de instancias existentes en la instalacion
	static private $singleton;

	private function __construct(){}
	
	/**
	*	Devuelve una referencia a la INSTALACION
	*/
	function get_instalacion( $manejador_interface )
	{
		if ( ! isset( $this->instalacion ) ) {
			$this->instalacion = new toba_modelo_instalacion();
			$this->instalacion->set_manejador_interface( $manejador_interface );
		}
		return $this->instalacion;
	}

	/**
	*	Devuelve una referencia a un INSTANCIA.
	*/
	function get_instancia( $id_instancia, $manejador_interface )
	{
		if ( ! isset ( $this->instancia[ $id_instancia ] ) ) {
			$instalacion = $this->get_instalacion( $manejador_interface );
			$this->instancia[ $id_instancia ] = new toba_modelo_instancia( $instalacion, $id_instancia );
			$this->instancia[ $id_instancia ]->set_manejador_interface( $manejador_interface );
		}
		return $this->instancia[ $id_instancia ];
	}
	
	/**
	*	Devuelve una referencia a un PROYECTO
	*/
	function get_proyecto( $id_instancia, $id_proyecto, $manejador_interface )
	{
		$instancia = $this->get_instancia( $id_instancia, $manejador_interface );
		$archivo_proy = $instancia->get_path_proyecto($id_proyecto)."/php/toba_modelo/$id_proyecto.php";
		if (file_exists($archivo_proy)) {
			require_once($archivo_proy);
			$proyecto = new $id_proyecto( $instancia, $id_proyecto );
		} else {
			$proyecto = new toba_modelo_proyecto( $instancia, $id_proyecto );
		}
		$proyecto->set_manejador_interface( $manejador_interface );
		return $proyecto;
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	function get_nucleo( $manejador_interface )
	{
		$nucleo = new toba_modelo_nucleo();
		$nucleo->set_manejador_interface( $manejador_interface );
		return $nucleo;
	}

	/**
	*	Devuelve una referencia al CONVERSOR
	*/
	function get_conversor( $id_instancia, $manejador_interface )
	{
		$instancia = self::get_instancia( $id_instancia, $manejador_interface );
		$conversor = new toba_modelo_conversor( $instancia );
		$conversor->set_manejador_interface( $manejador_interface );
		return $conversor;
	}

	/**
	*	Singleton
	*/
	static function instanciacion()
	{
		if (!isset(self::$singleton)) {
			self::$singleton = new toba_modelo_catalogo();	
		}
		return self::$singleton;	
	}	
}
?>
