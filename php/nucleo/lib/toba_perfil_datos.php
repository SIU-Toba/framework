<?php
/*
	Limitaciones parser:
		- todo subquery tiene que estar entre parentesis
		- no sepermiten subquerys en el FROM (extraños)
		
	Features perfiles
		- Una tabla puede implicar dos dimensiones
		
*/
class toba_perfil_datos
{
	protected $restricciones = array();
	protected $info_dimensiones = array();
	protected $indice_gatillos = array();
	protected $gatillos_activos = array();

	function __construct()
	{
		if( $this->get_id() ) { //Si el usuario tiene un perfil de datos
			$this->cargar_info_restricciones();
			if($this->posee_restricciones()) {
				$this->cargar_info_dimensiones();
				$this->indexar_gatillos();	
				$this->cargar_info_relaciones_entre_tablas();
			}
		}
	}
	
	function cargar_info_restricciones()
	{
		$restricciones = toba_proyecto_implementacion::get_perfil_datos_restricciones( $this->get_id() );
		if($restricciones) {
			foreach( $restricciones as $restriccion ) {
					$this->restricciones[$restriccion['dimension']][] = explode(',',$restriccion['clave']);
			}
		}		
	}

	function cargar_info_dimensiones()
	{
		foreach($this->get_lista_dimensiones_restringidas() as $dim) {
			$this->info_dimensiones[$dim] = toba::proyecto()->get_info_dimension($dim);
		}
	}

	function indexar_gatillos()
	{
		$gatillos = array();
		foreach($this->info_dimensiones as $d => $dim) {
			foreach($dim['gatillos'] as $g => $gatillo) {
				//El indice es por tablas a detectar, se deja la posicion del gatillo en la dim correspondiente
				$this->indice_gatillos[$d][$gatillo['tabla_rel_dim']] = $g;
				$gatillos[] = $gatillo['tabla_rel_dim'];
			}	
		}
		$this->gatillos_activos = array_unique($gatillos);
	}

	function cargar_info_relaciones_entre_tablas()
	{
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
		return toba_manejador_sesiones::get_perfil_datos();		
	}

	/**
	*	Indica si el perfil de datos del usuario
	*	@return $value	boolean
	*/
	function posee_restricciones()
	{
		return count($this->restricciones) > 0;
	}

	/**
	*	Retorna un array con las restricciones aplicadas sobre las dimensiones
	*	@return $value	Retorna un array de dimensiones con un subarray de restricciones
	*/
	function get_restricciones()
	{
		return $this->restricciones;
	}

	/**
	*	Retorna un array con las dimensiones sobre las que se establecieron restricciones
	*	@return $value	Retorna un array de dimensiones
	*/
	function get_lista_dimensiones_restringidas()
	{
		if( $this->restricciones) return array_keys($this->restricciones);
	}

	/**
	*	Devuelve la lista de gatillos que esta utilizando el esquema para filtrar SQLs
	*/
	function get_gatillos_activos()
	{
		return $this->gatillos_activos;	
	}

	//--------------------------------------------------------------------
	//----- API Filtrado -------------
	//--------------------------------------------------------------------

	/**
	*	Agrega clausulas WHERE en un SQl de acuerdo al perfil de datos del usuario actual
	*/
	function filtrar($sql)
	{
		if( $this->posee_restricciones() ) {
			$where = array();
			//-- 1 -- Busco GATILLOS en el SQL
			$tablas_gatillo_encontradas = $this->buscar_tablas_gatillo_en_sql( $sql );
			//-- 2 -- Busco las dimensiones implicadas
			$dimensiones_implicadas = $this->reconocer_dimensiones_implicadas( array_keys($tablas_gatillo_encontradas) );
			//-- 3 -- Obtengo la clausula WHERE correspondiente a cada dimension
			foreach( $dimensiones_implicadas as $dimension => $tabla ) {
				$alias_tabla = $tablas_gatillo_encontradas[$tabla];
				$where[] = $this->get_where_dimension_gatillo($dimension, $tabla, $alias_tabla);
			}
			//-- 4 -- Altero el SQL
			if($where) {
				$sql = sql_concatenar_where($sql, $where);				
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
	function reconocer_dimensiones_implicadas($tablas_encontradas)
	{
		$dimensiones = array();
		$dimensiones_posicion = array();
		foreach($tablas_encontradas as $tabla_encontrada) {
			foreach($this->indice_gatillos as $dim => $gatillo) {
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
	function get_where_dimension_gatillo($dimension, $tabla_gatillo, $alias_tabla)
	{
		$where = '';
		//Busco la definicion del gatillo
		$indice_gatillo = $this->indice_gatillos[$dimension][$tabla_gatillo];
		$def =& $this->info_dimensiones[$dimension]['gatillos'][$indice_gatillo];
		$restric =& $this->restricciones[$dimension];
		if ($def['tipo'] == 'directo') {										// gatillo DIRECTO!
			$columnas_gatillo = explode( ',', $def['columnas_rel_dim'] );
			if(count($columnas_gatillo) == 1) {		//-- COMPARACION simple
				foreach($restric as $clave) {
					$claves[] = $clave[0];
				}
				$where .= $alias_tabla . '.' . $columnas_gatillo[0] . ' IN (\'' . (implode('\',\'',$claves)) . '\')';
			} else {								//-- COMPARACION multicolumna
				
			}
		} else {																// gatillo INDIRECTO!
			
		}
		return $where;
	}
	
	//--------------------------------------------------------------------
	//----- API PARSER -------------
	//--------------------------------------------------------------------

	/*
	* 	Devuelve la lista de tablas gatillo (tablas que indican la presencia de una dimension)
	*		encontradas en el query
	*/
	function buscar_tablas_gatillo_en_sql($sql)
	{
		//falta Controlar si hay algun gatillo en el SQL antes de hacer todo
		$sql_from = $this->get_clausula_from( $sql );
		$gatillos = $this->buscar_gatillos_from( $sql_from );
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
	function buscar_gatillos_from($sql_from)
	{
		$gatillos = $this->get_gatillos_activos();
		$tablas = array();
		$clausulas = explode(",",$sql_from);
		foreach($clausulas as $clausula) {
			$temp = preg_split("/\s+/", trim($clausula) );
			if(isset($temp[0])) {
				$tabla = $temp[0];
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