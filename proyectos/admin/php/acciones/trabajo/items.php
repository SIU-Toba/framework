<?
	function refrescar_listado_item_apex()
	//Esta funcion refresca el LISTADO de la izquierda cuando se modifico
	//el estado de existencia de un EDITABLE y esta tiene que impactar allado
	{
		echo "<script language'javascript'>";
		echo "if(parent.".apex_frame_lista.".editor == 'item') parent.".apex_frame_lista.".location.reload()";
		echo "</script>";
	}
	
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		

	enter();
	echo "<div align='center'>\n";
	$maximo = 10;//Maximo nivel de anidacion...(Tope maximo estimado, que quede olgado)
	$formulario = "portafolio_items";
	$prefijo_items = "item_";//Prefijo de los checkbox
	$boton_post = "guardar_portafolio_item";
	$boton_post_nombre = "Guargar";
	
	if( ($_SERVER["REQUEST_METHOD"]=="POST") && 
	($_POST[$boton_post]==$boton_post_nombre) )
	//SI hay un POST, y estuvo disparado por este formulario
	{
		//-[1]- Busco los items que tengo que guardar en el portafolio.
		foreach($_POST as $etiqueta => $valor)
		{
			if((substr($etiqueta,0,strlen($prefijo_items)))==$prefijo_items){
				$nodo = trim(substr($etiqueta,strlen($prefijo_items)));
				$items[] = $nodo;
				$carpetas[] = substr($nodo, 0, strrpos($nodo,"/") );
			}
		}			
		//Junto los ITEMs y las CARPETAS
		$claves = array_merge($items, $carpetas);
		$claves = array_unique($claves);
		//ei_arbol($claves,"CLAVES");
		//------------------------------------------------------------------------------

		//-[2]- Realizo la TRANSACCION en la base
		$db["instancia"][apex_db_con]->Execute("BEGIN TRANSACTION");
		//1) Borro los permisos existentes
		$sql = "DELETE FROM apex_et_item WHERE usuario = '".
				$this->hilo->obtener_usuario()."' AND item_proyecto = '".$this->hilo->obtener_proyecto()."';\n";
		if($db["instancia"][apex_db_con]->Execute($sql) === false)
		{
			echo ei_mensaje("CAMBIAR DESPUES!! - " .$db["instancia"][apex_db_con]->ErrorMsg());
			$rs = $db["instancia"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
		}
		else{
			$ok = true;
			if(is_array($claves)){
				foreach($claves as $clave){
					//2) Inserto los permisos ACTUALIZADOS
					$sql = "INSERT INTO apex_et_item (usuario, item_proyecto, item) 
							VALUES ('".$this->hilo->obtener_usuario()."','".$this->hilo->obtener_proyecto()."','$clave');\n";
					if($db["instancia"][apex_db_con]->Execute($sql) === false)
					{
						echo ei_mensaje("Ha ocurrido un error ELIMINANDO los items del portafolios - " .$db["instancia"][apex_db_con]->ErrorMsg());
						$rs = $db["instancia"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
						$ok = false;
						break;//Salgo del foreach
					}
				}
			}
			//COMMIT
			if($ok){
				echo ei_mensaje("El portafolio ha sido actualizado correctamente",null,null,350);
				$rs = $db["instancia"][apex_db_con]->Execute("COMMIT TRANSACTION");
			}
		}
		refrescar_listado_item_apex();
 	}
	//------------------------------------------------------------------------------
?>
<script languaje='javascript'>
function cascada(item_padre, estado)
{
	var item_actual, regex_item_padre, x, ultimo_elemento;
	formulario = document.<? echo $formulario ?>;
	regex_item_padre = '<? echo $prefijo_items ?>' + item_padre + '/';
	for (x=0 ; x < formulario.elements.length ; x++)	
	{
		if(formulario.elements[x].type=="checkbox")
		{
			item_actual = formulario.elements[x].name;
			if (item_actual.search(regex_item_padre) != -1)
			{
				formulario.elements[x].checked = estado;
			}
		}
	}
}
</script>
<?
	include_once("nucleo/browser/interface/form.php");
			
	//Cuantos items hay?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_item";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}
			
	echo form::abrir($formulario, $this->vinculador->generar_solicitud(null,null,null,true));
?>
<table class='cat-item' align='center' width='450'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "
			SELECT 	i.proyecto			as proyecto,
					i.item		 		as item,
					i.padre		 		as padre,
					i.nombre	 		as nombre,
					i.carpeta			as carpeta,
					i.menu				as menu,
					g.usuario			as permiso
			FROM 	apex_item i
					LEFT OUTER JOIN apex_et_item g
						ON	i.item = g.item 
						AND i.proyecto = g.item_proyecto
						AND g.usuario = '".$this->hilo->obtener_usuario()."'
			WHERE	i.proyecto = '".$this->hilo->obtener_proyecto()."'
			AND		i.solicitud_tipo <> 'fantasma'
			AND		(i.publico <> 1 OR i.publico IS NULL)
			ORDER BY 2,4;";		
	//dump_sql( $sql );
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","ASIGNACION de permisos - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td colspan="<?echo ($maximo)+3?>"  class="cat-item-categ1">
<?
	echo form::submit($boton_post,$boton_post_nombre);
?>
		  </td>
        </tr>

<?
	while(!$rs->EOF)
	{ 
?>
        <tr> 
<?	
	//Indentado del arbol
	$nivel = substr_count($rs->fields["item"], "/");
	for($a=0;$a<$nivel;$a++){
		echo "<td width='2%'  class='cat-arbol'>".gif_nulo(4,1)."</td>";
	}
	if($rs->fields["carpeta"]){
	//******************< Carpetas >*****************************
?>
          <td  class='cat-arbol-carpeta' width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/items/carpeta_propiedades", array(apex_hilo_qs_zona => $rs->fields["pro_id"] . apex_qs_separador . $rs->fields["item"]) ) ?>" class='cat-item'>
			<img src='<? echo recurso::imagen_apl("items/carpeta.gif") ?>' border='0'></a>
		  </td>

          <td  class='cat-arbol-carpeta-info'  width='2%'>
<? if($rs->fields["menu"]){?>
			<img src='<? echo recurso::imagen_apl("items/menu.gif") ?>' border='0'>
<? }else{ echo gif_nulo(); } 
// AGREGAR SI EL ITEM ESTA EN EL PORTAFOLIO DE OTRO USUARIO
?>
		  </td>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="#" class='cat-item' onclick="cascada('<? echo $rs->fields["item"] ?>',true);return false;"><? echo recurso::imagen_apl("check_cascada_on.gif",true,null,null,"ACTIVAR hijos") ?></a>
		  </td>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="#" class='cat-item' onclick="cascada('<? echo $rs->fields["item"] ?>',false);return false;"><? echo recurso::imagen_apl("check_cascada_off.gif",true,null,null,"DESACTIVAR hijos") ?></a>
		  </td>
          <td  class='cat-arbol-carpeta-info'  colspan='<? echo (($maximo-$nivel)+1)?>'><? echo $rs->fields["item"] ?></td>
		  
<? }else{
	//******************< Items comunes >*************************
?>
          <td  class='cat-arbol-item'  width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/items/propiedades", array(apex_hilo_qs_zona => $rs->fields["pro_id"] . apex_qs_separador . $rs->fields["item"]) ) ?>" class='cat-item'>
			<img src='<? echo recurso::imagen_apl("items/item.gif") ?>' border='0'></a>
		  </td>
          <td  class='cat-item-botones2'  width='2%'>
<? if($rs->fields["menu"]){?>
			<img src='<? echo recurso::imagen_apl("items/menu.gif") ?>' border='0'>
<? }else{ echo gif_nulo(); } ?>
		  </td>
          <td  class='cat-item-botones2'  width='2%'>
<? echo form::checkbox($prefijo_items.$rs->fields["item"],$this->hilo->obtener_usuario(),$rs->fields["permiso"]) ?>
		  </td>
          <td  class='cat-item-dato1' colspan='<? echo ($maximo-$nivel)?>'><? echo strrchr($rs->fields["item"],"/")?></td>
<? } ?>
        </tr>
<?		$rs->movenext();	
		}
?>
        <tr> 
          <td colspan="<?echo ($maximo)+3?>" align="center" class="cat-item-categ1">
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
	}

		
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>