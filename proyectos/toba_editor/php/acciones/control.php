<?php
	$escapador = toba::escaper();
	$js_cambiar_color_1 = " onmouseover=\"this.className='listado-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-tabn';\"";
	$js_cambiar_color_2 = " onmouseover=\"this.className='listado-barra-superior-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-barra-superior-tabn';\"";
                      
	if (isset($_POST['admin_proyecto'])) {
		toba_editor::set_proyecto_cargado($_POST['admin_proyecto']);
		toba::memoria()->set_dato_instancia('proyecto', $_POST['admin_proyecto']);
		$opciones = array('validar' => false);
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), 1000231, array(), $opciones);
		
		//-- Fuerza a recargar los datos de instalacion e instancia
		toba_manejador_sesiones::recargar_info_instalacion();
		toba_manejador_sesiones::recargar_info_instancia();
		
		//--- Refresca los otros frames
		echo toba_js::abrir();
		echo "top.location.href = '". $escapador->escapeJs($vinculo)."';";
		echo toba_js::cerrar();
	}
	echo toba_form::abrir('cambiar_proyecto', '');
	toba::manejador_sesiones()->enviar_csrf_hidden();
?>

<style type='text/css'>
.ci-tabs-h-lista a {
	padding: 5px 10px 2px 3px;
	min-height: 20px;
}
#barra_superior {
	display:none;
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
		imagen.src = '<?php echo $escapador->escapeJs(toba_recurso::imagen_toba('nucleo/expandir_der.gif', false)); ?>';
		frame_admin.cols = '12,*';
		expandido = false;
	} else {
		imagen.src = '<?php echo $escapador->escapeJs(toba_recurso::imagen_toba('nucleo/expandir_izq.gif', false)); ?>';
		frame_admin.cols = ancho_frame;
		expandido = true;
	}
}

function abrir_toba_instancia(){
	var url = '<?php echo $escapador->escapeJs(toba::vinculador()->get_url('toba_usuarios', '3432', array(), array('menu' => true))); ?>';
	if ( url == '') {
		alert('No posee permisos para acceder al proyecto "toba_usuarios"!');	
		return;
	}
	var opciones = {'width': 1000, 'scrollbars' : 'yes', 'height': 650, 'resizable': 'yes'};
	abrir_popup('toba_instancia', url, opciones, null, false);
}
</script>

<?php
	$js_editor = toba_recurso::js('editor.js');
	$datos = toba_editor::get_parametros_previsualizacion_js();
	$parametros_previsualizacion = toba_js::arreglo($datos, true);
?>
<SCRIPT language='JavaScript1.4' type='text/javascript' src='<?php echo $js_editor; ?>'></SCRIPT>
<SCRIPT language='JavaScript1.4' type='text/javascript' >
	editor.set_parametros_previsualizacion(<?php echo $parametros_previsualizacion; ?>);

	var tab_actual = null;	
	function seleccionar_tab(span)
	{
		if (isset(tab_actual)) {
			tab_actual.parentNode.className = 'ci-tabs-h-solapa';
		}
		span.parentNode.className = 'ci-tabs-h-solapa-sel';
		tab_actual = span;
	}
</script>

<table width='100%' class='tabla-0' >
<tr><td><div class='listado-barra-logo'>
        <a title='Página inicial del editor' href="<?php echo $escapador->escapeHtmlAttr(toba::vinculador()->get_url(toba_editor::get_id(), 1000265)); ?>" target="<?php echo  apex_frame_centro; ?>">
		SIU-TOBA
		</a></div>
</td></tr>
<tr class='listado-barra-fila'><td  class='listado-barra-superior'>

	<table class='tabla-0' style='margin-top: 6px'>
	<tr> 
		<td class='listado-vacia' width='1%' nowrap valign='middle'>
	        <a title='Oculta el frame izq. del editor' href="javascript: mostrar_ocultar_frame();"><img src="<?php echo $escapador->escapeHtmlAttr(toba_recurso::imagen_toba('nucleo/expandir_izq.gif', false)); ?>" id='imagen_manejo_frame' border='0' style='margin: 0px 0px 0px 0px;' alt='' /></a>		
		 </td>
<?php
	echo "<td class='listado-barra-superior-tabi' title='Recarga el Proyecto en el Editor'>";
	$js_cambio = "onclick='document.cambiar_proyecto.submit()'";
	echo "<a href='#' $js_cambio>";
	echo toba_recurso::imagen_toba('refrescar.png', true);
	echo '</a>';
	echo '</td>';
	echo "<td class='listado-barra-superior-tabi2'>";
	$actual = toba_editor::get_proyecto_cargado();
	$instancia = toba_modelo_catalogo::instanciacion()->get_instancia(toba_editor::get_id_instancia_activa(), new toba_mock_proceso_gui);
	$proyectos = array();
	foreach ($instancia->get_lista_proyectos_vinculados() as $proy) {
		$extra = toba_personalizacion::get_personalizacion_iniciada($proy) ? " (personalizado) " : "";
		$proyectos[$proy] = $proy.$extra;
	}
	$js_cambio = "onchange='document.cambiar_proyecto.submit()'";
	echo toba_form::select('admin_proyecto', $actual, $proyectos, 'ef-combo', $js_cambio);
	echo '</td>';		

	echo "<td class='listado-barra-superior-tabi'>";
	$img = toba_recurso::imagen_toba('instanciar.png', true);
	echo "<a title='Previsualiza el proyecto' href='javascript: top.frame_control.editor.previsualizar()'>$img</a>";
	echo '</td>';		
