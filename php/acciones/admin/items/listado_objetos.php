<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo">OBJETOS x ITEM</td>
</tr>
</table>
<table width="100%" class='cat-item'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	p.proyecto 					as pro_id,
					p.descripcion		 		as pro_des,
					p.orden		 				as pro_orden,
					i.item				 		as item_id,
					i.menu						as item_menu,
					i.nombre			 		as item_nombre,
					o.objeto 					as obj_id,
					o.nombre 					as obj_nombre,
                    io.orden        		    as obj_orden,
					c.clase 					as cla_id,
					c.icono 					as cla_icono,
					c.editor_proyecto 			as cla_editor_proyecto,
					c.editor_item 				as cla_editor,
					c.instanciador_proyecto		as cla_instanciador_proyecto,
					c.instanciador_item			as cla_instanciador,
					c.descripcion 				as cla_des,
					f.fuente_datos 				as fue_id,
					f.descripcion 				as fue_des
			FROM 	apex_proyecto p,
					apex_item i,
					apex_item_objeto io,
					apex_objeto o,
					apex_clase c,
					apex_fuente_datos f
			WHERE	p.proyecto = i.proyecto
			AND		i.item = io.item
			AND		io.objeto = o.objeto
			AND		o.clase = c.clase
			AND		o.fuente_datos = f.fuente_datos 
            AND     i.proyecto = '".$this->hilo->obtener_proyecto()."'
			ORDER BY 3,4,9";
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Catogo de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
	while(!$rs->EOF)
	{ 
		//********  Propiedades del ITEM  ***********
?>
        <tr> 
          <td  class='cat-arbol-item'  width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/propiedades",array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["item_id"]))?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<img src='<? echo recurso::imagen_apl("items/item.gif") ?>' border='0'></a>
		  </td>
          <td width="2%" class='cat-item-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud($rs->fields['pro_id'], $rs->fields["item_id"]) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" border='0'>
			</a>
		  </td>
          <td  class='cat-item-botones'  width='2%'>
<? if($rs->fields["item_menu"]){?>
			<img src='<? echo recurso::imagen_apl("items/menu.gif") ?>' border='0'>
<? }else{ echo gif_nulo(); } ?>
		  </td>
          <td  class='cat-item-dato2' colspan='4'><? echo $rs->fields["item_id"]?></td>
          <td  class='cat-item-botones'><img src='<? echo recurso::imagen_apl("nota.gif") ?>' border='0'></td>
        </tr>
<?
			$item = $rs->fields["item_id"];
			while((!$rs->EOF)&&($rs->fields["item_id"]==$item ))
			{ 
			//********  Propiedades del OBJETO  ********

?>
        <tr> 
          <td width="2%" ><? echo gif_nulo(1,1) ?></td>
          <td width="2%" class='lista-obj-dato1'>
				<img src="<? echo recurso::imagen_apl($rs->fields["cla_icono"]) ?>" alt="<? echo trim($rs->fields["cla_des"])?>" border="0">
		  </td>
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
          <td width="2%" class='lista-obj-botones'>
			<img src="<? echo recurso::imagen_apl("nota.gif") ?>">
		  </td>
        </tr> 
<?
				$rs->movenext();	
			}
	}
?>
</table>
<? }
?>