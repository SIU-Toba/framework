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
    
	static function get_parametros()
	{
		$parametros = ef::get_parametros_carga();
		unset($parametros['lista']);		
		unset($parametros['clave']);		
		unset($parametros['valor']);		
		$parametros["sql"]["descripcion"]="SQL utilizado para recuperar la descripcion en la modificacion del registro.".
											" (En el alta la descripcion la proveia el POPUP)\n".
											" ATENCION, el query tiene que devolver ID y DESCRIPCION, en este orden; y tiene que tener la cadena %w%".
											" en el lugar donde debe insertar el WHERE de filtrado por clave";
		$parametros["sql"]["opcional"]=1;
		
		$parametros["tamano"]["etiqueta"]="Cantidad Caracteres";
		$parametros["tamano"]["descripcion"]="";
		$parametros["tamano"]["opcional"]=1;
		$parametros["maximo"]["etiqueta"]="Maximo Caracteres";
		$parametros["maximo"]["descripcion"]="";
		$parametros["maximo"]["opcional"]=1;		
		$parametros["item_destino"]["etiqueta"]="Item destino";
		$parametros["item_destino"]["descripcion"]="Par `item,proyecto` a invocar. Si no se especifíca el proyecto se asume el actual";
		$parametros["item_destino"]["opcional"]=0;
		$parametros["ventana"]["etiqueta"]="Parametros Ventana";
		$parametros["ventana"]["descripcion"]="ancho, alto, scroll";
		$parametros["ventana"]["opcional"]=1;
		$parametros["editable"]["etiqueta"]="Editable";		
		$parametros["editable"]["descripcion"]="El valor es editable libremente por parte del usuario,".
								" notar que la clave debe ser igual que el valor. La ventana de popup funciona sólo como una forma rápida de carga.";
		$parametros["editable"]["opcional"]=1;	
		$parametros["estado"]["descripcion"]="Indica un valor predeterminado para el campo";
		$parametros["estado"]["opcional"]=1;	
		$parametros["estado"]["etiqueta"]="Valor defecto";
		return $parametros;
	}

	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros)
	{ 
        if(isset($parametros["columna_clave"])){
            $this->columna_clave = $parametros["columna_clave"];
        }
        if(isset($parametros["ventana"])){
            $this->ventana = trim($parametros["ventana"]);
            unset($parametros['ventana']);
        }else{
        	$this->ventana = null;
    	}
		$this->editable = false;
		if (isset($parametros["editable"])) {
			$this->editable = $parametros["editable"];
			unset($parametros["editable"]);
		}
        if (isset($parametros["item_destino"])){
			$destino = explode(',',$parametros['item_destino']);
			$this->item_destino = $destino[0];
			if(count($destino)==2){
				$this->item_destino_proyecto = $destino[1];
			}else{
				$this->item_destino_proyecto = toba::get_hilo()->obtener_proyecto();
			}
            unset($parametros['item_destino']);
			$vinculo = new vinculo(	$this->item_destino_proyecto, 
											$this->item_destino,
											true,
											$this->ventana );
            $this->id_vinculo = toba::get_vinculador()->registrar_vinculo( $vinculo );
		}		
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
