<?php
require_once("buffer_db.php");
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
	-- no_sql (array):			columnas que no se utilizan para operaciones SQL

	( ATENCION!!: Las entradas (orden, secuencia, no_duplicado, no_nulo y no_sql )
	tienen que tener como valor valores existentes en los arrays "columna" o "clave" )

	*** PENDIENTE ***

 -> Valores unicos producto de varias columnas...
 -> Obtencion y mapeo de informacion de COSMETICA...
 -> Metodo para controlar la perdida de sincronizacion por TIMESTAMP??
 -> Manejo de datos por referencia para disminuir la cantidad de memoria utilizada??
 -> Es necesario implementar UPDATES que solo incluyan columnas afectadas??
 -> Es realmente necesario fijar la clave interna a los registros??

*/
class buffer_db_s extends buffer_db
{
	function __construct($id, $definicion, $fuente)
	{
		if( !isset($definicion['columna'] )){
			$definicion['columna'] = array();
		}
		parent::__construct($id, $definicion, $fuente);
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
		//-[1]- EJECUTO SQL
		//-- INSERT --
		foreach(array_keys($sql_i) as $registro)
		{
			$this->ejecutar_sql($sql_i[$registro],false);
			if(count($this->campos_secuencia)>0){
				foreach($this->definicion['secuencia'] as $secuencia){
					//Actualizo el valor
					$this->datos[$registro][$secuencia['col']] = $this->recuperar_secuencia($secuencia['seq']);
				}
			}
		}
		//-- DELETE --
		foreach(array_keys($sql_d) as $registro){
			$this->ejecutar_sql($sql_d[$registro]);
		}
		//-- UPDATE --
		foreach(array_keys($sql_u) as $registro){
			$this->ejecutar_sql($sql_u[$registro]);
		}
		
		//-[2]- Todo bien, actualizo los METADATOS del BUFFER

		//-- INSERT --
		foreach(array_keys($sql_i) as $registro)
		{
			//Actualizo el valor del array de control
			$this->control[$registro]['estado'] = "db";
		}
		//-- DELETE --
		foreach(array_keys($sql_d) as $registro){
			unset($this->control[$registro]);
			unset($this->datos[$registro]);
		}
		//-- UPDATE --
		foreach(array_keys($sql_u) as $registro){
			$this->control[$registro]['estado'] = "db";
		}
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
			if(!isset($registro[$campo])){
				$set[] = " $campo = NULL ";
			}else{
				$set[] = " $campo = '". addslashes($registro[$campo]) . "' ";
			}
		}
		$sql = "UPDATE " . $this->definicion["tabla"] . " SET ".
				implode(", ",$set) .
				" WHERE " . implode(" AND ",$sql_where) .";";
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

}
?>