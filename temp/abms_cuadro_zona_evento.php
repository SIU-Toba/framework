<?
        if($editable = $this->zona->obtener_editable_propagado())
        { 
	        $this->zona->cargar_editable();
            $this->zona->obtener_html_barra_superior(); 
            $aux = $this->zona->obtener_editable_cargado(); 
        
            echo "<br>";                       

        	$lista = $this->cargar_objeto("objeto_cuadro",0);
        	if($lista > -1){
        		$abms = $this->cargar_objeto("objeto_mt_abms",0);
        		if($abms > -1){
                
                    if (! isset($aux[0])) 
                    {
                        echo ei_mensaje("El editable no fue propagado");                                                                            
                        $where = "";
                        $cliente = "";
                    }else
                    {
                        $cliente['cod_evento']= $aux[0];
                        $where = array("cod_evento='".$aux[0]."'");
                    }
                    
        			$this->objetos[$abms]->procesar($cliente,TRUE);
        			if($this->objetos[$abms]->obtener_etapa()=="PM-D")
                    {
                            //Aca habria que reventar el editable de la zona
                    }
                    elseif ($this->objetos[$abms]->obtener_etapa() == "SA") 
                    {
                        $this->objetos[$abms]->memoria["proxima_etapa"] = "SM";
                    }
                    
        			$this->objetos[$lista]->cargar_datos($where);  

                    $this->objetos[$abms]->cargar_estado_ef($cliente);
        			$this->objetos[$lista]->obtener_html();
        			$this->objetos[$abms]->obtener_html();
        		}else{
        			echo ei_mensaje("No fue posible instanciar el ABM");
        		}
        	}else{
        		echo ei_mensaje("No fue posible instanciar el LISTADO");
        	}
       }
       else
       {
/*            echo "<br><table width='100%' class='tabla-0'>\n";
            echo "<tr>\n<td colspan=20 class='barra-separador'>";
            echo "<a href='".$this->vinculador->obtener_vinculo_a_item('siu-quilmes','/abms/eventos/listado', NULL, false)."'>";
            echo recurso::imagen_pro('doc.gif',true,null,null,'Volver al listado');        
            echo "</a></td></tr></table>";
*/
            enter();
    		$abms = $this->cargar_objeto("objeto_mt_abms",0);
            $this->objetos[$abms]->dependencias["formulario"]->info_ut_formulario["ev_mod_limpiar"] = true;
    		$this->objetos[$abms]->procesar();
            
   			if($this->objetos[$abms]->obtener_etapa()=="PA"){
	 			if($this->objetos[$abms]->obtener_estado_proceso()=="OK"){
   					$clave_registro = $this->objetos[$abms]->obtener_clave();
                        $this->zona->cargar_editable($clave_registro);
                        //Aca iria la parte que redirige al cliente...
                }
            }//Esto es por si no se proceso bien el ABM..
   			$this->objetos[$abms]->obtener_html();
        }
?>