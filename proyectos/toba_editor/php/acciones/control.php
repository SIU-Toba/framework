<?php


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
<style type='text/css'>
.ci-tabs-h-lista a {
	padding: 5px 12px 4px 3px;
}
</style>
<script type="text/javascript" language='javascript'>
var frame_admin = top.document.getElementById('frameset_admin');
if (frame_admin) {
	var ancho_frame = frame_admin.cols;
}
var expandido = true;
function mostrar_ocultar_frame() {
	var imagen = document.getElementById('imagen_manejo_frame');
	if (expandido) {
		imagen.src = '<?php echo toba_recurso::imagen_proyecto("expandir.gif",false);?>';
		frame_admin.cols = '8,*';
		expandido = false;
	} else {
		imagen.src = '<?php echo toba_recurso::imagen_proyecto("contraer.gif",false);?>';
		frame_admin.cols = ancho_frame;
		expandido = true;
	}
}

function abrir_toba_instancia(){
	var url = '<?php echo toba::vinculador()->generar_solicitud('toba_instancia','3329',array('instancia'=>toba_editor::get_id_instancia_activa()),false,false,null,true) ?>';
	if ( url == '') {
		alert('No posee permisos para acceder al proyecto "toba_instancia"!');	
		return;
	}
	var opciones = {'width': 1000, 'scrollbars' : true, 'height': 650, 'resizable': true};
	abrir_popup('toba_instancia', url, opciones, null, false);
}
</script>

<?php
	$js_editor = toba_recurso::js('editor.js');
	$datos = toba_editor::get_parametros_previsualizacion_js();
	$parametros_previsualizacion = toba_js::arreglo($datos, true);
	
	$url_li_sel = 'url("'.toba_recurso::imagen_skin('tabs/left_on.gif').'") no-repeat left top';
	$url_a_sel = 'url("'.toba_recurso::imagen_skin('tabs/right_on.gif').'") no-repeat right top';
	$url_li = 'url("'.toba_recurso::imagen_skin('tabs/left.gif').'") no-repeat left top';
	$url_a = 'url("'.toba_recurso::imagen_skin('tabs/right.gif').'") no-repeat right top';	
	$estilo_li_sel = "background: $url_li_sel";
	$estilo_a_sel = "background: $url_a_sel";
	$estilo_li = "background: $url_li";
	$estilo_a = "background: $url_a";
?>
<SCRIPT language='JavaScript1.4' type='text/javascript' src='<?php echo $js_editor  ?>'></SCRIPT>
<SCRIPT language='JavaScript1.4' type='text/javascript' >
	editor.set_parametros_previsualizacion(<?php echo $parametros_previsualizacion ?>);

	var tab_actual = null;	
	function seleccionar_tab(span)
	{
		if (isset(tab_actual)) {
			tab_actual.parentNode.style.background = '<?php echo $url_li ?>';
			tab_actual.style.background = '<?php echo $url_a ?>';
		}
		span.parentNode.style.background = '<?php echo $url_li_sel ?>';
		span.style.background = '<?php echo $url_a_sel ?>';
		tab_actual = span;
	}
</script>


<table width='100%'  class='tabla-0' >
<tr><td class='listado-barra-superior'><?php echo gif_nulo(1,4) ?></td></tr>
<tr><td  class='listado-barra-superior'>

	<table class='tabla-0' width='100%'>
	<tr> 
		<td class='listado-vacia' width='1%' nowrap valign='middle'>
        <a title='P�gina inicial del editor' href="<?php echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),'/inicio') ?>" class="list-obj"  target="<?php echo  apex_frame_centro ?>">
		 <?php echo toba_recurso::imagen_toba('icono_24.png',true)?></a>
		 </td>
		<td width='100%'><?php echo gif_nulo(3,1) ?></td>
