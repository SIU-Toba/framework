<?php

class toba_evento_usuario
{
	protected $datos;
	protected $vinculo;
	protected $activado = true;		//Utilizado en los evento sobre_fila para filtrar filas puntuales
	protected $parametros = null;
		
	function __construct($datos)
	{
		$this->datos = $datos;
	}

	/**
	*	Devuelve el vinculo asociado al evento
	*/
	function vinculo()
	{
		if ( $this->posee_accion_vincular() ) {
			if ( !isset( $this->vinculo ) ) {
				$this->vinculo = new toba_vinculo(	toba::hilo()->obtener_proyecto(), 
										$this->datos['accion_vinculo_item'],
										$this->datos['accion_vinculo_popup'],
										$this->datos['accion_vinculo_popup_param'] );
				if( $this->datos['accion_vinculo_celda'] ) {
					$this->vinculo->set_opciones(array('celda_memoria'=>$this->datos['accion_vinculo_celda']));	
				}
				if( $this->datos['accion_vinculo_target'] ) {
					$this->vinculo->set_target($this->datos['accion_vinculo_target']);
				}
			}
			return $this->vinculo;
		} else {
			throw new toba_excepcion('El evento "' . $this->get_id() . '" no posee un VINCULO ASOCIADO.');
		}
	}

	//--------- Preguntas ------------------

	function maneja_datos()
	{
		return ( $this->datos['maneja_datos'] == true );
	}
	
	function esta_sobre_fila()
	{
		return ( $this->datos['sobre_fila'] == true );
	}
	
	function posee_confirmacion()
	{
		return ( trim($this->datos['confirmacion']) !== '' );
	}

	function esta_en_botonera()
	{
		// La condicion del EI agregaba esto: !isset($evento['en_botonera'])
		return ( $this->datos['en_botonera'] == true );
	}
	
	function es_implicito()
	{
		return ( $this->datos['implicito'] == true );
	}
	
	function es_predeterminado()
	{
		return ( $this->datos['defecto'] == true );
	}

	function posee_accion_asociada()
	{
		return ( $this->datos['accion'] != '' );
	}
	
