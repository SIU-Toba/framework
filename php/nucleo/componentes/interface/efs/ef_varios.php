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
    
    static function get_lista_parametros()
    {
    	return array(
    					'check_valor_si',
    					'check_valor_no',
    					'check_desc_si',
    					'check_desc_no'
    	);
    }
    
    function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		//VAlor FIJO
		if(isset($parametros['estado_defecto'])){
			$this->estado_defecto = $parametros['estado_defecto'];		
			$this->estado = $this->estado_defecto;
		}
		if (isset($parametros['check_valor_si'])){
		    $this->valor = $parametros['check_valor_si'];
		} else {
			$this->valor = '1';
		}
		if (isset($parametros['check_valor_no'])){
		    $this->valor_no_seteado = $parametros['check_valor_no'];
		} else {
			$this->valor_no_seteado = '0';	
		}	
		if (isset($parametros["check_desc_si"])){
		    $this->valor_info = $parametros["check_desc_si"];
		}
		if (isset($parametros["check_desc_no"])){
		    $this->valor_info_no_seteado = $parametros["check_desc_no"];
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
			$tab = $this->padre->get_tab_index();
			$extra = " tabindex='$tab'";		
            return form :: checkbox($this->id_form, $this->estado, $this->valor,null, $extra.' '.$this->javascript);
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
		$consumos = array('efs/ef','efs/ef_checkbox');
		return $consumos;
	}	
	
	function tiene_estado()
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
class ef_fijo extends ef_oculto
{
	private $estilo;
	private $maneja_datos;
	
	static function get_lista_parametros()
	{
		$parametros[] = 'fijo_sin_estado';
		return $parametros;
	}
	
	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
		$this->estilo = "ef-fijo";
		if(isset($parametros['fijo_sin_estado']) && $parametros['fijo_sin_estado'] == 1){
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
		$consumos = array('efs/ef');
		return $consumos;
	}	
	
	function crear_objeto_js()
	{
		return "new ef_fijo({$this->parametros_js()})";
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

	static function get_lista_parametros()
	{
		$parametros[] = 'editor_ancho';
		$parametros[] = 'editor_alto';
		$parametros[] = 'editor_botonera';
		return $parametros;
	}	
	
	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		$this->ancho = (isset($parametros['editor_ancho']))? $parametros['editor_ancho'] : "100%";
		$this->alto = (isset($parametros['editor_alto']))? $parametros['editor_alto'] : "300px";
		$this->botonera = (isset($parametros['editor_botonera']))? $parametros['editor_botonera'] : "Toba";
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

?>