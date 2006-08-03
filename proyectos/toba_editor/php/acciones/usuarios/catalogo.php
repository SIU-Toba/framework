<?
    if ($modo = toba::get_hilo()->obtener_parametro("usu_modo")) {
        toba::get_hilo()->persistir_dato_global("usu_modo",$modo);
    }else{
        if(!($modo = toba::get_hilo()->recuperar_dato_global("usu_modo"))){
            $modo = "grupo_acceso";
        }
    }

    if($modo=="grupo_acceso"){

        //Listado de GRUPO de ACCESO
        $titulo = "USUARIOS por GRUPO de ACCESO";
        $editor = "/admin/usuarios/grupo";
        $editor_img = "grupo";
        $editor_tip = "Grupo de Acceso";
        $editor2 = "3278";
        $editor2_img = "permisos";
        $editor2_tip = "Permisos globales del Grupo de Acceso";
        //Cambio de MODO
        $link="perfil_datos";
        $link_img="perfil";
        $link_tip="Ver listado de PERFILES de DATOS";
        $sql2 = "
SELECT      ga.proyecto as                      grupo_acceso_proyecto,
            ga.usuario_grupo_acc as             grupo_acceso,
            ga.nombre as                        corte_descripcion,
            ga.nivel_acceso as                  nivel_acceso,
            up.usuario as                       usuario,
            u.nombre as                         usuario_nombre,
            pd.usuario_perfil_datos as          perfil_datos,
            pd.nombre as                        perfil_datos_nombre
FROM        apex_usuario_proyecto up
            INNER JOIN apex_usuario u
                ON up.usuario = u.usuario
            INNER JOIN apex_usuario_perfil_datos pd
                ON  up.proyecto = pd.proyecto
                AND up.usuario_perfil_datos = pd.usuario_perfil_datos
            RIGHT OUTER JOIN apex_usuario_grupo_acc ga
                ON  ga.proyecto = up.proyecto
                AND     ga.usuario_grupo_acc = up.usuario_grupo_acc
WHERE       ga.proyecto = '".editor::get_proyecto_cargado()."'
--AND         ga.nivel_acceso >= ".toba::get_hilo()->obtener_usuario_nivel_acceso()."
ORDER BY    3,6;";

    }else{
        //Listado de PERFIL de DATOS
        $titulo = "USUARIOS por PERFIL de DATOS";
        $editor = "/admin/usuarios/perfil";
        $editor_img = "perfil";
        $editor_tip = "Perfil de Dato";
        //Cambio de MODO
        $link="grupo_acceso";
        $link_img="grupo";
        $link_tip="Ver listado de GRUPOS de ACCESO";
        $sql2 = "SELECT  pd.proyecto as              perfil_datos_proyecto,
                        pd.usuario_perfil_datos as  perfil_datos,
                        pd.nombre as                corte_descripcion,
                        u.usuario as                usuario,
                        u.nombre as                 usuario_nombre
                FROM    apex_usuario_proyecto up
                        INNER JOIN apex_usuario u
                            ON u.usuario = up.usuario
                        RIGHT OUTER JOIN apex_usuario_perfil_datos pd
                            ON up.proyecto = pd.proyecto
                            AND up.usuario_perfil_datos = pd.usuario_perfil_datos
                WHERE   pd.proyecto = '".editor::get_proyecto_cargado()."'
                ORDER BY    1,3,5;";
    }
?>
<table width="100%"  class='cat-item'>

<tr>
     <td colspan='3'>
         <table class='cat-item'  width="100%">
         <tr><td width="3%">
    <td width="92%">
    </td>

   <td width="2%">
<?
    $param['tipo'] = "normal";
    $param['texto'] = "Editar los Permisos Globales del PROYECTO";
    $param['imagen_recurso_origen'] = "apex";
    $param['imagen'] = "usuarios/permisos.gif";
    $param['frame'] = "frame_centro";
    echo toba::get_vinculador()->generar_solicitud(editor::get_id(),'3276',array(),false,false,$param,true, 'central') ;
?>
    </td>
    </tr>
    </table>
     </td>
</tr>
<table>
<?
//--------------------------------------------------------------------------------------
//--------------------------<  PROPIEDADES de OTROS proyectos  >------------------------
//--------------------------------------------------------------------------------------

	if(editor::get_proyecto_cargado()==editor::get_id())
	{
?>
</table>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >Edicion GENERAL de Grupos de Acceso de la INSTANCIA</td>
</tr>
</table>
<? 	
	$sql = "SELECT 	*
			FROM apex_proyecto
			WHERE proyecto <> 'toba'";
	//dump_sql($sql);
	$rs = toba::get_db()->consultar($sql);
	if( $rs ) {
?>
<table width="100%" class='lista-obj'>
<?
	foreach( $rs as $registro ) {
?>
        <tr> 
		  </td>
          <td  class='lista-obj-dato2'  width="1">
		 	<a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/proyectos/usuarios",array('proyecto' => $registro['proyecto'])) ?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"Ver Perfiles") ?>
			</a>
		  </td>
          <td  class='lista-obj-dato1'  width="50%"><? echo $registro["proyecto"] ?></td>
          <td  class='lista-obj-dato1'  width="50%"><? echo $registro["descripcion_corta"] ?></td>
        </tr>
<?
	}
?>
</table>
<?
 } }
?>
<?
//-------------------------------------------------------------------------
//	Vista general
//-------------------------------------------------------------------------
?>
<script language='javascript'>
    editor='usuario';
