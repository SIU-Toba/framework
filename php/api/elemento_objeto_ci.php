<?php
require_once('api/elemento_objeto_ei.php');
require_once('api/elemento_objeto_ci_pantalla.php');

class elemento_objeto_ci extends elemento_objeto_ei
{
	
	function es_hoja()
	{
		return (count($this->hijos()) == 0);
	}	
	
	//---- Recorrido como arbol
	function hijos()
	{
		//Las dependencias son sus hijos
		//Hay una responsabilidad no bien limitada
		//Este objeto tiene las dependencias, cada pantalla debería poder sacar las que les concierne
		//Pero tambien este objeto debería saber cuales no son utilizadas por las pantallas
		$pantallas = array();
		if (isset($this->datos['apex_objeto_ci_pantalla'])) {
			//Se ordena por la columna orden
			$datos_pantallas = rs_ordenar_por_columna($this->datos['apex_objeto_ci_pantalla'],'orden');
			foreach ($datos_pantallas as $pantalla) {
				$pantallas[] = new elemento_objeto_ci_pantalla($pantalla, $this->subelementos);
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
		$eventos = array('inicializar');	
		//$eventos = array('procesar', 'cancelar', 'inicializar', 'limpieza_memoria', 'post_recuperar_interaccion', 'validar_datos',
		//				'error_proceso_hijo', 'pre_cargar_datos_dependencias', 'post_cargar_datos_dependencias');	
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
	
	function generar_metodos_basicos()
	{
		$basicos = parent::generar_metodos_basicos();
		$basicos[] = "\t".
'function mantener_estado_sesion()
	!#c2//Declarar todas aquellas propiedades de la clase que se desean persistir automáticamente
	!#c2//entre los distintos pedidos de página en forma de variables de sesión.
	{
		$propiedades = parent::mantener_estado_sesion();
		!#c1//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}
';
		return $this->filtrar_comentarios($basicos);
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
		//Se incluyen los eventos de los hijos
		foreach ($this->subelementos as $elemento) {
			$eventos += $elemento->generar_eventos($solo_basicos);
		}		
		return $eventos;
	}

	function utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al controlador",
			'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'ci', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id),
										false, false, null, true, "central")
		);
		return array_merge($iconos, parent::utilerias());	
	}		

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'proceso';
		$modelo[0]['nombre'] = 'Proceso';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'proceso':
				$evento[0]['identificador'] = "procesar";
				$evento[0]['etiqueta'] = "Proce&sar";
				$evento[0]['orden'] = 0;
				$evento[0]['en_botonera'] = 1;
				$evento[1]['identificador'] = "cancelar";
				$evento[1]['etiqueta'] = "&Cancelar";
				$evento[1]['maneja_datos'] = 0;
				$evento[1]['orden'] = 1;
				$evento[1]['en_botonera'] = 1;
				break;
		}
		return $evento;
	}
}
?>