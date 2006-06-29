<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		$proyecto = $editable[0];
		
		//Inicializacion de la interface
		$formulario = "permisos";
		$prefijo_grupo = "ga_";//Prefijo de los COMBOS
		$boton_post = "asignar_permisos";
		$boton_post_nombre = "Guardar";

		include_once("nucleo/lib/form.php");
		include_once("nucleo/obsoleto/efs_obsoletos/ef.php");
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	//************************************************************************
	//*************************  Ejecuto la TRANSACCION  *********************
	//************************************************************************

	//SI hay un POST, y estuvo disparado por este formulario
	if( ($_SERVER["REQUEST_METHOD"]=="POST") && 
	($_POST[$boton_post]==$boton_post_nombre) )
	{
		//-[1]- Recupero la INFORMACION de la INTERFACE
		foreach($_POST as $etiqueta => $valor)
		{
			//Busco los combos...
			if((substr($etiqueta,0,strlen($prefijo_grupo)))==$prefijo_grupo){
				//Filtro los que poseen un valor definido...
				if( $valor != apex_ef_no_seteado ){
					$usuario = trim(substr($etiqueta,strlen($prefijo_grupo)));
					//Armo el array de permisos
					$permiso[$usuario] = $valor;
				}
			}
		}
		//ei_arbol($permiso,"PERMISOS");
		//------------------------------------------------------------------------------

		//-[2]- Realizo la TRANSACCION en la base
		
		$db["instancia"][apex_db_con]->Execute("BEGIN TRANSACTION");
		//1) Borro los permisos existentes
		$sql = "DELETE FROM apex_usuario_proyecto WHERE proyecto = '$proyecto'\n";
		if($db["instancia"][apex_db_con]->Execute($sql) === false)
		{
			echo ei_mensaje("Ha ocurrido un error (1) - " .$db["instancia"][apex_db_con]->ErrorMsg());
			$rs = $db["instancia"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
		}
		else{
			$ok = true;
			if(isset($permiso)){
				if(is_array($permiso)){
					foreach($permiso as $usuario => $grupo ){
						//2) Inserto los permisos ACTUALIZADOS
						$sql = "INSERT INTO apex_usuario_proyecto (usuario, proyecto, usuario_grupo_acc, usuario_perfil_datos)
								VALUES ('$usuario','$proyecto','$grupo','no');\n";
						if($db["instancia"][apex_db_con]->Execute($sql) === false)
						{
							echo ei_mensaje("Ha ocurrido un error ELIMINANDO los permisos - " .$db["instancia"][apex_db_con]->ErrorMsg());
							$rs = $db["instancia"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
							$ok = false;
							break;//Salgo del foreach
						}
					}
				}
			}
			//COMMIT
			if($ok){
				echo ei_mensaje("Los permisos han sido actualizados correctamente");
				$rs = $db["instancia"][apex_db_con]->Execute("COMMIT TRANSACTION");
			}
		}
 	}
	//************************************************************************
	//*************************  GENERO la INTERFACE  ************************
	//************************************************************************
		
	echo form::abrir($formulario, $this->vinculador->generar_solicitud(null,null,null,true));

?>
<br>
<div align='center'>
<table width="400" align='center' class='objeto-base'>
<tr> 
	 <td width="98%" colspan='4' class="lista-obj-titulo" >Usuarios de la INSTANCIA</td>
</tr>
<tr><td colspan="4"  class="cat-item-categ1">
<?	echo form::submit($boton_post,$boton_post_nombre); ?>
</td></tr>
	<tr> 
          <td  colspan='2' width="20%" class='lista-obj-titcol'></td>
          <td width="50%" class='lista-obj-titcol'>Nombre</td>
		  <td width="30%" class='lista-obj-titcol'>Grupo Acceso</td>
	</tr>
<? 	
	$sql = "
SELECT 	u.usuario as 			usuario,
		u.nombre as 			nombre,
		p.proyecto as 			proyecto,
		p.usuario_grupo_acc as	grupo_acc
FROM	apex_usuario u
		LEFT OUTER JOIN apex_usuario_proyecto p
			ON p.usuario = u.usuario
			AND p.proyecto = '$proyecto'
ORDER BY 2;";
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de USUARIOS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql]". $sql ,false,true,true);
	if(!$rs->EOF){
	while(!$rs->EOF)
	{ 
		//- Armo un EF para manejar el GRUPO de acceso del USUARIO
		$nombre_combo = $prefijo_grupo . $rs->fields["usuario"];
		$parametros["sql"] = "SELECT 	usuario_grupo_acc,
										nombre
								FROM 	apex_usuario_grupo_acc
								WHERE	proyecto = '$proyecto'
								ORDER BY nombre;";
		$parametros["no_seteado"] = "__  NO  __";
		//echo $parametros["sql"]; 
		$ef = new ef_combo_db(	null, "", $nombre_combo, $nombre_combo,
								"Seleccione el grupo correspondiente al usuario.",
								"","",$parametros);
		//- Establezco el valor actual para el usuario
		$grupo_acc_actual = (trim($rs->fields["grupo_acc"])!="") ? $rs->fields["grupo_acc"] : apex_ef_no_seteado;
		$ef->cargar_estado($grupo_acc_actual);//Que el elemento seteado
		$html_ef = $ef->obtener_input();
?>
        <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/usuarios/propiedades",array(apex_hilo_qs_zona => $rs->fields['usuario'])) ?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"Modificar USUARIO") ?>
			</a>
  	  </td>
          <td width="20%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["usuario"] ?></td>
          <td width="50%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["nombre"] ?></td>
		  <td width="30%" class='lista-obj-dato1'><? echo $html_ef ?></td>
	</tr>
<?
	$rs->movenext();	
	}
}
?>
<tr><td colspan="4"  class="cat-item-categ1">
<?	echo form::submit($boton_post,$boton_post_nombre); ?>
</td></tr>
</table>
</div>
<br><br>
<?		
		echo form::cerrar();
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el PROYECTO a editar","error");
	}
?>