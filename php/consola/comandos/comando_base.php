<?php
require_once('comando_toba.php');

class comando_base extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de BASES de DATOS';
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba base OPCION [-d id_base]");
		$this->consola->enter();
	}	

	function tiene_definido_base()
	{
		$param = $this->get_parametros();
		if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
			return true;
		} else {
			return false;		
		}
	}
	
	function get_info_extra()
	{
		if ($this->tiene_definido_base()) {
			$db = $this->get_id_base_actual();
			$param = $this->get_instalacion()->get_parametros_base( $db );
			$salida = "";
			foreach ($param as $id => $valor) {
				$salida .= "$id: $valor\n";	
			}
			return $salida;
		}
	}
	
	/**
	 * Muestra un listado de las bases disponibles
	 * @gtk_icono info_chico.gif
	 */
	function opcion__listar()
	{
		$this->mostrar_bases_definidas();
	}
	
	
	/**
	 * Agrega la definición de una base al archivo bases.ini. 
	 * @consola_parametros Opcional: [-o base_origen] toma los datos de otra definicion
	 * @gtk_icono nucleo/agregar.gif  
	 * @gtk_param_extra registrar_base
	 */
	function opcion__registrar($parametros=null)
	{
		if (isset($parametros)) {
			list($def, $origen, $datos) = $parametros;			 
		}
		//--- Nombre del registro
		if (!isset($def)) {
			$def = $this->get_id_base_actual();
		}
		if ( $this->get_instalacion()->existe_base_datos_definida( $def ) ) {
			throw new toba_error( "Ya existe una base definida con el ID '$def'");
		}
		
		
		//--- Base de origen
		$param = $this->get_parametros();
		if (!isset($origen) && isset($param['-o']) &&  (trim($param['-o']) != '') ) {
			$origen =  $param['-o'];
		}
		if (isset($origen)) {
			if (! $this->get_instalacion()->existe_base_datos_definida($origen)) {
				throw new toba_error( "No existe la base origen '$origen'");
			}
			$datos = $this->get_instalacion()->get_parametros_base($origen);
		}
		
		//---- Datos	
		if (!isset($datos)) {
			$form = $this->consola->get_formulario("Definir una nueva BASE de DATOS");
			$form->agregar_campo( array( 'id' => 'motor', 	'nombre' => 'MOTOR (ej. postgres7)' ));
			$form->agregar_campo( array( 'id' => 'profile',	'nombre' => 'HOST/PROFILE (ej. localhost)' ));
			$form->agregar_campo( array( 'id' => 'puerto',	'nombre' => 'PUERTO (ej. 5432)', 'obligatorio' => false));
			$form->agregar_campo( array( 'id' => 'usuario', 'nombre' => 'USUARIO (ej. postgres)' ));
			$form->agregar_campo( array( 'id' => 'clave', 	'nombre' => 'CLAVE', 'obligatorio' => false ));
			$form->agregar_campo( array( 'id' => 'base', 	'nombre' => 'BASE' ));
			$form->agregar_campo( array( 'id' => 'schema', 'nombre' => 'SCHEMA (ej. public)' , 'obligatorio' => false));
			$datos = $form->procesar();
		}		
		if (! isset($datos['puerto']) || trim($datos['puerto']) == '') {			//Si no lo cargo en pantalla o no viene seteado del otro origen voy al default
			$datos['puerto'] = '5432';
		}
		//--- Registración
		$this->get_instalacion()->agregar_db( $def, $datos );
		if (! isset($datos['encoding']) || trim($datos['encoding']) == '') {
                    $this->get_instalacion()->determinar_encoding( $def );
		}
	}	
	

	/**
	 * Elimina la definición de la base en bases.ini
	 * @gtk_icono borrar.gif
	 * @gtk_separador 1 
	 */
	function opcion__desregistrar()
	{
		$i = $this->get_instalacion();
		$def = $this->get_id_base_actual();
		if ( $i->existe_base_datos_definida( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("DEFINICION: $def");
			$this->consola->lista_asociativa( $i->get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la definicion?") ) {
				$i->eliminar_db( $def );
			}
		} else {
			throw new toba_error( "NO EXISTE una base definida con el ID '$def'");
		}
	}	
	
	
	/**
	* Crea físicamente la base de datos
	* @gtk_icono nucleo/agregar.gif
	*/
	function opcion__crear()
	{
		$def = $this->get_id_base_actual();
		if( $this->get_instalacion()->existe_base_datos( $def ) !== true ) {
			$this->get_instalacion()->crear_base_datos( $def );
		} else {
			throw new toba_error( "La base '$def' ya está creada en el MOTOR");
		}
	}


	/**
	* Elimina físicamente la base de datos
	* @gtk_icono borrar.png
	*/
	function opcion__eliminar()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("BASE de DATOS: $def");
			$this->consola->lista_asociativa( $this->get_instalacion()->get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la BASE de DATOS?") ) {
				$this->get_instalacion()->borrar_base_datos( $def );
			}
		} else {
			throw new toba_error( "NO EXISTE una base '$def' en el MOTOR");
		}
	}
	
	/**
	* Ejecuta un archivo sql
	* @consola_parametros [-a archivo]
	* @gtk_icono sql.gif
	* @gtk_param_extra ejecutar_sql
	*/
	function opcion__ejecutar_sql($archivo=null)
	{
		if (! isset($archivo)) {
			$param = $this->get_parametros();
			if ( isset($param['-a']) &&  (trim($param['-a']) != '') ) {
				$archivo = $param['-a'];
			} else {
				throw new toba_error("Es necesario indicar el archivo a ejecutar. Utilice el modificador '-a'");
			}
		}
		$db = $this->get_instalacion()->conectar_base($this->get_id_base_actual());
		$db->ejecutar_archivo($archivo);
	}		

	/**
	* Chequea la conexión con la base
	* @gtk_icono fuente.png
	*/
	function opcion__test_conexion()
	{
		$def = $this->get_id_base_actual();
		$existe = $this->get_instalacion()->existe_base_datos( $def, array(), true );
		if ($existe === true) {
			$this->consola->mensaje('Conexion OK!');
		} else {
			$this->consola->error("No es posible conectarse a '$def': $existe");
		}
	}
	
	/**
	 * Actualiza las secuencias de la base, solo funciona con PostgreSQL
	 */
	function opcion__actualizar_secuencias()
	{
		$this->consola->mensaje("Actualizando secuencias", false);		
		$db = $this->get_instalacion()->conectar_base($this->get_id_base_actual());
		$secuencias = $db->get_lista_secuencias();
		$db->abrir_transaccion();
		foreach ($secuencias as $datos) {
			$sql_nuevo = "SELECT 
								max(CASE {$datos['campo']}::varchar ~ '^[0-9]+$' WHEN true THEN {$datos['campo']}::bigint ELSE 0 END) as nuevo		
						  FROM {$datos['tabla']}
			";			
			$res = $db->consultar($sql_nuevo, null, true);
			$nuevo = $res[0]['nuevo'];
			//Si no hay un maximo, es el primero del grupo
			if ($nuevo == NULL) {
				$nuevo = 1;
			}

			$sql = "SELECT setval('{$datos['nombre']}', $nuevo)";
			$db->consultar( $sql );				
			$this->consola->progreso_avanzar();
		}
		$db->cerrar_transaccion();		
		$this->consola->progreso_fin();
	}
	
	
	/**
	*	Determina sobre que base definida en 'info_bases' se va a trabajar
	*/
	private function get_id_base_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
			return $param['-d'];
		} else {
			throw new toba_error("Es necesario indicar el ID de la definición de la base. Utilice el modificador '-d'");
		}
	}	

}
?>