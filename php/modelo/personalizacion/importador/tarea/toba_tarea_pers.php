<?php
abstract class toba_tarea_pers {
	/**
	 * @var toba_importador_plan_item
	 */
    protected $item_plan;

	/**
	 * @var toba_db_postgres7
	 */
	protected $db;

	/**
	 * @var SimpleXMLElement
	 */
	protected $raw_data = null;

	/**
	 * @var toba_tarea_datos
	 */
	protected $datos;
	
	protected $preparada = false;

	/**
	 * @param toba_importador_plan_item $item_plan Cada tarea tiene asociado un item del plan
	 * al que pertenece
	 * @param $db la base de datos donde va a impactar la tarea
	 */
	function  __construct(toba_importador_plan_item $item_plan, $db)
	{
		$this->item_plan = $item_plan;
		$this->db = $db;
		$this->cargar_componente();
		$this->armar_datos();
	}

	/**
	 * Carga la componente desde el archivo xml
	 */
	protected function cargar_componente()
	{
		$contenido = file_get_contents($this->item_plan->get_path_absoluto());
		$this->raw_data = simplexml_load_string($contenido, 'SimpleXMLIterator');
	}

	/**
	 * Registra todos los toba_registro_conflictos que hay en esta tarea a los toba_registro_conflictos de
	 * la personalización
	 */
	function registrar_conflictos()
	{
		$reg_conflictos = toba_personalizacion::get_registro_conflictos();
		$this->armar_datos();
		foreach ($this->datos as $registro) {
			$conf_array = $registro->get_conflictos();
			foreach ($conf_array as $conf) {
				$reg_conflictos->add_conflicto($conf, $this->item_plan->get_path());
			}
		}
	}

	function ejecutar(consola $consola = null)
	{
		$this->db->abrir_transaccion();

		foreach ($this->datos as $registro) {
			/** @todo Agregar llamada al migrador con $this->datos para que modifique los campos que tenga que modificar */
			if (!is_null($consola) && $registro->tiene_conflictos()) {
				$conflicto = $registro->get_conflicto_irresoluble();
				if (!is_null($conflicto)) {
					$this->io_conflicto_irresoluble($consola, $conflicto);
					$this->db->abortar_transaccion();	// Abortamos la transacción
					return;	// cortamos la ejecución
				}

				$conflictos = $registro->get_conflictos_solubles();
				foreach ($conflictos as $conflicto) {
					$continuar = $this->io_conflicto_soluble($consola, $conflicto);
					if (!$continuar) {
						$this->db->abortar_transaccion();	// Abortamos la transacción
						return;	// cortamos la ejecución
					}
				}
			}
			$registro->grabar();
		}

		$this->db->cerrar_transaccion();
	}


	protected function io_conflicto_soluble(consola $consola, toba_registro_conflicto $conflicto)
	{
		return $consola->dialogo_simple("Se encontró el siguiente conflicto: {$conflicto->get_descripcion()}. Desea continuar?");
	}

	protected function io_conflicto_irresoluble(consola $consola, toba_registro_conflicto $conflicto)
	{
		$consola->mensaje("Se encontro un error irrecuperable, se abortará la importación de esta tarea. El error fue: {$conflicto->get_descripcion()}.");
	}

	abstract protected function armar_datos();
}
?>
