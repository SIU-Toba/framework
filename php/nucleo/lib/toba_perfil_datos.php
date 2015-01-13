<?php

/**
 * El perfil de datos permite restringir los datos que surgen desde la base de datos en base a una dimensión dada (carrera, sexo, dependencia, etc.)
 * El método filtrar analiza una consulta SQL dada, identificando las tablas que se relacionan con las dimensiones definidas en el proyecto y
 * agregando clausulas WHERE necesarias para filtrar las mismas. Por ejemplo si es una consulta SQL de personas, tenemos una dimensión sexo y el usuario actual tiene definido
 * un perfil de datos por el valor Masculino, agregará sexo='M' a las clausulas de la consulta.
 *
 * Los perfiles de datos se definen en toba_usuarios (un usuario puede tener 0 o 1 perfil)
 * Las dimensiones se definen por el proyecto en toba_editor
 * 
 * @package Seguridad
	
	To-Do:
		* No soporta el uso de parentesis
 */
class toba_perfil_datos
{
	const separador_multicol_db = '|%-,-%|';
	protected $proyecto;						// Proyecto sobre el que se va a trabajar
	protected $id;								// ID del perfil sobre el que se va a trabajar
	protected $restricciones = array();
	protected $fuentes_restringidas = array();
	protected $info_dimensiones = array();
	protected $indice_gatillos = array();
	protected $gatillos_activos = array();
	protected $id_alias_unico = 0;
	protected $relaciones_entre_tablas = array();
	//-- Operaciones sobre conjuntos
	protected $operaciones_de_conjuntos = array('union all' 	=> '\sunion\s+all\s+',	
												'union' 		=> '\sunion\s+',
												'intersect all' => '\sintersect\s+all\s+',	
												'intersect'		=> '\sintersect\s+',
												'except all' 	=> '\sexcept\s+all\s+',	
												'except' 		=> '\sexcept\s+');	
	protected $operacion_tipo;
	protected $operacion_segmentos = array();
	protected $operadores_asimetricos;				//Arreglo que mantiene posicionalmente la ocurrencia de left/right join dentro del from

	function __construct()
	{
		// Por defecto el sistema se activa sobre el proyecto y usuario actual
		$this->id = toba::manejador_sesiones()->get_perfil_datos_activo();	
		$this->inicializar( toba::proyecto()->get_id() );
	}
	
