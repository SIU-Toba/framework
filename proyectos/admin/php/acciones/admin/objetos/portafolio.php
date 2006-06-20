<script language='javascript'>
	editor='objeto';
</script>
<table width="100%"  class='listado-base'>
<tr>
<?
$maximo = 10;//Maximo nivel de anidacion...

	$cronometro->marcar('basura');	

	include_once("nucleo/browser/interface/ef.php");

	//Cuantos items hay?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_et_objeto
            WHERE   objeto_proyecto = '".$this->hilo->obtener_proyecto()."'
			AND		usuario ='".$this->hilo->obtener_usuario()."'";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}

	$cronometro->marcar('Consulto los objetos');
    $where_tipo_clase = "";

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
					(SELECT COUNT(*) FROM apex_vinculo x3 WHERE x3.destino_objeto_proyecto = o.proyecto AND x3.destino_objeto = o.objeto) as obj_vinc_e,
					eobj.usuario					as checkout
			FROM 	apex_objeto o,
					apex_proyecto p,
					apex_clase c,
					apex_clase_tipo t,
					apex_fuente_datos f,
                    apex_et_objeto eo LEFT OUTER JOIN apex_et_objeto eobj ON (eo.objeto=eobj.objeto AND eo.objeto_proyecto=eobj.objeto_proyecto AND eo.usuario <>  eobj.usuario)
			WHERE	c.clase_tipo = t.clase_tipo
			AND		o.fuente_datos = f.fuente_datos 
			AND 	f.proyecto = p.proyecto
			AND		o.proyecto = p.proyecto
			AND		o.clase = c.clase
			AND		o.clase <> 'objeto'
			AND		eo.objeto = o.objeto
			AND		o.proyecto = eo.objeto_proyecto
			AND 	p.proyecto = '" . $this->hilo->obtener_proyecto() . "'
			AND		eo.usuario = '" . $this->hilo->obtener_usuario() . "'
            $where_tipo_clase
            ORDER BY cla_orden, obj_nombre;";
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Libreria de OBJETOS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);

?>	
	<td class="lista-obj-titulo"  width="100%">[ <? echo $total ?> ]</td>
	<td width="2%"  class='lista-obj-titulo'>
	<? echo $this->vinculador->obtener_vinculo_a_item('toba','/trabajo/objetos',array( apex_hilo_qs_zona => 'toba' .apex_qs_separador. $rs->fields["obj_id"]),true) ?>
	</td> 	
</tr>
</table>
<?
	if(!$rs->EOF){
	echo "<table width='100%' class='listado-base'>";
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' >c</td>
          <td width="80%" class='lista-obj-titcol' >NOMBRE</td>
          <td class='lista-obj-titcol'></td>
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
          <td height='22' width='99%' colspan='9' class='cat-item-dato4'>&nbsp;<b><?  echo $rs->fields["cla_desc"] ?></b></td>
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
          <td width="90%" class='lista-obj-dato1<? echo $clase ?>'><? echo trim($rs->fields["obj_nombre"]);?>
		  <td class='lista-obj-dato3<? echo $clase ?>'>
		 <?if ($rs->fields["checkout"] != ""){ ?>
		 <a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/objetos/checkouts",array( apex_hilo_qs_zona => $rs->fields['pro_id'] .apex_qs_separador. $rs->fields["obj_id"])) ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
		 <?		echo recurso::imagen_apl("contramano.gif",true,null,null,"OBJETO utilizado por otro usuario");
		  }
		  ?></a>
		  </td>
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