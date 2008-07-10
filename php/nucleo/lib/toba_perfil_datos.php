<?php
/*
	Limitaciones parser:
		- todo subquery tiene que estar entre parentesis
		- no sepermiten subquerys en el FROM (extraños)
*/
class toba_perfil_datos
{
	const separador_multicol_db = '|%-,-%|';
	protected $id;
	protected $restricciones = array();
	protected $fuentes_restringidas = array();
	protected $info_dimensiones = array();
	protected $indice_gatillos = array();
	protected $gatillos_activos = array();
	protected $id_alias_unico = 0;
	protected $relaciones_entre_tablas = array();

	function __construct()
	{
		$this->id = toba::manejador_sesiones()->get_perfil_datos();	
		if( isset($this->id) && $this->id !== '') { //Si el usuario tiene un perfil de datos
			$this->cargar_info_restricciones();
			foreach( $this->fuentes_restringidas as $fuente ) {
				if( $this->posee_restricciones($fuente) ) {
					$this->cargar_info_dimensiones($fuente);
					$this->indexar_gatillos($fuente);	
				}
			}
		}
	}
	
	function cargar_info_restricciones()
	{
		$restricciones = toba_proyecto_implementacion::get_perfil_datos_restricciones( toba::proyecto()->get_id(), $this->id );
		if($restricciones) {
			foreach( $restricciones as $restriccion ) {
				$this->restricciones[$restriccion['fuente_datos']][$restriccion['dimension']][] = explode(self::separador_multicol_db,$restriccion['clave']);
			}
		}		
		$this->fuentes_restringidas = array_keys($this->restricciones);
	}

	function cargar_info_dimensiones($fuente)
	{
		foreach($this->get_lista_dimensiones_restringidas($fuente) as $dim) {
			$this->info_dimensiones[$fuente][$dim] = toba::proyecto()->get_info_dimension($dim);
		}
	}

	function indexar_gatillos($fuente)
	{
		$gatillos = array();
		foreach($this->info_dimensiones[$fuente] as $d => $dim) {
			foreach($dim['gatillos'] as $g => $gatillo) {
				//El indice es por tablas a detectar, se deja la posicion del gatillo en la dim correspondiente
				$this->indice_gatillos[$fuente][$d][$gatillo['tabla_rel_dim']] = $g;
				$gatillos[] = $gatillo['tabla_rel_dim'];
			}	
		}
		$this->gatillos_activos[$fuente] = array_unique($gatillos);
	}

	function dump()
	{
		ei_arbol($this->info_dimensiones,'info_dimensiones');	
		ei_arbol($this->indice_gatillos,'indice_gatilloss');	
	}
	
	//-----------------------------------------------
	//------ API Informacion
	//-----------------------------------------------

	/**
	*	retorna el perfil de datos del usuario
	*	@return $value string. Si el usuario no posee un perfil devuelve NULL
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	*	Indica si el perfil de datos del usuario
	*	@return $value	boolean
	*/
	function posee_restricciones($fuente)
	{
		if(isset($this->restricciones[$fuente])) {
			return count($this->restricciones[$fuente]) > 0;
		}
		return false;
	}

	/**
	*	Retorna un array con las restricciones aplicadas sobre las dimensiones
	*	@return $value	Retorna un array de dimensiones con un subarray de restricciones
	*/
	function get_restricciones( $fuente )
	{
		if( isset($this->restricciones[$fuente]) ) {
			return $this->restricciones[$fuente];
		}
	}

	/**
	*	Retorna un array con las dimensiones sobre las que se establecieron restricciones
	*	@return $value	Retorna un array de dimensiones
	*/
	function get_lista_dimensiones_restringidas($fuente)
	{
		if( isset($this->restricciones[$fuente]) ) {
			return array_keys( $this->restricciones[$fuente] );
		}
	}

	/**
	*	Devuelve la lista de gatillos que esta utilizando el esquema para filtrar SQLs
	*/
	function get_gatillos_activos($fuente)
	{
		if( isset($this->gatillos_activos[$fuente]) ) {
			return $this->gatillos_activos[$fuente];	
		}
	}

