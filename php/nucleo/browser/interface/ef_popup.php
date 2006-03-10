<?

class ef_popup extends ef_editable
{
    var $descripcion_estado;
    var $item_destino;
    private $item_destino_proyecto;
    var $ventana;
	protected $editable;
    
	static function get_parametros()
	{
		$parametros["tamano"]["etiqueta"]="Cantidad Caracteres";
		$parametros["tamano"]["descripcion"]="";
		$parametros["tamano"]["opcional"]=1;
		$parametros["maximo"]["etiqueta"]="Maximo Caracteres";
		$parametros["maximo"]["descripcion"]="";
		$parametros["maximo"]["opcional"]=1;		$parametros["item_destino"]["etiqueta"]="Item destino";
		$parametros["item_destino"]["descripcion"]="";
		$parametros["item_destino"]["opcional"]=0;
		$parametros["ventana"]["etiqueta"]="Parametros Ventana";
		$parametros["ventana"]["descripcion"]="ancho, alto, scroll";
		$parametros["ventana"]["opcional"]=1;
		$parametros["editable"]["etiqueta"]="Editable";		
		$parametros["editable"]["descripcion"]="El valor es editable libremente por parte del usuario,".
								" notar que la clave debe ser igual que el valor. La ventana de popup funciona sólo como una forma rápida de carga.";
		$parametros["editable"]["opcional"]=1;	
		$parametros["sql"]["etiqueta"]="SQL recup. descripcion";
		$parametros["sql"]["descripcion"]="SQL utilizado para recuperar la descripcion en la modificacion del registro.".
											" (En el alta la descripcion la proveia el POPUP)\n".
											" ATENCION, el query tiene que devolver ID y DESCRIPCION, en este orden; y tiene que tener la cadena %w%".
											" en el lugar donde debe insertar el WHERE de filtrado por clave";
		$parametros["sql"]["opcional"]=1;
		$parametros["columna_clave"]["etiqueta"]="SQL recup. descripcion (CLAVE)";
		$parametros["columna_clave"]["descripcion"]="CLAVE del Query utilizado para recuperar la descripcion";
		$parametros["columna_clave"]["opcional"]=1;
		
		$parametros["dao"]["descripcion"]="Cargar el valor de un metodo.";
		$parametros["dao"]["opcional"]=1;	
		$parametros["dao"]["etiqueta"]="Carga Dao: metodo";
		$parametros["clase"]["descripcion"]="(Util solo si existe [dao]) Nombre de la clase que posee el metodo.";
		$parametros["clase"]["opcional"]=1;	
		$parametros["clase"]["etiqueta"]="Carga Dao: clase";
		$parametros["include"]["descripcion"]="(Util solo si existe [dao]) Archivo que posee la definicion de la clase.";
		$parametros["include"]["opcional"]=1;	
		$parametros["include"]["etiqueta"]="Carga Dao: include";
		return $parametros;
	}

	function ef_popup($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros)
	{ 
        if(isset($parametros["columna_clave"])){
            $this->columna_clave = $parametros["columna_clave"];
        }
        if(isset($parametros["sql"])){
            $this->sql = stripslashes($parametros["sql"]); 
            unset($parametros['sql']);
        }
        if(isset($parametros["ventana"])){
            $this->ventana = explode(",",$parametros["ventana"]); 
			if(count($this->ventana)!=3){
				$this->ventana = null;
			}else{
				for($a=0;$a<count($this->ventana);$a++){
					$this->ventana[$a] = "'". $this->ventana[$a] ."'";
				}
			}
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
				if($destino[1]=='toba'){
					$this->item_destino_proyecto = $destino[1];
				}else{
					throw new excepcion_toba_def("No es posible abrir un popup de un proyecto externo que no sea el administrador.");
				}
			}else{
				$this->item_destino_proyecto = toba::get_hilo()->obtener_proyecto();
			}
            unset($parametros['item_destino']);
		}		
		parent::ef_editable($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros);
	}
	
