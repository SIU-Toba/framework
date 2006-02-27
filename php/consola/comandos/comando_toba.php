<?
require_once('consola/comando.php');
require_once('modelo/catalogo_modelo.php');

/*
	FALTA: Tendria que existir un esquema para extender un comando
			por ejemplo, despues de crear una instancia, un proyecto puede querer
			agregar mas tablas a la misma
*/
class comando_toba extends comando
{
	private $interprete; 

	function __construct( gui $manejador_interface, $interprete = null )
	{
		parent::__construct( $manejador_interface );
		$this->interprete = $interprete;
	}

	//-----------------------------------------------------------
	// Acceso a los SUJETOS sobre los que actuan los comandos
	//-----------------------------------------------------------

	/**
	*	Devuelve una referencia a la INSTANCIA
	*/
	protected function get_instancia()
	{
		return catalogo_modelo::instanciacion()->get_instancia(	$this->get_id_instancia_actual(),
																$this->consola );
	}

	/**
	*	Devuelve una referencia al PROYECTO 
	*/
	protected function get_proyecto()
	{
		return catalogo_modelo::instanciacion()->get_proyecto( 	$this->get_id_instancia_actual(),
																$this->get_id_proyecto_actual(),
																$this->consola );
	}

	/**
	*	Devuelve una referencia al la INSTALACION
	*/
	protected function get_instalacion()
	{
		return catalogo_modelo::instanciacion()->get_instalacion( $this->consola );
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	protected function get_nucleo()
	{
		return catalogo_modelo::instanciacion()->get_nucleo( $this->consola );
	}

	/**
	*	Devuelve una referencia al CONVERSOR
	*/
	protected function get_conversor()
	{
		return catalogo_modelo::instanciacion()->get_conversor( $this->get_id_instancia_actual(), $this->consola );
	}

	//-----------------------------------------------------------
	// Acceso a los PARAMETROS comunes
	//-----------------------------------------------------------

	/**
	*	Determina la INSTANCIA sobre la que se va a trabajar
	*/
	protected function get_id_instancia_actual( $obligatorio = true )
	{
		$id = null;
		$param = $this->get_parametros();
		if ( isset($param['-i'] ) && ( trim( $param['-i'] ) != '') ) {
			$id = $param['-i'];
		} else {
			$id = $this->get_entorno_id_instancia();
		}
		if ( $obligatorio && is_null( $id ) ) {
			throw new excepcion_toba("Es necesario definir una INSTANCIA. Utilice el modificador '-i' o defina la variable de entorno 'toba_instancia'");
		}
		return $id;
	}

	/**
	*	Describe el parametro INSTANCIA
	*/
	protected function get_info_parametro_instancia()
	{
		$ei = $this->get_entorno_id_instancia();
		$valor_i = isset( $ei ) ?  $ei : 'No definida';
		$this->consola->mensaje("[-i id_instancia] Asume el valor de la variable de entorno 'toba_instancia': $valor_i");
	}

	/**
	*	Determina el PROYECTO sobre el que se va a trabajar
	*/
	protected function get_id_proyecto_actual( $obligatorio = true )
	{
		$id = null;
		$param = $this->get_parametros();
		if ( isset($param['-p']) &&  (trim($param['-p']) != '') ) {
			$id = $param['-p'];
		} else {
			$id = $this->get_entorno_id_proyecto();
		}
		if ( $obligatorio && is_null( $id ) ) {
			throw new excepcion_toba("Es necesario definir un PROYECTO. Utilice el modificador '-p' o defina la variable de entorno 'toba_proyecto'");
		}
		return $id;
	}
	
	/**
	*	Describe el parametro PROYECTO
	*/
	protected function get_info_parametro_proyecto()
	{
		$ep = $this->get_entorno_id_proyecto();
		$valor_p = isset( $ep ) ?  $ep : 'No definida';
		$this->consola->mensaje("[-p id_proyecto] Asume el valor de la variable de entorno 'toba_proyecto': $valor_p");
	}

	//-----------------------------------------------------------
	// Acceso al entorno
	//-----------------------------------------------------------
	
	/**
	*	Acceso a la variable de entorno 'toba_instancia'
	*/
	protected function get_entorno_id_instancia( $obligatorio = false )
	{
		if ( isset( $this->interprete ) ) {
			
		} else {
			if ( isset( $_SERVER['toba_instancia'] ) ) {
				return $_SERVER['toba_instancia'];
			}
		}
	}

	/**
	*	Acceso a la variable de entorno 'toba_proyecto'
	*/
	protected function get_entorno_id_proyecto( $obligatorio = false )
	{
		if ( isset( $this->interprete ) ) {
			
		} else {
			if ( isset( $_SERVER['toba_proyecto'] ) ) {
				return $_SERVER['toba_proyecto'];
			}
		}
	}
}
?>