	//--------------------------------------------------------------------
	//----- API Filtrado -------------
	//--------------------------------------------------------------------

	/**
	*	Agrega clausulas WHERE en un SQl de acuerdo al perfil de datos del usuario actual
	*/
	function filtrar($sql, $fuente_datos=null)
	{
		if(!$fuente_datos) $fuente_datos = toba::proyecto()->get_parametro('fuente_datos');
		if( $this->posee_restricciones($fuente_datos) ) {
			$where = array();
			//-- 1 -- Busco GATILLOS en el SQL
			$tablas_gatillo_encontradas = $this->buscar_tablas_gatillo_en_sql( $sql, $fuente_datos );
			//-- 2 -- Busco las dimensiones implicadas
			$dimensiones_implicadas = $this->reconocer_dimensiones_implicadas( array_keys($tablas_gatillo_encontradas), $fuente_datos );
			//-- 3 -- Obtengo la clausula WHERE correspondiente a cada dimension
			foreach( $dimensiones_implicadas as $dimension => $tabla ) {
				$alias_tabla = $tablas_gatillo_encontradas[$tabla];
				$where[] = $this->get_where_dimension_gatillo($fuente_datos, $dimension, $tabla, $alias_tabla);
			}
			//-- 4 -- Altero el SQL
			if(! empty($where)) {
				$sql = sql_concatenar_where($sql, $where, 'PERFIL DE DATOS');				
			}
		}
		return $sql;
	}
	
	/**
	*	Arma la lista de dimensiones implicadas y el gatillo a utilizar por cada una
	*		(Los gatillos tienen un orden de preferencia -el orden viene del sql de gatillos-,
	*			y no debe utilizarse mas de uno por dimension)
	*		(Un gatillo puede pertenecer a mas de una dimension)
	*/
	function reconocer_dimensiones_implicadas($tablas_encontradas, $fuente_datos)
	{
		$dimensiones = array();
		$dimensiones_posicion = array();
		foreach($tablas_encontradas as $tabla_encontrada) {
			foreach($this->indice_gatillos[$fuente_datos] as $dim => $gatillo) {
				foreach($gatillo as $tabla => $posicion) {
					if($tabla_encontrada == $tabla ) {
						if(isset($dimensiones[$dim]) ) {
							if ($dimensiones_posicion[$dim] > $posicion ) {
								$dimensiones[$dim] = $tabla;
								$dimensiones_posicion[$dim] = $posicion;
							}
						}else{
							$dimensiones[$dim] = $tabla;
							$dimensiones_posicion[$dim] = $posicion;
						}
					}
				}
			}
		}
		return $dimensiones;
	}

