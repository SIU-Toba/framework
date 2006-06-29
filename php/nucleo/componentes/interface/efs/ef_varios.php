<?
require_once("ef.php");
require_once("ef_oculto.php");


/**
 * ef <abstracta>
 * 			|
 * 			+----> ef_checkbox
 * 			|
 * 			+----> ef_fijo
 * 			|
 * 			+----> ef_elemento_ini (FALTA botones: limpiar, inicializar, parametros mejor)
 * 			|
 * 			+----> ef_combo_editable
 * 
 * #########################################################################################################
 * #######################################################################################################
 */

class ef_checkbox extends ef
{
    protected $valor;
    protected $valor_no_seteado;
    protected $valor_info = 'Sí';
    protected $valor_info_no_seteado = 'No';
    
	static function get_parametros()
	{
		$parametros["valor"]["descripcion"]="Valor que toma el elemento cuando esta activado (por defecto 1).";
		$parametros["valor"]["opcional"]=0;	
		$parametros["valor"]["etiqueta"]="Valor ACTIVADO.";	
		$parametros["valor_no_seteado"]["descripcion"]="Valor que toma el elemento cuando esta desactivado (por defecto 0)";
		$parametros["valor_no_seteado"]["opcional"]=1;	
		$parametros["valor_no_seteado"]["etiqueta"]="Valor DESACTIVADO";	
		$parametros["estado"]["descripcion"]="";
		$parametros["estado"]["opcional"]=0;	
		$parametros["estado"]["etiqueta"]="Valor por defecto";	
		$parametros["valor_info"]["descripcion"]="Descripcion coloquial del valor ACTIVADO";
		$parametros["valor_info"]["opcional"]=0;	
		$parametros["valor_info"]["etiqueta"]="Info Valor Act.";	
		$parametros["valor_info_no_seteado"]["descripcion"]="Descripcion coloquial del valor DESACTIVACION";
		$parametros["valor_info_no_seteado"]["opcional"]=0;	
		$parametros["valor_info_no_seteado"]["etiqueta"]="Info Valor DesAct.";			
		return $parametros;
	}

    function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		//VAlor FIJO
		if(isset($parametros["estado"])){
			$this->estado_defecto = $parametros["estado"];		
			$this->estado = $this->estado_defecto;
		}
		if (isset($parametros["valor"])){
		    $this->valor = $parametros["valor"];
		} else {
			$this->valor = 1;
		}
		if (isset($parametros["valor_no_seteado"])){
		    $this->valor_no_seteado = $parametros["valor_no_seteado"];
		} else {
			$this->valor_no_seteado = 0;	
		}	
		if (isset($parametros["valor_info"])){
		    $this->valor_info = $parametros["valor_info"];
		}
		if (isset($parametros["valor_info_no_seteado"])){
		    $this->valor_info_no_seteado = $parametros["valor_info_no_seteado"];
		}		
		parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
    }
    
	function get_input()
    {
    	//Esto es para eliminar un notice en php 5.0.4
    	if (!isset($this->estado))
    		$this->estado = null;
    		
         if ($this->solo_lectura) 
         {
		 	if ($this->estado != "")
	            $html_devuelto = form::hidden($this->id_form, $this->estado);
			else
				$html_devuelto = "";
				
            if ($this->seleccionado()) {
                $html_devuelto .= recurso::imagen_apl('checked.gif',true,16,16);
            } else {
                $html_devuelto .= recurso::imagen_apl('unchecked.gif',true,16,16);            
            }
            return $html_devuelto;   
         }else
         {
         	//$extra = "DISABLED" . $this->javascript;
         	//echo ei_mensaje($extra);
            return form :: checkbox($this->id_form, $this->estado, $this->valor,null,$this->javascript);
         }            
    }

	function set_estado($estado)
	//Carga el estado interno
	{
   		if(isset($estado)){								
    		$this->estado=$estado;
			return true;
	    }else{
			//Si el valor no seteado existe, paso el estado a ese valor.
			if (isset($this->valor_no_seteado)) {
	    		$this->estado = $this->valor_no_seteado;
	    		return true;
			} else {
    			$this->estado = null;			
			}
    	}
		return false;
	}
	
	function cargar_estado_post()
	{
		if(isset($_POST[$this->id_form])) {
			$this->set_estado($_POST[$this->id_form]);
    	} else {
    		$this->set_estado(null);
    	}
		return false;		
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('interface/ef','interface/ef_checkbox');
		return $consumos;
	}	
	
	function activado()
	{
		return isset($this->estado) && 
				($this->estado == $this->valor || $this->estado == $this->valor_no_seteado);
	}	

	function seleccionado()
	{
		return isset($this->estado) && 
				($this->estado == $this->valor);
	}	
	
	function crear_objeto_js()
	{
		return "new ef_checkbox({$this->parametros_js()})";
	}	


	function get_descripcion_estado()
	{
		if ( !isset($this->estado) || $this->estado == $this->valor_no_seteado ) {
			return $this->valor_info_no_seteado;
		} else {
			return $this->valor_info;
		}
	}
	
}
// ########################################################################################################
// ########################################################################################################
// PARAMETROS ADICIONALES:
// "estado": Valor que tiene que tomar el elemento
class ef_fijo extends ef_oculto
{
	private $estilo;
	private $maneja_datos;
	

