<?php

/**
 * La fuente de datos encapsula un mecanismo de entrada/salida de datos, t�picamente una base relacional
 * Esta clase contiene ventanas antes y despues de la conexi�n de la fuente y permite acceder al objeto db 
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
			//-- Se pide una conexi�n aislada, que no la reutilize ninguna otra parte de la aplicaci�n
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
			toba_logger::instancia()->error("No se encuentra el datos_tabla asociado a la tabla $tabla en la fuente {$this->definicion['fuente_datos']}");
			throw new toba_error('No se encuentra el datos_tabla asociado a la tabla solicitada en la fuente de datos, revise el log');
		}
	}
	
	/**
	*	Ventana para personalizar las acciones previas a la conexi�n
	* @ventana
	*/
	function pre_conectar() {}
	
	/**
	* Ventana para personalizar las acciones posteriores a la conexi�n
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
			$sql = array('SET tt_usuario.usuario TO '. $db->quote($usuario) . ';', 
						 'SET tt_usuario.id_solicitud TO '. $id_solicitud .';'
			);
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