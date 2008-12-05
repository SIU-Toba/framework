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
	protected $img_editar = 'editar.gif';

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
		if ( isset($this->descripcion_estado)) {
			$valor = $this->descripcion_estado;
		} elseif (isset($this->estado)) { 
			$valor = $this->estado;
		} else {
			$valor = null;
		}
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
		$this->img_editar = $url;
	}
	
	function get_input()
	{
		$tab = $this->padre->get_tab_index();
		$extra = " tabindex='$tab'";		
		if(!isset($this->estado)) $this->estado="";	
		if (!isset($this->descripcion_estado) || $this->descripcion_estado == '') {
			$this->descripcion_estado = $this->estado;		
		}
		$js = '';	
		$r = '';
		if ($this->cuando_cambia_valor != '') {
			$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
		}
		$r .= "<span class='{$this->clase_css}'>";
		if ($this->editable) {
			$r .= toba_form::hidden($this->id_form."_desc", $this->estado);
			$disabled = ($this->solo_lectura) ? "disabled" : "";
			$r .= toba_form::text($this->id_form, $this->descripcion_estado, false, "", $this->tamano, "ef-input", $extra.' '.$disabled.' '.$js);
			$extra = '';
		} else {
			$r .= toba_form::hidden($this->id_form, $this->estado, $js);
			$r .= toba_form::text($this->id_form."_desc", $this->descripcion_estado, false, "", $this->tamano, "ef-input", "disabled ");
		}	
		if (isset($this->id_vinculo)) {
			$display = ($this->solo_lectura) ? "visibility:hidden" : "";
			$r .= "<a id='{$this->id_form}_vinculo' style='$display' $extra";
			$r .= " onclick=\"{$this->objeto_js()}.abrir_vinculo();\"";
	        $r .= " href='#'>".toba_recurso::imagen_toba($this->img_editar, true,16,16,"Seleccionar un elemento")."</a>";
		}
		if ($this->no_oblig_puede_borrar) {
			$display = ($this->solo_lectura) ? "visibility:hidden" : "";
			$r .= "<a id='{$this->id_form}_borrar' style='$display' $extra";
			$r .= " onclick=\"{$this->objeto_js()}.set_estado(null, null);\"";
	        $r .= " href='#'>".toba_recurso::imagen_toba('limpiar.png',true,null,null,"Limpia la selección actual")."</a>";
		}		
		$r .= "</span>\n";
		return $r;
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
			$this->descripcion_estado = '';		
		}
		if($this->tiene_estado()){
			unset($this->estado);
		}
		if (isset($this->estado_defecto)) {
			$this->estado = $this->estado_defecto;	
		}
	}

}
//########################################################################################################
//########################################################################################################
?>
