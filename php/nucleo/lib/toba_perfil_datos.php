<?php
/*
	Limitaciones parser:
		- todo subquery tiene que estar entre parentesis
		- no sepermiten subquerys en el FROM (extraños)

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
		foreach($this->info_dimensiones as $d => $dim) {
			foreach($dim['gatillos'] as $g => $gatillo) {
				//El indice es por tablas a detectar, se deja la posicion del gatillo en la dim correspondiente
				$this->indice_gatillos[$gatillo['tabla_rel_dim']][$d] = $g;
			}	
		}
		$this->gatillos_activos = array_keys($this->indice_gatillos);
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

	function filtrar($sql)
	{
		if( $this->posee_restricciones() ) {
			$where = array();
			//-- 1 -- Busco las dimensiones implicadas
			$tablas_gatillo = $this->buscar_tablas_gatillo_en_sql( $sql );
			foreach( $tablas_gatillo as $tabla => $alias_tabla ) {
				foreach( $this->indice_gatillos[$tabla] as $dimension => $indice_gatillo) {
					$where[] = $this->get_where_dimension_gatillo( $dimension, $indice_gatillo, $alias_tabla );
				}
				//-- 3 -- Agrego el WHERE en el SQL
			}
			ei_arbol($where,"WHERE");
		} else {
			return $sql;	
		}
	}


	/**
	*	Devuelve el WHERE correspondiente a un gatillo de una dimension
	*/
	function get_where_dimension_gatillo($dimension, $indice_gatillo, $alias_tabla)
	{
		$where = '';
		//Busco la definicion del gatillo
		$def =& $this->info_dimensiones[$dimension]['gatillos'][$indice_gatillo];
		//ei_arbol($def);
		if ($def['tipo'] == 'directo') {
			$where .= $alias_tabla . '.' . $def['columnas_rel_dim'] . ' IN ';
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