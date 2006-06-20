<script language='javascript'>
	editor='objeto';
</script>
<table width="100%"  class='listado-base'>
<tr> 
<?
		include_once("nucleo/browser/interface/ef.php");
		$parametros["no_seteado"] = "Todas las CLASES";
		$parametros["sql"] = "SELECT DISTINCT clase, clase from apex_clase WHERE proyecto = '".$this->hilo->obtener_proyecto()."' or proyecto='toba';";	
		$tipo_clase =& new ef_combo_db("clase","",apex_sesion_post_proyecto,apex_sesion_post_proyecto,"Seleccione el tipo de objeto.","","",$parametros);

		//Si se eligio un tipo de clase solo muestra ese tipo
		if(acceso_post()){
			$tipo_clase->cargar_estado();
			$temp = $tipo_clase->obtener_estado();
			//Verifica que la opcion elegida no sea 'Todos'
			if($temp!='NULL'){
				$this->hilo->persistir_dato_global("tipo_clase",$temp);
				$where_tipo_clase = " AND c.clase = '". $temp ."'";
			}else{
				$this->hilo->eliminar_dato_global("tipo_clase");
				$where_tipo_clase = "";
			}
		}
		else{
			if ($temp = $this->hilo->recuperar_dato_global("tipo_clase")){
				$tipo_clase->cargar_estado($temp);
				$where_tipo_clase = " AND c.clase = '". $temp ."'";	
			}
			else{
				$where_tipo_clase = "";
			}	
		}
		
		//Cuantos objetos hay?
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "SELECT 	COUNT(*) as total
				FROM 	apex_objeto c
	            WHERE   proyecto = '".$this->hilo->obtener_proyecto()."' $where_tipo_clase;";

		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		$total = "NO DEFINIDO";
		if(($rs)&&(!$rs->EOF)){
			$total = $rs->fields["total"];
		}
	
	echo "<td class='lista-obj-titulo'>&nbsp;";
	echo recurso::imagen_apl('clases.gif',true,null,null,"Filtrar OBJETOS por CLASE");
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo form::abrir("tipo_objeto",$this->vinculador->generar_solicitud());
	echo $tipo_clase->obtener_input();
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo form::image('filtrar',recurso::imagen_apl('cambiar_proyecto.gif',false));
	echo "</td>";
	echo form::cerrar();
?>	
	<td class="lista-obj-titulo"  width="100%">[ <? echo $total ?> ]</td>
 	<td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/objetos/propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("objetos/objeto_nuevo.gif",true,null,null,"Crear un OBJETO") ?>
		</a>
	</td>
</tr>
</table>
<?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	p.proyecto  	    		as pro_id,
					p.descripcion   	    	as pro_des,
					p.orden 		    	    as pro_orden,
					f.fuente_datos			   	as fue_id,
					f.descripcion 		        as fue_des,
					c.clase 			        as cla_id,
					c.clase_tipo				as cla_tipo,
					t.descripcion				as cla_desc,
					t.orden						as cla_orden,
					c.icono 			        as cla_icono,
					c.editor_item   	        as cla_editor,
					c.editor_proyecto			as cla_editor_proyecto,
					c.instanciador_item	        as cla_instanciador,
					c.instanciador_proyecto		as cla_instanciador_proyecto,
					c.descripcion        		as cla_des,
					o.objeto 			        as obj_id,
					o.nombre 			        as obj_nombre,
					date(o.creacion) 	        as obj_fecha,
					o.subclase					as obj_subclase,
					(SELECT COUNT(*) FROM apex_item_objeto x1 WHERE x1.objeto = o.objeto AND x1.proyecto = o.proyecto) as obj_ri,
					(SELECT COUNT(*) FROM apex_vinculo x2 WHERE x2.origen_objeto_proyecto = o.proyecto AND x2.origen_objeto = o.objeto) as obj_vinc_s,
					(SELECT COUNT(*) FROM apex_vinculo x3 WHERE x3.destino_objeto_proyecto = o.proyecto AND x3.destino_objeto = o.objeto) as obj_vinc_e
			FROM 	apex_objeto o,
					apex_proyecto p,
					apex_clase c,
					apex_clase_tipo t,
					apex_fuente_datos f
			WHERE	c.clase_tipo = t.clase_tipo
			AND		o.fuente_datos = f.fuente_datos 
			AND 	f.proyecto = p.proyecto
			AND		o.proyecto = p.proyecto
			AND		o.clase = c.clase
			AND		o.clase <> 'objeto'
			AND 	p.proyecto = '" . $this->hilo->obtener_proyecto() . "'
            $where_tipo_clase
            ORDER BY cla_orden, obj_nombre;";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Libreria de OBJETOS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){

	echo "<table width='100%' class='listado-base'>";
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' >c</td>
          <td width="80%" class='lista-obj-titcol' >NOMBRE</td>
