<script language='javascript'>
	editor='dimension';
</script>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/apex/elementos/dimension_tipo") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	&nbsp;<? echo recurso::imagen_apl("dimension_tipo.gif",true) ?>&nbsp;
	</a>
	</td>
    <td width="98%" class="lista-obj-titulo" >DIMENSIONES</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("dimension.gif",true) ?>
	</a>
	</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	d.proyecto as				dim_proy,
					d.dimension as 				dim_id,
					d.nombre as 				dim_des,
					d.fuente_datos as 			fue_id,
					t.dimension_tipo_perfil	as	perfil,
					(SELECT COUNT(*) FROM apex_objeto_filtro f WHERE f.dimension = d.dimension AND f.dimension_proyecto = d.proyecto) as filtros,
					(SELECT COUNT(*) FROM apex_dimension_perfil_datos f WHERE f.dimension = d.dimension AND f.dimension_proyecto = d.proyecto) as perfiles
			FROM 	apex_dimension d,
					apex_dimension_tipo t
			WHERE	d.dimension_tipo = t.dimension_tipo
            AND     d.proyecto = '".$this->hilo->obtener_proyecto()."'
			ORDER BY 2";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de dimensiones - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td colspan="5" align="center" class="cat-item-categ1"></td>
        </tr>
        <tr> 
          <td width="2%" class='lista-obj-titcol' colspan='2'>e</td>
          <td width="80%" class='lista-obj-titcol' >NOMBRE</td>
          <td width="2%" class='lista-obj-titcol' >F</td>
          <td width="2%" class='lista-obj-titcol' >Pd</td>
          <td width="10%" class='lista-obj-titcol' >FUENTE</td>
        </tr>
<?
	while(!$rs->EOF)
	{ 
?>
        <tr> 
<?	if($rs->fields["perfil"]!="no"){ ?>
          <td width="2%" class='lista-obj-dato5'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("dimension.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="2%" class='lista-obj-dato5'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/probar",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="90%" class='lista-obj-dato4'>&nbsp;<? echo "[<b>" . $rs->fields["dim_id"] ."</b>] - ". trim($rs->fields["dim_des"]) ?></td>
          <td width="2%" class='lista-obj-dato4'>&nbsp;<? echo trim($rs->fields["filtros"]) ?></td>
          <td width="2%" class='lista-obj-dato4'>&nbsp;<? echo trim($rs->fields["perfiles"]) ?></td>
          <td width="2%" class='lista-obj-dato5'><? echo trim($rs->fields["fue_id"]) ?></td>
<? }else {?>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("dimension.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/probar",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="90%" class='lista-obj-dato1'><? echo "[<b>" . $rs->fields["dim_id"] ."</b>] - ". trim($rs->fields["dim_des"]) ?></td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo trim($rs->fields["filtros"]) ?></td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo trim($rs->fields["perfiles"]) ?></td>
          <td width="2%" class='lista-obj-dato2'>&nbsp;<? echo trim($rs->fields["fue_id"]) ?></td>
<? } ?>
        </tr>
<?		$rs->movenext();	
		}
	}
?>
</table>