	private function inicializar($proyecto)
	{
		toba::logger()->debug('Inicializando perfil de datos para el proyecto ' . $proyecto);
		$this->proyecto = $proyecto;
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
	
	/**
	*	@ignore
	*		Setea un perfil por el request (Utilizado en las pruebas del perfil)
	*/
	function set_perfil($proyecto, $id)
	{
		$this->id = $id;
		$this->inicializar($proyecto);
	}
	
	function cargar_info_restricciones()
	{
		$restricciones = toba_proyecto_implementacion::get_perfil_datos_restricciones( $this->proyecto, $this->id );
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
			$this->info_dimensiones[$fuente][$dim] = toba::proyecto()->get_info_dimension($dim, $this->proyecto);
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
	*	Indica si el perfil de datos del usuario posee restricciones
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
	 * Indica si el perfil de datos del usuario posee una dimension en particular para una fuente datos dada.
	 * 
	 * @param varchar $dimension nombre de la dimension a consultar.
	 * @param unknown_type $fuente_datos fuente de datos donde deberia estar la dimension.
	 * @return $value boolean
	 */
	function posee_dimension($dimension, $fuente_datos=null) 
	{
		if(!$fuente_datos) $fuente_datos = toba::proyecto()->get_parametro('fuente_datos');
		if (isset($this->info_dimensiones[$fuente_datos])) {
			foreach ($this->info_dimensiones[$fuente_datos] as $id => $dims) {
				if ($dims['nombre'] == $dimension) {
					return true;
				}
			}	
		}		
		return false;
	}


	/**
	* Retorna las restricciones aplicadas sobre una dimensión específica
	* @param string $nombre Nombre de la dimension
	* @return array Arreglo de restricciones si aplica, sino null
	*/
	function get_restricciones_dimension($fuente, $nombre)
	{
		if (!isset($this->info_dimensiones[$fuente])) {
			return;
		}
		foreach ($this->info_dimensiones[$fuente] as $id => $datos) {
			if ($datos['nombre'] == $nombre) {
				$id_dimension = $id;
				break;
			}
		}
		if (isset($id_dimension) && isset($this->restricciones[$fuente][$id_dimension]) ) {
			$valores = array();
			foreach ($this->restricciones[$fuente][$id_dimension] as $valor) {
				if (count($valor) == 1) {
					$valores[] = current($valor);
				} else {
					$valores[] = $valor;
				}
			}
			return $valores;
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
		} else {
			return array();
		}
	}

	static function get_restricciones_usuario($usuario, $proyecto)
	{
		$perfil = toba_proyecto_implementacion::get_perfil_datos($usuario, $proyecto);
		if ($perfil !== null) {
			$restricciones = toba_proyecto_implementacion::get_perfil_datos_restricciones($proyecto, $perfil);
			return $restricciones;
		} else {
			toba::logger()->error("El usuario $usuario no posee perfil de datos en el proyecto $proyecto");
			throw new toba_error_def("El usuario no posee perfil de datos en el proyecto");
		}
	}
	
	//--------------------------------------------------------------------
	//----- API Filtrado -------------
	//--------------------------------------------------------------------

	/**
	*	Agrega clausulas WHERE en un SQl de acuerdo al perfil de datos del usuario actual
	*/
	function filtrar($sql, $fuente_datos=null,$dimensiones_desactivar = null, $gatillos_exclusivos = array())
	{
		if (!$fuente_datos) $fuente_datos = toba::proyecto()->get_parametro('fuente_datos');
		if ($this->posee_restricciones($fuente_datos)) {
			if ($this->hay_combinaciones_de_querys($sql)) {
				$id_operador = 0; $sql = '';
				foreach ($this->operacion_segmentos as $id => $segmento_sql) {
					$sql .= $this->filtrar_sql($segmento_sql, $fuente_datos);
					if (isset($this->operacion_tipo[$id_operador])) {
						$sql .= "\n" . $this->operacion_tipo[$id_operador] . "\n";
					}
					$id_operador++;
				}
			} else {
				$sql = $this->filtrar_sql($sql, $fuente_datos,$dimensiones_desactivar, $gatillos_exclusivos);
			}
		}
		toba::logger()->debug('SQL con perfil de datos: ' .$sql);
		return $sql;
	}
	
	function filtrar_sql($sql, $fuente_datos=null,$dimensiones_desactivar = null, $gatillos_exclusivos=array())
	{
		$where = $where_join = array();
		$this->operadores_asimetricos = array();
		$sql = $this->quitar_comentarios_sql($sql);
		//-- 1 -- Busco GATILLOS en el SQL
		$tablas_gatillo_encontradas = $this->buscar_tablas_gatillo_en_sql( $sql, $fuente_datos );		
		if (! empty($gatillos_exclusivos)) {
			foreach($tablas_gatillo_encontradas as $key=> $tabla) {					//Elimino todas aquellas tablas que no esten en los gatillos requeridos
				if (! in_array($key, $gatillos_exclusivos)) {
					unset($tablas_gatillo_encontradas[$key]);
				}
			}			
		}
		//-- 2 -- Busco las dimensiones implicadas
		$dimensiones_implicadas = $this->reconocer_dimensiones_implicadas( array_keys($tablas_gatillo_encontradas), $fuente_datos );
		//-- 3 -- Obtengo la clausula WHERE correspondiente a cada dimension
		foreach( $dimensiones_implicadas as $dimension => $tabla ) {
			if(isset($dimensiones_desactivar) && in_array($dimension,$dimensiones_desactivar)) continue;
			$alias_tabla = $tablas_gatillo_encontradas[$tabla];
			//Genero la clausula para la tabla gatillo			
			$where_gatillo = $this->get_where_dimension_gatillo($fuente_datos, $dimension, $tabla, $alias_tabla);			
			if (isset($this->operadores_asimetricos[$tabla]) && ($this->operadores_asimetricos[$tabla] != ',')) {			//Si existe un operador opcional para la tabla gatillo
				$where_join[$tabla] = $where_gatillo;
			} else {
				$where[] = $where_gatillo;											//Lo incorporo a las clausulas del where
			}
		}
		$sql = $this->quitar_comentarios_sql($sql);		
		//-- 4 -- Altero el SQL
		if(! empty($where)) {
			$sql = sql_concatenar_where($sql, $where, 'PERFIL DE DATOS');
		}
		
		// -- 5 -- Altero el From cuando hay left/right joins (Esta detras porque sql_concatenar_where no se banca subselects)
		if (! empty($where_join)) {					
			$sql = sql_concatenar_clausulas_producto_cartesiano($sql, $fuente_datos, $where_join);
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
				$tablas_vinculantes = array_reverse($tablas_vinculantes);
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
				$relacion_tablas = toba_info_relacion_entre_tablas::get_relacion($tabla_hija,$tabla_padre,$fuente_datos, $this->proyecto);
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
		return 'toba_pdtasoc_' . $this->id_alias_unico;
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
		$i = 0;		
		$tablas = array();
		$gatillos = $this->get_gatillos_activos($fuente_datos);
		$clausulas = preg_split('/\s+(LEFT|RIGHT)?\s*(OUTER|inner)?\s*JOIN\s*|[\,]/is', $sql_from );			//Separo no solo por coma, sino tambien por las variantes del JOIN
		$operadores = $this->buscar_operadores_asimetricos($sql_from);
		foreach($clausulas as $clausula) {
			$temp = preg_split("/\s+/", trim($clausula) );					//Separo por espacios 
			if(isset($temp[0])) {				
				if (strpos($temp[0], '.') !== false) {
					list($esquema, $tabla) = explode('.', $temp[0]);
				} else {
					$tabla = $temp[0];
				}
				if ( in_array(strtolower($tabla), $gatillos) ) {	
					//La tabla pertenece a una dimension
					if (isset($temp[2]) && strtolower($temp[1]) == 'as') {			//Que se trate de un AS para el alias
						$alias = $temp[2];
					} elseif (isset($temp[1])) {
						$alias = (trim($temp[1]) !='' && strtolower($temp[1]) != 'on') ? $temp[1] : $temp[0];		//Que no sea el ON de un join con tabla sin alias
					}else {
						$alias = $temp[0];			//Nombre de la tabla.
					}
					$tablas[$tabla] = $alias;					
				}
				if (isset($operadores[$i])) {
					$this->operadores_asimetricos[$tabla] = $operadores[$i];		//Guardo el operador encontrado para esta tabla
				}
			}
			$i++;
		}
		return $tablas;
	}

	private function buscar_operadores_asimetricos($sql_from)
	{
		preg_match_all('/(\s+(LEFT|RIGHT)?\s*(OUTER|inneR)?\s*JOIN\s*|[\,])/is', $sql_from, $ops );
		if (! empty($ops)) {
			return array_merge(array('0' => NULL), $ops[1]);		//La componente cero tiene todo el string matcheado, yo necesito el primer nivel de parentesis
		}
		return array();
	}
	
	function hay_combinaciones_de_querys($sql)
	{
		$pattern = '/'. implode('|', $this->operaciones_de_conjuntos) .'/is';
		preg_match_all($pattern, $sql, $ops_encontradas);
		if ($ops_encontradas !== false && ! empty($ops_encontradas[0]) ) {
				$partes = preg_split($pattern, $sql);
				$this->operacion_segmentos = array_map('trim',$partes);
				$this->operacion_tipo = current($ops_encontradas);
				return true;
			}
		return false;
	}

	//--------------------------------------------------------------------
	//----- Testing
	//--------------------------------------------------------------------

	/**
	*	Estado del sistema de perfiles de datos para el usuario actual
	*/
	function get_info($fuente_datos)
	{
		$info['perfil_id'] = $this->get_id();
		$datos_perfil = toba_proyecto_implementacion::get_info_perfiles_datos( $this->proyecto ,$this->get_id());
		$info['perfil_nombre'] = $datos_perfil['nombre'];
		$info_dims = array();
		$dims = $this->get_lista_dimensiones_restringidas($fuente_datos);
		if( $dims ) {
			foreach(  $dims as $dim) {
				$info_dims[$dim] = $this->info_dimensiones[$fuente_datos][$dim]['nombre'];
			}
		}
		$info['dimensiones_restringidas'] = $info_dims;
		$info['gatillos_activos'] = $this->get_gatillos_activos($fuente_datos);
		return $info;
	}
	
	/**
	*	Ejecuta el filtrado de SQL sobre un conjunto de SQLs
	*/
	function probar_sqls($fuente_datos, $sqls, $contar_filas=false, $mostrar_filas=false)
	{
		$test = array();
		if( ! $this->posee_restricciones($fuente_datos) ) {
			return $test;
		}
		foreach($sqls as $id => $sql) {
			$tablas_gatillo = $this->buscar_tablas_gatillo_en_sql($sql, $fuente_datos);
			//- SQL ORIGINAL ----------------------------------
			$test[$id]['sql_original'] = $sql;
			if( $tablas_gatillo ) {	
				$test[$id]['modificado'] = true;
				$dimensiones = $this->reconocer_dimensiones_implicadas(array_keys($tablas_gatillo), $fuente_datos);
				//- ANALISIS -------------------------------
				$test[$id]['gatillos'] = array_keys($tablas_gatillo);
				$dims = array();
				foreach( $dimensiones as $dim => $gat) {
					$dims[$dim] = $this->info_dimensiones[$fuente_datos][$dim]['nombre'];
				}
				$test[$id]['dimensiones'] = $dims;
				//- WHERE ------------------------
				$where = array();
				foreach( $dimensiones as $dimension => $tabla ) {
					$alias_tabla = $tablas_gatillo[$tabla];
					$where[] = $this->get_where_dimension_gatillo($fuente_datos, $dimension, $tabla, $alias_tabla);
				}
				$test[$id]['where'] = $where;
				//- SQL MODIFICADO ------------------------------
				$sql_modif = $this->filtrar($sql, $fuente_datos);
				$test[$id]['sql_modificado'] = $sql_modif;
				//- Probar los SQL contra la DB
				if ( $contar_filas || $mostrar_filas ) {	
					$pso = toba::db($fuente_datos, $this->proyecto)->sentencia_preparar($sql);
					$pso_f = toba::db($fuente_datos, $this->proyecto)->sentencia_ejecutar($pso);
					$psm = toba::db($fuente_datos, $this->proyecto)->sentencia_preparar($sql_modif);
					$psm_f = toba::db($fuente_datos, $this->proyecto)->sentencia_ejecutar($psm);
					//- CONTAR FILAS ----------------------------
					if($contar_filas) {
						$test[$id]['query_filas_orig'] = $pso_f;
						$test[$id]['query_filas_modif'] = $psm_f;
					}
					//- MOSTRAR FILAS ---------------------------
					if($mostrar_filas) {
						$test[$id]['query_datos_orig'] = toba::db($fuente_datos)->sentencia_datos($pso);
						$test[$id]['query_datos_modif'] = toba::db($fuente_datos)->sentencia_datos($psm);
					}
				}			
			} else {
				$test[$id]['modificado'] = false;
			}
		}
		return $test;
	}

	/**
	 *  Quita los comentarios de la sentencia SQL con formato -- o el tipico formato /* * /
	 *  @param string $sql  Sentencia a la que se le quiere quitar los comentarios
	 *  @return string 
	 */
	protected function quitar_comentarios_sql($sql)
	{
		//\/\*(.|[\r\n])*?\*\/|(-{2,}[\w+|\s+|\r])(.)*
		$expresion = "/\/\*(.|[\r\n])*?\*\/|(-{2,}[\w+|\s+|\r])(.)*/im";
		$resultado = preg_split($expresion, $sql);
		if (! empty($resultado)) {
			return implode(' ',$resultado);
		}
		return $sql;
	}

}
?>