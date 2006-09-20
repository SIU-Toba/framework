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
			if ((string) $this->datos['info_ci_me_pantalla'][$i]['pantalla'] === (string) $id) {
				return new info_ci_pantalla($this->datos['info_ci_me_pantalla'][$i],
											$this->subelementos, $this->proyecto, $this->id);
			}
		}
		throw new toba_error("No se encuentra la pantalla $id");
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
			'imagen' => toba_recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al controlador",
			'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
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

	/**
	*	Retorna la metaclase del subcomponente
	*/
	function get_metadatos_subcomponente($subcomponente)
	{
		for ($i = 0 ; $i < count($this->datos['info_ci_me_pantalla']) ; $i++) {
			if ($this->datos['info_ci_me_pantalla'][$i]['identificador'] === $subcomponente) {
				$sub['clase'] = $this->datos['info_ci_me_pantalla'][$i]['subclase'];
				$sub['archivo'] = $this->datos['info_ci_me_pantalla'][$i]['subclase_archivo'];
				$sub['padre_clase'] = 'toba_ei_pantalla';
				$sub['padre_archivo'] = 'nucleo/componentes/interface/toba_ei_pantalla.php';
				$sub['meta_clase'] = new info_ci_pantalla($this->datos['info_ci_me_pantalla'][$i],array(), $this->proyecto, $this->id);
				//toba::logger()->var_dump($sub);
				return $sub;
			}
		}
		throw new toba_error("No se encuentra la pantalla '$id'");
	}

	function get_plan_construccion_metodos()
	{
		$plan = array();
		//**************** PROPIOS ****************
		//Inicializacion
		$plan['ini']['desc'] = 'INICIALIZACION';
		$plan['ini']['bloque'][0]['metodos']['ini'] = array('comentarios'=>array(), 'parametros'=>array());
		$plan['ini']['bloque'][0]['metodos']['ini_operacion'] = array('comentarios'=>array(), 'parametros'=>array());
		//Configuracion general
		$plan['conf']['desc'] = 'CONFIGURACION';
		$plan['conf']['bloque'][0]['metodos']['conf'] = array('comentarios'=>array(), 'parametros'=>array());
		//Configuracion de pantallas
		$datos_pantallas = rs_ordenar_por_columna($this->datos['info_ci_me_pantalla'],'orden');
		foreach($datos_pantallas as $pantalla) {
			$plan['conf']['bloque'][0]['metodos']['conf__' . $pantalla['identificador']] = array('comentarios'=>array(), 'parametros'=>array());
		}
		//Eventos propios
		if (count($this->eventos_predefinidos()) > 0) {
			$plan['evt_propios']['desc'] = 'EVENTOS';
			foreach ($this->eventos_predefinidos() as $evento => $info) {
				$plan['evt_propios']['bloque'][0]['metodos']['evt__' . $evento] = array('comentarios'=>array(), 'parametros'=>array());
			}
		}
		//**************** DEPENDENCIAS ***************
		if (count($this->subelementos)>0) {
			$plan['evt_deps']['desc'] = 'DEPENDENCIAS';
			foreach ($this->subelementos as $id => $elemento) {
				$es_ei = ($elemento instanceof info_ei) && !($elemento instanceof info_ci);
				$rol = $elemento->rol_en_consumidor();
				if ($es_ei) {
					$plan['evt_deps']['bloque'][$id]['desc'] = $rol;
					//Metodo de CONFIGURACION!
					$m = 'conf__' . $rol;
					$plan['evt_deps']['bloque'][$id]['metodos'][$m] = array();
					$plan['evt_deps']['bloque'][$id]['metodos'][$m]['parametros'][] = 'componente';
					$comentario_carga = $elemento->get_comentario_carga();
					if($comentario_carga) {
						$plan['evt_deps']['bloque'][$id]['metodos'][$m]['comentarios'][] = $comentario_carga;
					}else{
						$plan['evt_deps']['bloque'][$id]['metodos'][$m]['comentarios'] = array();
					}
					//Eventos predefinidos del elemento
					if (count($elemento->eventos_predefinidos()) > 0) {
						foreach ($elemento->eventos_predefinidos() as $evento => $info) {
							$m = 'evt__' . $rol . '__' .$evento;
							$plan['evt_deps']['bloque'][$id]['metodos'][$m] = array();
							$plan['evt_deps']['bloque'][$id]['metodos'][$m]['parametros'] = $info['parametros'];
							$plan['evt_deps']['bloque'][$id]['metodos'][$m]['comentarios'] = $info['comentarios'];
						}
					}
					//Metodo de CARGA!
					$m = 'conf__' . $rol;
					$plan['evt_deps']['bloque'][$id]['metodos'][$m] = array();
					$plan['evt_deps']['bloque'][$id]['metodos'][$m]['parametros'][] = 'componente';
					$comentario_carga = $elemento->get_comentario_carga();
					if($comentario_carga) {
						$plan['evt_deps']['bloque'][$id]['metodos'][$m]['comentarios'][] = $comentario_carga;
					}else{
						$plan['evt_deps']['bloque'][$id]['metodos'][$m]['comentarios'] = array();
					}
				}
			}
		}
		//***************** JAVASCRIPT *****************
		if (count($this->eventos_predefinidos()) > 0) {
			$plan['javascript']['desc'] = 'EVENTOS JAVASCRIPT';
			$plan['javascript']['bloque'][0]['metodos'] = $this->get_plan_construccion_eventos_js();
		}
		return $plan;
	}

	static function get_eventos_internos(toba_datos_relacion $dr)
	{
		$eventos = array();
		$navegacion = $dr->tabla('prop_basicas')->get_columna('tipo_navegacion');
		if (isset($navegacion)) {
			if ($navegacion == 'wizard') {
				$eventos['cambiar_tab__siguiente'] = "El usuario avanza de pantalla, generalmente con el botón <em>Siguiente</em>.";
				$eventos['cambiar_tab__anterior'] = "El usuario retrocede de pantalla, generalmente con el botón <em>Anterior</em>.";
			} else {
				$eventos['cambiar_tab_X'] = "El usuario cambia a la pantalla X utilizando los tabs o solapas.";
			}
		}
		return $eventos;
	}	
}
?>
