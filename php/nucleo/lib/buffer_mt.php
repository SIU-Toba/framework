<?php
require_once("buffer.php");

class buffer_mt extends buffer
/*
	BUFFER de multiples tablas
	
	NOTAS

	- Delete: primero secundarias despues primarias
	- Insert: primeto secundarias, despues primarias
	- columnas repetidas
	- orden de ejecucion
	- claves iguales?
	- 

*/
{
	function buffer_mt($id, $definicion, $fuente)
	{
		parent::buffer($id, $definicion, $fuente);
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
		//- CAMPOS no DUPLICADOS:
		if(isset($this->definicion['no_duplicado'])){
			$this->campos_no_duplicados = $this->definicion['no_duplicado'];
		}else{
			$this->campos_no_duplicados = array();
		}
		//- CAMPOS no NULOS
		if(isset($this->definicion['no_nulo'])){
			$this->campos_no_nulo = $this->definicion['no_nulo'];
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
	{
		if($this->control_sincro_db){
			$ok = $this->controlar_alteracion_db();
		}
		//-<1>- Crear ARRAYS de SQLs de SINCRONIZACION
		$sql_i=array(); $sql_d=array(); $sql_u=array(); $inserts=array();
		foreach(array_keys($this->control) as $registro){
			switch($this->control[$registro]['estado']){
				case "d":
					$sql_d[$registro] = $this->generar_sql_delete($registro);
					break;
				case "i":
					$sql_i[$registro] = $this->generar_sql_insert($registro);
					$inserts[] = $registro; //Tengo que obtener secuencias!
					break;
				case "u":
					$sql_u[$registro] = $this->generar_sql_update($registro);
					break;
			}
		}
		//$sql = array_merge($sql_d, $sql_u, $sql_i);
		//ei_arbol($sql, "SQL de sincronizacion con la DB");
		//-<2>- Ejecuto los SQL en el motor
		//DELETE
		foreach(array_keys($sql_d) as $registro){
			$resultado = $this->ejecutar_sql($sql_d[$registro]);
			if($resultado[0]!="ok"){
				return $resultado;
			}else{
				//Actualizo el estado del array de control
				$this->control[$registro]['estado'] = "db";
			}
		}
		//UPDATE
		foreach(array_keys($sql_u) as $registro){
			$resultado = $this->ejecutar_sql($sql_u[$registro]);
			if($resultado[0]!="ok"){
				return $resultado;
			}else{
				//Actualizo el estado del array de control
				$this->control[$registro]['estado'] = "db";
			}
		}
		//INSERT
		foreach(array_keys($sql_i) as $registro){
			$resultado = $this->ejecutar_sql($sql_i[$registro]);
			if($resultado[0]!="ok"){
				return $resultado;
			}else{
				//Recupero secuencias
				if(is_array($this->campos_secuencia)){
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
		return array("ok","El buffer se sincronizo satisfactoriamente.");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_sql_select()
	//ATENCION: Falta incorporar el FROM
	{
		//Campos utilizados
		if(isset($this->definicion['no_sql'])){
			$campos_select = array_diff($this->campos, $this->definicion['no_sql']);
		}else{
			$campos_select = $this->campos;
		}
		$sql =	" SELECT	" . implode(",	",$campos_select) . 
				" FROM "	. $this->definicion["tabla"] .
				" WHERE " .	implode(" AND ",$this->where) .";";
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_insert($id_registro)
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
				" ( " . implode(",",$campos) . " ) ".
				" VALUES ('" . implode("','", $valores) . "');";
		//Formateo NULOS
		$sql = ereg_replace("'NULL'","null",$sql);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_update($id_registro)
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
		foreach($this->campos_update as $campo){
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