<?php
/**
 * Representa un evento asociado a un EI
 * @package Componentes
 * @subpackage Eis
 */
class toba_evento_usuario extends toba_boton
{
	protected $vinculo;
	protected $parametros = null;
	protected $seleccion_multiple = false;
	protected $es_check_activo = false;
	protected $tiene_alineacion_pre_columnas = false;
	protected $accion_disparo_diferido = false;

	/**
	* Devuelve el vinculo asociado al evento
	* @param boolean $forzar_instancia Por defecto solo se crea una instancia de un vinculo asociada al evento,
	* 		con este parámetro fuerza a crear un nuevo objeto vinculo
	* @return toba_vinculo
	*/
	function vinculo($forzar_instancia=false)
	{
		if ( $this->posee_accion_vincular() ) {
			if ( !isset($this->vinculo) || $forzar_instancia) {
				$item = $this->datos['accion_vinculo_item'];
				if ($this->es_autovinculo()) {
					$aux_it = toba::memoria()->get_item_solicitado();
					$item =  $aux_it[1];
				}

				$this->vinculo = new toba_vinculo(	toba::proyecto()->get_id(), 
										$item,
										$this->datos['accion_vinculo_popup'],
										$this->datos['accion_vinculo_popup_param'] );
										
				if( isset($this->datos['accion_vinculo_celda']) && !is_null($this->datos['accion_vinculo_celda']) ) {
					$this->vinculo->set_opciones(array('celda_memoria'=>$this->datos['accion_vinculo_celda']));	
				}
				if( isset($this->datos['accion_vinculo_target']) && !is_null($this->datos['accion_vinculo_target']) ) {
					$this->vinculo->set_target($this->datos['accion_vinculo_target']);
				}
				$this->vinculo->set_propagar_zona();
				if (! $this->es_autovinculo()){
					$this->vinculo->agregar_opcion('menu', 1);	
				}
				if ( isset($this->datos['accion_vinculo_servicio']) && !is_null($this->datos['accion_vinculo_servicio']) ){
					$this->vinculo->set_servicio($this->datos['accion_vinculo_servicio']);
				}
			}
			return $this->vinculo;
		} else {
			throw new toba_error_def('El evento "' . $this->get_id() . '" no posee un VINCULO ASOCIADO.');
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
		return (isset($this->datos['implicito']) && ($this->datos['implicito'] == true ));
	}
	
	function es_autovinculo()
	{
		//No se mira la carpeta porque es un dato cosmetico (esta de mas en realidad)
		$hay_item = (isset($this->datos['accion_vinculo_item']) && ! is_null($this->datos['accion_vinculo_item']));
		if ($hay_item) {
			$es_autovinculo = (toba::solicitud()->get_id_operacion() == $this->datos['accion_vinculo_item']);
		} else {
			$es_autovinculo = (isset($this->datos['es_autovinculo']) && ($this->datos['es_autovinculo'] == '1'));
		}
		return $es_autovinculo;
	}
	
	
	/**
	 * El evento predeterminado si se encuentra en botonera se dispara al presionar la tecla ENTER.
	 * En caso de existir mï¿½s de uno en una pï¿½gina el browser utiliza el primero en el orden del fuente HTML.
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
	
	function posee_accion_vista_pdf()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'F');
	}
	
	function posee_accion_vista_excel()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'X');
	}	
	
	function posee_accion_respuesta_popup()
	{
		return ($this->posee_accion_asociada() && ($this->datos['accion'] == 'P'));
	}

	function posee_accion_vista_xslfo()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'xslfo');
	}	

	function posee_accion_vista_jasperreports()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'jasperreports');
	}			
	
	function posee_accion_vista_xml()
	{
		return $this->posee_accion_asociada() && ($this->datos['accion'] == 'xml');
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
	
	/**
	 * Retorna el evento_id de la base
	 */
	function get_id_metadato()
	{
		return isset($this->datos['evento_id']) ? $this->datos['evento_id'] : null;
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
	
	function es_seleccion_multiple()
	{
		return (isset($this->datos['es_seleccion_multiple']) && $this->datos['es_seleccion_multiple'] == '1');
	}

	function tiene_alineacion_pre_columnas()
	{
		return $this->tiene_alineacion_pre_columnas;
	}

	function posee_accionar_diferido()
	{
		return $this->accion_disparo_diferido;
	}
	//--------- Seters ---------------------
	
	function set_parametros($parametros = null)
	{
		$this->parametros = $parametros;
	}

	function set_vista_previa_impresion($estado=true)
	{
		$this->datos['accion_imphtml_debug'] = $estado;
	}
	
	function set_maneja_datos($maneja){
		$this->datos['maneja_datos'] = $maneja;
	}
	
	/**
	 * Aplica solo a los eventos a nivel de fila del cuadro
	 */
	function set_seleccion_multiple()
	{
		$this->datos['es_seleccion_multiple'] = '1';
	}

	/**
	 * Permite definir en runtime si el evento es una respuesta de popup o no
	 * @param char $accion Los unicos valores permitidos son 'P' o cadena vacia.
	 */
	function set_accion_respuesta_popup($accion)
	{
		if (($accion == '') || ($accion == 'P')) {
			$this->datos['accion'] = $accion;
		}
	}
	
	/**
	 * Permite definir en runtime si el evento invoca al servicio XSLFO
	 */
	function set_accion_xslfo()
	{
		$this->datos['accion'] = 'xslfo';
	}	
	
	function set_accion_imprimir()
	{
		$this->datos['accion'] = 'H';
	}
	
	function set_accion_jasperreports()
	{
		$this->datos['accion'] = 'jasperreports';
	}
	
	function set_check_activo($activo)
	{
			$this->es_check_activo = $activo;
	}

	function set_alineacion_pre_columnas($valor = true)
	{
		$this->tiene_alineacion_pre_columnas = $valor;
	}

	function set_disparo_diferido($disparo_diferido)
	{
		$this->accion_disparo_diferido = $disparo_diferido;
	}

	/**
	 *	Genera el HTML del BOTON
	 */
	function get_html($id_submit, $objeto_js, $id_componente)
	{
		$componente =  toba::output()->get("EventoUsuario");
		
		if ( $this->anulado ) return null;
		$tab_order = toba_manejador_tabs::instancia()->siguiente();
		$tip = '';
		if (isset($this->datos['ayuda'])) {
			$tip = $this->datos['ayuda'];
		}
		$acceso = tecla_acceso( $this->datos['etiqueta'] );		
		$image_resource = isset($this->datos['imagen_recurso_origen']) ? $this->datos['imagen_recurso_origen'] : null;
		$image_file = isset($this->datos['imagen']) ? $this->datos['imagen'] : null;
		$style = isset($this->datos['estilo']) ? $this->datos['estilo']: null;
		$imagen = $componente->getImagen($image_file, $image_resource, $this->esta_sobre_fila(), $style); 		
		if (! $this->es_seleccion_multiple()) {
			$defecto = isset($this->datos['defecto']) ? $this->datos['defecto']: null;			
			$clase = $componente->getCSS($this->esta_sobre_fila(), $style, $defecto, $this->activado);			
			$tipo_boton = $componente->getTipoBoton($this->esta_sobre_fila(), $style, $defecto);			
			$estilo_inline = $this->oculto ? 'display: none' : null;			
			$html = isset($acceso[0]) ? $acceso[0]: '';
			$tecla = $acceso[1];			
			$js = $this->get_invocacion_js($objeto_js, $id_componente);
			if (isset($js)) {
				$js = 'onclick="'.$js.'"';				
				return $componente->getInputButton( $id_submit. '_'. $this->get_id(), $html, $imagen , $js, $tab_order, $tecla, $tip, $tipo_boton, '', $clase, true, $estilo_inline, $this->activado, $this->esta_sobre_fila());
			}
		} else {
			$js = $this->get_invocacion_js($objeto_js, $id_componente);			
			$valor_actual = ($this->es_check_activo) ? $this->parametros : null;			
			if (isset($js)) {
				$extra = 'onclick="'.$js.'"';
				$extra .= " title='$tip'";
				$extra .= $this->activado ? '' : ' disabled';								
			}
			return $componente->getInputCheckbox($id_submit . '_' .$this->get_id(), $valor_actual, $this->parametros, '', $extra, $imagen );
		}
	}

	/**
	 * Genera el radio para un evento de seleccion en dos pasos
	 */
	function get_html_evento_diferido($id_submit, $fila, $objeto_js, $id_componente)
	{
		if ( $this->anulado ) return null;
		$tab_order = toba_manejador_tabs::instancia()->siguiente();
		$tip = '';
		$html = '';
		if (isset($this->datos['ayuda'])) {
			$tip = $this->datos['ayuda'];
		}
		$clase_predeterminada = $this->esta_sobre_fila() ? 'ei-boton-fila' : 'ei-boton';
		$clase = ( isset($this->datos['estilo']) && (trim( $this->datos['estilo'] ) != "")) ? $this->datos['estilo'] : $clase_predeterminada;
		$estilo_inline = $this->oculto ? 'display: none' : null;
		$js = $this->get_invocacion_js($objeto_js, $id_componente);
		if (isset($js)) {
			$js = 'onclick="'.$js.'"';
			$valor_actual = ($this->es_check_activo) ? 'checked' : '';
			$html =  toba_form::radio_manual($id_submit . $fila, $id_submit, $this->parametros, $valor_actual, $clase, $js, $tab_order, '');
		}
		return $html;
	}

	/**
	 *	Genera el evento JS
	 */
	function get_evt_javascript()
	{
		$evento = array();
		$evento['confirmacion'] = $this->get_msg_confirmacion();
		$evento['maneja_datos'] = $this->maneja_datos();
		return toba_js::evento($this->get_id(), $evento, $this->parametros, $this->es_implicito());
	}
	
	/**
	 * Genera la invocación JS necesaria para incluir en un onclick por ejemplo
	 */
	function get_invocacion_js($objeto_js=null, $id_componente = null)
	{
		if (! isset($objeto_js)) {
			$objeto_js = $this->contenedor->get_id_objeto_js();
		}
		if (! isset($id_componente)) {
			$id_componente = $this->contenedor->get_id();
		}		
		if ( $this->posee_accion_imprimir() ) {
			// ---*** IMPRIMIR HTML ***---
			$opciones['servicio'] = 'vista_toba_impr_html';
			$opciones['objetos_destino'] = array( $id_componente );
			//$opciones['celda_memoria'] = 'popup';
			$url = toba::vinculador()->get_url( null, null, array(), $opciones );
			if ( $this->datos['accion_imphtml_debug'] == 1 ) {
				$js = "imprimir_html('$url',true);";
			} else {
				$js = "imprimir_html('$url');";
			}
		} elseif ( $this->posee_accion_vista_pdf()) {
			// ---*** VISTA PDF ***---
			$opciones['servicio'] = 'vista_pdf';
			$opciones['objetos_destino'] = array( $id_componente );
			$url = toba::vinculador()->get_url( null, null, array(), $opciones );
			$js = "document.location.href='$url';";
		} elseif ( $this->posee_accion_vista_excel()) {
			// ---*** VISTA EXCEL ***---
			$opciones['servicio'] = 'vista_excel';
			$opciones['objetos_destino'] = array( $id_componente );
			$url = toba::vinculador()->get_url( null, null, array(), $opciones );
			$js = "document.location.href='$url';";			
		} elseif ( $this->posee_accion_vista_xslfo()) {
			// ---*** VISTA XSLFo ***---
			$opciones['servicio'] = 'vista_xslfo';
			$opciones['objetos_destino'] = array( $id_componente );
			$url = toba::vinculador()->get_url( null, null, array(), $opciones );
			$js = "document.location.href='$url';";			
		} elseif ( $this->posee_accion_vista_jasperreports()) {
			// ---*** VISTA JASPERREPORTS ***---
			$opciones['servicio'] = 'vista_jasperreports';
			$opciones['objetos_destino'] = array( $id_componente );
			$url = toba::vinculador()->get_url( null, null, array(), $opciones );
			$js = "document.location.href='$url';";			
		} elseif ( $this->posee_accion_vista_xml()) {
			// ---*** VISTA XML ***---
			$opciones['servicio'] = 'vista_xml';
			$opciones['objetos_destino'] = array( $id_componente );
			$url = toba::vinculador()->get_url( null, null, array(), $opciones );
			$js = "document.location.href='$url';";			
		} elseif ( $this->posee_accion_vincular() ) {
			// ---*** VINCULO ***---
			
			if (isset($this->datos['accion_vinculo_servicio']) && !is_null($this->datos['accion_vinculo_servicio'])){
				$this->vinculo()->set_servicio($this->datos['accion_vinculo_servicio']);
			}
			// Registro el vinculo en el vinculador
			$id_vinculo = toba::vinculador()->registrar_vinculo( $this->vinculo() );
			if( !isset( $id_vinculo ) ) { //Si no tiene permisos no devuelve un identificador
				return null;
			}
			
			$es_boton_visible = ($this->esta_en_botonera() || $this->esta_sobre_fila()) && $this->esta_activado();
			// Escribo la sentencia que invocaria el vinculo
			if ($this->posee_confirmacion() && $es_boton_visible)  {
				$conf_msg = $this->get_msg_confirmacion();
				$js = "{$objeto_js}.invocar_vinculo_confirmado('".$this->get_id()."', '$id_vinculo', '$conf_msg');";
			} else {
				$js = "{$objeto_js}.invocar_vinculo('".$this->get_id()."', '$id_vinculo');";
			}
		} elseif ( $this->posee_accion_respuesta_popup() ) {
			//--- En una respuesta a un ef_popup
			$param = addslashes(str_replace('"',"'",$this->parametros));
			$js = "iniciar_respuesta_popup(this, '$param');";
		} else {
			// Manejo estandar de eventos
			$submit = toba_js::bool(! ($this->es_seleccion_multiple() || $this->posee_accionar_diferido()));
			$js = "{$objeto_js}.set_evento(".$this->get_evt_javascript().", $submit, this);";
		}
		return $js;
	}
}
?>