	/**
	*	Devuelve el WHERE correspondiente a un gatillo para una dimension particular
	*/
	function get_where_dimension_gatillo($fuente_datos, $dimension, $tabla_gatillo, $alias_tabla)
	{
		//Busco la definicion del gatillo
		$indice_gatillo = $this->indice_gatillos[$fuente_datos][$dimension][$tabla_gatillo];
		$def =& $this->info_dimensiones[$fuente_datos][$dimension]['gatillos'][$indice_gatillo];
		if ($def['tipo'] == 'directo') {										
			// Gatillo DIRECTO -----------------------------------------------
			$where = $this->get_where_aplicacion_restriccion($fuente_datos, $dimension, $def['columnas_rel_dim'], $alias_tabla);
		} else {																
			// Gatillo INDIRECTO -------------------------------------------------
			//ei_arbol($def,'DEFINICION');
			//- 1 - Genero el WHERE correspondiente al gatillo DIRECTO referenciado por el indirecto (ultimo nivel de anidamiento del where)
			$tabla_gatillo_directo = $def['tabla_gatillo'];
			$indice_gatillo_directo = $this->indice_gatillos[$fuente_datos][$dimension][$tabla_gatillo_directo];
			$columnas = $this->info_dimensiones[$fuente_datos][$dimension]['gatillos'][$indice_gatillo_directo]['columnas_rel_dim'];
			$alias_tabla_gatillo_directo = $this->get_alias_unico();
			$where = $this->get_where_aplicacion_restriccion($fuente_datos, $dimension, $columnas, $alias_tabla_gatillo_directo);
			//- 2 - Armo la cadena de tablas vinculantes que van del gatillo indirecto al directo (con sus alias)
			// La construccion se hace desde abajo hacia arriba, comenzando por el anidamiento mas profundo
			$cadena_tablas[] = $tabla_gatillo_directo;	//La cadena empieza con el gatillo directo.
			$cadena_tablas_alias[] = $alias_tabla_gatillo_directo;
			if ($def['ruta_tabla_rel_dim'] != '') {
				$tablas_vinculantes = explode( ',', $def['ruta_tabla_rel_dim']);
				$tablas_vinculantes = array_map('trim', $tablas_vinculantes);
				foreach($tablas_vinculantes as $tv) {
					$cadena_tablas[] = $tv;	
					$cadena_tablas_alias[] = $this->get_alias_unico();
				}
			}
			$cadena_tablas[] = $tabla_gatillo;			//La cadena termina con el gatillo indirecto
			$cadena_tablas_alias[] =  $alias_tabla;		//El ultimo alias es el de la tabla encontrada
			//ei_arbol(array($cadena_tablas,$cadena_tablas_alias),'Cadena de tablas');
			//- 3 - Armo la estructura con la metadata de relaciones
			for($a=0;$a<(count($cadena_tablas)-1);$a++) {
				$tabla_hija = $cadena_tablas[$a];
				$tabla_padre = $cadena_tablas[$a+1];
				$relacion_tablas = toba_info_relacion_entre_tablas::get_relacion($tabla_hija,$tabla_padre,$fuente_datos);
				$relaciones[$a]['hija']['tabla'] = $tabla_hija;
				$relaciones[$a]['hija']['alias'] = $cadena_tablas_alias[$a];
				$relaciones[$a]['hija']['cols'] = $relacion_tablas[$tabla_hija];
				$relaciones[$a]['padre']['tabla'] = $tabla_padre;
				$relaciones[$a]['padre']['alias'] = $cadena_tablas_alias[$a+1];
				$relaciones[$a]['padre']['cols'] = $relacion_tablas[$tabla_padre];
			}
			//ei_arbol($relaciones, 'Relaciones');
			//- 4 - Construyo los SUBQUERYS anidados!
			foreach($relaciones as $relacion) {
				// Armo la porcion del SUB-SELECT con la tabla hija
				$sql_temp_subselect = " (" . $this->get_lista_columnas_sql($relacion['padre']['cols'], $relacion['padre']['alias']) . ") IN\n";
				$sql_temp_subselect .= '( SELECT ' . $this->get_lista_columnas_sql($relacion['hija']['cols'], $relacion['hija']['alias']) . "\n";
				$sql_temp_subselect .= ' FROM ' . $relacion['hija']['tabla'] . ' ' . $relacion['hija']['alias'] . "\n";
				$sql_temp_subselect .= ' WHERE ' . $where . ")\n";
				$where = $sql_temp_subselect;
			}
		}
		return $where;
	}
	
	function get_where_aplicacion_restriccion($fuente_datos, $dimension, $columnas_aplicacion_restriccion, $alias_tabla)
	{
		$where = '';
		$restric =& $this->restricciones[$fuente_datos][$dimension];
		// Busco las columnas
		$columnas_matcheo = explode( ',', $columnas_aplicacion_restriccion);
		$columnas_matcheo = array_map('trim', $columnas_matcheo);
		if(count($columnas_matcheo) == 1) {		//-- COMPARACION simple
			foreach($restric as $clave) {
				$claves[] = $clave[0];
			}
			$where = $alias_tabla . '.' . $columnas_matcheo[0] . ' IN (\'' . (implode('\',\'',$claves)) . '\')';
		} else {								//-- COMPARACION multicolumna
			foreach($restric as $clave) {
				$claves[] = "('" . implode("','",$clave) . "')";
			}
			foreach($columnas_matcheo as $col) {
				$columnas[] = $alias_tabla . '.' . $col;
			}
			$where =  "(" . implode(", ",$columnas) . ") IN (" . (implode(', ',$claves)) . ")";
		}
		return " ( " . $where . " ) ";
	}
	
