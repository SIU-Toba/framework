<?
    if($editable = $this->zona->obtener_editable_propagado()){
    //--> Estoy navegando la ZONA con un editable...
        $mt = $this->cargar_objeto("objeto_mt_mds",0);
        if($mt > -1){
            //$this->info_estado();
            //$this->objetos[$mt]->obtener_info_dependencias();
            $this->objetos[$mt]->procesar($editable);
            $etapa = $this->objetos[$mt]->obtener_etapa();
            if( ( $etapa == "PA")||( $etapa == "PM-U") ){
                if( $this->objetos[$mt]->obtener_estado_proceso() == "OK"){
                    //Refresco la barra lateral
                    $this->zona->cargar_editable();//Cargo el editable de la zona
                    $this->zona->refrescar_listado_editable_apex();
                    $this->zona->obtener_html_barra_superior();
                    $this->objetos[$mt]->obtener_html();
                    $this->zona->obtener_html_barra_inferior();
                }else{
                    $this->zona->cargar_editable();//Cargo el editable de la zona
                    $this->zona->obtener_html_barra_superior();
                    $this->objetos[$mt]->obtener_html();
                    $this->zona->obtener_html_barra_inferior();
                }
            }elseif( $etapa == "PM-D" ){
                if( $this->objetos[$mt]->obtener_estado_proceso() == "OK"){
                    //Refresco la barra lateral
                    $this->zona->refrescar_listado_editable_apex();
                    $this->objetos[$mt]->obtener_html();
                }else{
                    $this->zona->cargar_editable();//Cargo el editable de la zona
                    $this->zona->obtener_html_barra_superior();
                    $this->objetos[$mt]->obtener_html();
                    $this->zona->obtener_html_barra_inferior();
                }
            }else{
                $this->zona->cargar_editable();//Cargo el editable de la zona
                $this->zona->obtener_html_barra_superior();
                $this->objetos[$mt]->obtener_html();
                $this->zona->obtener_html_barra_inferior();
            }
        }else{
            echo ei_mensaje("No fue posible cargar la INTERFACE");
        }
    }else{
        $mt = $this->cargar_objeto("objeto_mt_mds",0);
        if($mt > -1){
            //Se paso un grupo de acceso como parametro?
            //$this->objetos[$mt]->info_estado();
            //$this->objetos[$mt]->obtener_info_dependencias();
            if($grupo_acceso = $this->hilo->obtener_parametro("grupo_acceso")){
                $datos_ef["grupo_acceso"]["proyecto"] = $this->hilo->obtener_proyecto();
                $datos_ef["grupo_acceso"]["usuario_grupo_acc"] = $grupo_acceso;
                $this->objetos[$mt]->ut_asignar_ef("detalle_1",$datos_ef);
            }
            $this->objetos[$mt]->procesar();
            //Refresco la barra lateral
            if($this->objetos[$mt]->obtener_etapa()=="PA"){
                if($this->objetos[$mt]->obtener_estado_proceso()=="OK"){
                    //Si el INSERT fue satisfactorio, cargo la ZONA
                    $clave_registro = $this->objetos[$mt]->obtener_clave();
                    if($this->zona->cargar_editable($clave_registro)){
                        $this->zona->refrescar_listado_editable_apex();
                        echo "<script language'javascript'>\n";
                        echo "document.location.href='".$this->vinculador->generar_solicitud($this->hilo->obtener_proyecto(),
                                NULL,null,true)."'\n";
                        echo "</script>\n";
/*

                        $this->zona->obtener_html_barra_superior();
                        $this->objetos[$mt]->obtener_html();
                        $this->zona->obtener_html_barra_inferior();*/
                    }else{
                        echo ei_mensaje("No es posible cargar la zona");
                    }
                }else{
                    $this->objetos[$mt]->obtener_html();
                }
            }else{
                $this->objetos[$mt]->obtener_html();
            }
            //$this->objetos[$mt]->mostrar_memoria();
            //$this->objetos[$mt]->info_definicion();
        }else{
            echo ei_mensaje("No fue posible cargar la INTERFACE");
        }
    }
    //$this->hilo->dump_memoria();
    //$this->objetos[$mt]->info_estado();

//################################################################
?>
