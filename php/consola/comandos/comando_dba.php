<?
require_once('comando_toba.php');

class comando_dba extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de bases de datos';
	}

	/**
	*	Lista las BASES DEFINIDAS en el entorno.
	*/
	function opcion__info()
	{
		$a = 0;
		foreach( dba::get_lista_bases_archivo() as $db ) {
			$base[ $a ]['nombre'] = $db;
			$param = dba::get_parametros_base( $db );
			$base[ $a ]['parametros'] = implode(',  ',$param);
			//$base[ $a ]['existe'] = dba::existe_base_datos( $db );//Alenta
			$a++; 
		}
		$txt_param = implode( ', ', array_keys( $param) );
		$this->consola->tabla( $base , array( 'BASE', "Parametros ( $txt_param )" ) );
	}
	
	/**
	*	Agrega una BASE en el entorno. [-d 'id_base']
	*/
	function opcion__agregar_def()
	{
		$def = $this->get_id_definicion_actual();
		if ( dba::existe_base_datos_definida( $def ) ) {
			throw new excepcion_toba( "Ya existe una base definida con el ID '$def'");
		}
		$form = $this->consola->get_formulario("Definir una nueva BASE de DATOS");
		$form->agregar_campo( array( 'id' => 'motor', 	'nombre' => 'MOTOR' ) );
		$form->agregar_campo( array( 'id' => 'profile',	'nombre' => 'HOST/PROFILE' ) );
		$form->agregar_campo( array( 'id' => 'usuario', 'nombre' => 'USUARIO' ) );
		$form->agregar_campo( array( 'id' => 'clave', 	'nombre' => 'CLAVE' ) );
		$form->agregar_campo( array( 'id' => 'base', 	'nombre' => 'BASE' ) );
		$datos = $form->procesar();
		dba::agregar_definicion( $def, $datos );
	}

	/**
	*	Elimina una BASE en el entorno. [-d 'id_base']
	*/
	function opcion__eliminar_def()
	{
		$def = $this->get_id_definicion_actual();
		if ( dba::existe_base_datos_definida( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("DEFINICION: $def");
			$this->consola->lista_asociativa( dba::get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la definicion?") ) {
				dba::eliminar_definicion( $def );
			}
		} else {
			throw new excepcion_toba( "NO EXISTE una base definida con el ID '$def'");
		}
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