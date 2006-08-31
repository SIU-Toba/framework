<?

class odt_pantallas extends toba_datos_tabla
{
	function configuracion()
	{
		$this->set_no_duplicado(array('identificador'));
	}
	
	function get_ids_pantallas()
	{
		$pantallas = array();
		$filas = $this->get_filas(null, true);		
		foreach ($filas as $id => $pantalla) {
			$pantallas[] = $pantalla['identificador'];
		}
		return $pantallas;
	}
	
	//-----------------------------------------------------
	//--- Manejo de la relacion con las DEPENDENCIAS
	//-----------------------------------------------------

	function set_dependencias_pantalla($pantalla, $dependencias)
	//Setea las dependencias asociadas a una pantalla		
	{
		$deps = implode(",", $dependencias);
		$this->set_fila_columna_valor($pantalla, 'objetos', $deps);
	}
	
	function get_dependencias_pantalla($pantalla)
	//Devuelve las dependencias asociadas a una pantalla
	{
		$out = null;
		$deps = $this->get_fila_columna($pantalla, 'objetos');
		if(trim($deps)!=""){
			$out = array_map("trim", explode(",", $deps ) );		
		}
		return $out;
	}

	function eliminar_dependencia($dependencia)
	//Elimino una dependencia de todas las pantallas donde este
	{
		$ids = $this->get_id_fila_condicion();
		//Recorro las pantallas
		foreach($ids as $id){
			$deps = $this->get_dependencias_pantalla($id);
			if(is_array($deps)){
				$deps = array_flip( $deps );
				if(isset($deps[$dependencia])){
					unset($deps[$dependencia]);
					$this->set_dependencias_pantalla( $id, array_flip($deps) );
				}
			}
		}
	}
	
	/**
	*	Cambia el id de una dependencia en todas las pantallas donde este
	*/
	function cambiar_id_dependencia($anterior, $nuevo)
	{
		$ids = $this->get_id_fila_condicion();
		//Recorro las pantallas
		foreach($ids as $id){
			$deps = $this->get_dependencias_pantalla($id);
			if (is_array($deps)) {
				foreach (array_keys($deps) as $dep) {
					if ($deps[$dep] == $anterior) {
						$deps[$dep] = $nuevo;	
					}
				}
				$this->set_dependencias_pantalla( $id, $deps );
			}
		}		
	}
	
	//-----------------------------------------------------
	//--- Manejo de la relacion con los EVENTOS
	//-----------------------------------------------------

	function set_eventos_pantalla($pantalla, $eventos)
	//Setea las eventos asociadas a una pantalla		
	{
		$deps = implode(",", $eventos);
		$this->set_fila_columna_valor($pantalla, 'eventos', $deps);
	}
	
	/**
	 * Setea las pantallas en las que esta presente un evento
	 */
	function set_pantallas_evento($pant_presentes, $evento)
	{
		$filas = $this->get_filas(array(), true);
		//--- Si no se pasan pantallas, se asumen todas
		if (!isset($pant_presentes)) {
			foreach ($filas as $id => $pantalla) {
				$pant_presentes[] = $pantalla['identificador'];
			}
		}
		//Se recorre las pantallas
		foreach ($filas as $id => $pantalla) {
			$eventos_actuales = $this->get_eventos_pantalla($id);
			$debe_estar_evento = in_array($pantalla['identificador'], $pant_presentes);
			$esta_actualmente = in_array($evento, $eventos_actuales);
			if ($debe_estar_evento && !$esta_actualmente) {
				//Hay que agregarlo
				$eventos_actuales[] = $evento;
			}
			if (!$debe_estar_evento && $esta_actualmente) {
				//Hay que eliminarlo	
				$pos = array_search($evento, $eventos_actuales);
				unset($eventos_actuales[$pos]);
			}
			$this->set_eventos_pantalla($id, $eventos_actuales);
		}
	}
	/**
	 * Retorna las pantallas en las que esta incluido el evento
	 */
	function get_pantallas_evento($evento)
	{
		$filas = $this->get_filas(array(), true);
		$pantallas = array();
		//Se recorre las pantallas
		foreach ($filas as $id => $pantalla) {
			if (in_array($evento, $this->get_eventos_pantalla($id))) {
				$pantallas[] = $pantalla['identificador'];
			}
		}
		return $pantallas;
	}
	
	function get_eventos_pantalla($pantalla)
	//Devuelve las eventos asociadas a una pantalla
	{
		$out = array();
		$deps = $this->get_fila_columna($pantalla, 'eventos');
		if(trim($deps)!=""){
			$out = array_map("trim", explode(",", $deps ) );		
		}
		return $out;
	}

	function eliminar_evento($evento)
	//Elimino una evento de todas las pantallas donde este
	{
		$ids = $this->get_id_fila_condicion();
		//Recorro las pantallas
		foreach($ids as $id){
			$evts = $this->get_eventos_pantalla($id);
			if(is_array($evts)){
				$evts = array_flip( $evts );
				if(isset($evts[$evento])){
					unset($evts[$evento]);
					$this->set_eventos_pantalla( $id, array_flip($evts) );
				}
			}
		}
	}
	
	/**
	*	Cambia el id de un evento en todas las pantallas donde este
	*/	
	function cambiar_id_evento($anterior, $nuevo)
	{
		$ids = $this->get_id_fila_condicion();
		//Recorro las pantallas
		foreach($ids as $id){
			$evs = $this->get_eventos_pantalla($id);
			if (is_array($evs)) {
				$cambio = false;
				foreach (array_keys($evs) as $ev) {
					if ($evs[$ev] == $anterior) {
						$cambio = true;
						$evs[$ev] = $nuevo;	
					}
				}
				if ($cambio) {
					$this->set_eventos_pantalla( $id, $evs );
				}
			}
		}				
	}	
	//-----------------------------------------------------
}
?>