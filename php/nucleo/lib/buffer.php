<?php
define("apex_buffer_debug","0");

class buffer
/*
	*** DEFINICION *** (array asociativo con las siguientes entradas)
	
	-- tabla (string):			Nombre de la tabla
	-- control_sincro (0/1): 	Controlar que los datos no se modifiquen durante la transaccion
	-- clave (array): 			Claves de la tabla (no incluirlas en columna)
	-- columna (array): 		Columnas de la tabla
	-- orden (array): 			claves o columnas que se usan para ordenar los registros
					 			(facilita el algoritmo de control de sincro)
	-- secuencia (array[2]):	claves o columnas que son secuencias en la DB
								(Los valores son un array("col"=>"X", seq=>"Y")).
								Atencion: las columnas especificadas como secuencias no tienen que 
								figurar en los arrays 'no_duplicado' y 'no_nulo', porque esos
								campos solo indican controles en las columnas MANIPULABLES y
								la secuencia no lo es...
	-- no_duplicado (array): 	claves o columnas que son UNIQUE en la DB
	-- no_nulo (array):			columnas que no pueden ser ""
	-- no_sql (array):			columnas que no se utilizan para operaciones SQL

	( ATENCION!!: Las entradas (orden, secuencia, no_duplicado, no_nulo y no_sql )
	tienen que tener como valor valores existentes en los arrays "columna" o "clave" )

	*** PENDIENTE ***

 -> Hay que hacer algo con el manejo de NULLs...
 -> Valores unicos producto de varias columnas...
 -> Obtencion y mapeo de informacion de COSMETICA...
 -> Metodo para controlar la perdida de sincronizacion por TIMESTAMP??
 -> Manejo de datos por referencia para disminuir la cantidad de memoria utilizada??
 -> Es necesario implementar UPDATES que solo incluyan columnas afectadas??
 -> Es realmente necesario fijar la clave interna a los registros??
*/
{
	var $definicion;				//Definicion que indica la construccion del BUFFER
	var $fuente;					//Fuente de datos utilizada
	var $identificador;				//Identificador del registro
	var $campos;					//Campos del BUFFER
	var $campos_secuencia;		
	var $campos_manipulables;		
	var $where;						//Condicion utilizada para cargar datos - WHERE
	var $from;						//Condicion utilizada para cargar datos - FROM
	var $control = array();			//Estructura de control
	var $datos = array();			//Datos cargados en el BUFFER
	var $datos_orig = array();		//Datos tal cual salieron de la DB (Control de SINCRO)
	var $proximo_registro = 0;		//Posicion del proximo registro en el array de datos
	var $control_sincro_db;			//Se activa el control de sincronizacion con la DB?
	var $posicion_finalizador;		//Posicion del objeto en el array de finalizacion
	var $sql;						//Array de SQLs ejecutados

	function buffer($id, $definicion, $fuente)
	{
		$this->identificador = $id; //ID unico, para buscarse en la sesion
		$this->definicion = $definicion;
		$this->fuente = $fuente;
		//la interaccion con la interface?
		if(isset($this->definicion["control_sincro"])){
			if($this->definicion["control_sincro"]=="1"){	
				$this->control_sincro_db = true;
			}else{
				$this->control_sincro_db = false;
			}
		}else{
			$this->control_sincro_db = false;
		}
		//Registro la finalizacion del objeto
		$this->posicion_finalizador = registrar_finalizacion( $this );
		//Inicializar la estructura de campos
		$this->inicializar_definicion_campos();
		//-- Si el BUFFER fue creado en el request previo, lo recargo
		if( $this->existe_instanciacion_previa() ){
			$this->cargar_datos_sesion();
		}

	}
	//-------------------------------------------------------------------------------

	function inicializar_definicion_campos()
	{
		//- CAMPOS: (columnas + claves)
		$this->campos = array_merge($this->definicion['clave'],$this->definicion['columna']);
		//ei_arbol($this->campos,"campos");
		//- CAMPOS_SECUENCIA:
		if(isset($this->definicion['secuencia'])){
			for($a=0;$a<count($this->definicion['secuencia']);$a++){
				$this->campos_secuencia[] = $this->definicion['secuencia'][$a]['col'];
			}
		}else{
			$this->campos_secuencia = array();
		}
		//- CAMPOS_MANIPULABLES:
		$this->campos_manipulables = array_diff($this->campos, $this->campos_secuencia);
		//$this->campos_manipulables = $this->campos;
		//- CAMPOS no DUPLICADOS:
		if(isset($this->definicion['no_duplicado'])){
			//Solo hay que trabajar sobre los manipulables
			$this->campos_no_duplicados = array_diff($this->definicion['no_duplicado'], $this->campos_secuencia);
		}else{
			$this->campos_no_duplicados = array();
		}
		//- CAMPOS no NULOS
		if(isset($this->definicion['no_nulo'])){
			//Solo hay que trabajar sobre los manipulables
			$this->campos_no_nulo = array_diff($this->definicion['no_nulo'], $this->campos_secuencia);
		}else{
			$this->campos_no_nulo = array();
		}
	}
	//-------------------------------------------------------------------------------

	function finalizar()
	//Finaliza la ejecucion del buffer
	{
		$this->guardar_datos_sesion();
	}
	//-------------------------------------------------------------------------------

	function desregistrar_finalizacion()
	//Desregistrar el destructor, por si se necesita eliminar un objeto registrado
	{
		desregistar_finalizacion($this->posicion_finalizador);
	}
	//-------------------------------------------------------------------------------

	function info($mostrar_datos=false)
	//Informacion del buffer
	{
		$estado['control']=$this->control;
		$estado['proximo_registro']=$this->proximo_registro;
		$estado['where']=$this->where;
		$estado['from']=$this->from;
		if($mostrar_datos) $estado['datos']=$this->datos;
		return $estado;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------  Manejo GENERAL de DATOS  ---------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_datos($where=null, $from=null)
	//Cargar datos en el BUFFER (DB o SESION). 
	{
		if( $this->existe_instanciacion_previa() ){
			//Es posible que el usuario haya cambiado de WHERE
			if( !($this->controlar_conservacion_where($where)) ){
				$this->cargar_datos_db($where, $from);
			}
		}else{
			$this->cargar_datos_db($where, $from);
		}
	}
	//-------------------------------------------------------------------------------
	
	function controlar_conservacion_where($where)
	//Si el consumidor cambia el WHERE, hay que traer datos de
	//la base nuevamente. Mejorar controles
	{
		if(!isset($this->where)){
			if(isset($where)){
				if(apex_buffer_debug) echo "BUFFER {$this->identificador} - Control WHERE: No existe<br>";
				return false;	
			}
		}else{
			for($a=0;$a<count($this->where);$a++){
				if(!isset($where[$a])){
					if(apex_buffer_debug) echo "BUFFER {$this->identificador} - Control WHERE: nuevo mas corto<br>"; 
					return false;
				}else{
					if($where[$a] !== $this->where[$a]){
						if(apex_buffer_debug) echo "BUFFER {$this->identificador} - Control WHERE: nuevo distinto<br>"; 
						return false;	
					}
				}
			}
		}
		if(apex_buffer_debug) echo "BUFFER {$this->identificador} - Control WHERE: OK!<br>";
		return true;
	}
	//-------------------------------------------------------------------------------

	function cargar_datos_db($where=null, $from=null)
	//Cargo los BUFFERS con datos de la DB
	//ATENCION: Los datos solo se cargan si se le pasa como parametro un WHERE
	{
		if(apex_buffer_debug) echo "BUFFER {$this->identificador} - Cargar de DB<br>";
		$this->where = $where;
		$this->from = $from;
		//Obtengo los datos de la DB
		if(!isset($where)){
			//ei_arbol($where,"WHERE con datos");
			$this->datos = $this->cargar_db();
		}else{
			$this->datos = array();
		}
		//ei_arbol($this->datos);
		//Se solicita control de SINCRONIA a la DB?
		if(isset($this->definicion["control_sincro"])){
			if($this->definicion["control_sincro"]=="1"){	
				$this->datos_orig = $this->datos;
			}
		}
		//Genero la estructura de control
		$this->control = array();
		for($a=0;$a<count($this->datos);$a++){
			$this->control[$a]['estado']="db";
			//Creo la columna que referencia a la posicion del registro en el BUFFER
			$this->datos[$a][apex_buffer_clave]=$a;
		}
		//Le saco los caracteres de escape a los valores traidos de la DB
		for($a=0;$a<count($this->datos);$a++){
			foreach(array_keys($this->datos[$a]) as $columna){
				$this->datos[$a][$columna] = stripslashes($this->datos[$a][$columna]);
			}	
		}
		//Actualizo la posicion en que hay que incorporar al proximo registro
		$this->proximo_registro = count($this->datos);	
	}
	//-------------------------------------------------------------------------------

	function cargar_db($carga_estricta=false)
	//Cargo los BUFFERS con datos de la DB
	//Los datos son 
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = $this->generar_sql_select();
		//echo $sql . "<br>";
		//-- Intento cargar el BUFFER
		$rs = $db[$this->fuente][apex_db_con]->Execute($sql);
		if((!$rs)){
			monitor::evento("bug","[BUFFER: {$this->identificador} ] Error cargando DATOS'. $sql . "
						.$db[$this->fuente][apex_db_con]->ErrorMsg());
		}
		if($rs->EOF){
			if($carga_estricta){
				monitor::evento("bug","[BUFFER: {$this->identificador} ] No se recuperarron DATOS' ");
			}
			return null;
		}else{
			$datos =& $rs->getArray();
			//ei_arbol($datos);
			//Los campos NO SQL deberian estar metidos en el array
			if(isset($this->definicion['no_sql'])){
				foreach($this->definicion['no_sql'] as $no_sql){
					for($a=0;$a<count($datos);$a++){
						$datos[$a][$no_sql] = "";
					}
				}
			}
			return $datos; 
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_datos_sesion()
	//Cargo el BUFFER desde la sesion
	{
		if(apex_buffer_debug) echo "BUFFER {$this->identificador} - Cargar de SESION<br>";
		global $solicitud;
		$datos = $solicitud->hilo->recuperar_dato_global($this->identificador);
		//Traera un problema el pasaje por referencia
		$this->datos = $datos['datos'];
		$this->datos_orig = $datos['datos_orig'];
		$this->control = $datos['control'];
		$this->proximo_registro = $datos['proximo_registro'];
		$this->where = $datos['where'];
		$this->from = $datos['from'];
	}
	//-------------------------------------------------------------------------------

	function guardar_datos_sesion()
	//Guardo datos en la sesion
	{
		global $solicitud;
		$datos['where'] = $this->where;
		$datos['from'] = $this->from;
		$datos['datos'] = $this->datos;
		$datos['datos_orig'] = $this->datos_orig;
		$datos['control'] = $this->control;
		$datos['proximo_registro'] = $this->proximo_registro;
		$solicitud->hilo->persistir_dato_global($this->identificador, $datos, true);
	}
	//-------------------------------------------------------------------------------
	
	function existe_instanciacion_previa()
	{
		global $solicitud;
		return $solicitud->hilo->existe_dato_global($this->identificador);
	}
	//-------------------------------------------------------------------------------

/*	function resetear()
	{
		if($this->existe_instanciacion_previa()){
			global $solicitud;
			return $solicitud->hilo->eliminar_dato_global($this->identificador);
		}
	}
*/
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------  Primitivas de ACCESO y MODIFICACION de REGISTROS   -------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_registros($condiciones=null)
	{
		$datos = null;
		foreach(array_keys($this->control) as $registro){
			//Si no esta eliminado, lo devuelvo
			if($this->control[$registro]['estado']!="d"){
				$datos[] = $this->datos[$registro];
			}
		}
		return $datos;
	}
	//-------------------------------------------------------------------------------

	function obtener_registros_a_insertar($condiciones=null)
	{
		$datos = null;
		foreach(array_keys($this->control) as $registro){
			//Si no esta eliminado, lo devuelvo
			if($this->control[$registro]['estado']=="i"){
				$datos[] = $this->datos[$registro];
			}
		}
		return $datos;
	}
	//-------------------------------------------------------------------------------
	
	function obtener_registro($id)
	{
		if(isset($this->datos[$id])){
			return  $this->datos[$id];
		}else{
			return null;
		}
	}
	//-------------------------------------------------------------------------------

	function agregar_registro($registro)
	{
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_buffer_clave])) unset($registro[apex_buffer_clave]);
		$resultado = $this->validar_registro($registro);
		if( $resultado[0] == "ok" ){
			$registro[apex_buffer_clave]=$this->proximo_registro;
			$this->datos[$this->proximo_registro] = $registro;
			$this->control[$this->proximo_registro]['estado'] = "i";
			$this->proximo_registro++;
		}else{
			throw new excepcion_toba("BUFFER: ERROR de validacion. " . $resultado[1]);
		}
	}
	//-------------------------------------------------------------------------------

	function modificar_registro($registro, $id)
	{
		if(!isset($this->datos[$id])){
			throw new excepcion_toba("BUFFER: No existe un registro con el INDICE indicado");
		}
		//Saco el campo que indica la posicion del registro
		if(isset($registro[apex_buffer_clave])) unset($registro[apex_buffer_clave]);
		$resultado = $this->validar_registro($registro, $id);
		if( $resultado[0] == "ok" ){
			if($this->control[$id]['estado']=="i"){
				$this->datos[$id] = $registro;
				$this->datos[$id][apex_buffer_clave] = $id; 
			}else{
				$this->control[$id]['estado']="u";
				foreach(array_keys($registro) as $clave){
					$this->datos[$id][$clave] = $registro[$clave];
				}
				$this->datos[$id][apex_buffer_clave] = $id; 
			}
		}else{
			throw new excepcion_toba("BUFFER: ERROR de validacion. " . $resultado[1]);
		}
	}
	//-------------------------------------------------------------------------------

	function eliminar_registro($id=null)
	{
		if(!isset($this->datos[$id])){
			throw new excepcion_toba("BUFFER: No existe un registro con el INDICE indicado");
		}
		if($this->control[$id]['estado']=="i"){
			unset($this->control[$id]);
			unset($this->datos[$id]);
		}else{
			$this->control[$id]['estado']="d";
		}
	}
	//-------------------------------------------------------------------------------

	function cantidad_registros()
	{
		return count($this->datos);
	}
	//-------------------------------------------------------------------------------

	function obtener_registro_valor($id, $columna)
	{
		if(isset($this->datos[$id][$columna])){
			return  $this->datos[$id][$columna];
		}else{
			return null;
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------  Primitivas de ACCESO y MODIFICACION de COLUMNAS   --------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_columna_valores($columna)
	//Obtiene una columna
	{
		$datos_columna = null;
		foreach(array_keys($this->control) as $registro){
			if($this->control[$registro]['estado']!="d"){
				$datos_columna[$registro] = $this->datos[$registro][$columna];
			}
		}
		return $datos_columna;
	}
	//-------------------------------------------------------------------------------

	function establecer_valor_columna($columna, $valor)
	//Setea todas las columnas con un valor
	{
		foreach(array_keys($this->control) as $registro){
			if($this->control[$registro]['estado']!="d"){
				$this->datos[$registro][$columna] = $valor;
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  VALIDACION de REGISTROS   ------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function validar_registro($registro, $id=null)
	//Valida el registro
	{
		//Estructura
		$resultado = $this->control_estructura_registro($registro);
		if($resultado[0]== "ok"){
			//Nulos
			$resultado = $this->control_nulos($registro);
			if($resultado[0] == "ok"){
				//Unicos
				$resultado = $this->control_valores_unicos($registro, $id);
				if($resultado[0] == "ok"){
					return array("ok","El registro es valido.");
				}else{
					return array("buffer", $resultado[1]);
				}
			}else{
				return array("buffer", $resultado[1]);
			}
		}else{
			return array("buffer", $resultado[1]);
		}		
	}
	//-------------------------------------------------------------------------------

	function control_estructura_registro($registro)
	//Controla que los campos del registro existan
	{
		foreach($registro as $campo => $valor){
			//SI el registro no esta en la lista de manipulables o 
			//en las secuencias...
			if( !((in_array($campo, $this->campos_manipulables)) ||
				(in_array($campo,$this->campos_secuencia)) ) ){
				return array("buffer","El campo '$campo' no existe.");
			}
		}
		return array("ok","Estructura OK");
	}
	//-------------------------------------------------------------------------------

	function control_valores_unicos($registro, $id=null)
	//Controla que no se dupliquen valores unicos del BUFFER
	{
		foreach($this->campos_no_duplicados as $campo)
		{
			//Busco los valores existentes en la columna
			$valores_columna = $this->obtener_columna_valores($campo);
			//Si esto es llamado desde un MODIFICAR, 
			//tengo que sacar de la lista al propio registro
			if(isset($id)){
				unset($valores_columna[$id]);
			}
			if(is_array($valores_columna)){
				//Controlo que el nuevo valor no exista
				if(in_array($registro[$campo], $valores_columna)){
					return array("buffer","El valor '".$registro[$campo]
											."' crea un duplicado en el campo '" . $campo . "'.");
				}
			}
		}
		return array("ok","No duplicados OK.");
	}
	//-------------------------------------------------------------------------------
	
	function control_nulos($registro)
	//Controla que los valores obligatorios existan
	{
		foreach($this->campos_no_nulo as $campo){
			if(isset($registro[$campo])){
				if((trim($registro[$campo]==""))||(trim($registro[$campo]=="NULL"))){
					return array("buffer","El campo '". $campo ."' no admite valores NULOS.");
				}
			}else{
				return array("buffer","Es necesario especificar un valor para el campo '". $campo ."'.");
			}
		}
		return array("ok","No nulo OK.");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function sincronizar_db()
	//Sincroniza las modificaciones del BUFFER con la DB
	//ATENCION, mejorar control de errores
	{
		if($this->control_sincro_db){
			$ok = $this->controlar_alteracion_db();
		}
		//-<1>- Crear ARRAYS de SQLs de SINCRONIZACION
		$sql_i=array(); $sql_d=array(); $sql_u=array();
		foreach(array_keys($this->control) as $registro){
			switch($this->control[$registro]['estado']){
				case "d":
					$sql_d[$registro] = $this->generar_sql_delete($registro);
					break;
				case "i":
					$sql_i[$registro] = $this->generar_sql_insert($registro);
					break;
				case "u":
					$sql_u[$registro] = $this->generar_sql_update($registro);
					break;
			}
		}

		//$sql = array_merge($sql_d, $sql_u, $sql_i);
		//ei_arbol($sql, "SQL de sincronizacion con la DB");
		//-<2>- Ejecuto los SQL en el motor
		//ATENCION! hay que cambiar esto, los inserts van primero
		//-- INSERT --
		foreach(array_keys($sql_i) as $registro){
			$resultado = $this->ejecutar_sql($sql_i[$registro],false);
			
			if($resultado[0]!="ok"){
				$resultado[1] = "Error INSERTANDO el registro '$registro'. " . $resultado[1];
				throw new excepcion_toba( $resultado[1] );
				//return $resultado;
			}else{
				//Recupero secuencias
				if(count($this->campos_secuencia)>0){
					foreach($this->definicion['secuencia'] as $secuencia){
						$resultado = $this->recuperar_secuencia($secuencia['seq']);

						if($resultado[0] != "ok"){
							return $resultado;
						}else{
							//Actualizo el valor del BUFFER
							$this->datos[$registro][$secuencia['col']] = $resultado[1];
							//Actualizo el valor del array de control
							$this->control[$registro]['estado'] = "db";
						}
					}
				}
			}
		}
		//-- DELETE --
		foreach(array_keys($sql_d) as $registro){
			$resultado = $this->ejecutar_sql($sql_d[$registro]);
			if($resultado[0]!="ok"){
				$resultado[1] = "Error ELIMINANDO el registro '$registro'. " . $resultado[1];
				return $resultado;
			}else{
				//Actualizo el estado del array de control
				unset($this->control[$registro]);
				unset($this->datos[$registro]);
			}
		}
		//-- UPDATE --
		foreach(array_keys($sql_u) as $registro){
			$resultado = $this->ejecutar_sql($sql_u[$registro]);
			if($resultado[0]!="ok"){
				$resultado[1] = "Error MODIFICANDO el registro '$registro'. " . $resultado[1];
				return $resultado;
			}else{
				//Actualizo el estado del array de control
				$this->control[$registro]['estado'] = "db";
			}
		}
		return array("ok","El buffer se sincronizo satisfactoriamente.");
	}
	//-------------------------------------------------------------------------------

	function ejecutar_sql($sql,$controlar_ar=true)
	//ATENCION!!!!! los update fallan pero el error no se reporta!!!
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		
		//if($db[$this->fuente][apex_db_con]->Execute($sql) === true){
		if(!$db[$this->fuente][apex_db_con]->Execute($sql)){
			return array("buffer","El SQL no se ejecuto correctamente. " 
							.$db[$this->fuente][apex_db_con]->ErrorMsg() . " SQL: " . $sql);
		}else{
			if($controlar_ar){
				$registros_afectados = $db[$this->fuente][apex_db_con]->affected_rows();
				//echo "REGISTROS: " . $registros_afectados;
				if($registros_afectados === 1){
					$this->sql[] = $sql;
					return array("ok","El SQL se ejecuto correctamente.");
				}else{
					return array("buffer","No hay registros afectados.");
				}
			}else{
				$this->sql[] = $sql;
				return array("ok","El SQL se ejecuto correctamente.");
			}
		}
	}
	//-------------------------------------------------------------------------------

	function recuperar_secuencia($secuencia)
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "SELECT currval('$secuencia') as seq;";
		$rs = $db[$this->fuente][apex_db_con]->Execute($sql);
		
		//print $sql;
		
		if((!$rs)){
			return array("buffer","El sql de recuperacion de secuencia esta mal formado." 
							.$db[$this->fuente][apex_db_con]->ErrorMsg());
		}
		if($rs->EOF){
			return array("buffer","Error recuperando la secuencia.");
		}else{
			$datos =& $rs->getArray();
			return array("ok",$datos[0]['seq']);
		}
	}	
	
	function obtener_sql_ejecutado()
	{
		return $this->sql;	
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_sql_select()
	{
		//Campos utilizados
		if(isset($this->definicion['no_sql'])){
			$campos_select = array_diff($this->campos, $this->definicion['no_sql']);
		}else{
			$campos_select = $this->campos;
		}
		$sql =	" SELECT	a." . implode(",	a.",$campos_select) . 
				" FROM "	. $this->definicion["tabla"] . " a ";
		if(isset($this->from)){
			$sql .= ", " . implode(",",$this->from);
		}
		$sql .= " WHERE " .	implode(" AND ",$this->where) .";";
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_insert($id_registro)
	//Genera sentencia de INSERT
	{
		//Campos utilizados
		if(isset($this->definicion['no_sql'])){
			$campos_insert = array_diff($this->campos_manipulables, $this->definicion['no_sql']);
		}else{
			$campos_insert = $this->campos_manipulables;
		}
		$registro = $this->datos[$id_registro];
		//Escapo los caracteres que forman parte de la sintaxis SQL
		foreach($campos_insert as $id_campo => $campo){
			if(isset($registro[$campo])){
				$valores[$id_campo] = addslashes($registro[$campo]);	
			}else{
				$valores[$id_campo] = "NULL";
			}
		}
		$sql = "INSERT INTO " . $this->definicion["tabla"] .
				" ( " . implode(",",$campos_insert) . " ) ".
				" VALUES ('" . implode("','", $valores) . "');";
		//Formateo NULOS
		$sql = ereg_replace("'NULL'","null",$sql);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_update($id_registro)
	//Genera sentencia de UPDATE
	{
		//Campos utilizados
		if(isset($this->definicion['no_sql'])){
			$campos_update = array_diff($this->campos_manipulables, 
										$this->definicion['no_sql'],
										$this->definicion['clave']);
		}else{
			$campos_update = array_diff($this->campos_manipulables, 
										$this->definicion['clave']);
		}
		$registro = $this->datos[$id_registro];
		//Genero el WHERE
		foreach($this->definicion["clave"] as $clave){
			$sql_where[] =	"( $clave = '{$registro[$clave]}')";
		}
		//Escapo los caracteres que forman parte de la sintaxis SQL
		foreach($campos_update as $campo){
			$set[] = " $campo = '". addslashes($registro[$campo]) . "' ";
		}
		$sql = "UPDATE " . $this->definicion["tabla"] . " SET ".
				implode(", ",$set) .
				" WHERE " . implode(" AND ",$sql_where) .";";
		//Formateo NULOS
		$sql = ereg_replace("'NULL'","NULL",$sql);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_delete($id_registro)
	//Genera sentencia de DELETE
	{
		$registro = $this->datos[$id_registro];
		//Genero el WHERE
		foreach($this->definicion["clave"] as $clave){
			$sql_where[] =	"( $clave = '{$registro[$clave]}')";
		}
		$sql = "DELETE FROM " . $this->definicion["tabla"] .
				" WHERE " . implode(" AND ",$sql_where) .";";
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------  Control de SINCRONISMO  -----------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function controlar_alteracion_db_array()
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

	function controlar_alteracion_db_timestamp()
	//Esto tiene que basarse en una forma generica de trabajar sobre tablas
	//(Una columna que posea el timestamp, y triggers que los actualicen)
	{
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion()
	{
		return $this->definicion;
	}
	//-------------------------------------------------------------------------------
}
?>