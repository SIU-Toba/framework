<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >FUENTES de DATOS</td>
 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/datos/fuente")?>" class="list-obj" target="<? echo  apex_frame_centro ?>">
	<? echo recurso::imagen_apl("fuente_nueva.gif",true) ?>
	</a>
</td>
</tr>
</table>
<script language='javascript'>
	editor='datos';
</script>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "	
			SELECT 	m.fuente_datos_motor as 		motor,
					f.proyecto as					fuente_proyecto,
					f.fuente_datos as				fuente,
					f.base as						base,
					f.proyecto as					proyecto,
					f.host as						host,
					f.link_instancia as				link_instancia
			FROM apex_fuente_datos_motor m,
			apex_fuente_datos f
			WHERE m.fuente_datos_motor = f.fuente_datos_motor
            AND     f.proyecto = '".$this->hilo->obtener_proyecto()."'			
			ORDER BY f.fuente_datos;";
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de sesiones - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql]". $sql ,false,true,true);
	if(!$rs->EOF){
?>
<table width="100%" class='lista-obj'>
        <tr> 
          <td class='lista-obj-titcol' colspan='2'>&nbsp;</td>
          <td  class='lista-obj-titcol' >fuente</td>
          <td  class='lista-obj-titcol' >base</td>
          <td  class='lista-obj-titcol' >motor</td>
          <td  class='lista-obj-titcol' >proyecto</td>
          <td  class='lista-obj-titcol' >-I-</td>
        </tr>
<?
	while(!$rs->EOF)
	{ 
?>
        <tr> 
          <td  class='cat-item-botones2'>
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/datos/fuente",array( apex_hilo_qs_zona => $rs->fields["fuente_proyecto"] .apex_qs_separador. $rs->fields["fuente"]))?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("fuente.gif",true,null,null,"host/dsn: " . $rs->fields["host"]) ?>
			</a>
		  </td>
          <td  class='cat-item-botones2'>
		<a href='<? echo  $this->vinculador->generar_solicitud("toba","/admin/datos/fuente_sql",array( apex_hilo_qs_zona => $rs->fields["fuente_proyecto"] .apex_qs_separador. $rs->fields["fuente"])) ?>'  class="cat-item" target="<? echo  apex_frame_centro ?>">
	        <? echo recurso::imagen_apl("sql.gif",true)?> 
			</a>
		  </td>
          <td  class='lista-obj-dato1' width="5%"><? echo $rs->fields["fuente"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["base"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["motor"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["proyecto"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["link_instancia"] ?></td>
        </tr>
<?
		$rs->movenext();	
	}
?>
</table>
<?
}
?>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >TABLAS</td>
</tr>
</table>
</body>
</html>