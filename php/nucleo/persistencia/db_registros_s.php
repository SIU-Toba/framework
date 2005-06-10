<?php
require_once("db_registros.php");
/*
	Buffer DB SIMPLE. Maneja una unica tabla

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
	-- externa (array):			columnas que no se utilizan para operaciones SQL

	( ATENCION!!: Las entradas (orden, secuencia, no_duplicado, externa y no_nulo )
	tienen que tener como valor valores existentes en los arrays "columna" o "clave" )

*/
class db_registros_s extends db_registros
{
	function __construct($id, $definicion, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		if( !isset($definicion['columna'] )){
			$definicion['columna'] = array();
		}
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}
	//-------------------------------------------------------------------------------

	function inicializar_definicion_campos()
	{
		//- CAMPOS: (columnas + claves)
		$this->campos = array_merge($this->definicion['clave'], $this->definicion['columna']);
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
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function insertar($id_registro)
	{
		//- 1 - Armo el SQL
		//Campos utilizados
		if(isset($this->definicion['externa'])){
			$campos_insert = array_diff($this->campos_manipulables, $this->definicion['externa']);
		}else{
			$campos_insert = $this->campos_manipulables;
		}
		$registro = $this->datos[$id_registro];
		//Escapo los caracteres que forman parte de la sintaxis SQL, seteo NULL
		foreach($campos_insert as $id_campo => $campo){
			if(!isset($registro[$campo]) || (trim($registro[$campo]) == "") ){
				$valores[$id_campo] = "NULL";
			}else{
				$valores[$id_campo] = "'" . addslashes(trim($registro[$campo])) . "'";
			}
		}
		$sql = "INSERT INTO " . $this->definicion["tabla"] .
				" ( " . implode(", ",$campos_insert) . " ) ".
				" VALUES (" . implode(", ", $valores) . ");";
		//- 2 - Ejecutar el SQL
		$this->log("registro: $id_registro - " . $sql); 
		ejecutar_sql( $sql, $this->fuente);
		if(count($this->campos_secuencia)>0){
			foreach($this->definicion['secuencia'] as $secuencia){
				//Actualizo el valor
				$this->datos[$id_registro][$secuencia['col']] = recuperar_secuencia($secuencia['seq'], $this->fuente);
			}
		}
		return $sql;
	}
	//-------------------------------------------------------------------------------
	
	function modificar($id_registro)
	{
		//- 1 - Armo el SQL
		//Campos utilizados
		if(isset($this->definicion['externa'])){
			$campos_update = array_diff($this->campos_manipulables, 
										$this->definicion['externa'],
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
			if( (!isset($registro[$campo])) || (trim($registro[$campo]) == "") ){
				$set[] = " $campo = NULL ";
			}else{
				$set[] = " $campo = '". addslashes($registro[$campo]) . "' ";
			}
		}
		$sql = "UPDATE " . $this->definicion["tabla"] . " SET ".
				implode(", ",$set) .
				" WHERE " . implode(" AND ",$sql_where) .";";
		//- 2 - Ejecutar el SQL
		$this->log("registro: $id_registro - " . $sql); 
		ejecutar_sql( $sql, $this->fuente);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function eliminar($id_registro)
	{
		//- 0 - Genero el WHERE
		$registro = $this->datos[$id_registro];
		foreach($this->definicion["clave"] as $clave){
			$sql_where[] =	"( $clave = '{$registro[$clave]}')";
		}
		//- 1 - Armo el SQL
		if($this->baja_logica){
			$sql = "UPDATE " . $this->definicion["tabla"] .
					" SET " . $this->baja_logica_columna . " = '". $this->baja_logica_valor ."' " .
					" WHERE " . implode(" AND ",$sql_where) .";";
		}else{
			$sql = "DELETE FROM " . $this->definicion["tabla"] .
					" WHERE " . implode(" AND ",$sql_where) .";";
		}
		//- 2 - Ejecutar el SQL
		$this->log("registro: $id_registro - " . $sql); 
		ejecutar_sql( $sql, $this->fuente);
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
	//-------------------------------------------------------------------------------
	
	function generar_sql_select()
	{
		//Campos utilizados
		if(isset($this->definicion['externa'])){
			$campos_select = array_diff($this->campos, $this->definicion['externa']);
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
}
?>