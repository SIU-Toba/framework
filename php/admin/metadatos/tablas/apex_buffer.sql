INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '0', 'NO', 'NO', NULL, NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '1', 'Dump del contexto', 'Dump del contexto', 'dump_SESSION();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '4', 'Información general', 'Información general', 'enter();
dump_SESSION(); 
$this->info_definicion(); 
$this->info_estado(); 
$this->hilo->info(); 

dump_conexiones(); 

$this->vinculador->info(); 

//dump_COLOR(); 
//$this->hilo->limpiar_memoria();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '7', 'Prueba', 'Prueba', 'for ($a=0; $a<100; $a++)
{
   for($b=0;$b<$a;$b++) echo \" \";
   echo \"$a <br>\";
}', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '26', 'EDITABLE abm', 'EDITABLE abm', 'if($editable = $this->zona->obtener_editable_propagado()){ 
    //--> Estoy navegando la ZONA con un editable... 
        //$this->info(); 
        //$this->obtener_info_objetos(); 
        $abm = $this->cargar_objeto(\"objeto_abms\",0); 
        if($abm > -1){ 
            $this->objetos[$abm]->procesar($editable); 
            //Si el ABM acaba de procesar una BAJA, no mostrar la ZONA! 
            if($this->objetos[$abm]->obtener_etapa()==\"PM-D\"){ 
                //Como el editable que se cargo ya no existe mas, solo muestro el ABM 
                $this->objetos[$abm]->obtener_html(); 
                //Si la lista de la izquierda concuerda con la del EDITABLE eliminado 
                //tengo que refrescarla para que refleje el estado de la base 
                $this->zona->refrescar_listado_editable_apex(); 
            }else{ 
                $this->zona->cargar_editable();//Cargo el editable de la zona 
                //$this->zona->info(); 
                $this->zona->obtener_html_barra_superior(); 
                $this->objetos[$abm]->obtener_html(); 
                $this->zona->obtener_html_barra_inferior(); 
            } 
        }else{ 
            echo ei_mensaje(\"No fue posible instanciar el ABM (1)\"); 
        } 
    }else{ 
    //--> NO estoy navegando en la ZONA con un editable 
        $abm = $this->cargar_objeto(\"objeto_abms\",0); 
        if($abm > -1){ 
            $this->objetos[$abm]->procesar(); 
            //Si la el ABM acaba de procesar un ALTA, tengo que cargar la ZONA! 
            if($this->objetos[$abm]->obtener_etapa()==\"PA\"){ 
                //Si salio todo OK 
                if($this->objetos[$abm]->obtener_estado_proceso()==\"OK\"){ 
                    //Obtengo el ID del registro actual 
                  $clave_registro = $this->objetos[$abm]->obtener_clave();
                    //ei_arbol($this->objetos[$abm]->obtener_datos()); 
                    if($this->zona->cargar_editable($clave_registro)){ 
                        //$this->zona->info(); 
                        $this->zona->obtener_html_barra_superior(); 
                       $this->objetos[$abm]->obtener_html();      
                        $this->zona->obtener_html_barra_inferior(); 
                        //Si la lista de la izquierda concuerda
                        //tengo que refrescarla 
                        $this->zona->refrescar_listado_editable_apex(); 
                    } 
                }else{ 
                    $this->objetos[$abm]->obtener_html();                     
                } 
            }else{ 
                $this->objetos[$abm]->obtener_html(); 
            } 
        }else{ 
            echo ei_mensaje(\"No fue posible instanciar el ABM (2)\"); 
        } 
    }', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '28', 'ABM - CUADRO', 'ABM simple compuesto por un cuadro y un ABM', '$cuadro = $this->cargar_objeto(\"objeto_cuadro\",0); 
if($cuadro > -1){ 

//dump_conexiones(); 
//echo $this->info_estado(); 
 
    $abms = $this->cargar_objeto(\"objeto_abms\",0); 
    if($abms > -1){ 
        $this->objetos[$abms]->procesar(); 
        $this->objetos[$cuadro]->cargar_datos(); 
        $this->objetos[$abms]->obtener_html(); 
        $this->objetos[$cuadro]->obtener_html(); 
        //$this->objetos[$abms]->info_estado();         
        //$this->vinculador->info(); 
    }else{ 
        echo ei_mensaje(\"No fue posible instanciar el ABM\"); 
    } 
}else{ 
    echo ei_mensaje(\"No fue posible instanciar el cuadroDO\"); 
}', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '29', 'Limpiar memoria', 'Limpia la memoria.', '$this->hilo->limpiar_memoria();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '30', 'PHP info', 'Información del entorno de PHP', 'echo \"<br>\";
phpinfo();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '32', 'diapo', 'ddddd', 'echo \"<br>\";
    echo ei_centrar(recurso::imagen_apl($this->info[\"item_parametro_a\"],true,null,null,\"Elementos basicos\"));', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '33', 'Observar', 'Prueba de observacion', '$this->observar(\'info\',\'Prueba de observacion\');
$this->info_estado();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '34', 'prueba_recepcion_parametro', 'Recepcion de un parametro', 'echo \"Recepcion de un parametro especifico: \". $this->hilo->obtener_parametro(\'uno\');
echo \"<br><br>\";
echo \"Recepcion del array completo <br>\";
ei_arbol( $this->hilo->obtener_parametros(),\"parametros\");', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '36', 'prueba_pasaje_parametros', 'lklkj', '$parametros[\'uno\']=\"Hola\";
$parametros[\'dos\']=\"Chau\";

echo \"<br>\";
if( $vinculo = $this->vinculador->obtener_vinculo_a_item(\'toba\',\'/pruebas/pasar_parametro_get_2\',$parametros,true) ) {
echo ei_centrar($vinculo);
}else{
echo \"no posee permisos\";
}', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '38', 'Probar MT', 'Prueba de MT', '$mt = $this->cargar_objeto(\"objeto_mt\",0); 
    if($mt > -1){ 
        $this->objetos[$mt]->procesar(); 
        $this->objetos[$mt]->obtener_html(); 
    }else{ 
        echo ei_mensaje(\"No fue posible instanciar el MT\"); 
    }', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '39', 'sesion', 'Dump SESION PHP', 'echo addslashes(serialize($_SESSION));', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '41', 'SQL STATE', 'SQL STATE', 'echo \"Existe la funcion que devuelve el SQLSTATE (pg_result_error_field)? \";
if(function_exists(\"pg_result_error_field\"))
{
	echo \"SI.\";
}else{
	echo \"NO.\";
}

phpinfo();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '42', 'Pagina Inicial', 'Pagina Inicial', 'echo \"<pre>\";
echo \"

*** Administracion del proyecto **

--> Tareas pendientes
--> Link al GANTT dle proyecto

*** ACTIVIDAD LOCAL ***

--> Usuarios Logueados en el proyecto (enviar mensajes genericos a los mismos)
--> Mensajes al usuario (Notas a items, objetos, clases, o genericas asociadas al usuario ACTUAL)
--> Mensajes globales

*** PORTAFOLIO ****

--> Items y objetos

*** GENERAL ***

--> Link a la edicion de las preferencias


\";
echo \"</pre>\";', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '43', 'aaaa', 'aaaa', 'echo (5 + 2 - 100)', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba', '49', 'Consola Js', 'Abre una consola JS anexa a la ventana actual.', 'include(toba_dir().\"/www/js/utilidades/consola.htm\");', NULL);
