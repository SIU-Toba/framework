<?
require_once("nucleo/lib/arbol_items.php");

class arbol_items_admin extends arbol_items
{
	
	function __contruct($menu=false)
	{
		parent::__contruct($menu);	
	}
	
	function generar_html()
	{
		if($this->mensaje!=""){
			echo ei_mensaje($this->mensaje,"info");	
			return;
		}
	$maximo = 10;//Maximo nivel de anidacion...
	foreach ($this->items_ordenados as $item)
	{

?>
		<table width="100%" class='tabla-0'>
        <tr> 
<?	
	//Indentado del arbol
	$nivel = $item['nivel'];
	for($a=0;$a<$nivel;$a++){
		echo "<td width='2%'  class='cat-arbol'>".gif_nulo(10,1)."</td>";
	}
	if($item["carpeta"]){
	//******************< Carpetas >*****************************
?>
          <td  class='cat-arbol-carpeta' width='2%'>
			<a href="<? echo $this->solicitud->vinculador->generar_solicitud("toba","/admin/items/carpeta_propiedades",array( apex_hilo_qs_zona => $item['item_proyecto'] .apex_qs_separador. $item["item"])) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/carpeta.gif",true,null,null,"Editar propiedades de la carpeta") ?>
			</a>
		  </td>
          <td  class='cat-arbol-carpeta-info' > 	
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
			<a href="<? echo $this->solicitud->vinculador->generar_solicitud("toba","/admin/items/carpeta_ordenar", array("padre_p"=>$item["item_proyecto"], "padre_i"=>$item["item"]) ) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/carpeta_ordenar.gif",true,null,null,"Ordena alfabéticamente los items incluídos en esta CARPETA") ?></a>
		  </td>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="<? echo $this->solicitud->vinculador->generar_solicitud("toba","/admin/items/carpeta_propiedades", array("padre_p"=>$item["item_proyecto"], "padre_i"=>$item["item"]) ) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/carpeta_nuevo.gif",true,null,null,"Crear SUBCARPETA en esta rama del CATALOGO") ?></a>
		  </td>
          <td  class='cat-arbol-carpeta-info' width='2%'>
			<a href="<? echo $this->solicitud->vinculador->generar_solicitud("toba","/admin/items/propiedades", array("padre_p"=>$item["item_proyecto"], "padre_i"=>$item["item"]) ) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
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
			<a href="<? echo $this->solicitud->vinculador->generar_solicitud("toba","/admin/items/propiedades",array( apex_hilo_qs_zona => $item['item_proyecto'] .apex_qs_separador. $item["item"])) ?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/item.gif",true,null,null,"Editar propiedades del ITEM") ?></a>
		  </td>

          <td width="2%" class='cat-item-botones2'>
<?	if($item["solicitud_tipo"]=="consola"){
		echo recurso::imagen_apl("solic_consola.gif",true,null,null,"Solicitud de CONSOLA");
	}elseif($item["solicitud_tipo"]=="wddx"){
		echo recurso::imagen_apl("solic_wddx.gif",true,null,null,"Solicitud WDDX");
	}else {
?>
		 	<a href="<? echo $this->solicitud->vinculador->generar_solicitud($item['item_proyecto'], $item["item"], null,false,false,null,true) ?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("items/instanciar.gif",true,null,null,"Ejecutar el ITEM") ?></a>
<? } ?>
		  </td>
          <td  class='<? echo $estilo ?>'  >
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
		  <td  class='<? echo $estilo ?>-nb' width='2%' ><?
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
		  <td  class='cat-item-botones2' width='2%' ><? echo recurso::imagen_apl("nota.gif",true,null,null,$item["item"]) ?></td>
<? } ?>
        </tr>
		</table>
<?		
		flush();
	}
?>
<?
	}
	
}
?>