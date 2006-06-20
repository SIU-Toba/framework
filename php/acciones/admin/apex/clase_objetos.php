<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	p.proyecto  	    		as pro_id,
					p.descripcion_corta	    	as pro_des,
					p.orden 		    	    as pro_orden,
					f.fuente_datos 	        	as fue_id,
					f.descripcion 		        as fue_des,
					c.clase 			        as cla_id,
					c.icono 			        as cla_icono,
					c.editor_item   	        as cla_editor,
					c.editor_proyecto			as cla_editor_proyecto,
					c.instanciador_item	        as cla_instanciador,
					c.instanciador_proyecto		as cla_instanciador_proyecto,
					c.descripcion        		as cla_des,
					o.objeto 			        as obj_id,
					o.nombre 			        as obj_nombre,
					date(o.creacion) 	        as obj_fecha,
					(SELECT COUNT(*) FROM apex_item_objeto x1 WHERE x1.objeto = o.objeto AND x1.proyecto = o.proyecto) as obj_ri,
					(SELECT COUNT(*) FROM apex_vinculo x2 WHERE x2.origen_objeto_proyecto = o.proyecto AND x2.origen_objeto = o.objeto) as obj_vinc_s,
					(SELECT COUNT(*) FROM apex_vinculo x3 WHERE x3.destino_objeto_proyecto = o.proyecto AND x3.destino_objeto = o.objeto) as obj_vinc_e
			FROM 	apex_objeto o,
					apex_proyecto p,
					apex_clase c,
					apex_fuente_datos f
			WHERE	o.fuente_datos = f.fuente_datos 
			AND		o.proyecto = p.proyecto
			AND		o.clase = c.clase
			AND		c.clase = '".$this->zona->editable_info['clase']."'
			AND		c.proyecto = '".$this->zona->editable_info['proyecto']."'
			AND		o.proyecto = '".$this->hilo->obtener_proyecto()."'
			ORDER BY 3, 1, 14;";

	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Libreria de OBJETOS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
	echo "<br><table width='600' align='center' class='listado-base'>";
	while(!$rs->EOF)
	{ 
		$proyecto = $rs->fields["pro_id"];
?>
        <tr> 
          <td colspan="8" align="center" class="cat-item-categ1"><? echo $rs->fields["pro_des"] ?></td>
        </tr>
        <tr> 
          <td colspan="3" width="5%" class='lista-obj-titcol'>e</td>
          <td width="80%" class='lista-obj-titcol' >NOMBRE</td>
          <td width="10%" class='lista-obj-titcol' >FUENTE</td>
          <td width="2%" class='lista-obj-titcol' >Ri</td>
          <td width="2%" class='lista-obj-titcol' >Ro</td>
          <td width="2%" class='lista-obj-titcol' >O</td>
        </tr>
<?
		while((!$rs->EOF)&&($rs->fields["pro_id"]==$proyecto))
		{ 
?>
        <tr> 
          <td width="2%" class='lista-obj-botones'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/objetos/propiedades",array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"] ))?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO") ?>
			</a>
		  </td>
          <td width="2%" class='lista-obj-botones'>
<? if ($rs->fields["cla_id"]!="objeto"){?>
			<a href="<? echo $this->vinculador->generar_solicitud($rs->fields["cla_editor_proyecto"],$rs->fields["cla_editor"],array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"]))?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO") ?>
			</a>
<?}?>
		  </td>
          <td width="2%" class='lista-obj-botones'>
<? if ($rs->fields["cla_id"]!="objeto"){?>
			<a href="<? echo $this->vinculador->generar_solicitud($rs->fields["cla_instanciador_proyecto"],$rs->fields["cla_instanciador"],array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"]))?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("objetos/instanciar.gif",true,null,null,"INSTANCIAR el OBJETO") ?>
			</a>
<?}?>
		  </td>
          <td width="90%" class='lista-obj-dato1'><? echo trim($rs->fields["obj_nombre"]) ?></td>
          <td width="2%" class='lista-obj-dato2'><? echo trim($rs->fields["fue_id"]) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($rs->fields["obj_ri"]) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($rs->fields["obj_vinc_s"]) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($rs->fields["obj_vinc_e"]) ?></td>
        </tr>
<?		$rs->movenext();	
		}
	}
	echo "</table><br>";
}

	$this->zona->obtener_html_barra_inferior();
}else{
	echo ei_mensaje("No se explicito la CLASE a editar","error");
}
