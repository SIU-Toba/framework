<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >Administracion del PROYECTO</td>
</tr>
</table>
<script language='javascript'>
	editor='apex';
</script>
<table width="100%" class='lista-obj'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	proyecto, item, nombre, descripcion, imagen
			FROM 	apex_item
			WHERE padre = '/admin/proyectos'
			AND menu = 1
			ORDER BY orden";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de CLASES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol'></td>
          <td width="80%" class='lista-obj-titcol' ></td>
        </tr>
<?
	$zona = array( apex_hilo_qs_zona => $this->hilo->obtener_proyecto());
while(!$rs->EOF)
{
?>

         <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud($rs->fields["proyecto"],$rs->fields["item"],$zona) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl($rs->fields["imagen"]) ?>" alt="<? echo trim($rs->fields["descripcion"])?>" border="0">
			</a>
	  	  </td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["nombre"] ?></td>
        </tr>
<?		$rs->movenext();	
		}
	}

	if($this->hilo->obtener_proyecto()=="toba")
	{
?>
</table>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >Otros PROYECTOS</td>
</tr>
</table>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "	
			SELECT 	*
			FROM apex_proyecto
			WHERE proyecto <> 'toba'";
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de sesiones - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql]". $sql ,false,true,true);
	if(!$rs->EOF){
?>
<table width="100%" class='lista-obj'>
        <tr> 
          <td  colspan='2' class='lista-obj-titcol' width="1" ><? echo gif_nulo(1,1) ?></td>
          <td  class='lista-obj-titcol' >proyecto</td>
          <td  class='lista-obj-titcol' >descripcion</td>
        </tr>
<?
	while(!$rs->EOF)
	{ 
?>
        <tr> 
          <td  class='lista-obj-dato2'  width="1">
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/proyectos/propiedades",array(apex_hilo_qs_zona => $rs->fields['proyecto'])) ?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("proyecto.gif",true,null,null,"Editar proyecto") ?>
			</a>
		  </td>
          <td  class='lista-obj-dato2'  width="1">
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/proyectos/usuarios",array(apex_hilo_qs_zona => $rs->fields['proyecto'])) ?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"Ver Perfiles") ?>
			</a>
		  </td>
          <td  class='lista-obj-dato1'  width="50%"><? echo $rs->fields["proyecto"] ?></td>
          <td  class='lista-obj-dato1'  width="50%"><? echo $rs->fields["descripcion_corta"] ?></td>
        </tr>
<?
		$rs->movenext();	
	}
?>
</table>
<?
} }
 ?>
</body>
</html>
