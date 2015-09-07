<?php

/**
 * La fuente de datos encapsula un mecanismo de entrada/salida de datos, típicamente una base relacional
 * Esta clase contiene ventanas antes y despues de la conexión de la fuente y permite acceder al objeto db 
 * que es el que tiene el API de consultas/comandos
 * 
 * @package Fuentes
 */
class toba_fuente_datos
{
	protected $definicion;
	protected $db;
	
	function __construct($definicion)
	{
		$this->definicion = $definicion;
	}
	
	/**
	 * Accede al objeto db que tiene el API para consultas/comandos sobre la fuente
	 * @return toba_db
	 */
	function get_db($reusar = true)
	{
		if ($reusar) {
			if (!isset($this->db)) {
				$this->pre_conectar();
				$this->db = toba_dba::get_db_de_fuente(toba::instancia()->get_id(),
															$this->definicion['proyecto'],
															$this->definicion['fuente_datos'],
															$reusar);
				$this->crear_usuario_para_auditoria($this->db);
				$this->post_conectar();
				if (isset($this->definicion['schema']) && $this->db->get_schema() == null) {
					$this->db->set_schema($this->get_conf_schemas());
				}
				$this->configurar_parseo_errores($this->db);
			}
			return $this->db;
		} else {
			//-- Se pide una conexión aislada, que no la reutilize ninguna otra parte de la aplicación
			// Esta el codigo anterior repetido porque si se unifica, el post_conectar asume la presencia de $this->db y no habria forma de pedir una conexion aislada
			$db = toba_dba::get_db_de_fuente(toba::instancia()->get_id(),
															$this->definicion['proyecto'],
															$this->definicion['fuente_datos'],
															$reusar);
			$this->crear_usuario_para_auditoria($db);
			if (isset($this->definicion['schema'])  && $this->db->get_schema() == null) {
				$db->set_schema($this->get_conf_schemas());
			}
			$this->configurar_parseo_errores($db);
			return $db;												
		}
	}
	
	/**
	 * Dado el nombre de una tabla de la fuente, retorna el id de su datos_tabla asociado
	 * @param string $tabla
	 * @return int
	 */
	function get_id_datos_tabla($tabla)
	{
		if (! isset($this->definicion['mapeo_tablas_dt'])) {
			//-- Lazyload de la relacion entre tabla y dt por un tema de eficiencia
			$this->definicion['mapeo_tablas_dt'] = toba_proyecto_db::get_mapeo_tabla_dt($this->definicion['proyecto'], $this->definicion['fuente_datos']);
		}
		if (isset($this->definicion['mapeo_tablas_dt'][$tabla])) {
			return $this->definicion['mapeo_tablas_dt'][$tabla];
		} else {
			throw new toba_error("No se encuentra el datos_tabla asociado a la tabla $tabla en la fuente {$this->definicion['fuente_datos']}");
		}
	}
	
	/**
	*	Ventana para personalizar las acciones previas a la conexión
	* @ventana
	*/
	function pre_conectar() {}
	
	/**
	* Ventana para personalizar las acciones posteriores a la conexión
	* @ventana
	*/
	function post_conectar() {}

	function crear_usuario_para_auditoria($db)
	{
		if ($this->definicion['tiene_auditoria'] == '1') {
			$usuario = toba::usuario()->get_id();			
			if (! isset($usuario)) {
				$usuario = 'publico';
			}
			
			$id_solicitud = $db->quote(toba::instancia()->get_id_solicitud());
			$usuario = $db->quote($usuario);
			$sql = 'CREATE TEMP TABLE tt_usuario ( usuario VARCHAR(60), id_solicitud INTEGER);';
			if (isset($this->definicion['permisos_por_tabla']) && $this->definicion['permisos_por_tabla'] == '1') {
				$id_operacion = toba::memoria()->get_item_solicitado();
				$rol_runtime =  toba_modelo_proyecto::get_rol_prueba_db_basico($this->definicion['fuente_datos']);									//Obtengo el rol basico de prueba
				$rol_operacion = toba_modelo_proyecto::get_rol_prueba_db($this->definicion['fuente_datos'], $id_operacion['1']);			//Obtengo el particular para la operacion
				if ($db->existe_rol($rol_operacion)) {							//Si existe el rol para la operacion entonces lo seteo como el apropiado
					$rol_runtime = $rol_operacion;
				}

				$sql .= "GRANT SELECT, INSERT ON tt_usuario TO $rol_runtime ;";
			}			
			$sql .= "INSERT INTO tt_usuario (usuario, id_solicitud) VALUES ($usuario, $id_solicitud)";
			$db->ejecutar($sql);
		}
	}

	function configurar_parseo_errores($db)
	{
		if ($this->definicion['parsea_errores'] == '1'){
			$parseador = 'toba_parser_error_db_'. $this->definicion['motor'];
			$db->set_parser_errores(new $parseador);
		}
	}

	function set_fuente_posee_auditoria($tiene = false)
	{
		$this->definicion['tiene_auditoria'] = ($tiene) ? '1' : '0';
		$sql = 'UPDATE apex_fuente_datos 
			    SET tiene_auditoria = ' . $this->definicion['tiene_auditoria'] . 
			  ' WHERE 
				proyecto = '. quote($this->definicion['proyecto']) .
			 ' AND fuente_datos = '. quote($this->definicion['fuente_datos']);
		
		toba::instancia()->get_db()->ejecutar($sql);										//Usa la instancia de toba, no puede usar la conexion de esta base
	}

	function set_fuente_parsea_errores($parsea = false)
	{
		$this->definicion['parsea_errores'] = ($parsea) ? '1' : '0';
	}
	
	function usa_permisos_por_tabla()
	{
		return (isset($this->definicion['permisos_por_tabla']) && ($this->definicion['permisos_por_tabla'] == '1'));
	}
	
	private function get_conf_schemas()
	{
		return " '{$this->definicion['schema']}', 'public' ";
	}
	
	/**
	 * @ignore
	 */
	function resetear_mapeo_tablas()
	{
		if (isset($this->definicion['mapeo_tablas_dt'])) {
			unset($this->definicion['mapeo_tablas_dt']);
		}
	}
}
?>