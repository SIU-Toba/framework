<?
	//------------------------------------------------------------
	class catalogador_items
	{
		protected $items_originales;
		protected $carpeta_inicial;
		
		function __construct($items_originales)
		{
			$this->items_originales = $items_originales;
			$this->carpeta_inicial = '';
		}

		function set_carpeta_inicial($nombre)
		{
			$this->carpeta_inicial = $nombre;
		}
		
		function ordenar_por_menu()		
		{
			$carpeta = $this->buscar_carpeta_inicial();
			if ($carpeta !== false)
			{
				$items = $this->ordenar_recursivo($carpeta, 0);
				return $items;
			}
			else
				return array();
		}
		
		protected function buscar_carpeta_inicial()
		{
			foreach ($this->items_originales as $item)
			{
				if ($item['item'] == $this->carpeta_inicial)
					return $item;
			}
			//El item inicial no esta en el listado
			echo ei_mensaje("La carpeta inicial seleccionada está incluida sólo en la vista extendida", "error");
			return false;
		}
		
		protected function es_padre_de($carpeta, $item)
		{
			if ($item['item'] == '')
				return false;
				
			return $item['padre'] == $carpeta['item'];
		}
		
		protected function es_carpeta($item)
		{
			return $item['carpeta'] == 1;
		}

		/**
		*	Recorrido en profundidad del arbol
		* 	Se muestran primero las carpetas y ordenados por el 'orden' gracias al ORDER BY de la consulta
		*/
		protected function ordenar_recursivo($carpeta, $nivel)
		{
			$items_ordenados = array();
			$carpeta['nivel'] = $nivel;
			$items_ordenados[] = $carpeta;
			foreach ($this->items_originales as $item)
			{
				if ($this->es_padre_de($carpeta, $item))
				{
					if ($this->es_carpeta($item)) //Caso recursivo
					{
						$items_ordenados = array_merge($items_ordenados, $this->ordenar_recursivo($item, $nivel + 1));
					}
					else
					{
						$item['nivel'] = $nivel + 1;
						$items_ordenados[] = $item;
					}
				}
			}
			return $items_ordenados;
		}
	}
	//------------------------------------------------------------
	
	$maximo = 10;//Maximo nivel de anidacion...
	$cronometro->marcar('basura');	

	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------

	include_once("nucleo/browser/interface/ef.php");
	$parametros["no_seteado"] = "Arbol COMPLETO";
	//TODO: Esta sql no coincide con el orden en que se muestra el arbol...
	$parametros["sql"] = "
		SELECT item, nombre
		FROM apex_item 
		WHERE 
			carpeta = 1 AND 
			proyecto = '".$this->hilo->obtener_proyecto()."' AND 
			item <> ''
		ORDER BY nombre
		";	
	$ef_extendido_par['valor'] = 1;
	$tipo_clase =& new ef_combo_db("clase","",apex_sesion_post_proyecto,apex_sesion_post_proyecto,"Seleccione la parte del arbol que desea visualizar.","","",$parametros);
	$ef_extendido =& new ef_checkbox("extendido","",apex_sesion_post_proyecto,apex_sesion_post_proyecto,"Mostrar el arbol extendido a items no incluidos en el menú","","",$ef_extendido_par);
	
	$carpeta_seleccionada = '';
	//Si se eligio una carpeta solo empieza a mostrar a partir de alli
	if(acceso_post()){
		$tipo_clase->cargar_estado();
		$ef_extendido->cargar_estado();
		$clase = $tipo_clase->obtener_estado();
		$extendido = $ef_extendido->obtener_estado();
		//Verifica que la opcion elegida no sea 'Todos'
		if($clase!='NULL') {
			//Guarda el valor elegido en el hilo
			$this->hilo->persistir_dato_global("carpeta",$clase);
			$carpeta_seleccionada = $clase;
		}else{
			$this->hilo->eliminar_dato_global("carpeta");
		}
		$this->hilo->persistir_dato_global("extendido",$extendido);
	}
	else{
		//Si existe el dato en el hilo, el combo aparece seleccionado
		if ($clase = $this->hilo->recuperar_dato_global("carpeta")) {
			$tipo_clase->cargar_estado($clase);
			$carpeta_seleccionada = $clase;
		}
		if ($extendido = $this->hilo->recuperar_dato_global("extendido")) {
			$ef_extendido->cargar_estado($extendido);
		}
	}	
	
	if ($extendido != 1)
		$where = "			AND		(i.menu = 1 OR i.item = '')";
	else
		$where = "";

	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------

		
	//Trae TODOS los items
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	p.proyecto 						as item_proyecto,
					i.orden							as orden,
					p.descripcion 					as pro_des,
					i.item		 					as item,
					i.padre		 					as padre,
					i.nombre	 					as nombre,
					i.carpeta						as carpeta,
					i.menu							as menu,
					i.usuario						as usuario,
					i.actividad_buffer_proyecto 	as act_buf_p,
					i.actividad_buffer				as act_buf,
					i.actividad_patron_proyecto		as act_pat_p,
					i.actividad_patron				as act_pat,
					i.actividad_accion				as act_acc,
					i.publico						as publico,
					i.solicitud_registrar			as registrar,
					i.solicitud_registrar_cron		as crono,
					i.solicitud_tipo				as solicitud_tipo,
					(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as objetos
			FROM 	apex_item i,
					apex_proyecto p
			WHERE	i.proyecto = p.proyecto
            AND     i.proyecto = '".$this->hilo->obtener_proyecto()."'
			AND 	solicitud_tipo <> 'fantasma'
			$where
			ORDER BY i.carpeta, i.orden
			";

	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Catogo de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF)
		$items = $rs->GetArray();
	else
		$items = array();

	$catalogador = new catalogador_items($items);
	if ($carpeta_seleccionada != '')
		$catalogador->set_carpeta_inicial($carpeta_seleccionada);
		
	$items_ordenados = $catalogador->ordenar_por_menu();
	$total = count($items_ordenados);


	$cronometro->marcar('Consulto los items');	
