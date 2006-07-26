<?
   if($editable = $this->zona->get_editable()){
      $this->zona->obtener_html_barra_superior();
      
      $parametros = parsear_propiedades($this->zona->editable_info['inicializacion']);
      //ei_arbol($parametros);
/*
---------------------------------------------------------------------------------------------------
--: proyecto: ".editor::get_proyecto_cargado()."
--: dump: proyecto
--: dump_order_by: {$parametros['tab_ref_clave']}, usuario_perfil_datos
--: zona: perfiles
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
*/
      $sql = "CREATE TABLE {$parametros['tab_restric']}
(
   {$parametros['tab_ref_clave']} __TIPO__     NOT NULL, 
   usuario_perfil_datos_proyecto varchar(15)    NOT NULL,
   usuario_perfil_datos          varchar(20)    NOT NULL,
   PRIMARY KEY ({$parametros['tab_ref_clave']},usuario_perfil_datos_proyecto,usuario_perfil_datos),
   FOREIGN KEY (usuario_perfil_datos_proyecto,usuario_perfil_datos) REFERENCES apex_usuario_perfil_datos (proyecto,usuario_perfil_datos) ,
   FOREIGN KEY ({$parametros['tab_ref_clave']}) REFERENCES {$parametros['tab_ref']} ({$parametros['tab_ref_clave']}) 
);
--#################################################################################################
";
echo "<pre>$sql</pre>";
      
      
      $this->zona->obtener_html_barra_inferior();
   }else{
      echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
   }
?>