	static function get_parametros()
	{
		$parametros["estilo"]["etiqueta"]="Clase CSS";
		$parametros["estilo"]["descripcion"]="Clase CSS del campo (tiene que estar incluida en el archivo css del proyecto";
		$parametros["estilo"]["opcional"]=1;	
		$parametros["sin_datos"]["etiqueta"]="Sin datos";
		$parametros["sin_datos"]["descripcion"]="Si el valor es 1, indica que el elemento no maneja datos (es solo informativo)";
		$parametros["sin_datos"]["opcional"]=1;	
		return $parametros;
	}

	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
		$this->estilo = isset($parametros["estilo"]) ? $parametros["estilo"] : "ef-fijo";
		if(isset($parametros["sin_datos"]) && $parametros["sin_datos"] == 1){
			$this->maneja_datos = false;
		}else{
			$this->maneja_datos = true;
		}
		
	}
   
	function set_estado($estado=null)
	{
		/*
			Si el EF maneja datos utilizo la logica de persistencia del padre
		*/
		if($this->maneja_datos){
			return parent::set_estado($estado);
		}else{
			if(isset($estado)) {
				$this->estado = $estado;
			}		
		}
	}

	function get_input()
    {
		$estado = (isset($this->estado)) ? $this->estado : null;
		$html = "<div class='{$this->estilo}' id='{$this->id_form}'>".$estado."</div>";
		return $html;
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('interface/ef');
		return $consumos;
	}	
	
	function crear_objeto_js()
	{
		return "new ef_fijo({$this->parametros_js()})";
	}	
			
}

// ########################################################################################################
// ########################################################################################################
class ef_combo_editable extends ef
{
     var $ef_combo;
     var $ef_editable;
     var $dato;
    
	static function get_parametros()
	{
		$parametros[""]["descripcion"]="";
		$parametros[""]["opcional"]=1;	
		return $parametros;
	}

     function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
         if(count($dato) != 2){
             echo ei_mensaje("EF_COMBO_VALOR: El elemento posee 2 columnas asociadas");
             return;
             }
         $this->dato = $dato;
         $parametros_combo['no_seteado'] = isset($parametros['no_seteado'])? $parametros['no_seteado']: null;
         $parametros_combo['sql'] = isset($parametros['sql'])? $parametros['sql']: null;
         $parametros_combo['fuente'] = isset($parametros['fuente'])? $parametros['fuente']: null;
         $this->ef_combo = & new ef_combo_db($padre, $nombre_formulario,
             $id . "_" . $dato[0], $etiqueta,
             $descripcion, $dato[0],
             $obligatorio, $parametros_combo);
         $parametros_editable['estado'] = isset($parametros['estado'])? $parametros['estado']: null;
         $parametros_editable['tamano'] = isset($parametros['tamano'])? $parametros['tamano']: null;
         $parametros_editable['maximo'] = isset($parametros['maximo'])? $parametros['maximo']: null;
         $this->ef_editable = & new ef_editable($padre, $nombre_formulario,
             $id . "_" . $dato[1], $etiqueta,
             $descripcion, $dato[1],
             $obligatorio, $parametros_editable);
         parent :: ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio);
         }
    
     function set_estado($estado = null)
     { // Carga el estado interno
        if($estado != null){
			$this->ef_combo->set_estado($estado[$this->dato[0]]);
			$this->ef_editable->set_estado($estado[$this->dato[1]]);
		}else{
			$this->ef_combo->set_estado();
			$this->ef_editable->set_estado();
		}
	}
    
	function get_estado()
    {
         $temp[$this->dato[0]] = $this->ef_combo->get_estado();
         $temp[$this->dato[1]] = $this->ef_editable->get_estado();
         return $temp;
	}
    
     function get_input()
    {
         // Informacion al usuario sobre el elemento: AYUDA y PARAMETROS
        // Elementos de FORMULARIO
        $html = "";
         $html .= "<table class='tabla-0'>\n";
         $html .= "<tr><td>\n";
         $html .= $this->ef_combo->get_input();
         $html .= "</td><td>\n";
         $html .= $this->ef_editable->get_input();
         $html .= "</td></tr>\n";
         $html .= "</table>\n";
         return $html;
         }
    }

