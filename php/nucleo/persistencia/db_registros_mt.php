<?php
require_once("db_registros.php");

class db_registros_mt extends db_registros
{
	private $tabla;							// Tablas manejadas
	private $tabla_clave;					// Campos que conforman la clave de cada tabla
	private $tabla_maestra;					// Tabla principal
	private $tipo_join = "inner";			// Tipo de JOIN a usar
	private $campos_secuencia;				// Campos que poseen secuencias (asociativo: columna/secuencia)
	private $campos_sql_select;				// Campos utilizados para generar SQL SELECT
	private $campos_sql_insert;				// Campos utilizados para generar SQL INSERT
	private $campos_sql_update;				// Campos utilizados para generar SQL UPDATE
	private $join;							// SQL de joins entre las tablas

	public function validar_definicion()
	{
		/*
			- Tienen que haber por lo menos 2 tablas
			- Las tablas tienen que estar relacionadas
			- Un alias no puede llamarse como una columna que ya existe
			- Una tabla debe poseer una CLAVE
			- Si en una tabla declaro ID como sequencia (evita no_nulo) 
				y en otra tablo declaro ID como no_nulo. La definicion va a considerar el no_nulo.
			- Una columna con JOIN no deberia ser no_nulo, porque en general se basa en la principal
		*/
		foreach(array_keys($this->definicion) as $n_tab)
		{
			if(!isset($this->definicion[$n_tab]['nombre'])){
				throw new excepcion_toba("La tabla descripta en la posicicion $n_tab no posee un atributo 'nombre'");
			}
		}
	}

	protected function inicializar_definicion_campos()
	{
		/*
		
			Cosas a prevenir:
			
		
			Generacion de la DEFINICION OPERATIVA. (Se basa es $this->definicion, provista por el consumidor en la creacion)

				(*) $this->campos				- TODOS los campos			// Se respetan los ALIAS
				(*) $this->clave				- 'pk'=1
				(*) $this->campos_no_nulo		- 'no_nulo'=1
				(*) $this->campos_externa		- 'externa'=1

				$this->tabla					- Nombre de la tabla
				$this->campos_sql_select		- TODOS - externos (para buscar registros en la DB)			
				$this->campos_sql_insert		- TODOS - secuencias - externos 							(x tabla)
				$this->campos_sql_update		- TODOS - secuencias - externos - claves					(x tabla)
				$this->campos_secuencia			- 'secuencas'=1 (asociativo columna/secuencia)				(x tabla)
				$this->join						- JOINS
				$this->campos_alias				- Alias a los que fueron convertidos los campos
			
			Los que tienen (*) Se acceden desde el ancestro para la funcionalidad ESTANDAR
		*/
		$this->tabla_maestra = $this->definicion[0]['tabla'];
		foreach(array_keys($this->definicion) as $n_tab)
		{
			$tabla = $this->definicion[$n_tab]['tabla'];
			$this->tabla[] = $tabla;
			foreach(array_keys($this->definicion[$n_tab]['columna']) as $col)
			{
				$es_clave = isset($this->definicion[$n_tab]['columna'][$col]['pk']) && ($this->definicion[$n_tab]['columna'][$col]['pk'] == 1);
				$es_no_nulo = isset($this->definicion[$n_tab]['columna'][$col]['no_nulo']) && ($this->definicion[$n_tab]['columna'][$col]['no_nulo'] == 1);
				$es_externa = isset($this->definicion[$n_tab]['columna'][$col]['externa']) && ($this->definicion[$n_tab]['columna'][$col]['externa'] == 1) ;
				$posee_alias = isset($this->definicion[$n_tab]['columna'][$col]['alias']) && trim($this->definicion[$n_tab]['columna'][$col]['alias'] != "") ;
				$posee_join = isset($this->definicion[$n_tab]['columna'][$col]['join']) && trim($this->definicion[$n_tab]['columna'][$col]['join'] != "") ;
				$es_secuencia = isset($this->definicion[$n_tab]['columna'][$col]['secuencia']) && trim($this->definicion[$n_tab]['columna'][$col]['secuencia'] != "");
				$campo = $this->definicion[$n_tab]['columna'][$col]['nombre'];
				//Para mi ancestro
				if( $es_clave && $n_tab == 0) $this->clave[] = $campo;	//La clave general es la de la tabla principal
				if( $es_clave ) $this->tabla_clave[$tabla][] = $campo;
				if( $es_externa ) $this->campos_externa[] = $campo;
				if( !$es_secuencia && $es_no_nulo ) $this->campos_no_nulo[] = $campo;
				//Campos de referencia para el ancestro
				if( $posee_alias ){
					$this->campos[] = $this->definicion[$n_tab]['columna'][$col]['alias'];
				}else{
					$this->campos[] = $campo;
				}
				//JOINs
				if( $posee_join ){
					//Este campo relaciona a la tabla con la tabla maestra
					$this->join[$tabla][] = $this->tabla_maestra .".". $this->definicion[$n_tab]['columna'][$col]['join']. " = ". $tabla .".". $campo;
				}
				//Campos para el SELECT. 
				//  Por defecto las columnas iguales se aplanan, 
				//  si este efecto es indeseado se define un ALIAS
				if( !$es_externa ){
					if( $posee_alias ){
						$alias = $this->definicion[$n_tab]['columna'][$col]['alias'];
						$this->campos_sql_select[$alias] = $tabla . "." . $campo . " as " . $alias;
						$this->campos_alias[$tabla][$campo]=$alias;
					}else{
						//Solo lo incluyo si no existia
						if(!isset($this->campos_sql_select[$campo])) $this->campos_sql_select[$campo] = $tabla .".". $campo ." as ". $campo;
					}
				}
				//Campos INSERT
				if( !$es_secuencia && !$es_externa ) $this->campos_sql_insert[$tabla][] = $campo;
				//Campos UPDATE
				if( !$es_secuencia && !$es_externa && !$es_clave ) $this->campos_sql_update[$tabla][] = $campo;
				//Secuencias
				if( $es_secuencia ) $this->campos_secuencia[$tabla][$campo] = $this->definicion[$n_tab]['columna'][$col]['secuencia'];				
			}
		}
		$this->campos = array_unique($this->campos);
		//unset($this->definicion); Como viene la memoria??? 
	}

