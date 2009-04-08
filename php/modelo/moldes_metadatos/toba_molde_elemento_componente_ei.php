<?php
/*
*	
*/
class toba_molde_elemento_componente_ei extends toba_molde_elemento_componente
{
	protected $eventos = array();
	protected $proximo_evento = 0;
	protected $pantallas_evt_asoc = array();

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	

	function set_titulo($titulo)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'titulo',$titulo);
	}

	function agregar_evento($identificador)
	{
		$this->eventos[$identificador] = new toba_molde_evento($identificador);
		$this->eventos[$identificador]->set_orden($this->proximo_evento);
		$this->proximo_evento++;
		return $this->eventos[$identificador];
	}

	function evento($identificador)
	{
		if(!isset($this->eventos[$identificador])) {
			throw new toba_error_asistentes('Molde formulario: El evento solicitado no existe');	
		}
		return $this->eventos[$identificador];
	}

	function set_ancho($ancho)
	{
		if((strpos($ancho,'%')===false) && (strpos($ancho,'px')===false)) {
			throw new toba_error_asistentes("MOLDE CUADRO: El ancho debe definirse con el tipo de medida asociado ('%' o 'px'). Definido: $ancho");
		}
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'ancho',$ancho);
	}

	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------
	
	function generar()
	{
		foreach($this->eventos as $evento) {
		 	$this->datos->tabla('eventos')->nueva_fila($evento->get_datos());
		}
		$this->asociar_eventos_a_pantallas();
		parent::generar();
	}

	function asociar_eventos_a_pantallas()
	{
		//Ventana de extension para que se decida como asociar los eventos a pantallas.
		//Se hace mediante callback porque necesito hacerlo antes de ir al parent que sincroniza
		//Y quien hereda esta clase recien tiene el datos_tabla cargado cuando ya ha pasado
		//por toda su generacion.
	}
}
?>