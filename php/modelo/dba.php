<?
/**
*	Administra la utilizacion de bases de datos
*	Esto es suministrar las conexiones, crear, borrar y consultar su existencia.
*/
class dba
{
	private static $dba;			// Implementacion del singleton.
	private static $info_bases;		// Parametros de conexion.
	private $bases_conectadas;		// Conexiones abiertas

	//------------------------------------------------------------------------
	// Administracion de conexiones
	//------------------------------------------------------------------------

	private function __construct()
	{
		$this->bases_conectadas = array();
	}

	/**
	*	Retorna una referencia a una CONEXION con una base
	*	@param string $nombre Por defecto toma la constante fuente_datos_defecto o la misma base de toba
	*	@return db
	*/
	static function get_db($nombre=null)
	{
		$dba = self::get_instancia();
		if (is_null($nombre)) {
			$nombre = (defined("fuente_datos_defecto")) ? fuente_datos_defecto : "instancia";
		}
		return $dba->get_conexion($nombre);
	}
	

	/**
	*	Fuerza la recarga de los parametros de una conexion y reconecta a la base
	*/	
	static function refrescar($nombre)
	{
		if (isset(self::$info_bases[$nombre])) {
			unset(self::$info_bases[$nombre]);
		}
		$dba = self::get_instancia();
		$dba->reconectar($nombre);

		return self::get_db($nombre);
	}
	
	
	/**
	*	¿Hay una conexión abierta a la base?
	*/
	static function existe_conexion($nombre)
	{
		return self::get_instancia()->existe_conexion_privado($nombre);	
	}
	
	private function existe_conexion_privado($nombre)
	{
		return isset($this->bases_conectadas[$nombre]);
	}
	
	/**
	*	Manejo interno de las conexiones realizadas
	*/
	private function get_conexion($nombre)
	{
		if(!isset($this->bases_conectadas[$nombre])){
			$parametros = self::get_info_db($nombre);
			$this->bases_conectadas[$nombre] = self::conectar_db($parametros);
		}
		return $this->bases_conectadas[$nombre];
	}
	
	/**
	*	Fuerza a reconectar en el proximo pedido de bases
	*/
	private function reconectar($nombre)
	{
		if (isset($this->bases_conectadas[$nombre])) {
			unset($this->bases_conectadas[$nombre]);
		}
	}		

	//------------------------------------------------------------------------
	// Mantenimiento de BASES de DATOS
	//------------------------------------------------------------------------

	/**
	*	Crea la base de datos asociada a la fuente
	*	@param string $nombre Nombre de la fuente de datos
	*/
	static function crear_base_datos($nombre)
	{
		$info_db = self::get_info_db($nombre);
		$base_a_crear = $info_db['base'];
		if($info_db['motor']=='postgres7')
		{
			$info_db['base'] = 'template1';
			$db = self::conectar_db($info_db);
			$sql = "CREATE DATABASE $base_a_crear;";
			$db->ejecutar($sql);
		}else{
			throw new excepcion_toba("El metodo no esta definido para el motor especificado");
		}
	}

	/**
	*	Borra la base de datos asociada a la fuente
	*	@param string $nombre Nombre de la fuente de datos
	*/	
	static function borrar_base_datos($nombre)
	{
		$info_db = self::get_info_db($nombre);
		$base_a_borrar = $info_db['base'];
		if($info_db['motor']=='postgres7')
		{
			$info_db['base'] = 'template1';
			$db = self::conectar_db($info_db);
			$sql = "DROP DATABASE $base_a_borrar;";
			$db->ejecutar($sql);
		}else{
			throw new excepcion_toba("El metodo no esta definido para el motor especificado");
		}
	}

	/**
	*	Determina si la base de datos de la fuente existe
	*	@param string $nombre Nombre de la fuente de datos
	*/
	static function existe_base_datos($nombre)
	{
		try{
			$db = self::conectar_db( self::get_info_db($nombre) );
			$db->destruir();
		}catch(excepcion_toba $e){
			return false;
		}
		return true;
	}

	//------------------------------------------------------------------------
	// Servicios internos
	//------------------------------------------------------------------------

	private static function get_info_db($nombre)
	{
		if (!isset(self::$info_bases[$nombre])){

			//-------- MIGRACION 0.8.3 --------------
			//Se tiene que tener en cuenta que el caso particular de la misma instancia
			//Ya que no se pude conectar a la BD para averiguar sus paramnetros sino que 
			//salen de instancias.php
			if ($nombre == 'instancia') {
				self::$info_bases[$nombre] = self::parametros_instancia($nombre);
			} else {
				$sql = "SELECT 	*, 
								fuente_datos_motor as motor,
								host as profile
						FROM 	apex_fuente_datos
						WHERE	fuente_datos = '$nombre'
				";
				$rs = consultar_fuente($sql,'instancia');
				if (!$rs || count($rs) == 0) {
					throw new excepcion_toba("La base '$nombre' no fue definida");
				}
				$datos = $rs[0];
				//Es un link al archivo de instancias?
				if ($rs[0]['link_instancia'] == 1) {
					$id_instancia = (isset($rs[0]['instancia_id'])) ? $rs[0]['instancia_id'] : "instancia";
					$datos = array_merge($datos, self::parametros_instancia($id_instancia));
				}
				self::$info_bases[$nombre] = $datos;
			}
			//-------------------------------------------			
			//Se pospone para un release posterior
			//include_once("../instancias/bases.php");
		}
		return self::$info_bases[$nombre];
	}

	//--------  MIGRACION 0.8.3 --------------
	private static function parametros_instancia($nombre)
	{
		global $instancia;		
		$nombre_instancia = ($nombre == 'instancia') ? apex_pa_instancia : $nombre;
		if (!isset($instancia[$nombre_instancia]))
			throw new excepcion_toba("La entrada $nombre_instancia no esta definida en el archivo de instancias");
		$datos['profile'] = $instancia[$nombre_instancia][apex_db_profile];
		$datos['motor'] =  $instancia[$nombre_instancia][apex_db_motor];
		$datos['usuario'] = $instancia[$nombre_instancia][apex_db_usuario];
		$datos['clave'] = $instancia[$nombre_instancia][apex_db_clave];
		$datos['base'] = $instancia[$nombre_instancia][apex_db_base];
		$datos['fuente_datos'] = $nombre;
		return $datos;
	}
	//-------------------------------------------	
	
	private static function conectar_db($parametros)
	//Fabrica de conexiones
	{
		
		if (isset($parametros['subclase_archivo'])) {
			$archivo = $parametros['subclase_archivo'];
		} else {
			$archivo = "db_" . $parametros['motor'] . ".php";
		}
		if (isset($parametros['subclase_nombre'])) {
			$clase = $parametros['subclase_nombre'];
		} else {
			$clase = "db_" . $parametros['motor'];
		}		

		require_once($archivo);
		$objeto_db = new $clase(	$parametros['profile'],
							$parametros['usuario'],
							$parametros['clave'],
							$parametros['base'] );
		$conexion = $objeto_db->conectar();
		//--------  MIGRACION 0.8.3 --------------			
		//Como puente de migracion de versiones anteriores la bd se almacena como global
		global $db;
		$db[$parametros['fuente_datos']][apex_db_con] = $objeto_db;
		//-------------------------------------------
		return $objeto_db;
	}

	private static function get_instancia()
	//Devuelve una referencia a la instancia de la clase
	{
	   if (!isset(self::$dba)){
		   $c =	__CLASS__;
		   self::$dba = new $c;
	   }
	   return self::$dba;
	}
	//------------------------------------------------------------------------
}
?>
