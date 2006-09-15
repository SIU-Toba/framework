<?php
require_once('nucleo/lib/toba_boton.php');

/**
 * Representa la forma de graficar un evento
 * @package Componentes
 * @subpackage Eis
 */
class toba_evento_usuario extends toba_boton
{
	protected $vinculo;
	protected $parametros = null;

	/**
	*	Devuelve el vinculo asociado al evento
	*/
	function vinculo()
	{
		if ( $this->posee_accion_vincular() ) {
			if ( !isset( $this->vinculo ) ) {
				$this->vinculo = new toba_vinculo(	toba::proyecto()->get_id(), 
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
			throw new toba_error('El evento "' . $this->get_id() . '" no posee un VINCULO ASOCIADO.');
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
	
	function esta_en_botonera()
	{
		// La condicion del EI agregaba esto: !isset($evento['en_botonera'])
		return ( $this->datos['en_botonera'] == true );
	}
	
	function es_implicito()
	{
		return ( $this->datos['implicito'] == true );
	}
	
	/**
	 * El evento predeterminado si se encuentra en botonera se dispara al presionar la tecla ENTER.
	 * En caso de existir más de uno en una página el browser utiliza el primero en el orden del fuente HTML.
	 */
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
	
	//--------- Geters ---------------------
	
	function get_id()
	{	
		return $this->datos['identificador'];	
	}
	
	function get_grupos()
	{
		if ( $this->posee_grupo_asociado() ) {
			return array_map('trim',explode(',',$this->datos['grupo']));
		}
	}
	
	function get_parametros()
	{
		return $this->parametros;	
	}
	
	//--------- Seters ---------------------
	
	function set_parametros($parametros = null)
	{
		$this->parametros = $parametros;
	}

	//--------- Consumo interno ------------
	
	/**
	*	Genera el HTML del BOTON
	*/
	function get_html($id_submit, $id_componente)
	{
		if ( $this->anulado ) return null;
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
		$estilo_inline = $this->oculto ? 'display: none' : null;
		
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
			return toba_form::button_html( $id_submit."_".$this->get_id(), $html, $js, $tab_order, $tecla, 
											$tip, $tipo_boton, '', $clase, true, $estilo_inline, $this->activado);
		} elseif ( $this->posee_accion_vincular() ) {
			// ---*** VINCULO ***---
			// Registro el vinculo en el vinculador
			$id_vinculo = toba::vinculador()->registrar_vinculo( $this->vinculo() );
			if( isset( $id_vinculo ) ) { //Si no tiene permisos no devuelve un identificador
				// Escribo la sentencia que invocaria el vinculo
				$js = "onclick=\"{$id_componente}.invocar_vinculo('".$this->get_id()."', '$id_vinculo');\"";
				return toba_form::button_html( $id_submit."_".$this->get_id(), $html, $js, $tab_order, $tecla, 
												$tip, $tipo_boton, '', $clase, true, $estilo_inline, $this->activado);
			}
		} else {
			// Manejo estandar de eventos
			$js = "onclick=\"{$id_componente}.set_evento(".$this->get_evt_javascript().");\"";
			return toba_form::button_html( $id_submit."_".$this->get_id(), $html, $js, $tab_order, $tecla, 
											$tip, $tipo_boton, '', $clase, true, $estilo_inline, $this->activado);
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