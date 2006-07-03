<?
require_once('ef.php');

class ef_popup extends ef_editable
{
    protected $descripcion_estado = '';
    protected $item_destino;
    protected $item_destino_proyecto;
    protected $ventana;
	protected $editable;
	protected $id_vinculo;

    static function get_lista_parametros_carga()
    {
    	$parametros = ef::get_lista_parametros_carga_basico();    
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
    	$this->item_destino = $parametros['popup_item'];
		$this->item_destino_proyecto = $parametros['popup_proyecto'];
		$vinculo = new vinculo(	$this->item_destino_proyecto, 
										$this->item_destino,
										true,
										$this->ventana );
        $this->id_vinculo = toba::get_vinculador()->registrar_vinculo( $vinculo );

		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros);
	}
	
	function carga_depende_de_estado()
	{
		return true;	
	}
	
	function cargar_valores($descripcion=null)
	{
		if (!isset($descripcion)) {
			$this->solo_lectura = 1;
		} 
		if (!isset($this->estado)) {
			$this->estado = $descripcion;
			$this->descripcion_estado = $descripcion;
		}
	}
	
	function get_input()
	{
		if(!isset($this->estado)) $this->estado="";	
		if ($this->descripcion_estado == '') {
			$this->descripcion_estado = $this->estado;			
		}
		$js = '';	
		$r = '';
		if ($this->cuando_cambia_valor != '') {
			$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
		}
		$r .= "<span class='ef-popup'>";
		if ($this->editable) {
			$r .= form::hidden($this->id_form."_desc", $this->estado);
			$disabled = ($this->solo_lectura) ? "disabled" : "";
			$r .= form::text($this->id_form, $this->descripcion_estado ,false, "", $this->tamano, "ef-input", $disabled.' '.$js);
		} else {
			$r .= form::hidden($this->id_form, $this->estado, $js);
			$r .= form::text($this->id_form."_desc", $this->descripcion_estado ,false, "", $this->tamano, "ef-input", "disabled ");
		}	
		$display = ($this->solo_lectura) ? "visibility:hidden" : "";
		$r .= "<a id='{$this->id_form}_vinculo' style='$display' ";
		$r .= " onclick=\"{$this->objeto_js()}.abrir_vinculo();\"";
        $r .= " href='#'>".recurso::imagen_apl('editar.gif',true,16,16,"Seleccionar un elemento")."</a>";
        $r .= "</span>\n";
		return $r;
	}
    
	function get_consumo_javascript()
	{
		return array_merge(ef::get_consumo_javascript(), array('interface/ef_popup'));
	}	
	
	function parametros_js()
	{
		$vinculo = (is_numeric($this->id_vinculo)) ? $this->id_vinculo : "null";
		return ef::parametros_js().", $vinculo";
	}
	
	function crear_objeto_js()
	{
		return "new ef_popup({$this->parametros_js()})";
	}		
	
	function resetear_estado()
	{
		$this->descripcion_estado = '';		
		if($this->activado()){
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
