<?php
require_once('info_ei_formulario.php');

class info_ei_filtro extends info_ei_formulario
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	//-- Generacion de metadatos

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'basico';
		$modelo[0]['nombre'] = 'Filtrar - Limpiar';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'basico':
				$evento[0]['identificador'] = "filtrar";
				$evento[0]['etiqueta'] = "&Filtrar";
				$evento[0]['estilo'] = "ei-boton-filtrar";
				$evento[0]['orden'] = 1;
				$evento[0]['en_botonera'] = 1;		
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['grupo'] = 'cargado,no_cargado';
				$evento[0]['imagen_recurso_origen'] = 'apex';
				$evento[0]['imagen'] = 'filtrar.png';				
		
				$evento[1]['identificador'] = "cancelar";
				$evento[1]['etiqueta'] = "&Limpiar";
				$evento[1]['estilo'] = "ei-boton-limpiar";
				$evento[1]['orden'] = 2;
				$evento[1]['en_botonera'] = 1;		
				$evento[1]['grupo'] = 'cargado';
				$evento[1]['imagen_recurso_origen'] = 'apex';
				$evento[1]['imagen'] = 'limpiar.png';				
				break;
		}
		return $evento;
	}
}
?>