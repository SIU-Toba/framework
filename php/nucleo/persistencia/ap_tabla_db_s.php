<?php
require_once("ap_tabla_db.php");

class ap_tabla_db_s extends ap_tabla_db
/*
	Administrador de persistencia a DB con mapeo SIMPLE
	Supone que la tabla de datos se va a mapear a una tabla del modelo de datos
*/
{
	protected $campos_sql;		// Habria que precargar las definiciones de campos para UPDATES y INSERTS

	protected function inicializar_definicion_campos()
	{
		/*
			Generacion de la DEFINICION OPERATIVA. (Se basa es $this->definicion, provista por el consumidor en la creacion)
					
				$this->tabla					- Nombre de la tabla
				(*) $this->campos				- TODOS los campos
				(*) $this->clave				- 'pk'=1
				(*) $this->campos_no_nulo		- 'no_nulo'=1
				(*) $this->campos_externa		- 'externa'=1
				$this->campos_secuencia			- 'secuencas'=1 (asociativo columna/secuencia)
				$this->campos_sql				- TODOS - secuencias - externos (para insert y update)
				$this->campos_sql_select		- TODOS - externos (para buscar registros en la DB)
			
			Los que tienen (*) Se acceden desde el ancestro para la funcionalidad ESTANDAR
		*/
		$this->tabla = $this->definicion["tabla"];
		$this->alias = isset($this->definicion["alias"]) ? $this->definicion["alias"] : null;
		foreach(array_keys($this->definicion['columna']) as $col)
		{
			$es_clave = isset($this->definicion['columna'][$col]['pk']) && ($this->definicion['columna'][$col]['pk'] == 1);
			$es_no_nulo = isset($this->definicion['columna'][$col]['no_nulo']) && ($this->definicion['columna'][$col]['no_nulo'] == 1);
			$es_externa = isset($this->definicion['columna'][$col]['externa']) && ($this->definicion['columna'][$col]['externa'] == 1) ;
			$es_secuencia = isset($this->definicion['columna'][$col]['secuencia']) && trim($this->definicion['columna'][$col]['secuencia'] != "");
			$campo = $this->definicion['columna'][$col]['nombre'];
			//Para mi ancestro
			$this->campos[] = $campo;
			if( $es_clave ) $this->clave[] = $campo;
			if( $es_externa ) $this->campos_externa[] = $campo;
			if( !$es_secuencia && $es_no_nulo ) $this->campos_no_nulo[] = $campo;
			//Para mi
			if( !$es_secuencia && !$es_externa ) $this->campos_sql[] = $campo;
			if( !$es_externa ){
				//Hay que evitar que los nombre colapsen si se pone un FROM en cargar_datos.
				if(isset($this->alias)){	
					$this->campos_sql_select[] = $this->alias . "." .$campo ." as $campo";
				}else{
					$this->campos_sql_select[] = $this->tabla . "." .$campo ." as $campo";
				}
			}
			if( $es_secuencia ) $this->campos_secuencia[$campo] = $this->definicion['columna'][$col]['secuencia'];
		}
	}

	

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function insertar($id_registro)
	{
		//- 2 - Ejecutar el SQL
		$sql = $this->generar_sql_insert($id_registro);
		$this->log("registro: $id_registro - " . $sql); 
		$this->ejecutar_sql( $sql );
		//Actualizo las secuencias
		if(count($this->campos_secuencia)>0){
			foreach($this->campos_secuencia as $columna => $secuencia){
				$this->datos[$id_registro][$columna] = recuperar_secuencia($secuencia, $this->fuente);
			}
		}
		return $sql;
	}
	
	protected function generar_sql_insert($id_registro)
	{
		//- 1 - Armo el SQL
		//Campos utilizados
		$registro = $this->datos[$id_registro];
		//Escapo los caracteres que forman parte de la sintaxis SQL, seteo NULL
		foreach($this->campos_sql as $id_campo => $campo){
			if(!isset($registro[$campo]) || (trim($registro[$campo]) == "") ){
				$valores[$id_campo] = "NULL";
			}else{
				$valores[$id_campo] = "'" . addslashes(trim($registro[$campo])) . "'";
			}
		}
		$sql = "INSERT INTO " . $this->tabla .
				" ( " . implode(", ",$this->campos_sql) . " ) ".
				" VALUES (" . implode(", ", $valores) . ");";
		return $sql;
	}
	//-------------------------------------------------------------------------------

	protected function modificar($id_registro)
	{
		//- 1 - Armo el SQL
		$campos_update = $this->campos_sql;
		if(! $this->flag_modificacion_clave ){		//Extraigo las claves
			$campos_update = array_diff( $campos_update, $this->clave);
		}
		$registro = $this->datos[$id_registro];
		//Genero el WHERE
		foreach($this->clave as $clave){
			$sql_where[] =	"( $clave = '" . $this->control[$id_registro]['clave'][$clave] ."')";
		}
		//Escapo los caracteres que forman parte de la sintaxis SQL
		foreach($campos_update as $campo){
			if( (!isset($registro[$campo])) || (trim($registro[$campo]) == "") ){
				$set[] = " $campo = NULL ";
			}else{
				$set[] = " $campo = '". addslashes($registro[$campo]) . "' ";
			}
		}
		$sql = "UPDATE " . $this->tabla . " SET ".
				implode(", ",$set) .
				" WHERE " . implode(" AND ",$sql_where) .";";
		//- 2 - Ejecutar el SQL
		$this->log("registro: $id_registro - " . $sql); 
		ejecutar_sql( $sql, $this->fuente);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	protected function eliminar($id_registro)
	{
		//- 0 - Genero el WHERE
		$registro = $this->datos[$id_registro];
		foreach($this->clave as $clave){
			$sql_where[] =	"( $clave = '" . $this->control[$id_registro]['clave'][$clave] ."')";
		}
		//- 1 - Armo el SQL
		if($this->baja_logica){
			$sql = "UPDATE " . $this->tabla .
					" SET " . $this->baja_logica_columna . " = '". $this->baja_logica_valor ."' " .
					" WHERE " . implode(" AND ",$sql_where) .";";
		}else{
			$sql = "DELETE FROM " . $this->tabla .
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
	
	protected function generar_sql_select()
	{
		$sql =	" SELECT	" . implode(",	",$this->campos_sql_select); 
		if(isset($this->alias)){	
			$sql .= " FROM "	. $this->tabla  . " " . $this->alias;
		}else{
			$sql .= " FROM "	. $this->tabla;
		}
		if(isset($this->from)){
			$sql .= ", " . implode(",",$this->from);
		}
		if(isset($this->where)){
			$sql .= " WHERE " .	implode(" AND ",$this->where) .";";
		}
		$this->log("SQL de carga - " . $sql); 
		return $sql;
	}
	//-------------------------------------------------------------------------------
}
?>