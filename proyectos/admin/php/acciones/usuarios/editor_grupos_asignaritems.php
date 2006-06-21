<?
	include_once("nucleo/lib/form.php");
	require_once('modelo/lib/catalogo_items.php');

	define('separador_camino', '_%_');

	$grupo = $editable[1];
	$arbol = new catalogo_items(editor::get_proyecto_cargado());
	$arbol->cargar(array());
	$arbol->sacar_publicos();

	$maximo = $arbol->profundidad();
	$formulario = "permisos";
	$boton_post = "asignar_permisos";
	$boton_post_nombre = "Guardar";
	$prefijo_items = "item_";//Prefijo de los checkbox
		
	if( ($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST[$boton_post])) )
	{
		if($_POST[$boton_post]==$boton_post_nombre) 
		//SI hay un POST, y estuvo disparado por este formulario
		{
			$items= array();
			foreach($_POST as $etiqueta => $valor)
			{
				if((substr($etiqueta,0,strlen($prefijo_items)))==$prefijo_items){
					$nodo = trim(substr($etiqueta,strlen($prefijo_items)));
					$items[] = $nodo;
				}
			}
			$arbol->cambiar_permisos($items, $grupo);
			echo ei_mensaje("Los permisos han sido actualizados correctamente");
	 	}
	}
	
	//--------------------------------------------------------------------------
	//--------------<  INTERFACE DE ASIGNACION de PERMISOS  >-------------------
	//--------------------------------------------------------------------------	

	ei_separador("Acceso a ITEMs");	
	echo "<br>\n";
	echo "<div align='center'>\n";	
	echo form::abrir($formulario, $this->vinculador->generar_solicitud(null,null,null,true));	
	?><table width="600"  class='cat-item' align='center'>
        	<tr> 
          <td colspan="<? echo (5 + $maximo)?>"  class="cat-item-categ1">
			<?
				echo form::submit($boton_post,$boton_post_nombre);
			?>
		  </td>
        </tr>	
	<?
	foreach ($arbol->items() as $item)
	{ 
		echo "<tr>";
		//Indentado del arbol
		$nivel = $item->get_nivel_prof();
		for($a=0;$a<$nivel;$a++){
			echo "<td width='2%'  class='cat-arbol'>".gif_nulo(4,1)."</td>";
		}
		if ($item->es_carpeta()){
			$ultima_carpeta = $item->get_id();
			//******************< Carpetas >*****************************
			?>
	          <td  class='cat-arbol-carpeta' width='2%'>
				<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/items/carpeta_propiedades", array(apex_hilo_qs_zona => $item->get_proyecto() . apex_qs_separador . $item->get_id()) ) ?>" class='cat-item'>
				<img src='<? echo recurso::imagen_apl("items/carpeta.gif") ?>' border='0'></a>
			  </td>
	
	          <td  class='cat-arbol-carpeta-info'  width='2%'>
				<? if($item->es_de_menu()){?>
							<img src='<? echo recurso::imagen_apl("items/menu.gif") ?>' border='0'>
				<? }else{ echo gif_nulo(); } ?>
			  </td>
			  
	          <td  class='cat-arbol-carpeta-info' width='2%'>
				<a href="#" class='cat-item' onclick="cascada('<?=$item->get_id()?>',true);return false;"><? echo recurso::imagen_apl("check_cascada_on.gif",true,null,null,"ACTIVAR hijos") ?></a>
			  </td>
	          <td  class='cat-arbol-carpeta-info' width='2%'>
				<a href="#" class='cat-item' onclick="cascada('<?=$item->get_id()?>',false);return false;"><? echo recurso::imagen_apl("check_cascada_off.gif",true,null,null,"DESACTIVAR hijos") ?></a>
			  </td>
	          <td  class='cat-arbol-carpeta-info'  colspan='<? echo (($maximo-$nivel)+1)?>'><?=$item->get_nombre()?></td>
		  
			<? }else{
				//******************< Items comunes >*************************
			?>
			          <td  class='cat-arbol-item'  width='2%'>
						<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/items/propiedades", array(apex_hilo_qs_zona => $item->get_proyecto() . apex_qs_separador . $item->get_id()) ) ?>" class='cat-item'>
						<img src='<? echo recurso::imagen_apl("items/item.gif") ?>' border='0'></a>
					  </td>
			          <td  class='cat-item-botones2'  width='2%'>
			<? if($item->es_de_menu()){?>
						<img src='<? echo recurso::imagen_apl("items/menu.gif") ?>' border='0'>
			<? }else{ echo gif_nulo(); } ?>
					  </td>
			          <td  class='cat-item-botones2'  width='2%'>
			<? 
				$extra = " camino='".implode(separador_camino, $item->get_camino())."' ";
				echo form::checkbox($prefijo_items.$item->get_id(),$grupo,$item->grupo_tiene_permiso($grupo), 'ef-checkbox', $extra); ?>
					  </td>
			          <td  class='cat-item-dato1'   colspan='<? echo ($maximo-$nivel)?>'><?=$item->get_nombre()?></td>
			          <td  class='cat-item-dato1' width='100' ><? echo $item->get_id(); ?></td>
			          <td  class='cat-item-botones2' width='2%' ><img src='<? echo recurso::imagen_apl("nota.gif") ?>' alt='<? echo "Propietario: ". $item->propietario(); ?>' border='0'></td>
			<?					  
		}
		echo "</tr>";
	}
	?>			
		<tr> 
          <td colspan="<? echo (5 + $maximo)?>" align="center" class="cat-item-categ1">
	<?
		echo form::submit($boton_post,$boton_post_nombre);
		?>
		  </td>
        </tr>
	</table>
	</div>
	<br>
	<br>
	<? 
	echo form::cerrar();
?>
<script languaje='javascript'>
function cascada(item_padre, estado)
{
	var item_actual, regex_item_padre, x, ultimo_elemento;
	formulario = document.<? echo $formulario ?>;
	regex_item_padre = '<? echo separador_camino; ?>' + item_padre;
	for (x=0 ; x < formulario.elements.length ; x++)	
	{
		if(formulario.elements[x].type=="checkbox")
		{
			var camino = formulario.elements[x].getAttribute('camino');
			if (camino.indexOf(regex_item_padre) != -1 || (item_padre == '' && camino == ''))
			{
				formulario.elements[x].checked = estado;
			}
		}
	}
}
</script>