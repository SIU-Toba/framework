<script language='javascript'>
	editor='buffer';
</script>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >PHP en la Base de Datos</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/buffers/propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("buffers.gif",true) ?>
	</a>
	</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	proyecto,
					buffer,
					descripcion_corta,
					(SELECT COUNT(*) FROM apex_item x1 WHERE x1.actividad_buffer_proyecto = proyecto AND x1.actividad_buffer = buffer) as items_consumidores
			FROM 	apex_buffer
            WHERE  	(proyecto = '".$this->hilo->obtener_proyecto()."')
			AND	 NOT (( buffer = 0 ) AND ( proyecto = 'toba' ))
			ORDER BY 3";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de buffers - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' colspan='3'>e</td>
          <td width="80%" class='lista-obj-titcol' >Descripcion</td>
          <td width="10%" class='lista-obj-titcol' >cI</td>
        </tr>
<?
while(!$rs->EOF)
{
?>

         <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/buffers/propiedades", array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["buffer"]) ) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("buffers.gif") ?>" alt="Editar el BUFFER" border="0">
			</a>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/buffers/coloreado", array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["buffer"]) ) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("php.gif") ?>" alt="Visualizar PHP" border="0">
			</a>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/buffers/ejecutar", array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["buffer"]) ) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="Ejecutar" border="0">
			</a>
	  	  </td>
          <td width="90%" class='lista-obj-dato1'><? echo trim($rs->fields["descripcion_corta"]) ?></td>
          <td width="2%" class='cat-item-dato3'><? echo $rs->fields["items_consumidores"] ?></td>
        </tr>
<?		$rs->movenext();	
		}
	//}
?>
<?} ?>
</table>
</body>
</html>