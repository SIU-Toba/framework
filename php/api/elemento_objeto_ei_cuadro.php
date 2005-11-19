<?php
require_once('api/elemento_objeto_ei.php');

class elemento_objeto_ei_cuadro extends elemento_objeto_ei
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		if ($this->ordenable()){
			$eventos['ordenar']['parametros'] = '$columna, $sentido';
			$eventos['ordenar']['comentarios'] = '!#c3//$sentido puede ser "des" o "asc"';
		}
		return $eventos;
	}

	function ordenable() {
		return $this->datos['apex_objeto_cuadro'][0]['ordenar'];
	}

	//-- Generacion de metadatos

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'seleccion';
		$modelo[0]['nombre'] = 'Seleccion';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'seleccion':
				$evento[0]['identificador'] = "seleccion";
				$evento[0]['etiqueta'] = "";
				$evento[0]['orden'] = 1;
				$evento[0]['sobre_fila'] = 1;
				$evento[0]['en_botonera'] = 0;
				$evento[0]['imagen_recurso_origen'] = "apex";
				$evento[0]['imagen'] = "doc.gif";	
				break;
		}
		return $evento;
	}
}
?>