?>

		<td><?php echo gif_nulo(10, 1); ?></td>
		<td><div class='tabla-0 listado-tabc'><table class='tabla-0'>
		<td class='listado-tabi'>
		<a title="Administración de Usuarios" href="#" class="list-obj" onclick='javascript:abrir_toba_instancia();return false;'><?php echo toba_recurso::imagen_toba('usuarios/usuario.gif', true); ?></a>
		</td>
	
         <td class='listado-tabi'>
        <a  href="<?php echo $escapador->escapeHtmlAttr(toba::vinculador()->get_url(toba_editor::get_id(), '3357')); ?>" class="list-obj"  target="<?php echo  apex_frame_centro; ?>">
<?php

		$ayuda = '<a target=wiki href='.get_url_desarrollos().'/trac/toba title=\\\'Documentación WIKI\\\'>';			
		$ayuda .= '<img src='.toba_recurso::url_proyecto().'/doc/api/media/wiki-small.png ></a> ';
		$ayuda .= '<a target=api href='.toba_recurso::url_proyecto().'/doc/api/index.html title=\\\'Documentación código PHP\\\'>';
		$ayuda .= '<img src='.toba_recurso::url_proyecto().'/doc/api/media/php-small.png></a> ';
		$ayuda .= '<a target=api_js href='.toba_recurso::url_proyecto().'/doc/api_js/index.html title=\\\'Documentación código Javascript\\\'>';
		$ayuda .= '<img src='.toba_recurso::url_proyecto().'/doc/api/media/javascript-small.png></a>';
		echo toba_recurso::imagen_toba('ayuda.png', true, null, null, $ayuda);
 ?></a></td>

		<td class='listado-tabi'>
			<a title='Testing' href="<?php echo $escapador->escapeHtmlAttr(toba::vinculador()->get_url(toba_editor::get_id(), 1000270, null, array('menu' => true))); ?>" class="list-obj" target="<?php echo apex_frame_centro; ?>"><?php echo toba_recurso::imagen_toba('testing.gif', true); ?></a>
		</td>

<?php
	$parametros = array();
	$html_extra = array('id' => 'vinculo_logger',
						'imagen' => 'logger.gif',
						'imagen_recurso_origen' => 'apex',
						'tipo' => 'popup',
						'inicializacion' => '800,500,1,1',
						'texto' => 'Logger');
	$url = toba::vinculador()->get_url(toba_editor::get_id(), '1000003', $parametros, array('param_html' => $html_extra, 'celda_memoria' => 'logger'));
?>
         <td class='listado-tabi'><?php echo $url; ?></td>
         <td  class='listado-tabi'><a title='Cerrar la sesión' href="#" class="list-obj"  onclick='javascript:salir();return false;'>
		 <?php echo toba_recurso::imagen_toba('finalizar_sesion.gif', true); ?></a></td>
		 </table></div>
		 </td>	
		 <td><?php echo gif_nulo(5, 1); ?></td>
		 	 
	</tr>
	</table>
</td></tr>
<?php
	$item_actual = toba::memoria()->get_item_solicitado();
	//------------ TABS
	$tabs = array(
		array(
			'nombre' => 'Operaciones',
			'imagen' => toba_recurso::imagen_proyecto('item.gif', true),
			'url' => toba::vinculador()->get_url(toba_editor::get_id(), 1000239, null, array('menu' => true, 'celda_memoria' => 'lateral')),
			'ayuda' => 'Operaciones disponibles en el Proyecto',
		),
		array(
			'nombre' => 'Comp.',
			'imagen' => toba_recurso::imagen_toba('objetos/objeto.gif', true),
			'url' => toba::vinculador()->get_url(toba_editor::get_id(), 1240, null, array('menu' => true, 'celda_memoria' => 'lateral')),
			'ayuda' => 'Componentes disponibles en el Proyecto',
		),	
		array(
			'nombre' => 'Datos',
			'imagen' => toba_recurso::imagen_toba('fuente.png', true),
			'url' => toba::vinculador()->get_url(toba_editor::get_id(), 3397, null, array('menu' => true, 'celda_memoria' => 'lateral')),
			'ayuda' => 'Acceso a datos',
		),
		array(
			'nombre' => 'PHP',
			'imagen' => toba_recurso::imagen_toba('nucleo/php.gif', true),
			'url' => toba::vinculador()->get_url(toba_editor::get_id(), 30000012, null, array('menu' => true, 'celda_memoria' => 'lateral')),
			'ayuda' => 'Código PHP del proyecto',
		),	
		array(
			'nombre' => 'Conf.',
			'imagen' => toba_recurso::imagen_toba('configurar.png', true),
			'url' => toba::vinculador()->get_url(toba_editor::get_id(), 1000258, null, array('menu' => true, 'celda_memoria' => 'lateral')),
			'ayuda' => 'Configuración general del proyecto',
		),				
	);

	echo "<tr  class='listado-barra-fila'><td class='ci-tabs-h-lista'>\n";		
	echo "<ul  style='margin-top: 6px'>\n";
	$id = 'id="tab_inicial"';
	$i = 0;
	foreach ($tabs as $tab) {
		if ($i == 0) {
			$class = 'ci-tabs-h-solapa-sel';
		} else {
			$class = 'ci-tabs-h-solapa';
		}
		echo "<li class='$class'>";
		echo "<a $id href='".$escapador->escapeHtmlAttr($tab['url'])."' title='". $escapador->escapeHtmlAttr($tab['ayuda'])."'  onclick='seleccionar_tab(this)' target='".apex_frame_lista."'>{$tab['imagen']} {$tab['nombre']}</a>";
		echo '</li>';
		$id = '';
		$i++;
	}
	echo toba_js::ejecutar('$$("tab_inicial").onclick()');	
	echo '</ul>';
	echo "</td></tr>\n";
?>
</table>
<?php
	echo toba_form::cerrar();
?>
