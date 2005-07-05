<?php
require_once("db_registros.php");

class db_registros_s extends db_registros
{
	function inicializar_definicion_campos()
	{
		/*
			Generacion de la DEFINICION BASE sobre la que despues trabaja el DBR.
				(Se basa es $this->definicion, provista por el consumidor en la creacion)
					
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
		foreach(array_keys($this->definicion['columna']) as $col)
		{
			$es_secuencia = isset($this->definicion['columna'][$col]['secuencia']) && ($this->definicion['columna'][$col]['secuencia'] == 1);
			$es_clave = isset($this->definicion['columna'][$col]['pk']) && ($this->definicion['columna'][$col]['pk'] == 1);
			$es_no_nulo = isset($this->definicion['columna'][$col]['no_nulo']) && ($this->definicion['columna'][$col]['no_nulo'] == 1);
			$es_externa = isset($this->definicion['columna'][$col]['externa']) && ($this->definicion['columna'][$col]['externa'] == 1) ;
			$campo = $this->definicion['columna'][$col]['nombre'];
			$this->campos[] = $campo;
			if( $es_clave ) $this->clave[] = $campo;
			if( !$es_secuencia && !$es_externa ) $this->campos_sql[] = $campo;
			if( !$es_externa ) $this->campos_sql_select[] = $campo;
			if( $es_externa ) $this->campos_externa[] = $campo;
			if( !$es_secuencia && $es_no_nulo ) $this->campos_no_nulo[] = $campo;
			if( $es_secuencia ) $this->campos_secuencia[$nombre] = $this->definicion['columna'][$col]['secuencia'];
		}
	}

	//-------------------------------------------------------------------------------
	//-- Preguntas BASICAS
	//-------------------------------------------------------------------------------

	public function get_clave()
	{
		return $this->clave;
	}
	
	public function get_clave_valor($id_registro)
	{
		foreach( $this->clave as $clave ){
			$temp[$clave] = $this->obtener_registro_valor($id_registro, $clave);
		}	
		return $temp;
	}

	//-------------------------------------------------------------------------------
	//-- Especificacion de SERVICIOS
	//-------------------------------------------------------------------------------

	public function activar_baja_logica($columna, $valor)
	{
		$this->baja_logica = true;
		$this->baja_logica_columna = $columna;
		$this->baja_logica_valor = $valor;	
	}

	public function activar_modificacion_clave()
	{
		$this->flag_modificacion_clave = true;
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
		$campos_insert = $this->campos_sql;
		$registro = $this->datos[$id_registro];
		//Escapo los caracteres que forman parte de la sintaxis SQL, seteo NULL
		foreach($campos_insert as $id_campo => $campo){
			if(!isset($registro[$campo]) || (trim($registro[$campo]) == "") ){
				$valores[$id_campo] = "NULL";
			}else{
				$valores[$id_campo] = "'" . addslashes(trim($registro[$campo])) . "'";
			}
		}
		$sql = "INSERT INTO " . $this->tabla .
				" ( " . implode(", ",$campos_insert) . " ) ".
				" VALUES (" . implode(", ", $valores) . ");";
		//- 2 - Ejecutar el SQL
		$this->log("registro: $id_registro - " . $sql); 
		ejecutar_sql( $sql, $this->fuente);
		if(count($this->campos_secuencia)>0){
			foreach($this->campos_secuencia as $columna => $secuencia){
				//Actualizo el valor
				$this->datos[$id_registro][$columna] = recuperar_secuencia($secuencia, $this->fuente);
			}
		}
		return $sql;
	}
	//-------------------------------------------------------------------------------
	
	function modificar($id_registro)
	{
		//- 1 - Armo el SQL
		//Campos a utilizar
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

	function eliminar($id_registro)
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
	
	function generar_sql_select()
	{
		$sql =	" SELECT	a." . implode(",	a.",$this->campos_sql_select) . 
				" FROM "	. $this->tabla . " a ";
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