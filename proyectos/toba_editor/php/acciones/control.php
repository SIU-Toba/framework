<?php

	require_once('modelo/catalogo_modelo.php');
	
	$js_cambiar_color_1 = " onmouseover=\"this.className='listado-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-tabn';\"";
	$js_cambiar_color_2 = " onmouseover=\"this.className='listado-barra-superior-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-barra-superior-tabn';\"";
                      
	if (isset($_POST['admin_proyecto'])) {
		toba_editor::set_proyecto_cargado($_POST['admin_proyecto']);
		$opciones = array('validar' => false);
		$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(), '/admin/acceso', array(), $opciones);
		
		//--- Refresca los otros frames
		echo toba_js::abrir();
		echo "top.location.href = '$vinculo';";
		echo toba_js::cerrar();
	}
	echo toba_form::abrir("cambiar_proyecto", '');                        
?>
<script type="text/javascript" language='javascript'>
var frame_admin = top.document.getElementById('frameset_admin');
if (frame_admin) {
	var ancho_frame = frame_admin.cols;
}
var expandido = true;
function mostrar_ocultar_frame() {
	var imagen = document.getElementById('imagen_manejo_frame');
	if (expandido) {
		imagen.src = '<?echo toba_recurso::imagen_proyecto("expandir.gif",false);?>';
		frame_admin.cols = '8,*';
		expandido = false;
	} else {
		imagen.src = '<?echo toba_recurso::imagen_proyecto("contraer.gif",false);?>';
		frame_admin.cols = ancho_frame;
		expandido = true;
	}
}

function abrir_toba_instancia(){
	var url = '<? echo toba::vinculador()->generar_solicitud('toba_instancia','3329',null,false,false,null,true) ?>';
	var opciones = {'width': 1000, 'scrollbars' : true, 'height': 650, 'resizable': true};
	abrir_popup('toba_instancia', url, opciones, null, false);
}
</script>

<?
	$js_editor = toba_recurso::js('editor.js');
	$datos = toba_editor::get_parametros_previsualizacion_js();
	$parametros_previsualizacion = toba_js::arreglo($datos, true);
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
		<a title='Oculta el frame izq. del editor' href="javascript: mostrar_ocultar_frame();"><img src="<? echo toba_recurso::imagen_proyecto("contraer.gif",false); ?>" id='imagen_manejo_frame' border='0' style='margin: 0px 0px 0px 0px;'></a>
		 <? echo toba_recurso::imagen_proyecto("logo_barra_apex.gif",true)?>
		 </td>

		<td width='100%'><? echo gif_nulo(3,1) ?></td>
<?
		echo "<td class='listado-barra-superior-tabi' title='Recarga el Proyecto en el Editor'>";
		$js_cambio = "onclick='document.cambiar_proyecto.submit()'";
		echo "<a href='#' $js_cambio>";
		echo toba_recurso::imagen_toba('refrescar.png',true);
		echo "</a>";
        echo "</td>";
		echo "<td class='listado-barra-superior-tabi2'>";
		$actual = toba_editor::get_proyecto_cargado();
		$instancia = catalogo_modelo::instanciacion()->get_instancia(toba_editor::get_id_instancia_activa(), new mock_gui);
		$proyectos = array();
		foreach ($instancia->get_lista_proyectos_vinculados() as $proy) {
			$proyectos[$proy] = $proy;
		}
		$js_cambio = "onchange='document.cambiar_proyecto.submit()'";
		echo toba_form::select("admin_proyecto", $actual, $proyectos, 'ef-combo', "$js_cambio");
		echo "</td>";		

		echo "<td class='listado-barra-superior-tabi'>";
		$img = toba_recurso::imagen_toba('instanciar.png', true);
		echo "<a title='Previsualiza el proyecto' href='javascript: top.frame_control.editor.previsualizar()'>$img</a>";
		echo "</td>";		
?>
		<td><? echo gif_nulo(5,1) ?></td>

