<?php
require_once('info_ei.php');
require_once('info_ci_pantalla.php');

class info_ci extends info_ei
{
	/**
	*	Retorna la metaclase correspondiente a la pantalla
	*/
	function get_metaclase_subcomponente($subcomponente)
	{
		for ($i = 0 ; $i < count($this->datos['info_ci_me_pantalla']) ; $i++) {
			if ($this->datos['info_ci_me_pantalla'][$i]['identificador'] === $subcomponente) {
				return new info_ci_pantalla($this->datos['info_ci_me_pantalla'][$i],array(), $this->proyecto, $this->id);
			}
		}
		throw new toba_error("No se encuentra la pantalla '$id'");
	}
	
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
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
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

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		//************** Elementos PROPIOS *************
		//-- Inicializacion -----------------------
		$molde->agregar( new toba_molde_separador_php('Inicializacion',null,'grande') );
		$molde->agregar( new toba_molde_metodo_php('ini') );
		$molde->agregar( new toba_molde_metodo_php('ini_operacion') );
		$molde->agregar( new toba_molde_separador_php('Config.','Configuracion','grande') );
		$molde->agregar( new toba_molde_metodo_php('conf') );
		//-- Configuracion de pantallas -----------
		$molde->agregar( new toba_molde_separador_php('Configuracion de Pantallas','Pantallas') );
		$datos_pantallas = rs_ordenar_por_columna($this->datos['info_ci_me_pantalla'],'orden');
		foreach($datos_pantallas as $pantalla) {
			$molde->agregar( new toba_molde_metodo_php('conf__' . $pantalla['identificador'], array('pantalla') ) );
		}
		//-- Eventos propios ----------------------
		if (count($this->eventos_predefinidos()) > 0) {
			$molde->agregar( new toba_molde_separador_php('Eventos',null,'grande') );
			foreach ($this->eventos_predefinidos() as $evento => $info) {
				$molde->agregar( new toba_molde_metodo_php('evt__' . $evento) );
			}
		}
		//**************** DEPENDENCIAS ***************
		if (count($this->subelementos)>0) {
			$molde->agregar( new toba_molde_separador_php('DEPENDENCIAS',null,'grande') );
			foreach ($this->subelementos as $id => $elemento) {
				$es_ei = ($elemento instanceof info_ei) && !($elemento instanceof info_ci);
				$rol = $elemento->rol_en_consumidor();
				if ($es_ei) {
					$molde->agregar( new toba_molde_separador_php($rol) );
					//Eventos predefinidos del elemento
					if (count($elemento->eventos_predefinidos()) > 0) {
						foreach ($elemento->eventos_predefinidos() as $evento => $info) {
							$molde->agregar( new toba_molde_metodo_php('evt__' . $rol . '__' .$evento,	
																		$info['parametros'],
																		$info['comentarios']) );
						}
					}
					//Metodo de CONFIGURACION
					$molde->agregar( new toba_molde_metodo_php('conf__' . $rol,	
																array('componente'),
																array($elemento->get_comentario_carga()) ) );
				}
			}
		}
		//***************** JAVASCRIPT *****************
		$molde->agregar_bloque( $this->get_molde_eventos_js() );
		return $molde;
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