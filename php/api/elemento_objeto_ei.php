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
		//Si maneja datos pasarle un paratro
		//Si es sobre fila tambien.
		return $eventos;
	}
	
	function get_comentario_carga()
	{
		//FORM: "id_ef" => valor	
		//FORM_ML: 	!#c3//El formato debe ser una matriz array("id_fila" => array("id_ef" => valor, ...), ...);
	}
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function generar_metodos_basicos()
	{
		$basicos = parent::generar_metodos_basicos();
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