<?
	$parametros = array();
	$html_extra = array('id' => 'vinculo_logger',
						'imagen' => 'logger.gif',
						'imagen_recurso_origen' => 'apex',
						'tipo' => 'popup',
						'inicializacion' => '615,450,1,1',
						'texto' => 'Logger');
	$url = toba::vinculador()->generar_solicitud(toba_editor::get_id(),'1000003',$parametros, false, false, $html_extra, null, 'logger');
?>
         <td class='listado-tabi'><? echo $url ?></td>

         <td class='listado-tabi'>
        <a title='Indice de la ayuda disponible' href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),'3357') ?>" class="list-obj"  target="<? echo  apex_frame_centro ?>">
		 <? echo toba_recurso::imagen_toba("ayuda.gif",true) ?></a></td>
         
         
         <td class='listado-tabi'>
        <a title='Página inicial del editor' href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),'/inicio') ?>" class="list-obj"  target="<? echo  apex_frame_centro ?>">
		 <? echo toba_recurso::imagen_toba("home.png",true) ?></a></td>

		<td class='listado-tabi'>
		<a title="Administracion de la INSTANCIA" href="#" class="list-obj" onclick='javascript:abrir_toba_instancia();return false;'><? echo toba_recurso::imagen_toba("instancia.gif",true) ?></a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>
         <td><a title='Cerrar la sesión' href="#" class="list-obj"  onclick='javascript:salir();return false;'>
		 <? echo toba_recurso::imagen_toba("finalizar_sesion.gif",true) ?></a></td>
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

		 <td class='listado-tabi'><? echo toba_recurso::imagen_proyecto("item.gif",true) ?></td>
		 <td class='listado-tabn' <? echo $js_cambiar_color_1 ?> >
		<a title="Listado de Operaciones disponibles en el Proyecto" href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/items/catalogo_unificado",null,false,false,null,true,'lateral') ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">ITEMS</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo toba_recurso::imagen_toba("objetos/objeto.gif",true) ?></td>
		<td class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a title="Listado de Componentes creados en el Proyecto" href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),1240,null,false,false,null,true,'lateral') ?>" class="list-obj" target="<? echo apex_frame_lista ?>">COMPONENTES</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		<td class='listado-tabi'>
        <a title='LOG de modificación de componentes' href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),"3280") ?>" class="list-obj"  target="<? echo  apex_frame_lista ?>"><? echo toba_recurso::imagen_proyecto("actividad_local.gif",true) ?></a>
        </td>

		<td class='listado-tabi'>
			<a title="Crear un nuevo Componente" href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",null,false,false,null,true) ?>" class="list-obj" target="<? echo  apex_frame_centro ?>"><? echo toba_recurso::imagen_toba("objetos/objeto_nuevo.gif",true) ?></a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		<td class='listado-tabi'>
		<a title="Configuración de la previsualización del proyecto" href="<? echo toba::vinculador()->crear_vinculo( toba_editor::get_id(), '3287', array('menu'=>true, 'celda'=>'central') ) ?>" class="list-obj" target="<? echo  apex_frame_centro ?>"><? echo toba_recurso::imagen_proyecto("config_previsualizacion.gif",true) ?></a>
		</td>

		<td class='listado-tabi'>
			<a title='Testing Automático' href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/pruebas/testing_automatico_web",null,false,false,null,true) ?>" class="list-obj" target="<? echo apex_frame_centro ?>"><? echo toba_recurso::imagen_toba("testing.gif",true) ?></a>
		</td>

		<td  class='listado-tabi'>
		<a title="Configuración general del proyecto" href="<? echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/proyectos/organizador") ?>" class="list-obj" target="<? echo apex_frame_lista ?>"><? echo toba_recurso::imagen_toba("configurar.gif",true) ?></a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		</tr>
	</table>
</td></tr>
<tr><td  class='listado-normal'><? echo gif_nulo(1,4) ?></td></tr>
</table>
<?php 
	echo toba_form::cerrar();
?>