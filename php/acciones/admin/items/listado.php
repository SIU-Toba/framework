<?
	$cronometro->marcar('basura');	

	include_once("nucleo/browser/interface/ef.php");
	include_once("nucleo/browser/interface/arbol_carpetas.php");
	$menu = new arbol_carpetas();
	$parametros["valores"] = $menu->obtener_combo();
	$ef_solo_menu_par['valor'] = 1;
	$ef_carpeta =& new ef_combo("clase","",apex_sesion_post_proyecto,apex_sesion_post_proyecto,"Seleccione la parte del arbol que desea visualizar.","","",$parametros);
	$ef_solo_menu =& new ef_checkbox("solo_menu","",apex_sesion_post_proyecto,apex_sesion_post_proyecto,"Mostrar el arbol solo_menu a items no incluidos en el menú","","",$ef_solo_menu_par);

	//Si se eligio una carpeta solo empieza a mostrar a partir de alli
	$carpeta_seleccionada = '';
	if(acceso_post()){
		$ef_carpeta->cargar_estado();
		$ef_solo_menu->cargar_estado();
		$carpeta = $ef_carpeta->obtener_estado();
		$solo_menu = $ef_solo_menu->obtener_estado();
		//Verifica que la opcion elegida no sea 'Todos'
		if($carpeta!='NULL') {
			//Guarda el valor elegido en el hilo
			$this->hilo->persistir_dato_global("carpeta",$carpeta);
			$carpeta_seleccionada = $carpeta;
		}else{
			$this->hilo->eliminar_dato_global("carpeta");
		}
		$this->hilo->persistir_dato_global("solo_menu",$solo_menu);
	}
	else{
		//Si existe el dato en el hilo, el combo aparece seleccionado
		if ($carpeta = $this->hilo->recuperar_dato_global("carpeta")) {
			$ef_carpeta->cargar_estado($carpeta);
			$carpeta_seleccionada = $carpeta;
		}
		if ($solo_menu = $this->hilo->recuperar_dato_global("solo_menu")) {
			$ef_solo_menu->cargar_estado($solo_menu);
		}
	}	
	
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------

	include_once("nucleo/lib/arbol_items_admin.php");
	if ($solo_menu != 1)
		$menu = false;
	else
		$menu = true;

	$catalogador = new arbol_items_admin($this, $menu);
	if ($carpeta_seleccionada != '') 
		$catalogador->set_carpeta_inicial($carpeta_seleccionada);
	$catalogador->ordenar();
	$total = $catalogador->obtener_cantidad_items();

	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------

	$cronometro->marcar('Consulto los items');	

	echo form::abrir("tipo_objeto",$this->vinculador->generar_solicitud(),null,"GET");
	echo "<table width='100%'  class='cat-item'><tr>";

	echo "<td class='lista-obj-titulo'>";
	echo recurso::imagen_apl('items/carpeta.gif',true,null,null,"Filtrar ITEMS por CARPETA");
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo $ef_carpeta->obtener_input();
	echo "</td>";
	echo "<td class='lista-obj-titulo' width='100%'></td>";
	
	//echo "</tr>";
	//echo "<tr>";
	//echo "<td class='lista-obj-titulo'>";
	//echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo "&nbsp;&nbsp;MENU".$ef_solo_menu->obtener_input();
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo form::image('filtrar',recurso::imagen_apl('cambiar_proyecto.gif',false));
	echo "</td>";
	//echo "<td class='lista-obj-titulo' width='100%'>ITEMS: $total</td>";
	echo "</tr></table>";
	echo form::cerrar();

?>		
<script language='javascript'>
	editor='item';
</script>
<?
	$catalogador->generar_html();
	$cronometro->marcar('Armo el listado');	
?>
