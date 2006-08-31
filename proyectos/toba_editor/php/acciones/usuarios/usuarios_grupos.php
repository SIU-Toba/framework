<?
	$proyecto = $_GET['proyecto'];
	
	//Inicializacion de la interface
	$formulario = "permisos";
	$prefijo_grupo = "ef_ga";//Prefijo de los COMBOS
	$boton_post = "asignar_permisos";
	$boton_post_nombre = "Guardar";

	include_once("nucleo/lib/interface/form.php");
	include_once("nucleo/componentes/interface/efs/ef.php");

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
		//------------------------------------------------------------------------------

		//-[2]- Realizo la TRANSACCION en la base
		try {
			toba::get_db()->abrir_transaccion();
			//1) Borro los permisos existentes
			$sql = "DELETE FROM apex_usuario_proyecto WHERE proyecto = '$proyecto'\n";
			toba::get_db()->ejecutar($sql);		
			$ok = true;
			if(isset($permiso)){
				if(is_array($permiso)){
					foreach($permiso as $usuario => $grupo ){
						//2) Inserto los permisos ACTUALIZADOS
						$sql = "INSERT INTO apex_usuario_proyecto (usuario, proyecto, usuario_grupo_acc, usuario_perfil_datos)
								VALUES ('$usuario','$proyecto','$grupo','no');\n";
						toba::get_db()->ejecutar($sql);		
					}
				}
			}
			toba::get_db()->cerrar_transaccion();
		} catch( toba_excepcion $e ) {
			toba::get_db()->abortar_transaccion();
			toba::get_cola_mensajes()->agregar("Error modificando permisos: " . $e->getMessage());
		}
 	}
	//************************************************************************
	//*************************  GENERO la INTERFACE  ************************
	//************************************************************************
?>
<br>
<form  enctype='application/x-www-form-urlencoded' name='$formulario' method='POST' action='<? echo toba::get_vinculador()->generar_solicitud(null,null,array('proyecto'=>$proyecto)) ?>'>
<div align='center'>
<table width="400" align='center' class='objeto-base'>
<tr> 
	 <td width="98%" colspan='4' class="lista-obj-titulo" >Usuarios de la INSTANCIA</td>
</tr>
<tr><td colspan="4"  class="cat-item-categ1">
<?	echo toba_form::submit($boton_post,$boton_post_nombre); ?>
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
	$datos = toba::get_db()->consultar($sql);
	if($datos){
	$sql = "SELECT 	usuario_grupo_acc,
					nombre
			FROM 	apex_usuario_grupo_acc
			WHERE	proyecto = '$proyecto'
			ORDER BY nombre;";
	$temp = toba::get_db()->consultar($sql);
	foreach( $temp as $x) {
		$info_usuarios[$x['usuario_grupo_acc']] = $x['nombre'];
	}
	$info_usuarios[apex_ef_no_seteado] = "__  NO  __";
	foreach($datos as $rs)
	{ 
		//- Armo un EF para manejar el GRUPO de acceso del USUARIO
		$nombre_combo = $prefijo_grupo . $rs["usuario"];
		//echo $parametros["sql"]; 
		$grupo_acc_actual = (trim($rs["grupo_acc"])!="") ? $rs["grupo_acc"] : apex_ef_no_seteado;
		$html_ef = toba_form::select($nombre_combo, $grupo_acc_actual, $info_usuarios)
?>
        <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/usuarios/propiedades",array(apex_hilo_qs_zona => $rs['usuario'])) ?>" target="<? echo  apex_frame_centro ?>">
				<? echo toba_recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"Modificar USUARIO") ?>
			</a>
  	  </td>
          <td width="20%" class='lista-obj-dato1'>&nbsp;<? echo $rs["usuario"] ?></td>
          <td width="50%" class='lista-obj-dato1'>&nbsp;<? echo $rs["nombre"] ?></td>
		  <td width="30%" class='lista-obj-dato1'><? echo $html_ef ?></td>
	</tr>
<?
	}
}
?>
<tr><td colspan="4"  class="cat-item-categ1">
<?	echo toba_form::submit($boton_post,$boton_post_nombre); ?>
</td></tr>
</table>
</div>
<br><br>
<?		
	echo toba_form::cerrar();
?>