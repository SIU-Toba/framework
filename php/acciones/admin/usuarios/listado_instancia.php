<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >Asignar</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "
SELECT 	u.usuario,
		u.nombre
FROM		apex_usuario u
ORDER BY 2";
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de USUARIOS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql]". $sql ,false,true,true);
	if(!$rs->EOF){
	while(!$rs->EOF)
	{ 
?>
        <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/usuarios/propiedades",array(apex_hilo_qs_zona => $rs->fields['usuario'])) ?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"Modificar USUARIO") ?>
			</a>
  	  </td>
          <td width="20%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["usuario"] ?></td>
          <td width="50%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["nombre"] ?></td></tr>
<?
	$rs->movenext();	
	}
}
?>
</table>
