<?
require_once("nucleo/persistencia/objeto_datos_tabla.php");

class odt_pantallas extends objeto_datos_tabla
{

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

	//-----------------------------------------------------
	//--- Manejo de la relacion con los EVENTOS
	//-----------------------------------------------------

	function set_eventos_pantalla($pantalla, $eventos)
	//Setea las eventos asociadas a una pantalla		
	{
		$deps = implode(",", $eventos);
		$this->set_fila_columna_valor($pantalla, 'eventos', $deps);
	}
	
	function get_eventos_pantalla($pantalla)
	//Devuelve las eventos asociadas a una pantalla
	{
		$out = null;
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
	//-----------------------------------------------------
}
?>