</script>
<table width="100%"  class='listado-base'>
<tr>
     <td width="98%" class="lista-obj-titulo" ><? echo $titulo ?></td>
     <td width="2%"  class='lista-obj-titulo'>
    <a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),$editor, null, false, false, null, true, 'central') ?>"
    class="list-obj" target="<? echo  apex_frame_centro ?>">
    <? echo recurso::imagen_apl("usuarios/{$editor_img}_nuevo.gif",true,null,null,"Crear $editor_tip") ?>
    </a>
    </td>
</tr>
</table>
<table width="100%" class='lista-obj'>
    <tr>
      <td class='lista-obj-titcol' colspan='2'>ID</td>
      <td class='lista-obj-titcol' >NOMBRE</td>
    </tr>
<?
	$rs = toba::get_db()->consultar($sql2);
    if($rs) {
	$grupo_anterior = null;
	foreach( $rs as $registro ) 
	{
		if( $grupo_anterior != $registro[$modo] ) {
?>
    <tr>
     <td colspan='3'>
        <table class='tabla-0'>
        <tr>
          <td width="2%" class='cat-item-categ1'>
            <a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),$editor,array( apex_hilo_qs_zona => $registro[$modo.'_proyecto'] .apex_qs_separador. $registro[$modo]), false, false, null, true, 'central') ?>" target="<? echo  apex_frame_centro ?>">
                <? echo recurso::imagen_apl("usuarios/$editor_img.gif",true,null,null,"Modificar $editor_tip") ?>
            </a>
<? if (isset($editor2)){ ?>
      </td>
          <td width="2%" class='cat-item-categ1'>
            <a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),$editor2,array( apex_hilo_qs_zona => $registro[$modo.'_proyecto'] .apex_qs_separador. $registro[$modo]), false, false, null, true, 'central') ?>" target="<? echo  apex_frame_centro ?>">
                <? echo recurso::imagen_apl("usuarios/$editor2_img.gif",true,null,null,"Modificar $editor2_tip") ?>
            </a>
      </td>
<? } ?>
          <td align="center" class="cat-item-categ1"><? echo $registro["corte_descripcion"] ?></td>
         <td width="2%"  class='cat-item-categ1'>
            <a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/usuarios/propiedades",array($modo=>$registro[$modo]),false,false,null,true,'central') ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
            <? echo recurso::imagen_apl("usuarios/usuario_nuevo.gif",true,null,null,"Crear Usuario") ?>
            </a>
        </td>
        </tr>
        </table>
    </td></tr>
<?
	}

         if(trim($registro["usuario"])!=""){
?>
        <tr>
          <td width="2%" class='lista-obj-botones'>
            <a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/usuarios/propiedades",array(apex_hilo_qs_zona => $registro["usuario"]),false,false,null,true,'central') ?>" target="<? echo  apex_frame_centro ?>">
                <img src="<? echo recurso::imagen_apl("usuarios/usuario.gif") ?>" alt="Modificar USUARIO" border="0">
            </a>
      </td>
<?
    if( $registro["usuario"] == toba::get_hilo()->obtener_usuario() ){
?>
          <td width="30%" class='lista-obj-dato4'>&nbsp;<b><? echo $registro["usuario"] ?></b></td>
          <td width="70%" class='lista-obj-dato4'>&nbsp;<b><? echo $registro["usuario_nombre"] ?></b></td>
<?
    }else{
?>
          <td width="30%" class='lista-obj-dato1'>&nbsp;<? echo $registro["usuario"] ?></td>
          <td width="70%" class='lista-obj-dato1'>&nbsp;<? echo $registro["usuario_nombre"] ?></td>
<?
    }
?>
        </tr>
<?
            }
        $grupo_anterior = $registro[$modo];
    }
?>
</table>
<?}
//*******************************************************************************************
//***************************  Usuarios no asociados al proyecto  ***************************
//*******************************************************************************************
?>
<table width="100%"  class='cat-item'>
<tr>
     <td width="98%" class="lista-obj-titulo" >Usuarios de la instancia externos al PROYECTO</td>
</tr>
</table>
<script language='javascript'>
    editor='usuario';
</script>
<table width="100%" class='lista-obj'>
<?
    $sql = "
SELECT  u.usuario,
        u.nombre
FROM        apex_usuario u
WHERE       NOT EXISTS (
                        SELECT 1 FROM apex_usuario_proyecto p
                        WHERE p.usuario = u.usuario
                        AND p.proyecto = '".editor::get_proyecto_cargado()."' )
            ORDER BY 2";
	$rs = toba::get_db()->consultar($sql);
	if( $rs ) {
	foreach( $rs as $registro ) {
?>
        <tr>
          <td width="2%" class='lista-obj-botones'>
            <a href="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/usuarios/propiedades",array(apex_hilo_qs_zona => $registro['usuario']),false,false,null,true,'central') ?>" target="<? echo  apex_frame_centro ?>">
                <? echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"Modificar USUARIO") ?>
            </a>
      </td>
          <td width="30%" class='lista-obj-dato1'>&nbsp;<? echo $registro["usuario"] ?></td>
          <td width="70%" class='lista-obj-dato1'>&nbsp;<? echo $registro["nombre"] ?></td></tr>
<?
    }
    }else{
?>
        <tr>
            <td width="2%" class='lista-obj-dato1'>&nbsp;&nbsp;No existen usuarios NO asociados!</td>
        </tr>
<?
    }
?>
</table>