	//-------------------------------------------------------------------------------
	//-- Preguntas BASICAS
	//-------------------------------------------------------------------------------

	public function info_definicion()
	//Informacion del buffer
	{
		$estado = parent::info_definicion();
		$estado['tabla'] = $this->tabla;  
		$estado['campos_sql_insert'] = isset($this->campos_sql_insert) ? $this->campos_sql_insert : null;
		$estado['campos_sql_update'] = isset($this->campos_sql_update) ? $this->campos_sql_update : null;
		$estado['campos_sql_select'] = isset($this->campos_sql_select) ? $this->campos_sql_select : null;
		$estado['campos_secuencia']	= isset($this->campos_secuencia) ? $this->campos_secuencia: null;
		$estado['join'] = isset($this->join) ? $this->join : null;
		return $estado;
	}

	public function get_clave()
	{
		return $this->clave;
	}

	public function get_clave_valor($id_registro)
	{
		return $this->get_clave_valor_tabla($id_registro, $this->tabla_maestra);
	}

	private function get_clave_valor_tabla($id_registro, $tabla)
	// Trae los valores de las claves de una tabla anexa
	{
		foreach( $this->tabla_clave[$tabla] as $clave ){
			$temp[$clave] = $this->get_registro_valor($id_registro, $clave);
		}	
		return $temp;
	}

	//-------------------------------------------------------------------------------
	//-- Especificacion de SERVICIOS
	//-------------------------------------------------------------------------------

	public function activar_inner_join()
	{
		$this->tipo_join = "inner";
	}
	
	public function activar_outer_join()
	{
		$this->tipo_join = "outer";
	}

	//-------------------------------------------------------------------------------
	//-- Estructura de control 
	//-------------------------------------------------------------------------------
	/*
		Esto puede ser mas eficiente
	*/

	private function identificar_tablas_comprometidas($registro, $testigos, $valor_si=1, $valor_no=0)
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

	protected function generar_estructura_control_post_carga()
	{
		if($this->tipo_join == "outer")
		{
/*
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
*/
		}elseif($this->tipo_join == "inner"){
			//Estructura de control INNER join
			$this->control = array();
			for($r=0;$r<count($this->datos);$r++){
				$this->control[$r]['estado']="db";
				foreach($this->tabla as $tabla){
					$this->control[$r]['clave'][ $tabla ] = $this->get_clave_valor_tabla($r, $tabla);
				}
			}
		}
	}
	
	protected function actualizar_estructura_control($registro, $estado)
	{
		parent::actualizar_estructura_control($registro, $estado);
		/*
		if($this->tipo_join == "outer")
		{
			/*
				ATENCION!, esto puede estar mal, tal vez se inserta un campo que no se completo
							en una tabla anexa. Esto pasa cada vez que se las setea como "i"
							sin controlar lo que realmente paso
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
		}*/
	}

