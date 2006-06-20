<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		//--- Es un BUFFER??
		if(!(($this->zona->editable_info['actividad_buffer']==0) && 
			($this->zona->editable_info['actividad_buffer_proyecto']=='admin'))){
				$tipo_actividad = "buffer";
    			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	    		$sql = "SELECT cuerpo FROM apex_buffer 
						WHERE buffer = '".$this->zona->editable_info['actividad_buffer']."' 
						AND proyecto =  '".$this->zona->editable_info['actividad_buffer_proyecto']."';";
		    	$rs = $db["instancia"][apex_db_con]->Execute($sql);
			    if(!$rs){
    				echo ei_mensaje("No se pudo obtener el cuerpo del BUFFER correspondiente al ITEM ".$db["instancia"][apex_db_con]->ErrorMsg(),"error");
	    		}else{
		    		if($rs->EOF){
			    		//Supuestamente esto no pasa...
				    	echo ei_mensaje("EL BUFFER Solicitado NO EXISTE","error");
    				} else {
                        if(trim($rs->fields[0])==""){
                            echo ei_mensaje("SOLICITUD: EL BUFFER solicitado se encuentra VACIO","error");
                        }else{
            				//Ejecuto el codigo PHP de la base
							ei_separador("BUFFER: ". $this->zona->editable_info['actividad_buffer_proyecto'].
											 " - ". $this->zona->editable_info['actividad_buffer']);
							//echo "<br><pre>" . $rs->fields[0] . "</pre><br>";
							highlight_string( "<?\n\n" . $rs->fields[0] . "\n\n?>");
                        }
			    	}
    			}			
        }//--- Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON
		elseif(!(($this->zona->editable_info['actividad_patron']=="especifico") && 
			($this->zona->editable_info['actividad_patron_proyecto']=='admin'))){
				ei_separador("PATRON: ". $this->zona->editable_info['actividad_patron_proyecto'] . " - " .
										$this->zona->editable_info['actividad_patron'] .
										"<br>ARCHIVO: ". $this->zona->editable_info['actividad_patron_archivo'] );
            	$tipo_actividad = "patron";
				$proyecto = $this->zona->editable_info['actividad_patron_proyecto'];
				$archivo = $this->zona->editable_info['actividad_patron_archivo'];
        }//--- Es una ACCION. 
        else{
			ei_separador("ACCION: ". $this->zona->editable_info['actividad_accion']);
            $tipo_actividad = "accion";
			$proyecto = $this->zona->editable_info['proyecto'];
			$archivo = $this->zona->editable_info['actividad_accion'];
        }
		//Imprimo el archivo
		if($tipo_actividad!="buffer"){
			$archivo_real = toba::get_hilo()->obtener_proyecto_path()."/php/".$archivo;
			if(file_exists($archivo_real)){
				highlight_file($archivo_real);
			}else{
				echo ei_mensaje("ATENCION: el archivo <b>'$archivo'</b> no existe.","error");
			}
		}else{

		}

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}

?>
