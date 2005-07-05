<?php
require_once("db_registros.php");

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
		/*
			DEFINICION BASE (Extraida de $this->definicion)
		
			$this->tabla
			$this->campos					* TODOS
			$this->claves					* clave = 1
			$this->campos_manipulables		* TODOS - secuencias - externos (para insert y delete)
			$this->campos_select			* TODOS - externos (para insert y delete)
			$this->campos_no_nulo			* no es nulo
			$this->campos_secuencia			* Secuencas (columna/secuencia)
			$this->campos_externa			* externos
		*/
		$this->tabla = $this->definicion["tabla"];
		foreach(array_keys($this->definicion['columna']) as $col)
		{
			$es_secuencia = isset($this->definicion['columna'][$col]['secuencia']) && ($this->definicion['columna'][$col]['secuencia'] == 1);
			$es_clave = isset($this->definicion['columna'][$col]['clave']) && ($this->definicion['columna'][$col]['clave'] == 1);
			$es_no_nulo = isset($this->definicion['columna'][$col]['no_nulo']) && ($this->definicion['columna'][$col]['no_nulo'] == 1);
			$es_externa = isset($this->definicion['columna'][$col]['externa']) && ($this->definicion['columna'][$col]['externa'] == 1) ;
			$campo = $this->definicion['columna'][$col]['nombre'];
			$this->campos[] = $campo;
			if( $es_clave ) $this->clave[] = $campo;
			if( !$es_secuencia && !$es_externa ) $this->campos_manipulables[] = $campo;
			if( !$es_externa ) $this->campos_select[] = $campo;
			if( $es_externa ) $this->campos_externa[] = $campo;
			if( !$es_secuencia && $es_no_nulo ) $this->campos_no_nulo[] = $campo;
			if( $es_secuencia ) $this->campos_secuencia[$nombre] = $this->definicion['columna'][$col]['secuencia'];
		}
	}

	//-------------------------------------------------------------------------------
	//-- Especificacion de SERVICIOS
	//-------------------------------------------------------------------------------

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
		$campos_insert = $this->campos_manipulables;
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
		$campos_update = $this->campos_manipulables;
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
		$sql =	" SELECT	a." . implode(",	a.",$this->campos_select) . 
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