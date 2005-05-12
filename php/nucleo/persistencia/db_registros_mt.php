<?php
require_once("db_registros.php");
/*
	1)
		1 -> 1		: todo BIEN
		1 -> 0		: todo MAL
		
	2)
		Cuando las dos tablas tienen el mismo nombre falla
*/

class db_registros_mt extends db_registros
{
	function __construct($id, $definicion, $fuente)
	{
		foreach($definicion['tabla'] as $tabla)
		{
			if(! isset($definicion[$tabla]['columna'] )){
				$definicion[$tabla]['columna'] = array();
			}			
		}
		parent::__construct($id, $definicion, $fuente);
	}
	//-------------------------------------------------------------------------------

	function inicializar_definicion_campos()
	{
		$this->campos = array();
		$this->campos_secuencia = array();

		for($t=0;$t<count($this->definicion['tabla']);$t++)
		{
			$tabla = $this->definicion['tabla'][$t];
			//---- CAMPOS: (columnas + claves) ----
			$this->campos = array_merge(	$this->campos, 
											$this->definicion[$tabla]['clave'],
											$this->definicion[$tabla]['columna'] );
			//---- CAMPOS_SECUENCIA ----
			if(isset($this->definicion[$tabla]['secuencia'])){
				for($a=0;$a<count($this->definicion[$tabla]['secuencia']);$a++){
					$this->campos_secuencia[] = $this->definicion[$tabla]['secuencia'][$a]['col'];
					$this->campos_secuencia_tabla[$tabla][] = $this->definicion[$tabla]['secuencia'][$a]['col'];
				}
			}
		}
		array_unique($this->campos);
		//---- CAMPOS_MANIPULABLES ----
		$this->campos_manipulables = array_diff($this->campos, $this->campos_secuencia);
		//$this->campos_manipulables = $this->campos;
		//----- CAMPOS no DUPLICADOS ----
		if(isset($this->definicion['no_duplicado'])){
			//Solo hay que trabajar sobre los manipulables
			$this->campos_no_duplicados = array_diff($this->definicion['no_duplicado'], $this->campos_secuencia);
		}else{
			$this->campos_no_duplicados = array();
		}
		//---- CAMPOS no NULOS ----
		if(isset($this->definicion['no_nulo'])){
			//Solo hay que trabajar sobre los manipulables
			$this->campos_no_nulo = array_diff($this->definicion['no_nulo'], $this->campos_secuencia);
		}else{
			$this->campos_no_nulo = array();
		}
		//---- Columnas EXTERNAS ----
		if(!isset($this->definicion['externa'])){
			//Solo hay que trabajar sobre los manipulables
			$this->definicion['externa'] = array();
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
		$this->log->debug("BUFFER  " . get_class($this). " [{$this->identificador}] - SINCRONIZACION!"); 
		if($this->control_sincro_db){
			$ok = $this->controlar_alteracion_db();
		}
		//-[0]- Planifico
		$deletes=array(); $inserts=array(); $updates=array();
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
		//-[1]- Ejecuto
		//-- INSERT --
		foreach($inserts as $registro){
			$this->insertar($registro);
		}
		//-- DELETE --
		foreach($deletes as $registro){
			$this->eliminar($registro);
		}
		//-- UPDATE --
		foreach($updates as $registro){
			$this->modificar($registro);
		}

		//-[2]- Actualizo los METADATOS del BUFFER
		//-- INSERT --
		foreach($inserts as $registro){
			//Actualizo el valor del array de control
			$this->control[$registro]['estado'] = "db";
		}
		//-- DELETE --
		foreach($deletes as $registro){
			unset($this->control[$registro]);
			unset($this->datos[$registro]);
		}
		//-- UPDATE --
		foreach($updates as $registro){
			$this->control[$registro]['estado'] = "db";
		}
	}

	//-------------------------------------------------------------------------------
	//----------   SINCRONIZADORES  -------------------------------------------------
	//-------------------------------------------------------------------------------

	function insertar($id_registro)
	//Ejecuto los INSERTS en orden ascendente
	//MAL: estoy creando el plan de cada tabla por cada registro...
	{
		for($t=0;$t<count($this->definicion['tabla']);$t++)
		{
			$tabla = $this->definicion['tabla'][$t];
			//Armo la lista de campos
			$campos = array();
			$campos = array_merge(	$this->definicion[$tabla]['clave'],
									$this->definicion[$tabla]['columna'] );
			//Extraigo las secuencias de la tabla y las columnas externas
			if(isset($this->campos_secuencia_tabla[$tabla])){
				$campos = array_diff ( $campos, $this->definicion['externa'], $this->campos_secuencia_tabla[$tabla] );
			}else{
				$campos = array_diff ( $campos, $this->definicion['externa'] );
			}
			//Busco el registro
			$registro = $this->datos[$id_registro];
			//Escapo los caracteres que forman parte de la sintaxis SQL
			$valores = array();
			foreach($campos as $id => $col){
				if( !isset($registro[$col]) || (trim($registro[$col]) == "") ){
					$valores[$id] = "NULL";
				}else{
					$valores[$id] = "'" . addslashes(trim($registro[$col])) . "'";	
				}
			}
			//Armo el INSERT
			$sql = "INSERT INTO " . $tabla .
					" ( " . implode(", ",$campos) . " ) ".
					" VALUES (" . implode(" ,", $valores) . ");";
			$this->log->debug("BUFFER  " . get_class($this). " [{$this->identificador}] - " . $sql); 
			ejecutar_sql($sql, $this->fuente);
			//REcupero el valor de las secuencias
			if(isset($this->definicion[$tabla]['secuencia']))
			{
				foreach($this->definicion[$tabla]['secuencia'] as $sec)
				{
					$this->datos[$id_registro][ $sec['col'] ] = recuperar_secuencia( $sec['seq'], $this->fuente );
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	function modificar($id_registro)
	//Genera sentencia de UPDATE
	{
		for($t=0;$t<count($this->definicion['tabla']);$t++)
		{
			$tabla = $this->definicion['tabla'][$t];
			//Armo la lista de campos
			$campos_update = array();
			$campos_update = array_diff(	$this->definicion[$tabla]['columna'],
											$this->campos_secuencia, 
											$this->definicion['externa'] );
			if(count($campos_update) > 0)
			{
				//Busco el registro
				$registro = $this->datos[$id_registro];
				//Genero el WHERE
				$sql_where = array();
				foreach($this->definicion[$tabla]["clave"] as $clave){
					$sql_where[] =	"( $clave = '{$registro[$clave]}')";
				}
				//Escapo los caracteres que forman parte de la sintaxis SQL, seteo NULL
				$set = array();
				foreach($campos_update as $campo){
					if( ( !isset($registro[$campo])) || (trim($registro[$campo]) == "") ){
						$set[] = " $campo = NULL ";
					}else{
						$set[] = " $campo = '". addslashes(trim($registro[$campo])) . "' ";
					}
				}
				//Armo el QUERY
				$sql = "UPDATE $tabla SET ".
						implode(", ",$set) .
						" WHERE " . implode(" AND ",$sql_where) .";";
				$this->log->debug("BUFFER  " . get_class($this). " [{$this->identificador}] - " . $sql); 
				//Ejecuto el SQL
				ejecutar_sql($sql, $this->fuente);
			}
		}
	}
	//-------------------------------------------------------------------------------

	function eliminar($id_registro)
	//Elimina los registros.
	{
		//Primero las secundarias, despues las principales
		for( $t= count($this->definicion['tabla']) - 1; $t >= 0 ;$t--)
		{
			$tabla = $this->definicion['tabla'][$t];
			$registro = $this->datos[$id_registro];
			//Genero el WHERE
			$sql_where = array();
			foreach($this->definicion[$tabla]["clave"] as $clave){
				$sql_where[] =	"( $clave = '{$registro[$clave]}')";
			}
			$sql = "DELETE FROM $tabla WHERE " . implode(" AND ",$sql_where) .";";
			$this->log->debug("BUFFER  " . get_class($this). " [{$this->identificador}] - " . $sql); 
			ejecutar_sql($sql, $this->fuente);
		}
	}

	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
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
			if(isset($this->definicion['tabla']['externa'])){
				$campos = array_diff( $campos, $this->definicion[$tabla]['externa'] );
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


}
?>