<?/*
          <td width="10%" class='lista-obj-titcol' >FUENTE</td>
*/?>
           <td width="2%" class='lista-obj-titcol' >Ci</td>
          <td width="2%" class='lista-obj-titcol' >Vs</td>
          <td width="2%" class='lista-obj-titcol' >Ve</td>
          <td colspan="3" width="5%" class='lista-obj-titcol'>e</td>
        </tr>

<?
	while(!$rs->EOF)
	{ 
		$tipo = $rs->fields["cla_tipo"];
?>
        <tr> 
          <td height='22' width='99%' colspan='8' class='cat-item-dato4'>&nbsp;<b><?  echo $rs->fields["cla_desc"] ?></b></td>
        </tr>
<?
	while( (!$rs->EOF) && ($rs->fields["cla_tipo"] == $tipo ) )
	{
?>
        <tr height='10'> 
<? if (isset($rs->fields["obj_subclase"]))
{
	//OBJETOS de Subclases
	$clase = "b";
}else{
	//OBJETOS de Comunes
	$clase = "";
}
?>
          <td width="1%" class='lista-obj-botones<? echo $clase ?>'>
<? echo recurso::imagen_apl($rs->fields["cla_icono"],true,null,null,trim( "CLASE: " . $rs->fields["cla_id"])."  ID: ". $rs->fields['pro_id'] . "-" . $rs->fields["obj_id"]) ?>
		  </td>
          <td width="90%" class='lista-obj-dato1<? echo $clase ?>'><? echo trim($rs->fields["obj_nombre"]) ?></td>
<?/*
          <td width="2%" class='lista-obj-dato2<? echo $clase ?>'>&nbsp;<? echo trim($rs->fields["fue_id"]) ?>&nbsp;</td>
*/?>
           <td width="2%" class='lista-obj-dato3<? echo $clase ?>'><? echo trim($rs->fields["obj_ri"]) ?></td>
          <td width="2%" class='lista-obj-dato3<? echo $clase ?>'><? echo trim($rs->fields["obj_vinc_s"]) ?></td>
          <td width="2%" class='lista-obj-dato3<? echo $clase ?>'><? echo trim($rs->fields["obj_vinc_e"]) ?></td>
          <td width="1%" class='lista-obj-botones<? echo $clase ?>'>
			<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/objetos/propiedades",array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"] ))?>" target="<? echo  apex_frame_centro ?>" class='basico'>
				<? echo recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO") ?>
			</a>
		  </td>
          <td width="1%" class='lista-obj-botones<? echo $clase ?>'>
<? if (isset($rs->fields["cla_editor"])){?>
			<a href="<? echo $this->vinculador->generar_solicitud($rs->fields["cla_editor_proyecto"],$rs->fields["cla_editor"],array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"]))?>" target="<? echo  apex_frame_centro ?>" class='basico'>
				<? echo recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO") ?>
			</a>
<?}?>
		  </td>
          <td width="1%" class='lista-obj-botones<? echo $clase ?>'>
<? if (isset($rs->fields["cla_instanciador"])){?>
			<a href="<? echo $this->vinculador->generar_solicitud($rs->fields["cla_instanciador_proyecto"],$rs->fields["cla_instanciador"],array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"]))?>" target="<? echo  apex_frame_centro ?>" class='basico'>
				<? echo recurso::imagen_apl("objetos/instanciar.gif",true,null,null,"INSTANCIAR el OBJETO") ?>
			</a>
<?}?>
		  </td>
        </tr>
<?		$rs->movenext();	
	}
}
	echo "</table>";
} 
?>