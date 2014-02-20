<?php
/**
 * Clase base de los Controles
 * @package Centrales
 */
abstract class toba_control 
{
	private $mensaje = 'Ok !';
	private $actua_como = 'M';
	private $resultado = true;

	public function get_actua_como()
	{
		return $this->actua_como;
	}

	public function set_actua_como($actua_como)
	{
		$this->actua_como = $actua_como ;
	}

	public function get_mensaje()
	{
		return $this->mensaje;
	}

	public function set_mensaje($mensaje)
	{
		$this->mensaje = $mensaje ;
	}

	public function get_resultado()
	{
		return $this->resultado;
	}

	public function set_resultado($resultado)
	{
		$this->resultado = $resultado ;
	}

	abstract function ejecutar(&$parametros);
}

  /**
   * Brinda servicios de información sobre el estado de los puntos de control
   * 
   * @package Centrales
   */
class toba_puntos_control
{
	static private $instancia;

	// ------------------------------------------------------------------------------------
	// --------------------------- METODOS DE CLASE -------------------------------------
	// ------------------------------------------------------------------------------------

	/**
	 * @ignore. 
	 * @return toba_puntos_control
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
		  self::$instancia = new toba_puntos_control();	
		}
		return self::$instancia;
	}

	// ------------------------------------------------------------------------------------
	// ------------------------------- METODOS PRIVADOS -----------------------------------
	// ------------------------------------------------------------------------------------

	/**
	 * @ignore. Retorna el bloque de memoria de trabajo de los puntos de control
	 */
	private function &get_bloque()
	{
		return toba::manejador_sesiones()->segmento_memoria_puntos_control();
	}

	/**
	 * @ignore. Establece el valor de un parametro de un punto de control en particular.
	 */
	private function set_valor($punto_control, $parametro, $valor)
	{
		$data =& $this->get_bloque();

		if (!array_key_exists($punto_control, $data)) {
			throw new toba_error("El punto de control '$punto_control' no está definido.");
		 }

		if (!array_key_exists($parametro, $data[$punto_control]['parametros'])) {
			throw new toba_error("El parametro '$parametro' del punto de control '$punto_control' no está definido.");
		}
		$data[$punto_control]['parametros'][$parametro] = $valor;
	}

	/**
	 * @ignore. Crea un punto de control.
	 */
	private function crear_punto_control($punto_control, &$valores_item)
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		// Recupero, si existe, el ultimo punto de control.
		$punto_anterior = $this->get_ultimo_punto();
		// Si el punto de control no existe: lo creo.
		if (!array_key_exists($punto_control, $data)) {
			// Creo el nuevo punto de control
			$data[$punto_control] = array( 'parametros' => array(), 'controles' => array());

			// Agrego las definiciones de parametros
			$info = toba::proyecto()->get_info_punto_control($punto_control);
			// Recupero el parametro del punto de control
			for ($i=0; $i < count($info['parametros']); $i++) {
				$data[$punto_control]['parametros'][$info['parametros'][$i]['parametro']] = null;
			 }
			for ($i=0; $i < count($info['controles']); $i++) {
				$data[$punto_control]['controles'][$info['controles'][$i]['clase']] = array(
					'archivo' => $info['controles'][$i]['archivo'],
					'actua_como' => $info['controles'][$i]['actua_como'],
					'resultado' => null
				  );
			 }
		}

