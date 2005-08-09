<?php
require_once("objeto_mdc.php");

class objeto_mdc_db extends objeto_mdc
{

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  Carga de CAMPOS EXTERNOS   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	private function actualizar_campos_externos()
	//Actualiza los campos externos despues de cargar el db_registros
	{
		foreach(array_keys($this->control) as $registro)
		{
			$this->actualizar_campos_externos_registro($registro);
		}	
	}
	
	private function actualizar_campos_externos_registro($id_registro, $evento=null)
	/*
		Recuperacion de valores para las columnas externas.
		Para que esto funcione, la consultas realizadas tienen que devolver un solo registro,
			cuyas claves asociativas se correspondan con la columna que se quiere
	*/
	{
		//Itero planes de carga externa
		if(isset($this->proceso_carga_externa)){
			foreach(array_keys($this->proceso_carga_externa) as $carga)
			{
				//SI entre por un evento, tengo que controlar que la carga este
				//Activada para eventos, si no esta activada paso al siguiente
				if(isset($evento)){
					if(! $this->proceso_carga_externa[$carga]['sincro_continua'] ){	
						continue;
					}
				}
				//-[ 1 ]- Recupero valores correspondientes al registro
				$parametros = $this->proceso_carga_externa[$carga];
				if($parametros['tipo']=="sql")											//--- carga SQL!!
				{
					// - 1 - Obtengo el query
					$sql = $parametros['sql'];
					// - 2 - Reemplazo valores llave con los parametros correspondientes a la fila actual
					foreach( $parametros['col_parametro'] as $col_llave ){
						$valor_llave = $this->datos[$id_registro][$col_llave];
						$sql = ereg_replace( apex_db_registros_separador . $col_llave . apex_db_registros_separador, $valor_llave, $sql);
					}
					//echo "<pre>SQL: "  . $sql . "<br>";
					// - 3 - Ejecuto SQL
					$datos = consultar_fuente($sql, $this->fuente);//ei_arbol($datos);
					//ei_arbol($this->datos);
				}
				elseif($parametros['tipo']=="dao")										//--- carga DAO!!
				{
					// - 1 - Armo los parametros para el DAO
					foreach( $parametros['col_parametro'] as $col_llave ){
						$param_dao[] = $this->datos[$id_registro][$col_llave];
					}
					//ei_arbol($param_dao,"Parametros para el DAO");
					// - 2 - Recupero datos
					include_once($parametros['include']);
					$datos = call_user_func_array(array($parametros['clase'],$parametros['metodo']), $param_dao);
				}
				//ei_arbol($datos,"datos");
				//-[ 2 ]- Seteo los valores recuperados en las columnas correspondientes
				foreach( $parametros['col_resultado'] as $columna_externa ){
					$this->datos[$id_registro][$columna_externa] = $datos[0][$columna_externa];
				}
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function sincronizar($control_tope_minimo=true)
	//Sincroniza las modificaciones del db_registros con la DB
	{
		$this->log("Inicio SINCRONIZACION"); 
		if($control_tope_minimo){
			if( $this->tope_min_registros != 0){
				if( ( $this->get_cantidad_registros() < $this->tope_min_registros) ){
					$this->log("No se cumplio con el tope minimo de registros necesarios" );
					throw new excepcion_toba("Los registros cargados no cumplen con el TOPE MINIMO necesario");
				}
			}
		}
		$this->controlar_alteracion_db();
		// No puedo ejecutar los cambios en cualguier orden
		// Necesito ejecutar primero los deletes, por si el usuario borra algo y despues inserta algo igual
		$inserts = array(); $deletes = array();	$updates = array();
		foreach(array_keys($this->control) as $registro){
			switch($this->control[$registro]['estado']){
				case "d":
					$deletes[] = $registro;
					break;
				case "i":
					$inserts[] = $registro;
					break;
				case "u":
					$updates[] = $registro;
					break;
			}
		}
		try{
			if($this->utilizar_transaccion) abrir_transaccion();
			$this->evt__pre_sincronizacion();
			$modificaciones = 0;
			//-- DELETE --
			foreach($deletes as $registro){
				$this->evt__pre_delete($registro);
				$this->eliminar($registro);
				$this->evt__post_delete($registro);
				$modificaciones ++;
			}
			//-- INSERT --
			foreach($inserts as $registro){
				$this->evt__pre_insert($registro);
				$this->insertar($registro);
				$this->evt__post_insert($registro);
				$modificaciones ++;
			}
			//-- UPDATE --
			foreach($updates as $registro){
				$this->evt__pre_update($registro);
				$this->modificar($registro);
				$this->evt__post_update($registro);
				$modificaciones ++;
			}
			$this->evt__post_sincronizacion();
			if($this->utilizar_transaccion) cerrar_transaccion();
			//Actualizo la estructura interna que mantiene el estado de los registros
			$this->sincronizar_estructura_control();
			$this->log("Fin SINCRONIZACION: $modificaciones."); 
			return $modificaciones;
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}
	}

	protected function insertar($id_registro)
	{
	}
	
	protected function modificar($id_registro)
	{
	}

	protected function eliminar($id_registro)
	{
	}

	//-------------------------------------------------------------------------------
	//------  EVENTOS de SINCRONIZACION  --------------------------------------------
	//-------------------------------------------------------------------------------
	/*
		Este es el lugar para meter validaciones, 
		si algo sale mal se deberia disparar una excepcion	
	*/

	protected function evt__pre_sincronizacion()
	{
	}
	
	protected function evt__post_sincronizacion()
	{
	}

	protected function evt__pre_insert($id)
	{
	}
	
	protected function evt__post_insert($id)
	{
	}
	
	protected function evt__pre_update($id)
	{
	}
	
	protected function evt__post_update($id)
	{
	}

	protected function evt__pre_delete($id)
	{
	}
	
	protected function evt__post_delete($id)
	{
	}

	//-------------------------------------------------------------------------------

	public function get_sql_inserts()
	{
		$sql = array();
		foreach(array_keys($this->control) as $registro){
			$sql[] = $this->generar_sql_insert($registro);
		}
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------  Control de SINCRONISMO  -----------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function controlar_alteracion_db()
	//Controla que los datos
	{
		/*
			Esto hay que pensarlo bien
		*/
	}

	private function controlar_alteracion_db_array()
	//Soporte al manejo transaccional OPTIMISTA
	//Indica si los datos iniciales extraidos de la base difieren de
	//los datos existentes en el momento de realizar la transaccion
	{
		$ok = true;
		$datos_actuales = $this->cargar_db();
		//Hay datos?
		if(is_array($datos_actuales)){
			//La cantidad de filas es la misma?
			if(count($datos_actuales) == count($this->datos_orig)){
				for($a=0;$a<count($this->datos_orig);$a++){
					//Existe la fila?
					if(isset($datos_actuales[$a])){
						foreach(array_keys($this->datos_orig[$a]) as $columna){
							//El valor de las columnas coincide?
							if($this->datos_orig[$a][$columna] !== $datos_actuales[$a][$columna]){
								$ok = false;
								break 2;
							}
						}
					}else{
						$ok = false;
						break 1;
					}
				}
			}else{
				$ok = false;
			}
		}else{
			$ok = false;
		}
		return $ok;
	}
	//-------------------------------------------------------------------------------

	private function controlar_alteracion_db_timestamp()
	//Esto tiene que basarse en una forma generica de trabajar sobre tablas
	//(Una columna que posea el timestamp, y triggers que los actualicen)
	{
	}
	
}