	/**
	*	Provee alias de tablas unicos para la construccion de subquerys anidados.
	*/
	function get_alias_unico()
	{
		$this->id_alias_unico++;
		return 'tttttttt_' . $this->id_alias_unico;
	}
	
	function get_lista_columnas_sql($columnas, $alias)
	{
		$sql = '';
		$c = count($columnas);
		for($a=0;$a<$c;$a++){
			$sql .= $alias . '.' . $columnas[$a];
			if($a<($c)-1) $sql .= ', ';
		}
		return $sql;
	}

	//--------------------------------------------------------------------
	//----- API PARSER -------------
	//--------------------------------------------------------------------

	/*
	* 	Devuelve la lista de tablas gatillo (tablas que indican la presencia de una dimension)
	*		encontradas en el query
	*/
	function buscar_tablas_gatillo_en_sql($sql, $fuente_datos)
	{
		//falta Controlar si hay algun gatillo en el SQL antes de hacer todo
		$sql_from = $this->get_clausula_from( $sql );
		$gatillos = $this->buscar_gatillos_from( $sql_from, $fuente_datos );
		return $gatillos;
	}
	
	/*
	*	Recorta el la clausula FROM de nivel CERO de un SQL
	*/	
	function get_clausula_from($sql)
	{
		//-- 1: Preparo el SQL
		// Le saco el ';' de atras
		$sql = trim($sql);
		if(  substr($sql, -1, 1) ==';') {
			$sql = substr($sql, 0, (strlen($sql)-1) );
		}		
		//-- 2: Recorto la porciond de FROM ---
		$balance = 0;
		$en_from = false;	//Se esta procesando el FROM
		$sql_from = '';
		$tokens = preg_split("/\s+/",$sql);		//ei_arbol($tokens);
		foreach($tokens as $token) {
			//No considero los subquerys
			$balance += substr_count($token, '(');
			$balance -= substr_count($token, ')');
			//No considero las cosas entre comillas
			$balance += substr_count($token, '"');
			$balance -= substr_count($token, '"');
			//Sali del FROM?
			if($en_from && ( ( $balance == 0 && ( stripos($token,'where') !== false ) )
						 		|| ( $balance == 0 && ( stripos($token,'order') !== false ) )
						 		|| ( $balance == 0 && ( stripos($token,'group') !== false ) ) 
						 	) ) {	
				break;
			} 
			//Dentro del FROM...
			if($en_from) {
				$sql_from .= $token . ' ';
			}
			//Entre en el FROM?
			if ($balance == 0 && ( stripos($token,'from') !== false ) ) {
				$en_from = true;
			}
		}
		return $sql_from;
	}

	/*
		A partir del FROM del SQL recupera las tablas que corresponden a dimensiones ACTIVAS
		@return Array() asociativo con el nombre de la tabla y el alias
	*/
	function buscar_gatillos_from($sql_from, $fuente_datos)
	{
		$gatillos = $this->get_gatillos_activos($fuente_datos);
		$tablas = array();
		$clausulas = explode(",",$sql_from);
		foreach($clausulas as $clausula) {
			$temp = preg_split("/\s+/", trim($clausula) );
			if(isset($temp[0])) {
				if (strpos($temp[0], '.') !== false) {
					list($esquema, $tabla) = explode('.', $temp[0]);
				} else {
					$tabla = $temp[0];
				}
				if ( in_array($tabla, $gatillos) ) {	
					//La tabla pertenece a una dimension
					$alias = isset($temp[1]) ? $temp[1] : $temp[0];
					$tablas[$tabla] = $alias;
				}
			}
		}
		return $tablas;
	}
}
?>