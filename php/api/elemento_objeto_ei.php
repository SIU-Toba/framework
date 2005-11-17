<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei extends elemento_objeto
{
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
;';
		return $this->filtrar_comentarios($basicos);
	}
}

	function eventos_predefinidos()
	{
		$eventos = array('inicializar');	
		//$eventos = array('procesar', 'cancelar', 'inicializar', 'limpieza_memoria', 'post_recuperar_interaccion', 'validar_datos',
		//				'error_proceso_hijo', 'pre_cargar_datos_dependencias', 'post_cargar_datos_dependencias');	
		return $eventos;
	}

?>