<?php

class toba_modelo_catalogo
{
	private $instalacion;				// Instalacion
	private $instancia;					// Array de instancias existentes en la instalacion
	private $db;						// Base externa
	static private $singleton;

	private function __construct(){}

	/**
	*	Singleton
	* @return toba_modelo_catalogo
	*/
	static function instanciacion($refrescar=false)
	{
		if (!isset(self::$singleton) || $refrescar) {
			self::$singleton = new toba_modelo_catalogo();	
		}
		return self::$singleton;	
	}
	
	/**
	*	Devuelve una referencia a la INSTALACION
	* @return toba_modelo_instalacion 
	*/
	function get_instalacion( $manejador_interface = null )
	{
		if (! isset($manejador_interface)) {
			$manejador_interface = new toba_mock_proceso_gui();
		}
		if ( ! isset( $this->instalacion ) ) {
			$this->instalacion = new toba_modelo_instalacion();
		}
		$this->instalacion->set_manejador_interface( $manejador_interface );		
		if (isset($this->db)) {
			$this->instalacion->set_conexion_externa($this->db);
		}
		return $this->instalacion;
	}

	/**
	*	Devuelve una referencia a un INSTANCIA.
	* @return toba_modelo_instancia
	*/
	function get_instancia( $id_instancia, $manejador_interface=null)
	{
		if (! isset($manejador_interface)) {
			$manejador_interface = new toba_mock_proceso_gui();
		}
		if ( ! isset ( $this->instancia[ $id_instancia ] ) ) {
			$instalacion = $this->get_instalacion( $manejador_interface );
			$this->instancia[ $id_instancia ] = new toba_modelo_instancia( $instalacion, $id_instancia );
		}
		$this->instancia[ $id_instancia ]->set_manejador_interface( $manejador_interface );		
		return $this->instancia[ $id_instancia ];
	}
	
	/**
	*	Devuelve una referencia a un PROYECTO
	* @return toba_modelo_proyecto
	*/
	function get_proyecto( $id_instancia, $id_proyecto, $manejador_interface=null )
	{
		$instancia = $this->get_instancia( $id_instancia, $manejador_interface );
		$archivo_proy = $instancia->get_path_proyecto($id_proyecto)."/php/extension_toba/modelo_$id_proyecto.php";
		if (file_exists($archivo_proy)) {
			require_once($archivo_proy);
			$clase = 'modelo_'.$id_proyecto;
			$proyecto = new $clase( $instancia, $id_proyecto );
		} else {
			$proyecto = new toba_modelo_proyecto( $instancia, $id_proyecto );
		}
		if (! isset($manejador_interface)) {
			$manejador_interface = new toba_mock_proceso_gui();
		}		
		$proyecto->set_manejador_interface( $manejador_interface );
		return $proyecto;
	}

	/**
	 * Devuelve una referencia a los puntos de montaje de un proyecto
	 * @param toba_modelo_proyecto $proyecto
	 * @return toba_modelo_pms
	 */
	function get_pms(toba_modelo_proyecto $proyecto)
	{
		return new toba_modelo_pms($proyecto);
	}

	/**
	*	Devuelve una referencia al NUCLEO
	* @return toba_modelo_nucleo 
	*/
	function get_nucleo( $manejador_interface=null )
	{
		$nucleo = new toba_modelo_nucleo();
		if (! isset($manejador_interface)) {
			$manejador_interface = new toba_mock_proceso_gui();
		}		
		$nucleo->set_manejador_interface( $manejador_interface );
		return $nucleo;
	}

	function get_servicio_web( $proyecto, $identificador, $manejador_interface=null)
	{
		$servicio = new toba_modelo_servicio_web($proyecto, $identificador);
		if (! isset($manejador_interface)) {
			$manejador_interface = new toba_mock_proceso_gui();
		}
		$servicio->set_manejador_interface($manejador_interface);
		return $servicio;
	}		
	
	function get_rdi(toba_modelo_proyecto $proyecto, $manejador_interface=null)
	{
		$rdi = new toba_cliente_rdi();
		$rdi->set_proyecto($proyecto);
		$rdi->set_instalacion($this->get_instalacion($manejador_interface));
		return $rdi->get_cliente();
	}
	
	function set_db($db)
	{
		$this->db = $db;
	}
}
?>
