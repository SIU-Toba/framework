<?

class ef_popup extends ef_editable
{
    var $descripcion_estado;
    var $vinculo_item;
    var $ventana;
    
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
        
		parent::ef_editable($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros);

        if (isset($parametros["item_destino"])){
            $item = $parametros["item_destino"];
    		global $solicitud;
    		$proyecto = $solicitud->hilo->obtener_proyecto();
            $this->vinculo_item = $solicitud->vinculador->obtener_vinculo_a_item($proyecto, 
                                                          $item, array("ef_popup" => $this->id_form), false);

            unset($parametros['item_destino']);                                        
        }

	}
//-------------------- INTERFACE --------------------------

	function obtener_input()
	{
		if(!isset($this->estado)) $this->estado="";	
		if(!isset($this->descripcion_estado)) $this->descripcion_estado="";
		if($this->solo_lectura || $this->vinculo_item == NULL){
			$r = form::text("", $this->estado,$this->solo_lectura,"", $this->tamano ,"ef-input","disabled " . $this->javascript);
			$r .= form::hidden($this->id_form, $this->estado);
		}else{
			$r = $this->obtener_javascript_general() . "\n\n";		
            $recurso_js_cod = "document.{$this->nombre_formulario}.{$this->id_form}";
            $recurso_js_desc = "document.{$this->nombre_formulario}.{$this->id_form}_desc";
    		$r .= "<table class='tabla-0'>";
    		$r .= "<tr><td>\n";		
			$r .= form::hidden($this->id_form, $this->estado, $this->obtener_javascript_input());
    		$r .= form::text($this->id_form."_desc", $this->descripcion_estado ,false, "", $this->tamano, "ef-input", "disabled ". $this->javascript);
			$r .= "</td><td>\n";
			$r .= "<a id='link_{$this->id_form}'";
			if(!isset($this->ventana)){
				$inicializacion_ventana = "null";
			}else{
				//Parametros de inicializacion de la ventana
				//ancho, alto, scroll.
				$inicializacion_ventana = "[" . implode(",",$this->ventana) . "]";
			}
			$r .= " onclick=\" javascript: popup_abrir_item('{$this->vinculo_item}', '{$this->id_form}', $recurso_js_cod, $recurso_js_desc, $inicializacion_ventana)\"";
            $r .= "href='#' name='link_{$this->id_form}'>".
                   recurso::imagen_apl('doc.gif',true,16,16,"Selecionar un elemento")."</a> ";
			$r .= "</td></tr>\n</table>\n";            
        }
		return $r;
	}
    
	function obtener_consumo_javascript()
	{
		$consumo = parent::obtener_consumo_javascript();
		$consumo[] = "popup";
		return $consumo;
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
	
    function cargar_estado($estado=null)
    {
        parent::cargar_estado($estado);
        $this->obtener_descripcion_estado();                    
    }   
    
	function resetear_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			unset($this->estado);
            unset($this->descripcion_estado);
		}
	}

    //Carga la descripcion desde la base de datos en base al estado actual
    function obtener_descripcion_estado()
    {
        if (trim($this->estado) == '')
        {
            $this->descripcion_estado = ''; 
            return;
        }                                                                              

        if (isset($this->sql)) 
        {
            $where_adj = array();
            if (isset($this->columna_clave)) 
            {
               $where_adj[] = $this->columna_clave."='".$this->estado."'";    
               $temp_sql = sql_agregar_clausulas_where($this->sql,$where_adj);
            }
            else
                $temp_sql = sql_agregar_clausulas_where($this->sql, "");
            
            global $ADODB_FETCH_MODE, $db;
            $ADODB_FETCH_MODE = ADODB_FETCH_NUM;

            $rs = $db[$this->fuente][apex_db_con]->Execute($temp_sql); 
            
            if(!$rs)
            {   
                monitor::evento("bug","EF_POPUP: No se genero el recordset. ". $db[$this->fuente][apex_db_con]->ErrorMsg()." -- SQL: {$this->sql} -- ");
            }
            if($rs->EOF)
            {
                echo ei_mensaje("EF etiquetado '" . $this->etiqueta . "'<br>No se obtuvieron registros: ". $this->sql);
            }
            $this->estado = $rs->fields[0];  
            $this->descripcion_estado = $rs->fields[1];
        }  
        else
        {
            $this->descripcion_estado = $this->estado;
        }     
    }

    
}
//########################################################################################################
//########################################################################################################
?>
