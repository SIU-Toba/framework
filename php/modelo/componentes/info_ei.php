<?php
require_once('info_componente.php');
require_once('admin_util.php');
require_once('lib/reflexion/toba_molde_clase.php');

abstract class info_ei extends info_componente
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

	function eventos_sobre_fila()
	{
		$eventos_sobre_fila = array();
		foreach ($this->eventos_predefinidos() as $evento => $info) {
			if( isset($info['info']) && !$info['info']['implicito'] && $info['info']['sobre_fila']) {
				$eventos_sobre_fila[$evento] = $info;
			}
		}				
		return $eventos_sobre_fila;
	}
	
	function get_comentario_carga()
	{
		return "";
	}

	static function get_modelos_evento()
	{
		$modelo = array();
		return $modelo;
	}
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		return $evento;
	}
	
	function get_molde_eventos_js()
	{
		$bloque_molde[] = new toba_molde_separador_js('Eventos');
		foreach ($this->eventos_predefinidos() as $evento => $info) {
			//$info['info'] no esta seteado en los eventos predefinidos agregados a mano
			if( isset($info['info']) && !$info['info']['implicito'] ) {	//Excluyo los implicitos
				// Atrapar evento en JS
				if ($info['info']['accion'] == 'V') { //Vinculo
					$bloque_molde[] = new toba_molde_metodo_js('modificar_vinculo__' . $evento, array('id_vinculo'));
				} else {
					$bloque_molde[] = new toba_molde_metodo_js('evt__' . $evento);
				}
			}
		}
		return $bloque_molde;
	}	

	function get_molde_eventos_sobre_fila()
	{
		$bloque_molde[] = new toba_molde_separador_php('Config. EVENTOS sobre fila');
		foreach ($this->eventos_sobre_fila() as $evento => $info) {
			$bloque_molde[] = new toba_molde_metodo_php('conf_evt__' . $evento, array('evento', 'fila'));
		}
		return $bloque_molde;
	}
}
?>