<?php
require_once("db_registros.php");

class db_registros_mt extends db_registros
/*
	Queda un caso por resolver:
		- Relacion debil entre tablas que en la que solo se graban las cosas comprometidas.
			
*/
{
	protected $tablas_anexas;
	protected $tipo_relacion = "estricta";
	protected $columnas_convertidas;	//Columnas convertidas por tabla

	
	function __construct($id, $definicion, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		foreach($definicion['tabla'] as $tabla)
		{
			if(! isset($definicion[$tabla]['columna'] )){
				$definicion[$tabla]['columna'] = array();
			}			
		}
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}

	function establecer_relacion_estricta()
	{
		$this->tipo_relacion = "estricta";
	}
	
	function establecer_relacion_debil()
	{
		$this->tipo_relacion = "debil";
	}
	//-------------------------------------------------------------------------------

	function inicializar_definicion_campos()
	{
		$this->campos = array();
		$this->campos_secuencia = array();

		for($t=0;$t<count($this->definicion['tabla']);$t++)
		{
			//Es necesario escribir un ALIAS para cada tabla utilizada
			if(!isset($this->definicion['tabla_alias'][$t])){
				throw new excepcion_toba("Atencion, es necesario definir un alias para la tabla " . $this->definicion['tabla'][$t] );	
			}
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
		//---- tablas anexas ----
		for($t=1;$t<count($this->definicion['tabla']);$t++){
			$this->tablas_anexas[] = $this->definicion['tabla'][$t];
		}
	}

	//-------------------------------------------------------------------------------
	//-- Las estructuras de control del db_registros MT son ligeramente distintas
	//-------------------------------------------------------------------------------
	/*
		Esto puede ser mas eficiente
	*/

	function identificar_tablas_comprometidas($registro, $testigos, $valor_si=1, $valor_no=0)
	{
		$plan = array();
		foreach($testigos as $tabla => $testigo)
		{
			if(isset($this->datos[$registro][$testigo])){
				$plan[$tabla] = $valor_si;
			}else{
				$plan[$tabla] = $valor_no;
			}
		}		
		return $plan;
	}

	function generar_estructura_control_post_carga()
	{
		if($this->tipo_relacion == "debil")
		{
			//Creo las columnas testigo para cada tabla
			//ATENCION!! esto se basa en un proceso realizado en la generacion del SELECT
			foreach($this->tablas_anexas as $tabla)
			{
				$col_testigo = $this->definicion[$tabla]['clave'][0];
				if(isset($this->columnas_convertidas[$tabla][$col_testigo])){
					$col_testigo = $this->columnas_convertidas[$tabla][$col_testigo];
				}
				$testigos[$tabla] = $col_testigo;
			}
			//Genero la estructura del control		
			for($a=0;$a<count($this->datos);$a++){
				$this->control[$a]['estado']="db";
				$this->control[$a]['tablas']=$this->identificar_tablas_comprometidas($a, $testigos, "db", "null");
			}
		}else{
			parent::generar_estructura_control_post_carga();
		}
	}
	
	function actualizar_estructura_control($registro, $estado)
	{
		parent::actualizar_estructura_control($registro, $estado);
		if($this->tipo_relacion == "debil")
		{
			/*
				ATENCION!, esto puede estar mal, tal vez se inserta un campo que no se completo
							en una tabla anexa. Esto pasa cada vez que se las setea como "i"
							sin controlar lo que realmente paso
			*/
			switch($estado){
				case "i":
					foreach($this->tablas_anexas as $tabla){
						$this->control[$registro]['tablas'][$tabla] = "i";						//Falta control EXISTENCIA
					}
					break;
				case "u":
					//Si el campo es nuevo, no tengo nada que hacer
					if($this->control[$registro]['estado']=="i") return;
					foreach($this->tablas_anexas as $tabla){
						if( $this->control[$registro]['tablas'][$tabla] == "db" ){
							$this->control[$registro]['tablas'][$tabla] = "u";
						}elseif( $this->control[$registro]['tablas'][$tabla] == "null" ){
							$this->control[$registro]['tablas'][$tabla] = "i";					//Falta control EXISTENCIA
						}
					}
					break;
				case "d":
					if(isset($this->control[$registro]['estado'])) //Si era un "i" se elimino directamente
					{
						foreach($this->tablas_anexas as $tabla){
							if( $this->control[$registro]['tablas'][$tabla] == "db" ){
								$this->control[$registro]['tablas'][$tabla] = "d";
							}elseif( $this->control[$registro]['tablas'][$tabla] == "u" ){
								$this->control[$registro]['tablas'][$tabla] = "d";
							}
						}
					}
			}
		}
	}

	function sincronizar_estructura_control()
	{
/*
		foreach(array_keys($this->control) as $registro){
			switch($this->control[$registro]['estado']){
				case "d":	//DELETE
					unset($this->control[$registro]);
					unset($this->datos[$registro]);
					break;
				case "i":	//INSERT
					$this->control[$registro]['estado'] = "db";
					break;
				case "u":	//UPDATE
					$this->control[$registro]['estado'] = "db";
					break;
			}
		}
*/
		parent::sincronizar_estructura_control();
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
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
			$this->log("registro: $id_registro - tabla: $t - " . $sql); 
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
				//Ejecuto el SQL
				$this->log("registro: $id_registro - tabla: $t - " . $sql); 
				ejecutar_sql($sql, $this->fuente);
			}
		}
	}
	//-------------------------------------------------------------------------------

	function eliminar($id_registro)
	//Elimina los registros.
	{
		if($this->baja_logica){
			throw new excepcion_toba("No esta implementada la baja logica en MT");	
		}
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
			$this->log("registro: $id_registro - tabla: $t - " . $sql); 
			ejecutar_sql($sql, $this->fuente);
		}
	}

	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_sql_select()
	{
		$where = array();
		$alias_padre = $this->definicion['tabla_alias'][0];
		for($t=0;$t<count($this->definicion['tabla']);$t++)
		{
			$tabla = $this->definicion['tabla'][$t];
			$alias = $this->definicion['tabla_alias'][$t];
			if($this->tipo_relacion == "estricta")				// Relacion ESTRICTA
			{
				//-- *** FROM ***
				$tablas_from[] = "$tabla $alias";
				//Armo la lista de campos por tabla
				//-- *** WHERE ***
				if($t > 0){	//Relaciones de las tablas hijas con la maestra
					foreach($this->definicion['relacion'][$tabla] as $relacion ){
						$where[] = $alias_padre . "." . $relacion['pk'] . " = " . $alias . "." . $relacion['fk'];
					}
				}
			}
			else												// Relacion DEBIL
			{
				//-- *** FROM ***
				if($t > 0){
					//Tablas asociadas
					if(is_array($this->definicion['relacion'][$tabla]))
					{
						$join = "";
						foreach($this->definicion['relacion'][$tabla] as $relacion ){
							$join .= $alias_padre . "." . $relacion['pk'] . " = " . $alias .".". $relacion['fk'] . "\n";
						}
					}else{
						throw new excepcion_toba("Las relaciones de la tabla '$tabla' no se encuentran definidas");
					}
					$tablas_outer[] = "  LEFT OUTER JOIN $tabla $alias ON $join";
				}else{
					//Tabla principal
					$tablas_from[] = "$tabla $alias";
				}
			}
			//-- *** COLUMNAS ***
			//Armo la lista de campos por tabla
			$campos = array_merge( $this->definicion[$tabla]['columna'], $this->definicion[$tabla]['clave'] );
			//Elimino campos NO SQL
			if(isset($this->definicion['tabla']['externa'])){
				$campos = array_diff( $campos, $this->definicion[$tabla]['externa'] );
			}
			foreach($campos as $campo){
				//Si el campo ya existe, le cambio el nombre
				if(isset($campos_select[$campo])){
					$nuevo_nombre = $alias."_".$campo;
					$campos_select[$alias.".".$campo] = "$alias.$campo as $nuevo_nombre";
					$this->columnas_convertidas[$tabla][$campo] = $nuevo_nombre;
				}else{
					$campos_select[$campo] = "$alias.$campo as $campo";
				}
			}
		}//fin del ciclo por tabla
		
		//Concateno el SQL de la carga de datos
		//FROM
		if(isset($this->from)){
			$tablas_from = array_merge($tablas_from, $this->from);
		}
		//WHERE
		if(isset($this->where)){
			$where = array_merge($where, $this->where);
		}
		//ei_arbol($campos_select,"CAMPOS");
		//ei_arbol($tablas_from,"TABLAS");
		//ei_arbol($where,"WHERE");
		$sql =	" SELECT	" . implode(" ,\n ",$campos_select) . "\n" .
				" FROM "	. implode(" ,\n",$tablas_from) . "\n";
		if($this->tipo_relacion == "debil"){
			$sql .= implode(" ,\n",$tablas_outer) . "\n";
		}
		if(count($where) > 0 ){
			$sql .= " WHERE " .	implode(" \nAND ",$where) .";";
		}
		$this->log("SQL de carga - " . $sql); 
		return $sql;
	}
	//-------------------------------------------------------------------------------
}
?>