//-------------------- INTERFACE --------------------------

	function obtener_input()
	{
		if(!isset($this->estado)) $this->estado="";	
       	$this->obtener_descripcion_estado();  
        if (isset($this->item_destino)) {
            $vinculo_item = toba::get_vinculador()->obtener_vinculo_a_item(
					            							$this->item_destino_proyecto, 
															$this->item_destino,
															array("ef_popup" => $this->id_form, "ef_popup_valor" => $this->estado),
															false, false, false, null, null, true, "popup");
        }
		if(!isset($this->descripcion_estado)) $this->descripcion_estado="";
		if($this->solo_lectura || $vinculo_item == NULL){
			$r = form::text("", $this->descripcion_estado ,$this->solo_lectura,"", $this->tamano ,"ef-input","disabled " . $this->javascript);
			$r .= form::hidden($this->id_form, $this->estado);
		}else{
			$r = $this->obtener_javascript_general() . "\n\n";		
            $recurso_js_cod = "document.{$this->nombre_formulario}.{$this->id_form}";
            $recurso_js_desc = "document.{$this->nombre_formulario}.{$this->id_form}_desc";
    		$r .= "<table class='tabla-0'>";
    		$r .= "<tr><td>\n";		
			if ($this->editable) {
				$r .= form::hidden($this->id_form."_desc", $this->estado, $this->obtener_javascript_input());
    			$r .= form::text($this->id_form, $this->descripcion_estado ,false, "", $this->tamano, "ef-input", $this->javascript);
			} else {
				$r .= form::hidden($this->id_form, $this->estado, $this->obtener_javascript_input());
    			$r .= form::text($this->id_form."_desc", $this->descripcion_estado ,false, "", $this->tamano, "ef-input", "disabled ". $this->javascript);
			}	
			$r .= "</td><td>\n";
			$r .= "<a id='{$this->id_form}_vinculo'";
			if(!isset($this->ventana)){
				$inicializacion_ventana = "null";
			}else{
				//Parametros de inicializacion de la ventana
				//ancho, alto, scroll.
				$inicializacion_ventana = "[" . implode(",",$this->ventana) . "]";
			}
			$r .= " onclick=\" javascript: popup_abrir_item('$vinculo_item', '{$this->id_form}', $recurso_js_cod, $recurso_js_desc, $inicializacion_ventana)\"";
            $r .= "href='#' name='{$this->id_form}_vinculo'>".recurso::imagen_apl('editar.gif',true,16,16,"Selecionar un elemento")."</a> ";
			$r .= "</td></tr>\n</table>\n";            
        }
		return $r;
	}
    
	function obtener_consumo_javascript()
	{
		return array_merge(parent::obtener_consumo_javascript(), array('interface/ef_popup'));
	}	
	
	function crear_objeto_js()
	{
		return "new ef_popup({$this->parametros_js()})";
	}		
	
    function obtener_javascript()
    {
        //Si el campo es obligatorio, en el form hay que llenarlo si o si
        if($this->obligatorio){
            return "
				if( ereg_nulo.test(formulario.". $this->id_form .".value) ){
					alert(\"El campo '". $this->etiqueta ."' es obligatorio.\");
				    return false;
				}";
        }
    }
	
	function javascript_master_get_estado()
	{
		return "
		function master_get_estado_{$this->id_form}()
		{
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			return(s_.value);
		}
		";		
	}
	
	function javascript_master_cargado()
	{
		return "
		function master_cargado_{$this->id_form}()
		{
			return (master_get_estado_{$this->id_form}() != '".apex_ef_no_seteado.".');
		}
		";		
	}
	
 
	function resetear_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			unset($this->estado);
            unset($this->descripcion_estado);
		}
		if (isset($this->estado_defecto)) {
			$this->estado = $this->estado_defecto;	
		}
	}

    //Carga la descripcion desde la base de datos en base al estado actual
    function obtener_descripcion_estado()
    {
        if (trim($this->estado) == '') {
            $this->descripcion_estado = ''; 
            return;
        }                                                                              
        if (isset($this->sql)) {
            $where_adj = array();
            if (isset($this->columna_clave)) {
               $where_adj[] = $this->columna_clave."='".$this->estado."'";    
               $temp_sql = sql_agregar_clausulas_where($this->sql,$where_adj);
            } else {
                $temp_sql = sql_agregar_clausulas_where($this->sql, "");
			}
			try{
				$datos = consultar_fuente($temp_sql,$this->fuente,ADODB_FETCH_NUM);
				if(count($datos)>0){
		            $this->estado = $datos[0][0];  
					$this->descripcion_estado = $datos[0][1];
				}else{
					$this->descripcion_estado = 'ERROR: No se pudo recuperar la DESCRIPCION (No hay registros)';
				}
			}catch( excepcion_toba $e){
				$this->descripcion_estado = 'ERROR SQL' . $e->getMessage();
			}
        } else if (isset($this->dao) && $this->modo =="estatico" && isset($this->estado)) {
        	$this->descripcion_estado = $this->recuperar_datos_dao($this->estado);
        } else if (isset($this->dao) && $this->modo !="estatico" && isset($this->estado)) {
        	throw new excepcion_toba("Un ef_popup no puede cargar su descripcion a partir de un dao dinamico...");
        } else {
        	$this->descripcion_estado = $this->estado;	
        }
    }
   
 	function cargar_datos_dao()
 	{
 		//Para eliminar la herencia	
 	}
}
//########################################################################################################
//########################################################################################################
?>