	function posee_accion_imprimir()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'H');
	}

	function posee_accion_vincular()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'V');
	}
	
	function posee_grupo_asociado()
	{
		return trim($this->datos['grupo'])!='';
	}
	
	function pertenece_a_grupo($grupo)
	{
		return in_array($grupo, $this->get_grupos() );
	}

	function esta_activado()
	{
		return $this->activado;
	}

	//--------- Geters ---------------------
	
	function get_id()
	{	
		return $this->datos['identificador'];	
	}
	
	function get_etiqueta()
	{
		return $this->datos['etiqueta'];	
	}

	function get_msg_confirmacion()
	{
		return $this->datos['confirmacion'];	
	}

	function get_imagen()
	{
		if (isset($this->datos['imagen']) && $this->datos['imagen'] != '') {
			if (isset($this->datos['imagen_recurso_origen'])) {
				$img = toba_recurso::imagen_de_origen($this->datos['imagen'], $this->datos['imagen_recurso_origen']);
			} else {
				$img = $this->datos['imagen'];
			}
			return toba_recurso::imagen($img, null, null, null, null, null, 'vertical-align: middle;').' ';
		}
	}

	function get_imagen_url_rel()
	{
		return $this->datos['imagen'];
	}

	function get_msg_ayuda()
	{
		return $this->datos['ayuda'];	
	}

	function get_grupos()
	{
		if ( $this->posee_grupo_asociado() ) {
			return array_map('trim',explode(',',$this->datos['grupo']));
		}
	}
	
	//--------- Seters ---------------------
	
	function set_etiqueta($texto)
	{
		$this->datos['etiqueta'] = $texto;
	}
	
	function set_msg_confirmacion($texto)
	{
		$this->datos['confirmacion'] = $texto;
	}

	function set_imagen($url_relativa, $origen=null)
	{
		if (isset($origen) && ( ($origen != 'apex') || ( $origen != 'proyecto') ) ) {
			throw new toba_excepcion_def("EVENTO: El origen de la imagen debe ser 'apex' o 'proyecto'. Valor recibido: $origen");	
		} else {
			$origen = 'apex';	
		}
		$this->datos['imagen_recurso_origen'] = $origen;
		$this->datos['imagen'] = $url_relativa;
	}

	function set_msg_ayuda($texto)
	{
		$this->datos['ayuda'] = $texto;
	}

	function set_parametros($parametros = null)
	{
		$this->parametros = $parametros;
	}
	
	function desactivar()
	{
		$this->activado = false;			
	}

	function activar()
	{
		$this->activado = true;
	}

	//--------- Consumo interno ------------
	
	/**
	*	Genera el HTML del BOTON
	*/
	function generar_boton($id_submit, $id_componente)
	{
		$tab_order = manejador_tabs::instancia()->siguiente();
		$tip = '';
		if (isset($this->datos['ayuda'])) {
			$tip = $this->datos['ayuda'];
		}
		$clase_predeterminada = $this->esta_sobre_fila() ? 'ei-boton-fila' : 'ei-boton';
		$clase = ( isset($this->datos['estilo']) && (trim( $this->datos['estilo'] ) != "")) ? $this->datos['estilo'] : $clase_predeterminada;
		$tipo_boton = 'button';		
		if ( !$this->esta_sobre_fila() && isset($this->datos['defecto']) && $this->datos['defecto']) {
			$tipo_boton = 'submit';
			$clase .=  '  ei-boton-defecto';			
		}
		$acceso = tecla_acceso( $this->datos['etiqueta'] );
		$html = '';
		$html .= $this->get_imagen();
		$html .= $acceso[0];
		$tecla = $acceso[1];

		if ( $this->posee_accion_imprimir() ) {
			// ---*** IMPRIMIR HTML ***---
			$opciones['servicio'] = 'vista_toba_impr_html';
			$opciones['objetos_destino'] = array( $this->id );
			//$opciones['celda_memoria'] = 'popup';
			$url = toba::vinculador()->crear_vinculo( null, null, array(), $opciones );
			if ( $this->datos['accion_imphtml_debug'] == 1 ) {
				$js = "onclick=\"imprimir_html('$url',true);\"";
			} else {
				$js = "onclick=\"imprimir_html('$url');\"";
			}
			echo toba_form::button_html( $id_submit."_".$this->get_id(), $html, $js, $tab_order, $tecla, $tip, $tipo_boton, '', $clase);
		} elseif ( $this->posee_accion_vincular() ) {
			// ---*** VINCULO ***---
			// Registro el vinculo en el vinculador
			$id_vinculo = toba::vinculador()->registrar_vinculo( $this->vinculo() );
			if( isset( $id_vinculo ) ) { //Si no tiene permisos no devuelve un identificador
				// Escribo la sentencia que invocaria el vinculo
				$js = "onclick=\"{$id_componente}.invocar_vinculo('".$this->get_id()."', '$id_vinculo');\"";
				echo toba_form::button_html( $id_submit."_".$this->get_id(), $html, $js, $tab_order, $tecla, $tip, $tipo_boton, '', $clase);
			}
		} else {
			// Manejo estandar de eventos
			$js = "onclick=\"{$id_componente}.set_evento(".$this->get_evt_javascript().");\"";
			echo toba_form::button_html( $id_submit."_".$this->get_id(), $html, $js, $tab_order, $tecla, $tip, $tipo_boton, '', $clase);
		}
	}

	/**
	*	Genera el evento JS
	*/
	function get_evt_javascript()
	{
		$js_confirm = $this->posee_confirmacion() ? "'".$this->get_msg_confirmacion()."'" : "''";
		$js_validar = $this->maneja_datos() ? "true" : "false";
		if (is_array($this->parametros))
			$param = ", ".toba_js::arreglo($this->parametros, true);
		else		
			$param = (isset($this->parametros)) ? ", '".$this->parametros."'" : '';
		return "new evento_ei('".$this->get_id()."', $js_validar, $js_confirm $param)";
	}
}
?>