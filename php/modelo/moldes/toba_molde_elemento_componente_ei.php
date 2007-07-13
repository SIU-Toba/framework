<?php
/*
*	
*/
class toba_molde_elemento_componente_ei extends toba_molde_elemento_componente
{
	protected $eventos = array();
	protected $proximo_evento = 0;

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
			throw new toba_error('Molde formulario: El evento solicitado no existe');	
		}
		return $this->eventos[$identificador];
	}

	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------
	
	function generar()
	{
		foreach($this->eventos as $evento) {
		 	$this->datos->tabla('eventos')->nueva_fila($evento->get_datos());
		}
		parent::generar();
	}


}
?>