?>
<table width="100%"  class='cat-item'>
<tr> 
<?
	echo "<td class='lista-obj-titulo'>&nbsp;";
	echo recurso::imagen_apl('items/carpeta.gif',true,null,null,"Filtrar ITEMS por CARPETA");
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo form::abrir("tipo_objeto",$this->vinculador->generar_solicitud(),null,"GET");
	echo $tipo_clase->obtener_input();
	echo "Extendido".$ef_extendido->obtener_input();
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";
	echo form::image('filtrar',recurso::imagen_apl('cambiar_proyecto.gif',false));
	echo "</td>";
	echo "<td class='lista-obj-titulo'>";	
	echo "</td>";
	echo form::cerrar();
?>		
    <td class="lista-obj-titulo" width="100%">[ <? echo $total ?> ]</td>
</tr>
</table>
<script language='javascript'>
	editor='item';
</script>
<table width="100%" class='cat-item'>
<? 	
	foreach ($items_ordenados as $item)
	{
?>
        <tr> 
<?	
	//Indentado del arbol
	$nivel = $item['nivel'];
	for($a=0;$a<$nivel;$a++){
		echo "<td width='2%'  class='cat-arbol'>".gif_nulo(4,1)."</td>";
	}
	if($item["carpeta"]){
	//******************< Carpetas >*****************************
?>
          <td  class='cat-arbol-carpeta' width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/carpeta_propiedades",array( apex_hilo_qs_zona => $item['item_proyecto'] .apex_qs_separador. $item["item"])) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/carpeta.gif",true,null,null,"Editar propiedades de la carpeta") ?>
			</a>
		  </td>
          <td  class='cat-arbol-carpeta-info' colspan='<? echo (($maximo-$nivel)+5)?>'> 	
		  <? echo $item["nombre"] ?></td>
		  <td  class='cat-arbol-carpeta-info' width='2%'>
			<? if($item["menu"]){
		  			echo recurso::imagen_apl("items/menu.gif",true,null,null,"La CARPETA esta incluido en el MENU del PROYECTO");
		  	}?>
		  </td>
<? 
// Nueva carpeta, nuevo item
?>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/carpeta_ordenar", array("padre_p"=>$item["item_proyecto"], "padre_i"=>$item["item"]) ) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/carpeta_ordenar.gif",true,null,null,"Ordena alfabéticamente los items incluídos en esta CARPETA") ?></a>
		  </td>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/carpeta_propiedades", array("padre_p"=>$item["item_proyecto"], "padre_i"=>$item["item"]) ) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/carpeta_nuevo.gif",true,null,null,"Crear SUBCARPETA en esta rama del CATALOGO") ?></a>
		  </td>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/propiedades", array("padre_p"=>$item["item_proyecto"], "padre_i"=>$item["item"]) ) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/item_nuevo.gif",true,null,null,"Crear ITEM hijo en esta rama del CATALOGO") ?></a>
		  </td>
		  