// ########################################################################################################
// ########################################################################################################
//Editor WYSIWYG de HTML

class ef_html extends ef
{
	var $ancho;
	var $alto;
	var $botonera;

	static function get_parametros()
	{
		$parametros["ancho"]["descripcion"]= "Ancho, especificar medida ej. 450px o 80%";
		$parametros["ancho"]["opcional"] = 1;	
		$parametros["ancho"]["etiqueta"] = "Ancho";
		$parametros["alto"]["descripcion"]= "Alto, especificar medida ej. 450px o 80%";
		$parametros["alto"]["opcional"] = 1;	
		$parametros["alto"]["etiqueta"] = "Alto";			
		$parametros["botonera"]["descripcion"] = "Tipo de botonera (por defecto toba";	
		$parametros["botonera"]["opcional"] = 1;	
		$parametros["botonera"]["etiqueta"] = "Botonera";
		return $parametros;
	}

	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		$this->ancho = (isset($parametros["ancho"]))? $parametros["ancho"] : "100%";
		$this->alto = (isset($parametros["alto"]))? $parametros["alto"] : "300px";
		$this->botonera = (isset($parametros["botonera"]))? $parametros["botonera"] : "Toba";
         parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
	}

	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		//Consumo la expresion regular que machea numeros.
		$consumo[] = "fck_editor";
		return $consumo;
	}

	function get_input()
	{
		if(isset($this->estado)){
			$estado = $this->estado;
		}else{
			$estado = "";
		}

		if ($this->solo_lectura) {
			$html = "<div style='font-family: Arial, Verdana, Sans-Serif;
								 font-size: 12px;
								 padding: 5px 5px 5px 5px;
								 margin: 0px;
								 border-style: none;
								 background-color: #ffffff;'>
								$estado
								</div>";
		} else {
			$estado = addslashes($estado);
			$html = "<script type='text/javascript'>
						  var oFCKeditor = new FCKeditor('{$this->id_form}','{$this->ancho}','{$this->alto}','{$this->botonera}','{$estado}' ) ;
						  oFCKeditor.BasePath = 'js/fckeditor/';
						  oFCKeditor.Create() ;
					 </script>";
		}
		return $html;
	}
}
// ########################################################################################################
// ########################################################################################################
//Editor de PHP con sintaxis coloreada

class ef_php extends ef
{
	var $ancho;
	var $alto;

	static function get_parametros()
	{
		$parametros[""]["descripcion"]="";
		$parametros[""]["opcional"]=1;	
		return $parametros;
	}

	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		$this->ancho = (isset($parametros["ancho"]))? $parametros["ancho"] : "100%";
		$this->alto = (isset($parametros["alto"]))? $parametros["alto"] : "300";
         parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
	}
	//---------------------------------------------------------

	function obtener_interface_ut()
	{
		echo $this->obtener_input();
	}
	//---------------------------------------------------------

    function obtener_javascript()
    {
        //Obtengo el CODIGO PHP del iframe
        return "formulario.". $this->id_form .".value = ".$this->id_form."_editor.getContents();";
    }
	//---------------------------------------------------------

	function get_input()
	{
	    $estado = str_replace("\r", "", $this->estado);
	    $estado = str_replace("\n", "\\n", $estado);
	    $estado = str_replace('"', '\"', $estado);
	    $estado = str_replace("\t", "\\t", $estado);
	    $estado = "Hola, que tal";
		$html = "
<iframe name='{$this->id_form}_editor' src='".recurso::js('helene/editor.html').
				"' style='width: {$this->ancho}; height: {$this->alto};'></iframe>
<script type='text/javascript'>
function {$this->id_form}_init(){
//alert('$estado');
document.{$this->id_form}_editor.setContents(\"$estado\");
}
$this.onload = {$this->id_form}_init();
</script>";
		$html .= form::hidden($this->id_form,$estado);
		return $html;
	}
}
// ########################################################################################################
// ########################################################################################################
?>