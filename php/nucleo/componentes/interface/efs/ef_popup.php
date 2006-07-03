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
	
	static function get_parametros()
	{
		$parametros = ef::get_parametros_carga();		
		unset($parametros['carga_lista']);		
		unset($parametros['carga_col_clave']);		
		unset($parametros['carga_col_desc']);		
		$parametros["carga_sql"]["descripcion"]="SQL utilizado para recuperar la descripcion en la modificacion del registro.".
											" (En el alta la descripcion la proveia el POPUP)\n".
											" ATENCION, el query tiene que devolver ID y DESCRIPCION, en este orden; y tiene que tener la cadena %w%".
											" en el lugar donde debe insertar el WHERE de filtrado por clave";
		$parametros["carga_sql"]["opcional"]=1;
		
		$parametros["edit_tamano"]["etiqueta"]="Cantidad Caracteres";
		$parametros["edit_tamano"]["descripcion"]="";
		$parametros["edit_tamano"]["opcional"]=1;
		$parametros["edit_maximo"]["etiqueta"]="Maximo Caracteres";
		$parametros["edit_maximo"]["descripcion"]="";
		$parametros["edit_maximo"]["opcional"]=1;		
		$parametros["popup_item"]["etiqueta"]="Item destino";
		$parametros["popup_item"]["descripcion"]="Item a invocar.";
		$parametros["popup_item"]["opcional"]=0;
		$parametros["popup_proyecto"]["etiqueta"]="Proyecto destino";
		$parametros["popup_proyecto"]["descripcion"]="Proyecto destino";
		$parametros["popup_proyecto"]["opcional"]=0;
		$parametros["popup_ventana"]["etiqueta"]="Parametros Ventana";
		$parametros["popup_ventana"]["descripcion"]="ancho, alto, scroll";
		$parametros["popup_ventana"]["opcional"]=1;
		$parametros["popup_editable"]["etiqueta"]="Editable";		
		$parametros["popup_editable"]["descripcion"]="El valor es editable libremente por parte del usuario,".
								" notar que la clave debe ser igual que el valor. La ventana de popup funciona sólo como una forma rápida de carga.";
		$parametros["popup_editable"]["opcional"]=1;	
		$parametros["estado_defecto"]["descripcion"]="Indica un valor predeterminado para el campo";
		$parametros["estado_defecto"]["opcional"]=1;	
		$parametros["estado_defecto"]["etiqueta"]="Valor defecto";
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
