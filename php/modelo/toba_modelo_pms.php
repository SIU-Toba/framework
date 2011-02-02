<?php

class toba_modelo_pms
{
	const prefijo_ini = 'pm_';
	const pm_php  = 'proyecto';
	const pm_pers = 'personalizacion';

	/**
	 * @var toba_modelo_proyecto
	 */
	protected $proyecto;
	/**
	 * @var toba_db_postgres7
	 */
	protected $db;
	
	function __construct(toba_modelo_proyecto $proyecto)
	{
		$this->proyecto = $proyecto;
		$this->db = $proyecto->get_db();
	}

	function get_listado($excluir_predefinidos = true)
	{
		$proyecto = $this->db->quote($this->proyecto->get_id());
		$sql = "SELECT * FROM apex_puntos_montaje WHERE proyecto=$proyecto";
		$pms_base = $this->db->consultar($sql);

		$rs = array();
		foreach ($pms_base as $registro) {
			$punto = toba_punto_montaje_factory::construir($registro);
			if ($excluir_predefinidos && $punto->es_interno()) {
				continue;
			}
			$rs[] = $punto->to_array();
		}

		return $rs;
	}

	/**
	 * Devuelve verdadero si el punto pasado por parámetro existe en la base
	 * @param toba_punto_montaje $punto
	 * @return boolean
	 */
	function existe(toba_punto_montaje $punto)
	{
		$id = $punto->get_id();
		if (empty($id)) {
			return false;
		}
		
		$id = $this->db->quote($id);
		$sql = "SELECT * FROM apex_puntos_montaje WHERE id=$id";
		$registro = $this->db->consultar_fila($sql);

		if (empty($registro)) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $etiqueta
	 * @return toba_punto_montaje
	 */
	protected function get($etiqueta)
	{
		// idem anterior
		$etiqueta = $this->db->quote($etiqueta);
		$proyecto = $this->db->quote($this->proyecto->get_id());
		$sql = "SELECT * FROM apex_puntos_montaje WHERE etiqueta=$etiqueta AND proyecto=$proyecto";
		$registro = $this->db->consultar_fila($sql);
		if (empty($registro)) {
			throw new toba_error("PUNTOS MONTAJE: El punto de montaje con etiqueta $etiqueta no existe");
		} else {
			return toba_punto_montaje_factory::construir($registro);
		}
	}

	/**
	 * @param string $id
	 * @return toba_punto_montaje
	 */
	function get_por_id($id)
	{
		return $this->get_pm($id, $this->proyecto->get_id());
	}

	/**
	 * Guarda el punto de montaje pasado por parámetro. Dependiendo su tipo el
	 * guardado impactará: la base, instancia.ini y/o proyecto.ini
	 * @param toba_punto_montaje $punto
	 */
	function guardar(toba_punto_montaje $punto)
	{
		if ($this->existe($punto)) {
			$this->modificacion($punto);
		} else {
			$this->alta($punto);
		}
	}

	protected function alta(toba_punto_montaje $punto)
	{
		// primero se intenta impactar la base, si pasa de este punto se
		// escriben los archivos correspondientes
		$registro = toba_pm_a_registro::insert($punto, $this->db);
		$registro->grabar();
		
		//Se actualiza el registro con el serial asignado
		$punto->set_id($this->db->recuperar_secuencia("apex_puntos_montaje_seq"));
		
		if ($punto->es_de_proyecto()) {
			if (!$punto->es_interno()) {
				$this->proyecto->agregar_dependencia($punto->get_proyecto_referenciado());
			}
		} else {
			$this->actualizar_punto_indefinido($punto);
		}
	}

	/**
	 * Elimina el punto de montaje pasado por parámetro. Dependiendo su tipo el
	 * borrado impactará: la base, instancia.ini y/o proyecto.ini
	 * @param toba_punto_montaje $punto
	 */
	function baja(toba_punto_montaje $punto)
	{
		$registro = toba_pm_a_registro::delete($punto, $this->db);
		$registro->grabar();
		
		if ($punto->es_de_proyecto()) {
			if (!$punto->es_interno()) {
				$this->proyecto->quitar_dependencia($punto->get_proyecto_referenciado());
			}
		} else {
			$this->eliminar_punto_indefinido($punto);
		}
	}

	protected function modificacion(toba_punto_montaje $punto)
	{
		$registro = toba_pm_a_registro::update($punto, $this->db);
		$registro->grabar();
		
		if (!$punto->es_de_proyecto()) { // Si no es de proyecto no hay que modificar nada
			$this->actualizar_punto_indefinido($punto);
		}
	}

	protected function actualizar_punto_indefinido(toba_punto_montaje $punto)
	{
		$path_instancia_ini = toba::instancia()->get_path_ini();
		$instancia_ini = new toba_ini($path_instancia_ini);

		$id_proyecto = $punto->get_proyecto();
		$nombre = self::prefijo_ini.$punto->get_etiqueta();

		$datos = $instancia_ini->get_datos_entrada($id_proyecto);

		if ($punto->tiene_etiqueta_anterior()) {
			$nombre_anterior = self::prefijo_ini.$punto->get_etiqueta_anterior();
			// Cambió el nombre de la etiqueta, hay que eliminar la entrada
			unset($datos[$nombre_anterior]);	
		}

		$datos[$nombre] = $punto->get_path_absoluto();

		$instancia_ini->agregar_entrada($id_proyecto, $datos);
		$instancia_ini->guardar();
	}

	protected function eliminar_punto_indefinido(toba_punto_montaje $punto)
	{
		$path_instancia_ini = toba::instancia()->get_path_ini();
		$instancia_ini = new toba_ini($path_instancia_ini);

		$id_proyecto = $punto->get_proyecto();
		$nombre = self::prefijo_ini.$punto->get_etiqueta();

		if ($instancia_ini->existe_entrada($id_proyecto, $nombre)) {
			$datos = $instancia_ini->get_datos_entrada($id_proyecto);
			unset($datos[$nombre]);
		}

		$instancia_ini->agregar_entrada($id_proyecto, $datos);
		$instancia_ini->guardar();
	}

	/**
	 * Shortcut para crear el punto de montaje por defecto de un proyecto
	 */
	function crear_pm_proyecto()
	{
		$id_proyecto = $this->proyecto->get_id();
		$punto = new toba_punto_montaje_proyecto();
		$punto->set_etiqueta(toba_modelo_pms::pm_php);
		$punto->set_proyecto($id_proyecto);
		$punto->set_proyecto_referenciado($id_proyecto);
		$punto->set_descripcion('Punto de montaje por defecto de todos los proyectos de toba');
		$punto->set_path('php');
		$this->guardar($punto); // creamos el punto de montaje php del proyecto
		$this->proyecto->set_pm_defecto($punto);
	}

	/**
	 * Shortcut para crear el punto de montaje de la personalización de un proyecto
	 */
	function crear_pm_personalizacion()
	{
		$id_proyecto = $this->proyecto->get_id();

		$punto = new toba_punto_montaje_pers();
		$punto->set_etiqueta(toba_modelo_pms::pm_pers);
		$punto->set_proyecto($id_proyecto);
		$punto->set_proyecto_referenciado($id_proyecto);
		$punto->set_descripcion('Punto de montaje por defecto de la personalización de todos los proyectos de toba');
		$punto->set_path('personalizacion/php');

		$this->guardar($punto);
	}

	/**
	 * Shortcut para no instanciar el modelo sólo para obtener un pm
	 * @param string $id
	 */
	static function get_pm($id, $proyecto)
	{
		$id = toba::db()->quote($id);
		$proyecto = toba::db()->quote($proyecto);
		$sql = "SELECT * FROM apex_puntos_montaje WHERE id=$id AND proyecto = $proyecto";
		$registro = toba::db()->consultar_fila($sql);
		if (empty($registro)) {
			throw new toba_error("PUNTOS MONTAJE: El punto de montaje con id $id no existe");
		} else {
			return toba_punto_montaje_factory::construir($registro);
		}
	}
}
?>
