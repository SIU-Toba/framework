<?php
require_once("buffer_db.php");

class buffer_db_mt extends buffer_db
{
	function __construct($id, $definicion, $fuente)
	{
		parent::__construct($id, $definicion, $fuente);
		foreach($this->definicion['tabla'] as $tabla)
		{
			if(! isset($this->definicion[$tabla]['columna'] )){
				$this->definicion[$tabla]['columna'] = array();
			}			
		}
	}
	//-------------------------------------------------------------------------------

	function inicializar_definicion_campos()
	{
/*
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
*/
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
			unset($this->control[$registro]);
			unset($this->datos[$registro]);
		}
		//-- UPDATE --
		foreach(array_keys($sql_u) as $registro){
			$this->ejecutar_sql($sql_u[$registro]);
			$this->control[$registro]['estado'] = "db";
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
		$prefijo_alias = "xt_";
		for($t=0;$t<count($this->definicion['tabla']);$t++)
		{
			$alias = $prefijo_alias . $t; //Alias de la tabla
			$tabla = $this->definicion['tabla'][$t];
			//-- FROM
			$tablas_from[] = "$tabla $alias";
			//Armo la lista de campos por tabla
			//-- COLUMNAS
			$campos = array_merge( $this->definicion[$tabla]['columna'], $this->definicion[$tabla]['clave'] );
			//Elimino campos NO SQL
			if(isset($this->definicion['tabla']['no_sql'])){
				$campos = array_diff( $campos, $this->definicion[$tabla]['no_sql'] );
			}
			foreach($campos as $campo){
				//Los campos duplicados los comprimo
				$campos_select[$campo] = "$alias.$campo as $campo";
			}
			//-- WHERE
			if($t > 0){	//Relaciones de las tablas hijas con la maestra
				foreach($this->definicion['relacion'][$tabla] as $relacion ){
					$where[] = $alias . "." . $relacion['pk'] . " = " . $prefijo_alias . "0." . $relacion['fk'];
				}
			}
		}
		//Concateno el SQL de la carga de datos
		if(isset($this->from)){
			$tablas_from = array_merge($tablas_from, $this->from);
		}
		if(isset($this->where)){
			$where = array_merge($where, $this->where);
		}
		//ei_arbol($campos_select,"CAMPOS");
		//ei_arbol($tablas_from,"TABLAS");
		//ei_arbol($where,"WHERE");
		$sql =	" SELECT	" . implode(" ,\n ",$campos_select) . "\n" .
				" FROM "	. implode(" ,\n",$tablas_from) .
				" WHERE " .	implode(" \nAND ",$where) .";";
		//echo "<pre>" . $sql;
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