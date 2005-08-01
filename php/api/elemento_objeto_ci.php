<?php
require_once('api/elemento_objeto.php');
require_once('api/elemento_objeto_ci_pantalla.php');

class elemento_objeto_ci extends elemento_objeto
{
	function nombre_corto()
	{
		$nombre = parent::nombre_corto();
		return "CI - $nombre";
	}
	
	//---- Recorrido como arbol
	function hijos()
	{
		//Las dependencias son sus hijos
		//Hay una responsabilidad no bien limitada
		//Este objeto tiene las dependencias, cada pantalla debería poder sacar las que les concierne
		//Pero tambien este objeto debería saber cuales no son utilizadas por las pantallas
		$pantallas = array();
		if (isset($this->datos['apex_objeto_mt_me_etapa'])) {
			foreach ($this->datos['apex_objeto_mt_me_etapa'] as $pantalla) {
				$pantalla = new elemento_objeto_ci_pantalla($pantalla, $this->subelementos);
				//Una pantalla que no tiene nada no interesa en la vista de arbol
				if (! $pantalla->es_hoja())
					$pantallas[] = $pantalla;
			}
		}
		//Busca Dependencias libres
		$dependencias_libres = array();
		foreach ($this->subelementos as $dependencia) {
			$libre = true;
			foreach ($pantallas as $pantalla) {
				if ($pantalla->tiene_dependencia($dependencia)) {
					$libre = false;
				}
			}
			if ($libre) {
				$dependencias_libres[] = $dependencia;
			}
		}
		return array_merge($pantallas, $dependencias_libres);
	}	

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
	
	static function get_lista_eventos_estandar()
	{
		$evento[0]['identificador'] = "procesar";
		$evento[0]['etiqueta'] = "Proce&sar";
		$evento[1]['identificador'] = "cancelar";
		$evento[1]['etiqueta'] = "&Cancelar";
		return $evento;		
	}
}


?>