<?php
require_once('info_ei.php');

class info_ei_cuadro extends info_ei
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		if ($this->ordenable()){
			$eventos['ordenar']['parametros'] = array('columna','sentido');
			$eventos['ordenar']['comentarios'] = "	!#c3//\$sentido puede ser \"des\" o \"asc\"";
		}
		return $eventos;
	}

	function ordenable() {
		return $this->datos['info_cuadro']['ordenar'];
	}

	function get_comentario_carga()
	{
		return "	!#c3//El formato del retorno debe ser array( array('columna' => valor, ...), ...)";
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