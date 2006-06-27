<?php
require_once('info_ei.php');
require_once('info_ci_pantalla.php');

class info_ci extends info_ei
{
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function es_hoja()
	{
		$es_hoja = parent::es_hoja() && $this->get_cant_pantallas() == 0;
	}
	
	function tiene_hijos_cargados()
	{
		return (!$this->es_hoja() && count($this->subelementos) != 0) || $this->get_cant_pantallas() != 0;
	}
		
	function get_pantalla($id)
	{
		for ($i = 0 ; $i < count($this->datos['info_ci_me_pantalla']) ; $i++) {
			if ($this->datos['info_ci_me_pantalla'][$i]['pantalla'] == $id) {
				return new info_ci_pantalla($this->datos['info_ci_me_pantalla'][$i],
											$this->subelementos, $this->proyecto, $this->id);
			}
		}
		throw new excepcion_toba("No se encuentra la pantalla $id");
	}
	
	function get_cant_pantallas()
	{
		if ($this->carga_profundidad) {
			return count($this->datos['info_ci_me_pantalla']);
		} else {
			return 0;	
		}
	}
	
	function get_hijos()
	{
		//Las dependencias son sus hijos
		//Hay una responsabilidad no bien limitada
		//Este objeto tiene las dependencias, cada pantalla debería poder sacar las que les concierne
		//Pero tambien este objeto debería saber cuales no son utilizadas por las pantallas
		$pantallas = array();
		if ($this->carga_profundidad && count($this->datos['info_ci_me_pantalla'])>0) {
			//Se ordena por la columna orden
			$datos_pantallas = rs_ordenar_por_columna($this->datos['info_ci_me_pantalla'],'orden');
			foreach ($datos_pantallas as $pantalla) {
				$pantallas[] = new info_ci_pantalla($pantalla, $this->subelementos, $this->proyecto, $this->id);
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

	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al controlador",
			'vinculo' => toba::get_vinculador()->generar_solicitud("admin","/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'ci', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id ),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		return array_merge($iconos, parent::get_utilerias());	
	}		

	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		/*	Hay que agregar entradas y salidas de pantallas */
		return $eventos;
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
				$evento[0]['etiqueta'] = "&Guardar";
				$evento[0]['imagen_recurso_origen'] = 'apex';
				$evento[0]['imagen'] = 'guardar.gif';
				$evento[0]['maneja_datos'] = 1;
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
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function generar_cuerpo_clase($opciones)
	{
		$cuerpo = parent::generar_cuerpo_clase($opciones);
		//Eventos de la clase
		if (count($this->eventos_predefinidos()) > 0) {
			$cuerpo .= clase_php::separador_seccion_chica('Eventos CI');
			foreach ($this->eventos_predefinidos() as $evento => $info) {
				$cuerpo .= clase_php::generar_metodo('evt__' . $evento);
			}
		}
		//Eventos de las dependencias
		if (count($this->subelementos)>0) {
			$cuerpo .= clase_php::separador_seccion_grande('DEPENDENCIAS');
			foreach ($this->subelementos as $elemento) {
				$es_ei = ($elemento instanceof info_ei) && !($elemento instanceof info_ci);
				$rol = $elemento->rol_en_consumidor();
				$cuerpo .= clase_php::separador_seccion_chica($rol);
				if ($es_ei) {
					//Eventos predefinidos del elemento
					if (count($elemento->eventos_predefinidos()) > 0) {
						foreach ($elemento->eventos_predefinidos() as $evento => $info) {
							$metodo_evento = clase_php::generar_metodo('evt__' . $rol . '__' .$evento, 
																			$info['parametros'], null, $info['comentarios'] );
							$cuerpo .= $this->filtrar_comentarios_metodo($metodo_evento);
						}
					}
					//Metodo de CARGA!
					$comentario_carga = $elemento->get_comentario_carga();
					$metodo_carga = clase_php::generar_metodo('evt__' . $rol . '__carga', null, null, $comentario_carga);
					$cuerpo .= $this->filtrar_comentarios_metodo($metodo_carga);
				}
			}
		}
		return $cuerpo;
	}

	function generar_metodos()
	{
		$metodos = parent::generar_metodos();
		$metodos[] = "\t".
'function mantener_estado_sesion()
	!#c2//Declarar todas aquellas propiedades de la clase que se desean persistir automáticamente
	!#c2//entre los distintos pedidos de página en forma de variables de sesión.
	{
		$propiedades = parent::mantener_estado_sesion();
		!#c1//$propiedades[] = \'propiedad_a_persistir\';
		return $propiedades;
	}
';
		return $this->filtrar_comentarios($metodos);
	}
}
?>