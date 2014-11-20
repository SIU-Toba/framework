<?php

/**
 * Permite seleccionar un valor a partir de un item de popup. Pensado para conjunto grandes de valores
 * El ef solo se encarga del componente grafico que lanza el popup y de recibir el estado desde el popup, pero no de
 * armar la operación de popup ni su forma de elección del valor
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_popup ef_popup
 */
class toba_ef_popup extends toba_ef_editable
{
	protected $descripcion_estado;
	protected $item_destino;
	protected $item_destino_proyecto;
	protected $ventana;
	protected $editable;
	protected $vinculo;
	protected $id_vinculo;
	protected $clase_css = 'ef-popup';
	protected $no_oblig_puede_borrar = false;
	protected $img_editar; // = 'editar.gif';

	static function get_lista_parametros_carga()
	{
		$parametros = toba_ef::get_lista_parametros_carga_basico();
		array_borrar_valor($parametros, 'carga_lista');
		array_borrar_valor($parametros, 'carga_col_clave');
		array_borrar_valor($parametros, 'carga_col_desc');
		return $parametros;
	}
	 
	static function get_lista_parametros()
	{
		$parametros[] = 'edit_tamano';
		$parametros[] = 'edit_maximo';
		$parametros[] = 'popup_item';
		$parametros[] = 'popup_proyecto';
		$parametros[] = 'popup_ventana';
		$parametros[] = 'popup_editable';
		$parametros[] = 'popup_carga_desc_metodo';
		$parametros[] = 'popup_carga_desc_clase';
		$parametros[] = 'popup_carga_desc_include';
		$parametros[] = 'popup_puede_borrar_estado';
		$parametros[] = 'punto_montaje';
		$parametros[] = 'edit_placeholder';
		return $parametros;
	}

	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros)
	{
		if(isset($parametros['popup_ventana'])){
			$this->ventana = trim($parametros['popup_ventana']);
			unset($parametros['popup_ventana']);
		}else{
			$this->ventana = null;
		}
		$this->editable = false;
		if (isset($parametros['popup_editable'])) {
			$this->editable = $parametros['popup_editable'];
			unset($parametros['popup_editable']);
		}
		if (isset($parametros['popup_puede_borrar_estado'])) {
			$this->no_oblig_puede_borrar = 	$parametros['popup_puede_borrar_estado'];
			unset($parametros['popup_puede_borrar_estado']);
		}
		$this->item_destino = $parametros['popup_item'];
		$this->item_destino_proyecto = $parametros['popup_proyecto'];
		$this->vinculo = new toba_vinculo(	$this->item_destino_proyecto,
		$this->item_destino,
		true,
		$this->ventana );
		$this->vinculo->agregar_opcion('menu',true);
		$this->vinculo->set_celda_memoria('popup');
		if (is_null($this->ventana)) {
			$this->vinculo->set_popup_parametros( array(	'scrollbars'=>true,
															'resizable'=>true,
															'height'=>500,
															'width'=>500 ), true );
		}
		$this->id_vinculo = toba::vinculador()->registrar_vinculo( $this->vinculo );
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros);
	}

	/**
	 * Retorna el vinculo asociado al popup
	 */
	function vinculo()
	{
		return $this->vinculo;
	}

	function carga_depende_de_estado()
	{
		return !$this->editable;
	}

	/**
	 * Retorna la descripción asociada a la opción actualmente seleccionada
	 */
	function get_descripcion_estado($tipo_salida)
	{
		$valor = $this->get_descripcion_valor();
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$valor</div>";
				break;
			case 'pdf':
				return $valor;
			case 'excel':
				return array($valor, null);
				break;
		}
	}

	protected function get_descripcion_valor()
	{
		if ( isset($this->descripcion_estado)) {
			$valor = $this->descripcion_estado;
		} elseif (isset($this->estado)  && !is_array($this->estado)) {
			$valor = $this->estado;
		} else {
			$valor = null;
		}
		return $valor;
	}

	function set_opciones($descripcion, $maestros_cargados=true)
	{
		//--- No se actualiza $this->opciones_cargadas porque los popups requieren
		//--- que siempre se refresquen sus opciones porque se basan en su estado
		//--- En cambio se cambia su descripcion
		if (! $maestros_cargados) {
			$this->solo_lectura = 1;
		}
		$this->descripcion_estado = $descripcion;
	}

	function set_puede_borrar_estado($puede=true)
	{
		$this->no_oblig_puede_borrar = $puede;
	}

	function set_imagen_editar($url)
	{
		if (stripos($url, '<img') !== false) {			//Ya viene con el tag img armado
			$this->img_editar = $url;
		} else {
			$this->img_editar = toba_recurso::imagen($url);
		}
	}

	function get_input()
	{
		$js = '';
		$html = '';
		$tab = $this->padre->get_tab_index();
		$extra = " tabindex='$tab'";
		if(!isset($this->estado)) $this->estado="";
		if (!isset($this->descripcion_estado) || $this->descripcion_estado == '') {
			$this->descripcion_estado = $this->get_descripcion_valor();
		}

		$estado = (is_array($this->estado)) ? implode(apex_qs_separador, $this->estado) : $this->estado;
		$html .= "<span class='{$this->clase_css}'>";
		if ($this->cuando_cambia_valor != '') {
			$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
		}
		
		$extra .= $this->get_estilo_visualizacion_pixeles();	
		$extra .= $this->get_info_placeholder();
		if ($this->editable) {
			$disabled = ($this->es_solo_lectura()) ? "disabled" : "";			
			$html .= toba_form::hidden($this->id_form."_desc", $estado);
			$html .= toba_form::text($this->id_form, $this->descripcion_estado, false, "", $this->tamano, "ef-input", $extra.' '.$disabled.' '.$js);
			$extra = '';
		} else {
			$html .= toba_form::hidden($this->id_form, $estado, $js);
			$html .= toba_form::text($this->id_form."_desc", $this->descripcion_estado, false, "", $this->tamano, "ef-input", " $extra disabled ");
		}
		if (isset($this->id_vinculo)) {
			$display = ($this->es_solo_lectura()) ? "visibility:hidden" : "";
			$html .= "<a id='{$this->id_form}_vinculo' style='$display' $extra";
			$html .= " onclick=\"{$this->objeto_js()}.abrir_vinculo();\"";
			$html .= " href='#'>".$this->get_imagen_abrir()."</a>";
		}
		if ($this->no_oblig_puede_borrar) {
			$display = ($this->es_solo_lectura()) ? "visibility:hidden" : "";
			$html .= "<a id='{$this->id_form}_borrar' style='$display' $extra";
			$html .= " onclick=\"{$this->objeto_js()}.set_estado(null, null);\"";
			$html .= " href='#'>".$this->get_imagen_limpiar()."</a>";
		}
		$html .= $this->get_html_iconos_utilerias();
		$html .= "</span>\n";
		return $html;
	}

	function get_imagen_abrir()
	{
		if (!isset($this->img_editar)) {
			return toba_recurso::imagen_toba('editar.gif', true,16,16,"Seleccionar un elemento");
		} else {
			return $this->img_editar;
		}
	}

	function set_img_editar($img, $tooltip="Seleccionar un elemento")
	{
		$this->img_editar = toba_recurso::imagen_proyecto($img,true,null,null,$tooltip);
	}

	function get_imagen_limpiar()
	{
		if (!isset($this->custom_img_limpiar)) {
			return toba_recurso::imagen_toba('limpiar.png',true,null,null, 'Limpia la selección actual');
		} else {
			return $this->custom_img_limpiar;
		}
	}

	function set_img_limpiar($img,$tooltip="Limpia la selección actual")
	{
		$this->custom_img_limpiar = toba_recurso::imagen_proyecto($img,true,null,null,$tooltip);
	}



	function get_consumo_javascript()
	{
		return array_merge(toba_ef::get_consumo_javascript(), array('efs/ef_popup'));
	}

	function parametros_js()
	{
		$vinculo = (is_numeric($this->id_vinculo)) ? $this->id_vinculo : "null";
		return toba_ef::parametros_js().", $vinculo";
	}

	function crear_objeto_js()
	{
		return "new ef_popup({$this->parametros_js()})";
	}

	function resetear_estado()
	{
		if (isset($this->descripcion_estado)) {
			$this->descripcion_estado = null;
		}
		if($this->tiene_estado()){
			unset($this->estado);
		}
		if (isset($this->estado_defecto)) {
			$this->estado = $this->estado_defecto;
		}
	}

	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])) {
			$explotable = explode(apex_qs_separador, trim($_POST[$this->id_form]));
			if (count($explotable) == 1) {
				$this->estado = current($explotable);
			}else{
				$this->estado = $explotable;
			}
		} else {
			$this->estado = null;
		}
	}

	function set_estado($estado)
	{
		if(isset($estado)){
			$this->estado= (is_array($estado)) ? $estado :  trim($estado);
		} else {
			$this->estado = null;
		}
	}
}
//########################################################################################################
//########################################################################################################
?>