		// Le seteo los valores vía dos origenes:
		foreach ($data[$punto_control]['parametros'] as $parametro => $_value) {
			if ($parametro == 'actua_como' || $parametro == 'mensaje' || $parametro == 'resultado') {
				continue;
			}

			$values = array();		
			//  1) El valor seteado en el ultimo punto de control ejecutado (si hubiera)
			if (isset($punto_anterior) && $this->parametro_tiene_valor($punto_anterior, $parametro)) {
				foreach ($this->get_valor_punto($punto_anterior, $parametro) as $key => $value) {
						$values[] = $value;
				}
			 }
			//  2) Valores de los ef o las columnas de un objeto toba
			foreach ($valores_item as $key => $value) {
				if (array_key_exists($parametro, $value)) {
					$values[] = $value[$parametro];
				}
			}
			 // 3) Otro origen ? 
			$this->set_valor($punto_control, $parametro, $values);
		}
	}

	/**
	 * @ignore. Retorna los controles de un punto de control.
	 */
	private function &get_controles($punto_control)
	{
		$data =& $this->get_bloque();
		if (! array_key_exists($punto_control, $data)) {
			throw new toba_error("El punto de control '$punto_control' no está definido.");
		}
		return $data[$punto_control]['controles'];
	}

	/**
	 * @ignore. Ejecuta un control de un punto de control. 
	 */
	private function ejecutar_control($componente, $punto_control, $control)
	{
		//Ejecuto el control
		$var = $this->get_valores_punto($punto_control);
		$control->ejecutar($var);  
		$control->set_actua_como($this->get_actua_como_control($punto_control, get_class($control)));
		$resultado = $control->get_resultado();
		
		// Si falló invoco al CI
		if ($resultado === false || $resultado < 0) {
			$componente->controlador()->evt__falla_punto_control($punto_control, $control);
		}
		// Seteo el resultado  
		$this->set_resultado_control($punto_control, $control);  
	}

	/**
	* @ignore. Almacena el resultado de la ejecucion del control.
	*/
	private function set_resultado_control($punto_control, $control)
	{
		$data =& $this->get_bloque();
		if (!array_key_exists($punto_control, $data)) {
			throw new toba_error("El punto de control '$punto_control' no está definido.");
		}
		if (!array_key_exists(get_class($control), $data[$punto_control]['controles'])) {
			throw new toba_error("El control '$control' no está definido en el punto de control '$punto_control' .");
		}
		$data[$punto_control]['controles'][get_class($control)]['resultado'] = $control->get_resultado();
		$this->set_resultado($control);
	}

	/**
	* @ignore. Actualiza el resultado de la ejecucion del punto de control.
	*/
	private function set_resultado($control)
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		$resultado = $control->get_resultado();

		// Si el control falla entonces el punto de control queda 
		// marcado como fallado
		if ($resultado === false || $resultado < 0) {
			$data["resultado"] = $resultado;
			$actua_como = $control->get_actua_como();
			if (is_null($this->get_mensaje())) {
				$data["mensaje"] = '<ul>';
			}
			$data["mensaje"] .= ('<li>' . $control->get_mensaje());
			if (is_null($this->get_actua_como())) {
				$data["actua_como"] = $actua_como;
			} else if (($this->get_actua_como() == 'M' && $actua_como == 'A') || ($this->get_actua_como() == 'A' && $actua_como == 'E')) {
				$data["actua_como"] = $actua_como;
			 }
		}
	}

	// ------------------------------------------------------------------------------------
	// ------------------------------- METODOS PUBLICOS ---------------------------------
	// ------------------------------------------------------------------------------------

	/**
	 * Borra los resultados de ejecucion de todos los puntos de control.
	 */
	public function limpiar_estado()
	{
	}

	/**
	 * Retorna un dump del estado de ejecucion de los puntos de control
	 */
	public function dump_estado()
	{
		$data = $this->get_bloque();
		if(isset($data)) return $data;
	}  

	/**
	 * Retorna el id del ultimo punto de control ejecutado
	 */
	public function get_ultimo_punto()
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		foreach ($data as $key => $value) 
		{
			if ($key == 'actua_como' || $key == 'mensaje' || $key == 'resultado') {
				continue;
			}
			return $key;
		}
		return null;
	}

	/**
	 * Retorna los valores a utilizar por el punto de control
	 * para enviarle a los controles.
	 * @param toba_cn $controlador
	 */
	public function get_valores_punto($punto_control)
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		if (!array_key_exists($punto_control, $data)) {
			throw new toba_error("El punto de control '$punto_control' no está definido.");
		}
		if (!is_array($data[$punto_control]['parametros'])) {
			throw new toba_error("Los parametros de '$punto_control' no están definidos.");
		}
		return $data[$punto_control]['parametros'];
	}

	/**
	 * Retorna true si $parametro esta definido en el punto de control.
	 */
	public function parametro_tiene_valor($punto_control, $parametro)
	{
		$valores = $this->get_valores_punto($punto_control);
		return array_key_exists($parametro, $valores);
	}

	/**
	 * Retorna un valor determinado de un punto de control.
	 */
	public function get_valor_punto($pto_control, $parametro)
	{
		$parametros = $this->get_valores_punto($pto_control);
		return $parametros[$parametro];
	}

	/**
	 * Retorna si un control actua como mensaje o como advertencia. 
	 * Los parametros son:
	 */
	public function get_actua_como_control($punto_control, $control)
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		if (!array_key_exists($punto_control, $data)) {
			throw new toba_error("El punto de control '$punto_control' no está definido.");
		 }
		if (!array_key_exists($control, $data[$punto_control]['controles'])) {
			throw new toba_error("El control '$control' no está definido en el punto de control '$punto_control' .");
		}
		return $data[$punto_control]['controles'][$control]['actua_como'];
	}

	public function get_mensaje()
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		if (array_key_exists("mensaje", $data)) {
			return $data["mensaje"];
		}
		return null;
	}

	public function get_resultado()
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		if (array_key_exists("resultado", $data)) {
			return $data["resultado"];
		 }
		return null;
	}

	public function get_actua_como()
	{
		// Recupero bloque de datos
		$data =& $this->get_bloque();
		if (array_key_exists("actua_como", $data)) {
			return $data["actua_como"];
		 }
		return null;
	}

	/**
	 * Ejecuta los puntos de control de un ci sobre un componente para un evento especifico.
	 * @param $id del ci en ejecucion (Se utiliza para determinar si debo retener o no los resultados.). 
	 * @param $componente sobre el que se produce el evento.
	 * @param $evento disparado.
	 * @param $parametros del componente (Se utilizan para enviar como dato a cada uno de los controles).
	 */
	public function ejecutar_puntos_control($componente, $evento, &$parametros)
	{
		$puntos_control = $componente->get_puntos_control($evento);
		foreach ($puntos_control as $key => $pto_control) {
			$this->crear_punto_control($pto_control, $parametros);
			$controles = &$this->get_controles($pto_control);
			foreach ($controles as $control => $def_control) {
				// Incorporo el archivo con la clase
				require_once($def_control['archivo']);
				// Creo la instancia de la clase
				$inst_control = new $control;
				// Ejecuto el control
				$this->ejecutar_control($componente, $pto_control, $inst_control);
			}
		}

		if (! $this->get_resultado()) {
			if ($this->get_actua_como() == 'E') {
				throw new toba_error("ERROR: " . $this->get_mensaje() . "</ul>");
			} else {
				toba::notificacion()->agregar("MENSAJE:	" . $this->get_mensaje() . "</ul>");
			}
		}
	}
}
?>