<?php
		echo "<td class='listado-barra-superior-tabi' title='Recarga el Proyecto en el Editor'>";
		$js_cambio = "onclick='document.cambiar_proyecto.submit()'";
		echo "<a href='#' $js_cambio>";
		echo toba_recurso::imagen_toba('refrescar.png',true);
		echo "</a>";
        echo "</td>";
		echo "<td class='listado-barra-superior-tabi2'>";
		$actual = toba_editor::get_proyecto_cargado();
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia(toba_editor::get_id_instancia_activa(), new toba_mock_proceso_gui);
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

		<td><?php echo gif_nulo(3,1) ?></td>

		<td class='listado-tabi'>
		<a title="Administraci�n de la INSTANCIA" href="#" class="list-obj" onclick='javascript:abrir_toba_instancia();return false;'><?php echo toba_recurso::imagen_toba("instancia.gif",true) ?></a>
		</td>


		<td><?php echo gif_nulo(5,1) ?></td>

         <td class='listado-tabi'>
        <a title='Indice de la ayuda disponible' href="<?php echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),'3357') ?>" class="list-obj"  target="<?php echo  apex_frame_centro ?>">
		 <?php echo toba_recurso::imagen_toba("ayuda.png",true) ?></a></td>

		<td class='listado-tabi'>
			<a title='Testing Autom�tico' href="<?php echo toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/pruebas/testing_automatico_web",null,false,false,null,true) ?>" class="list-obj" target="<?php echo apex_frame_centro ?>"><?php echo toba_recurso::imagen_toba("testing.gif",true) ?></a>
		</td>

<?php
	$parametros = array();
	$html_extra = array('id' => 'vinculo_logger',
						'imagen' => 'logger.gif',
						'imagen_recurso_origen' => 'apex',
						'tipo' => 'popup',
						'inicializacion' => '615,450,1,1',
						'texto' => 'Logger');
	$url = toba::vinculador()->generar_solicitud(toba_editor::get_id(),'1000003',$parametros, false, false, $html_extra, null, 'logger');
?>
         <td class='listado-tabi'><?php echo $url ?></td>

		<td><?php echo gif_nulo(3,1) ?></td>

         <td><a title='Cerrar la sesi�n' href="#" class="list-obj"  onclick='javascript:salir();return false;'>
		 <?php echo toba_recurso::imagen_toba("finalizar_sesion.gif",true) ?></a></td>
		<td><?php echo gif_nulo(3,1) ?></td>

	</tr>
	</table>
</td></tr>
<?php
	$item_actual = toba::memoria()->get_item_solicitado();
	//------------ TABS
	$tabs = array(
		array(
			'nombre' => 'Operac.',
			'imagen' => toba_recurso::imagen_proyecto("item.gif",true),
			'url' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/items/catalogo_unificado",null,false,false,null,true,'lateral'),
			'ayuda' => 'Operaciones disponibles en el Proyecto',
		),
		array(
			'nombre' => 'Comp.',
			'imagen' => toba_recurso::imagen_toba("objetos/objeto.gif",true),
			'url' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),1240,null,false,false,null,true,'lateral'),
			'ayuda' => 'Componentes disponibles en el Proyecto',
		),	
		array(
			'nombre' => 'Datos',
			'imagen' => toba_recurso::imagen_toba('fuente.png',true),
			'url' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),3397,null,false,false,null,true,'lateral'),
			'ayuda' => 'Acceso a datos',
		),
		array(
			'nombre' => 'Moldes',
			'imagen' => toba_recurso::imagen_toba('wizard.png',true),
			'url' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),3394,null,false,false,null,true,'lateral'),
			'ayuda' => 'Creaci�n y edici�n de moldes de operaciones',
		),			
		array(
			'nombre' => '',
			'imagen' => toba_recurso::imagen_toba('configurar.png',true),
			'url' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/proyectos/organizador",null,false,false,null,true,'lateral'),
			'ayuda' => 'Configuraci�n general del proyecto',
		),				
	);

	$estilo = 'background: url("'.toba_recurso::imagen_skin('tabs/bg.gif').'") repeat-x bottom;';
	echo "<tr><td style='$estilo' class='ci-tabs-h-lista'>";		
	echo "<ul>\n";
	$id = 'id="tab_inicial"';
	foreach( $tabs as $tab ) {
		
		echo "<li class='ci-tabs-h-solapa' style='$estilo_li'>";
		echo "<a $id href='{$tab['url']}' title='{$tab['ayuda']}'  onclick='seleccionar_tab(this)' style='$estilo_a' target='".apex_frame_lista."'>{$tab['imagen']} {$tab['nombre']}</a>";
		echo "</li>";
		$id = '';
	}
	echo toba_js::ejecutar('$("tab_inicial").onclick()');	
	echo "</ul>";
	echo "</td></tr>\n";
?>
</table>
<?php 
	echo toba_form::cerrar();
?>
