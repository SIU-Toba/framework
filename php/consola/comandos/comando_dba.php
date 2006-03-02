<?
require_once('comando_toba.php');

class comando_dba extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de BASES de DATOS';
	}

	/**
	*	Lista las BASES DEFINIDAS en la instalacion.
	*/
	function opcion__info()
	{
		$this->consola->enter();
		comando_toba::mostrar_bases_definidas();
	}
	
	/**
	*	Crea una BASE DEFINIDA en el motor. [-d 'id_base']
	*/
	function opcion__crear_base()
	{
		$def = $this->get_id_definicion_actual();
		if( ! dba::existe_base_datos( $def ) ) {
			dba::crear_base_datos( $def );
		}
	}

	/**
	*	Elimina una BASE DEFINIDA existente dentro del motor. [-d 'id_base']
	*/
	function opcion__eliminar_base()
	{
		$def = $this->get_id_definicion_actual();
		if ( dba::existe_base_datos( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("BASE de DATOS: $def");
			$this->consola->lista_asociativa( dba::get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la BASE de DATOS?") ) {
				dba::borrar_base_datos( $def );
			}
		} else {
			throw new excepcion_toba( "NO EXISTE una base '$def' en el MOTOR");
		}
	}

	/**
	*	Ejecuta SQL sobre la base seleccionada.
	*/
	function falta_opcion__sql()
	{
	}

	/**
	*	Determina sobre que base definida en 'info_bases' se va a trabajar
	*/
	private function get_id_definicion_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
			return $param['-d'];
		} else {
			throw new excepcion_toba("Es necesario indicar el ID de la BASE a utilizar. Utilice el modificador '-d'");
		}
	}
}
?>