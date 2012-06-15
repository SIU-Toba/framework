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
	protected $descripcion_actual = '';
	protected $ejecuta_transaccion_global = false;

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
	function registrar_conflictos($save_to_log = true)
	{
		$reg_conflictos = toba_personalizacion::get_registro_conflictos();
		$this->armar_datos();
		foreach ($this->datos as $registro) {
			$conf_array = $registro->get_conflictos();			
			if ($save_to_log && ! empty($conf_array)) {
				foreach ($conf_array as $conf) {
					$conf->set_descripcion_componente($this->descripcion_actual); 		//Agregar en este punto la descripcion del componente obtenido de la tarea.
					$reg_conflictos->add_conflicto($conf, $this->item_plan->get_path());
				}
			}
		}
	}

	/**
	 * Indica si la ejecucion se hace con transaccion global o local
	 * @return boolean
	 */
	function ejecuta_en_transaccion_global()
	{
		return $this->ejecuta_transaccion_global;
	}
	
	/**
	 *  Se usa para avisar que el modo de ejecucion esta  en transaccion global
	 */
	function set_ejecuta_transaccion_global()
	{
		$this->ejecuta_transaccion_global = true;
	}	
	
	/**
	 * Intenta ejecutar una tarea, si hay conflicto el usuario decide si se guarda o no.
	 * En modo transaccion local, se aborta a pedido del usuario o por error de SQL 
	 * En modo global, se dispara excepcion a pedido del usuario
	 */
	function ejecutar(consola $consola = null)
	{
		if (! $this->ejecuta_en_transaccion_global()) {		//Se usan transacciones a nivel local.
			$this->db->abrir_transaccion();
		}
		
		foreach ($this->datos as $registro) {
			/** @todo Agregar llamada al migrador con $this->datos para que modifique los campos que tenga que modificar */			
			//Tomo en cuenta la postura del usuario segun el tipo de conflicto
			if (!is_null($consola) && $registro->tiene_conflictos()) {
				$conflicto = $registro->get_conflicto_irresoluble();
				if (!is_null($conflicto)) {
					$this->io_conflicto_irresoluble($consola, $conflicto);
					$this->elegir_camino_accion(false);
					return;
				}

				$conflictos = $registro->get_conflictos_solubles();
				foreach ($conflictos as $conflicto) {
					$continuar = $this->io_conflicto_soluble($consola, $conflicto);
					$this->elegir_camino_accion($continuar);
					if (! $continuar) { return;}
				}
			}		
			
			//Ejecuto la SQL que representa el registro
			try {			
				$registro->grabar();
			} catch (toba_error_db $e) {
				if ($this->ejecuta_en_transaccion_global()) {throw $e;}		//Si la transaccion esta afuera tiro para arriba la excepcion.
				$this->db->abortar_transaccion();
				return;
			}
		}
		
		if (! $this->ejecuta_en_transaccion_global()) {		//Si ejecuta con transaccion local			
			$this->db->cerrar_transaccion();
		}
	}

	protected function io_conflicto_soluble(consola $consola, toba_registro_conflicto $conflicto)
	{
		return $consola->dialogo_simple("Se encontró el siguiente conflicto: {$conflicto->get_descripcion()}. Desea importar este cambio de cualquier manera?");
	}

	protected function io_conflicto_irresoluble(consola $consola, toba_registro_conflicto $conflicto)
	{
		$consola->mensaje("Se encontro un error irrecuperable, se abortará la importación de esta tarea. El error fue: {$conflicto->get_descripcion()}.");
	}

	function elegir_camino_accion($usuario_elige_seguir)
	{
		if ( ! $usuario_elige_seguir) {		
			if ($this->ejecuta_en_transaccion_global()) {			//Si se esta ejecutando en una transaccion global se maneja afuera
				throw new toba_error_usuario('No se continua con el procedimiento, la importación falló. Se revertirán todos los cambios.');			
			} else {
				$this->db->abortar_transaccion();			//Si se usa transaccion local aborto aca.
			}
		}		
	}
	
	abstract protected function armar_datos();
}
?>
