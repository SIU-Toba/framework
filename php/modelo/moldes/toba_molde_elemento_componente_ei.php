<?php

/*
*	
*/
class toba_molde_elemento_componente_ei extends toba_molde_elemento_componente
{
	protected $mapeo_eventos;
	protected $proximo_evento = 0;
	
	function agregar_evento($identificador, $etiqueta=null, $orden=null, $maneja_datos=1, $en_botonera=1)
	{
		if(!isset($etiqueta)) $etiqueta = $identificador;
		if(!isset($orden)) $orden = $this->proximo_evento; $this->proximo_evento++;
		$datos = array(	'orden' 				=> $orden,
						'identificador'			=> $identificador,
						'etiqueta'				=> $etiqueta,
						'maneja_datos'			=> $maneja_datos,
						'en_botonera'			=> $en_botonera
					);
		$this->mapeo_eventos[$identificador] = $this->datos->tabla('eventos')->nueva_fila($datos);
	}
}
?>