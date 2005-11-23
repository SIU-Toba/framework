<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei extends elemento_objeto
{

	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	protected function hay_evento($nombre)
	{
		foreach ($this->datos['apex_objeto_eventos'] as $evento) {
			if ($evento['identificador'] == $nombre) {
				return true;
			}
		}
		return false;	
	}	

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();
		foreach ($this->datos['apex_objeto_eventos'] as $evt_db) {
			//ei_arbol($evt_db);
			$id = $evt_db['identificador'];
			$parametros = array();
			if( $evt_db['sobre_fila'] ){
				$parametros[] = 'seleccion';		
			}else{
				if($evt_db['maneja_datos'])	$parametros[] = 'datos';	
			}
			$eventos[$id]['parametros'] = $parametros;
			$eventos[$id]['comentarios'] = '';
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

	function generar_metodos()
	{
		$basicos = parent::generar_metodos();
		$basicos[] = "\t".
'function extender_objeto_js()
	!#c3//Se puede cambiar el comportamiento de una pantalla redefiniendo métodos en el javascript asociado a este objeto
	!#c2//La sintaxis para redefinir métodos javascript es:
	!#c2//	echo "{$this->objeto_js}.metodo = function(parametros) { cuerpo }";
	{
	}
';
		return $this->filtrar_comentarios($basicos);
	}
}
?>