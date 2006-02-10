<?
/*
	FALTA: Tendria que existir un esquema para extender un comando
			por ejemplo, despues de crear una instancia, un proyecto puede querer
			agregar mas tablas a la misma
*/
require_once('consola/comando.php');
require_once('modelo/catalogo_modelo.php');

class comando_toba extends comando
{
	private $instancia;
	
	/**
	*	Acceso a la variable de entorno 'toba_instancia'
	*/
	protected function get_entorno_id_instancia( $obligatorio = false )
	{
		if( !isset( $_SERVER['toba_instancia'] ) ) {
			if( $obligatorio ) {
				throw new excepcion_toba("COMANDO_TOBA: La variable de entorno 'toba_instancia' no esta definida");
			} else {
				return null;	
			}
		}
		return $_SERVER['toba_instancia'];
	}

	/**
	*	Acceso a la variable de entorno 'toba_instancia'
	*/
	protected function get_entorno_id_proyecto( $obligatorio = false )
	{
		if( !isset( $_SERVER['toba_proyecto'] ) ) {
			if( $obligatorio ) {
				throw new excepcion_toba("COMANDO_TOBA: La variable de entorno 'toba_proyecto' no esta definida");
			} else {
				return null;	
			}
		}
		return $_SERVER['toba_proyecto'];
	}
	
	//-----------------------------------------------------------
	// Acceso a los SUJETOS sobre los que actuan los comandos
	//-----------------------------------------------------------

	/**
	*	Devuelve una referencia a la INSTANCIA
	*/
	protected function get_instancia()
	{
		if ( ! isset ( $this->instancia ) ) {
			$this->instancia = catalogo_modelo::get_instancia( 	$this->get_id_instancia_actual(), $this->consola );
		}
		return $this->instancia;
	}

	/**
	*	Devuelve una referencia al PROYECTO 
	*/
	protected function get_proyecto()
	{
		return catalogo_modelo::get_proyecto( 	$this->get_id_instancia_actual(),
												$this->get_id_proyecto_actual(),
												$this->consola );
	}

	/**
	*	Devuelve una referencia al la INSTALACION
	*/
	protected function get_instalacion()
	{
		return catalogo_modelo::get_instalacion( $this->consola );
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	protected function get_nucleo()
	{
		return catalogo_modelo::get_nucleo( $this->consola );
	}
}
?>