<? }else{
	//******************< Items comunes >*************************
	//¿Que tipo de actividad tiene asociada? (buffer, patron, accion)

		//-- Es un BUFFER?
		if(!(($item['act_buf']==0) && 
			($item['act_buf_p']=="toba"))){
				$tipo_actividad = "buffer";
				$estilo = "cat-item-dato5";
        }//--- Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON
		elseif(!(($item['act_pat']=="especifico") && 
			($item['act_pat_p']=="toba"))){
            	$tipo_actividad = "patron";
				$estilo = "cat-item-dato4";
        }//--- Es una ACCION. 
        else{
            $tipo_actividad = "accion";
			$estilo = "cat-item-dato1";
        }
?>
          <td  class='cat-arbol-item'  width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/propiedades",array( apex_hilo_qs_zona => $item['item_proyecto'] .apex_qs_separador. $item["item"]))?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/item.gif",true,null,null,"Editar propiedades del ITEM") ?></a>
		  </td>

          <td width="2%" class='cat-item-botones2'>
<?	if($item["solicitud_tipo"]=="consola"){
		echo recurso::imagen_apl("solic_consola.gif",true,null,null,"Solicitud de CONSOLA");
	}elseif($item["solicitud_tipo"]=="wddx"){
		echo recurso::imagen_apl("solic_wddx.gif",true,null,null,"Solicitud WDDX");
	}else {
?>
		 	<a href="<? echo $this->vinculador->generar_solicitud($item['item_proyecto'], $item["item"]) ?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("items/instanciar.gif",true,null,null,"Ejecutar el ITEM") ?></a>
<? } ?>
		  </td>
          <td  class='<? echo $estilo ?>'   colspan='<? echo ($maximo-$nivel + 1)?>'>
		  &nbsp;<? echo $item["nombre"]; ?></td>
          <td  class='<? echo $estilo ?>-nb' width='2%'><?
		if($item["crono"] == 1){
			  //Se registra la solicitud?
			echo recurso::imagen_apl("cronometro.gif",true,null,null,"El ITEM se cronometra");
		}			
?>
</td>
          <td  class='<? echo $estilo ?>-nb' width='2%' ><?
		if($item["publico"] == 1){
			  //Es un item publico?
			echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"ITEM público");
		}			
?></td>
		  <td  class='<? echo $estilo ?>-nb' width='2%' colspan='2'><?
		if($item["registrar"] == 1){
			  //Se registra la solicitud?
			echo recurso::imagen_apl("solicitudes.gif",true,null,null,"El ITEM se registra");
		}			
?></td>
		  <td  class='<? echo $estilo ?>-nb' width='2%'><? 
	    if($item["menu"] == 1){
		  		//Se encuentra en el menu?
			echo recurso::imagen_apl("items/menu.gif",true,null,null,"El ITEM esta incluido en el MENU del PROYECTO");
		}
?></td>
		  <td  class='cat-item-dato3' width='2%' ><? echo $item["objetos"] ?></td>
		  <td  class='cat-item-botones2' width='2%' ><? echo recurso::imagen_apl("nota.gif",true,null,null,$item["nombre"]) ?></td>
<? } ?>
        </tr>
<?		
		flush();
	}
?>
</table>
<?
	$cronometro->marcar('Armo el listado');	
?>
