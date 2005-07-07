<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ci extends elemento_objeto
{
	function eventos_predefinidos()
	{
		$eventos = array('procesar', 'cancelar', 'inicializar', 'limpieza_memoria', 'post_recuperar_interaccion', 'validar_datos',
						'error_proceso_hijo', 'pre_cargar_datos_dependencias', 'post_cargar_datos_dependencias');	
		return $eventos;
	}

	function generar_constructor()
	{
		$constructor = 
'	function __construct($id)
	{
		!#c2//Zona apta para inicializaciones por defecto
		parent::__construct($id);
		!#c2//Aquí ya se restauraron los valores de las propiedades mantenidas en sesión
	}
';			
		return $this->filtrar_comentarios($constructor);

	}	
	
	function generar_eventos($solo_basicos)
	{
		$eventos = parent::generar_eventos($solo_basicos);
		if (!$solo_basicos) {
			foreach ($this->eventos_predefinidos() as $evento) {
				$funcion = "\tfunction evt__$evento()\n\t{\n\t}\n";
				$eventos['Propios'][] = $this->filtrar_comentarios($funcion);
			}
		}
		//ATENCION: Cuando puedan definirse nuevos eventos en el administrador incluirlos aquí
		
		//Se incluyen los eventos de los hijos
		foreach ($this->subelementos as $elemento) {
			$eventos += $elemento->generar_eventos($solo_basicos);
		}		
		return $eventos;
	}	
}


?>