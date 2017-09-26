<?php

	$editable = toba::zona()->get_editable();
	define('separador_camino', '_%_');

	$grupo = $editable[1];
	$arbol = new toba_catalogo_items(toba_editor::get_proyecto_cargado());
	$arbol->cargar_todo();
	$arbol->sacar_publicos();

	$maximo = $arbol->profundidad();
	$formulario = 'permisos';
	$boton_post = 'asignar_permisos';
	$boton_post_nombre = 'Guardar';
	$prefijo_items = 'item_';//Prefijo de los checkbox

	if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST[$boton_post]))) {
		if ($_POST[$boton_post ] == $boton_post_nombre) {	//SI hay un POST, y estuvo disparado por este formulario
			$items = array();
			foreach ($_POST as $etiqueta => $valor) {
				if ((substr($etiqueta, 0, strlen($prefijo_items))) == $prefijo_items) {
					$nodo = trim(substr($etiqueta, strlen($prefijo_items)));
					$items[] = $nodo;
				}
			}
			$arbol->cambiar_permisos($items, $grupo);
			echo ei_mensaje('Los permisos han sido actualizados correctamente');
		}
	}
	
	//--------------------------------------------------------------------------
	//--------------<  INTERFACE DE ASIGNACION de PERMISOS  >-------------------
	//--------------------------------------------------------------------------	

	echo "<br>\n";
	echo "<div align='center'>\n";	
	$escapador = toba::escaper();
?>
	<form  enctype='application/x-www-form-urlencoded' name='<?php echo $formulario; ?>' method='POST' action='<?php echo toba::vinculador()->get_url(null, null); ?>'>
	<table width="450" class='cat-item' align='center'>
        	<tr> 
          <td colspan="<?php echo $escapador->escapeHtmlAttr((5 + $maximo)); ?>"  class="cat-item-categ1">
			<?php
				echo toba_form::submit($boton_post, $boton_post_nombre);
			?>
		  </td>
        </tr>	
	<?php
	foreach ($arbol->items() as $item) { 
		echo '<tr>';
		//Indentado del arbol
		$nivel = $item->get_nivel_prof();
		for ($a = 0; $a < $nivel; $a++) {
			echo "<td width='2%'  class='cat-arbol'>".gif_nulo(4, 1).'</td>';
		}
		if ($item->es_carpeta()) {
			$ultima_carpeta = $item->get_id();
			//******************< Carpetas >*****************************
			?>
	          <td  class='cat-arbol-carpeta' width='1px'>
				<img src='<?php echo $escapador->escapeHtmlAttr(toba_recurso::imagen_toba('items/carpeta.gif')); ?>' border='0'>
			  </td>
	          <td  class='cat-arbol-carpeta-info' width='1px'>
				<a href="#" class='cat-item' onclick="cascada('<?php echo $escapador->escapeHtmlAttr($item->get_id()); ?>',true);return false;"><?php echo toba_recurso::imagen_proyecto('check_cascada_on.gif', true, null, null, 'ACTIVAR hijos'); ?></a>
			  </td>
	          <td  class='cat-arbol-carpeta-info' width='1px'>
				<a href="#" class='cat-item' onclick="cascada('<?php echo $escapador->escapeHtmlAttr($item->get_id()); ?>',false);return false;"><?php echo toba_recurso::imagen_proyecto('check_cascada_off.gif', true, null, null, 'DESACTIVAR hijos'); ?></a>
			  </td>
	          <td  class='cat-arbol-carpeta-info'  width='450px' colspan='<?php echo $escapador->escapeHtmlAttr($maximo - $nivel); ?>'>&nbsp;&nbsp;<?php echo $escapador->escapeHtml($item->get_nombre()); ?></td>
	          <td  class='cat-arbol-carpeta-info'  width='1px'>
				<?php if ($item->es_de_menu()) { ?>
							<img src='<?php echo toba_recurso::imagen_proyecto('menu.gif'); ?>' border='0'>
				<?php } else { echo gif_nulo(); } ?>
			  </td>
	          <td  class='cat-arbol-carpeta-info'  width='1px'></td>
		  
			<?php } else {
				//******************< Items comunes >*************************
			?>
			          <td  class='ei-arbol-nodo' width='100%'  colspan='<?php echo $escapador->escapeHtmlAttr(($maximo-$nivel) + 3); ?>'>
			<?php
				$extra = ' camino=\''.$escapador->escapeHtml(separador_camino.implode(separador_camino, $item->get_camino()).separador_camino).'\' ';
				echo toba_form::checkbox($prefijo_items.$item->get_id(), $grupo, $item->grupo_tiene_permiso($grupo), 'ef-checkbox', $extra); 
				echo $escapador->escapeHtml($item->get_nombre()); ?>

			          </td>
			          <td  class='ei-arbol-nodo'  width='1px'>
			<?php if ($item->es_de_menu()) { ?>
						<img src='<?php echo toba_recurso::imagen_proyecto('menu.gif'); ?>' border='0'>
			<?php } else { echo gif_nulo(); } ?>
					  </td>
			          <td  class='ei-arbol-nodo' width='1px' ><?php echo toba_recurso::imagen_proyecto('item.gif', true, null, null, 'ID: ' . $item->get_id()); ?></td>
			<?php
		}
		echo '</tr>';
	}
	?>			
		<tr> 
          <td colspan="<?php echo $escapador->escapeHtmlAttr(5 + $maximo); ?>" align="center" class="cat-item-categ1">
	<?php
		echo toba_form::submit($boton_post, $boton_post_nombre);
		?>
		  </td>
        </tr>
	</table>
	</div>
	<br>
	<br>
	<?php
	echo toba_form::cerrar();
?>
<script languaje='javascript'>
function cascada(item_padre, estado)
{
	var item_actual, regex_item_padre, x, ultimo_elemento;
	formulario = document.<?php echo $escapador->escapeJs($formulario); ?>;
	regex_item_padre = '<?php echo $escapador->escapeJs(separador_camino); ?>' + item_padre + '<?php echo $escapador->escapeJs(separador_camino); ?>';
	for (x=0 ; x < formulario.elements.length ; x++)	
	{
		if(formulario.elements[x].type=="checkbox")
		{
			var camino = formulario.elements[x].getAttribute('camino');
			if (camino.indexOf(regex_item_padre) != -1 || (item_padre == 1000271 && camino == 1000271))
			{
				formulario.elements[x].checked = estado;
			}
		}
	}
}
</script>