<?php
require_once('info_componente.php');
require_once('admin_util.php');

class info_ei extends info_componente
{

	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	protected function hay_evento($nombre)
	{
		foreach ($this->datos['info_eventos'] as $evento) {
			if ($evento['identificador'] == $nombre) {
				return true;
			}
		}
		return false;	
	}	

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();
		foreach ($this->datos['info_eventos'] as $evt_db) {
			//ei_arbol($evt_db);
			$id = $evt_db['identificador'];
			$parametros = array();
			if( $evt_db['sobre_fila'] ){
				$parametros[] = 'seleccion';
			}else{
				if($evt_db['maneja_datos'])	$parametros[] = 'datos';
			}
			$eventos[$id]['parametros'] = $parametros;
			$eventos[$id]['comentarios'] = array();
			$eventos[$id]['info'] = $evt_db;
		}
		//ei_arbol($eventos);
		return $eventos;
	}
	
	function get_comentario_carga()
	{
		return "";
	}
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	static function get_modelos_evento()
	{
		$modelo = array();
		return $modelo;
	}
	
	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		return $evento;
	}	
	
}
?>