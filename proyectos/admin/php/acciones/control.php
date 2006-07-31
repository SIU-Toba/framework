<?
	require_once('nucleo/lib/interface/form.php');
	require_once('modelo/catalogo_modelo.php');
	
	$js_cambiar_color_1 = " onmouseover=\"this.className='listado-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-tabn';\"";
	$js_cambiar_color_2 = " onmouseover=\"this.className='listado-barra-superior-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-barra-superior-tabn';\"";
                      
	if (isset($_POST['admin_proyecto'])) {
		editor::set_proyecto_cargado($_POST['admin_proyecto']);
		$opciones = array('validar' => false);
		$vinculo = toba::get_vinculador()->crear_vinculo('admin', '/admin/acceso', array(), $opciones);
		
		//--- Refresca los otros frames
		echo js::abrir();
		echo "top.location.href = '$vinculo';";
		echo js::cerrar();
	}
	echo form::abrir("cambiar_proyecto", '');                        
?>
<script type="text/javascript" language='javascript'>
var frame_admin = top.document.getElementById('frameset_admin');
var ancho_frame = frame_admin.cols;
var expandido = true;
function mostrar_ocultar_frame() {
	var imagen = document.getElementById('imagen_manejo_frame');
	if (expandido) {
		imagen.src = '<?echo recurso::imagen_apl("expandir.gif",false);?>';
		frame_admin.cols = '8,*';
		expandido = false;
	} else {
		imagen.src = '<?echo recurso::imagen_apl("contraer.gif",false);?>';
		frame_admin.cols = ancho_frame;
		expandido = true;
	}
}

</script>

<?
	$js_editor = recurso::js('editor.js');
	$datos = editor::get_parametros_previsualizacion_js();
	$parametros_previsualizacion = js::arreglo($datos, true);
?>
<SCRIPT language='JavaScript1.4' type='text/javascript' src='<? echo $js_editor  ?>'></SCRIPT>
<SCRIPT language='JavaScript1.4' type='text/javascript' >
	editor.set_parametros_previsualizacion(<? echo $parametros_previsualizacion ?>);
</script>


<table width='100%'  class='tabla-0' >
<tr><td class='listado-barra-superior'><? echo gif_nulo(1,4) ?></td></tr>
<tr><td  class='listado-barra-superior'>

	<table class='tabla-0' width='100%'>
	<tr> 
		<td class='listado-vacia' width='1%' nowrap valign='middle'>
		<a href="javascript: mostrar_ocultar_frame();"><img src="<? echo recurso::imagen_apl("contraer.gif",false); ?>" id='imagen_manejo_frame' border='0' style='margin: 0px 0px 0px 0px;'></a>
		 <? echo recurso::imagen_apl("logo_barra_apex.gif",true)?>
		 </td>

		<td width='100%'><? echo gif_nulo(3,1) ?></td>

<?
		echo "<td class='listado-barra-superior-tabi'>";
		$js_cambio = "onclick='document.cambiar_proyecto.submit()'";		
        echo form::image('cambiar',recurso::imagen_apl('cambiar_proyecto.gif',false), $js_cambio);
        echo "</td>";
		echo "<td class='listado-barra-superior-tabi2'>";
		$actual = editor::get_proyecto_cargado();
		$instancia = catalogo_modelo::instanciacion()->get_instancia(editor::get_id_instancia_activa(), new mock_gui);
		$proyectos = array();
		foreach ($instancia->get_lista_proyectos_vinculados() as $proy) {
			$proyectos[$proy] = $proy;
		}
		$js_cambio = "onchange='document.cambiar_proyecto.submit()'";
		echo form::select("admin_proyecto", $actual, $proyectos, 'ef-combo', "$js_cambio");
		echo "</td>";		
?>
		<td><? echo gif_nulo(3,1) ?></td>

		<td class='listado-tabi'>
        <a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"3280") ?>" class="list-obj"  target="<? echo  apex_frame_lista ?>">
        <? echo recurso::imagen_apl("actividad_local.gif",true,null,null,"LOG de modificacion de componentes") ?></a></td>

<?
	$parametros = array();
	$html_extra = array('id' => 'vinculo_logger');
	$url =toba::get_vinculador()->obtener_vinculo_a_item('admin','1000003',$parametros,true, false, false, '', $html_extra, null, 'logger');
?>
         <td class='listado-tabi'><? echo $url ?></td>

         <td class='listado-tabi'>
        <a href="<? echo toba::get_vinculador()->generar_solicitud('admin','/inicio') ?>" class="list-obj"  target="<? echo  apex_frame_centro ?>">
		 <? echo recurso::imagen_apl("home.gif",true,null,null,"Pagina inicial") ?></a></td>


		<td><? echo gif_nulo(3,1) ?></td>
         <td><a href="#" class="list-obj"  onclick='javascript:salir();return false;'>
		 <? echo recurso::imagen_apl("finalizar_sesion.gif",true,null,null,"Finalizar SESION") ?></a></td>
		<td><? echo gif_nulo(3,1) ?></td>

	</tr>
	</table>
</td></tr>
<tr><td class='listado-barra-superior' ><? echo gif_nulo(1,2) ?></td></tr>
<tr><td  class='listado-linea' ><? echo gif_nulo(1,1) ?></td></tr>
<tr><td  class='listado-normal'><? echo gif_nulo(1,4) ?></td></tr>
<tr><td class='listado-normal'>
	<table class='tabla-0' width='100%'>
	<tr > 

		<td><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("items/item.gif",true) ?></td>
		 <td class='listado-tabn' <? echo $js_cambiar_color_1 ?> >
		<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/admin/items/catalogo_unificado",null,false,false,null,true,'lateral') ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">ITEMS</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("objetos/objeto.gif",true) ?></td>
		<td class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',1240,null,false,false,null,true,'lateral') ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">COMPONENTES</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		<td  class='listado-tabi'>
		<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/admin/proyectos/organizador") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>"><? echo recurso::imagen_apl("configurar.gif",true,null,null,"Configurar Proyecto") ?></a>
		</td>

		<td class='listado-tabi'>
		<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/admin/usuarios/listado",null,false,false,null,true,'lateral') ?>" class="list-obj" target="<? echo  apex_frame_lista ?>"><? echo recurso::imagen_apl("usuarios/usuario.gif",true) ?></a>
		</td>

		<td class='listado-tabi'>
			<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/pruebas/testing_automatico_web",null,false,false,null,true) ?>" class="list-obj" target="<? echo  apex_frame_centro ?>"><? echo recurso::imagen_apl("testing.gif",true,null,null,"Testing automático") ?></a>
		</td>
		
		<td class='listado-tabi'>
			<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/admin/objetos_toba/crear",null,false,false,null,true) ?>" class="list-obj" target="<? echo  apex_frame_centro ?>"><? echo recurso::imagen_apl("objetos/objeto_nuevo.gif",true,null,null,"Crear Objeto") ?></a>
		</td>
		
		
		<td><? echo gif_nulo(3,1) ?></td>

		</tr>
	</table>
</td></tr>
<tr><td  class='listado-normal'><? echo gif_nulo(1,4) ?></td></tr>
</table>
<?php 
	echo form::cerrar();
?>