<?php
require_once('api/elemento_objeto_ei.php');

class elemento_objeto_ei_formulario_ml extends elemento_objeto_ei
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		if ($this->tipo_analisis() == 'EVENTOS') {
			$eventos['registro_alta']['parametros'] = array('id_fila', 'datos');
			$eventos['registro_alta']['comentarios'] = "	!#c3//El \$id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación";
			$eventos['registro_baja']['parametros'] = array('id_fila');
			$eventos['registro_baja']['comentarios'] = "	!#c3//El \$id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación";
			$eventos['registro_modificacion']['parametros'] = array('id_fila', 'datos');
			$eventos['registro_modificacion']['comentarios'] = "	!#c3//El \$id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación";
		}
		return $eventos;
	}
	
	function tipo_analisis() {
		return $this->datos['apex_objeto_ut_formulario'][0]['analisis_cambios'];
	}

	function get_comentario_carga()
	{
		return "	!#c3//El formato debe ser una matriz array('id_fila' => array('id_ef' => valor, ...), ...)";
	}

	//-- Generacion de metadatos

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'basico';
		$modelo[0]['nombre'] = 'Basico';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'basico':
				$evento[0]['identificador'] = "modificacion";
				$evento[0]['etiqueta'] = "&Modificacion";
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['implicito'] = true;
				$evento[0]['orden'] = 3;
				$evento[0]['en_botonera'] = 0;		
				break;
		}
		return $evento;
	}
}
?>