	protected function sincronizar_estructura_control()
	{
		parent::sincronizar_estructura_control();
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
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------  SINCRONIZACION con la DB   -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function insertar($id_registro)
	{
		foreach( $this->tabla as $tabla)
		{
			$registro = $this->datos[$id_registro];
			$valores = array();
			foreach( $this->campos_sql_insert[$tabla] as $id => $col)
			{
				//Si la columna fue redeclarada con un ALIAS, tengo buscar en el registro con el mismo
				if(isset($this->campos_alias[$tabla][$col])){
					$col = $this->campos_alias[$tabla][$col];
				}
				//ATENCION: esto tiene algo raro, no se pueden definir STRING NULOS
				if( !isset($registro[$col]) || (trim($registro[$col]) == "")  ){		
					$valores[$id] = "NULL";
				}else{
					$valores[$id] = "'" . addslashes(trim($registro[$col])) . "'";	
				}
			}
			//Armo el INSERT
			$sql = "INSERT INTO " . $tabla .
					" ( " . implode(", ",$this->campos_sql_insert[$tabla]) . " ) ".
					" VALUES (" . implode(" ,", $valores) . ");";
			$this->log("registro: $id_registro - tabla: $tabla - " . $sql); 
			ejecutar_sql($sql, $this->fuente);
			//Recupero el valor de las secuencias
			if(isset($this->campos_secuencia[$tabla]))
			{
				foreach($this->campos_secuencia[$tabla] as $col => $sec)
				{
					$this->datos[$id_registro][ $col ] = recuperar_secuencia( $sec, $this->fuente );
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	protected function modificar($id_registro)
	//Genera sentencia de UPDATE
	{
		foreach( $this->tabla as $tabla)
		{
			if( isset($this->campos_sql_update[$tabla]) && count($this->campos_sql_update[$tabla]) > 0)
			{
				//Busco el registro
				$registro = $this->datos[$id_registro];
				//Genero el WHERE
				$sql_where = array();
				foreach($this->tabla_clave[$tabla] as $clave){
					$sql_where[] =	"( $clave = '" . $this->control[$id_registro]['clave'][$tabla][$clave] ."')";
				}
				//Escapo los caracteres que forman parte de la sintaxis SQL, seteo NULL
				$set = array();
				foreach($this->campos_sql_update[$tabla] as $campo)
				{
					//Si la columna fue redeclarada con un ALIAS, tengo buscar en el registro con el mismo
					if(isset($this->campos_alias[$tabla][$campo])){
						$campo = $this->campos_alias[$tabla][$campo];
					}
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
				$this->log("registro: $id_registro - tabla: $tabla - " . $sql); 
				ejecutar_sql($sql, $this->fuente);
			}
		}
	}
	//-------------------------------------------------------------------------------

	protected function eliminar($id_registro)
	//Elimina los registros.
	{
		if($this->baja_logica){
			throw new excepcion_toba("No esta implementada la baja logica en MT");	
		}
		//Primero las secundarias, despues las principales

		for($t=(count($this->tabla)-1); $t >= 0; $t--)
		{
			$tabla = $this->tabla[$t];
			$sql_where = array();
			foreach($this->tabla_clave[$tabla] as $clave){
				$sql_where[] =	"( $clave = '" . $this->control[$id_registro]['clave'][$tabla][$clave] ."')";
			}
			$sql = "DELETE FROM $tabla WHERE " . implode(" AND ",$sql_where) .";";
			$this->log("registro: $id_registro - tabla: $t - " . $sql); 
			ejecutar_sql($sql, $this->fuente);
		}
	}

	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
	//-------------------------------------------------------------------------------

	protected function generar_sql_select()
	{
		$sql =	"\n SELECT	" . implode(" ,\n ",$this->campos_sql_select) . "\n";
		if($this->tipo_join == "inner")		// INNER!
		{
			if(isset($this->from)){
				$tablas_from = array_merge($this->tabla, $this->from);
			}else{
				$tablas_from = $this->tabla;
			}
			$sql .=	" FROM " . implode(" ,\n ",$tablas_from ) . "\n";
			foreach($this->join as $join_tabla){
				$where[] = $join_tabla;
			}
			$sql .= " WHERE " . implode(" \n AND ",$join_tabla ) . "\n";
	
			if(isset($this->where)){
				$sql .= " AND " . implode(" \n AND ",$this->where) . "\n";
			}
			$sql .= "\n;";
		}
		elseif($this->tipo_join == "outer")	// OUTER!
		{
			/*
				ATENCION, falta concatenar el WHERE y el FROM ********************************
			*/
			$sql .=	" FROM " . $this->tabla_maestra . "\n";
			foreach($this->join as $tabla => $join){
				$sql .=	" LEFT OUTER JOIN $tabla ON  " . implode(" \nAND ",$join ) . "\n";
			}
		}
		else{
			asercion::error("El tipo de JOIN debe ser INNER o OUTER");
		}
		$this->log("SQL de carga - " . $sql); 
		return $sql;
	}		
//-------------------------------------------------------------------------------
}
?>