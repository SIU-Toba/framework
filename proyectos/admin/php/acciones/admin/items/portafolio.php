<?
$maximo = 10;//Maximo nivel de anidacion...

	$cronometro->marcar('basura');	

	include_once("nucleo/browser/interface/ef.php");

	//Cuantos items hay?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_et_item
            WHERE   item_proyecto = '".$this->hilo->obtener_proyecto()."'
			AND		usuario ='".$this->hilo->obtener_usuario()."'";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}	

	$cronometro->marcar('Consulto los items');

	
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td class="lista-obj-titulo" width="99%">ITEMS [ <? echo $total ?> ]</td>
	<td class="lista-obj-titulo" width="1%"><? echo $this->vinculador->obtener_vinculo_a_item('toba','/trabajo/items',array(apex_hilo_qs_zona=>$this->hilo->obtener_usuario()),true)?></td>
</tr>
</table>
<script language='javascript'>
	editor='item';
</script>
<table width="100%" class='cat-item'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	p.proyecto 						as item_proyecto,
					p.orden							as orden,
					p.descripcion 					as pro_des,
					i.item		 					as item,
					i.padre		 					as padre,
					i.nombre	 					as nombre,
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
					(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as objetos,
					eiu.usuario						as checkout
			FROM 	apex_item i,
					apex_proyecto p,
					apex_et_item ei LEFT OUTER JOIN
					apex_et_item eiu ON ei.item = eiu.item AND eiu.usuario <> ei.usuario AND ei.item_proyecto = eiu.item_proyecto
			WHERE	i.proyecto = p.proyecto
			AND 	i.proyecto = ei.item_proyecto
			AND		i.item	   = ei.item
            AND     i.proyecto = '".$this->hilo->obtener_proyecto()."'
			AND 	ei.usuario = '".$this->hilo->obtener_usuario()."'
			ORDER BY 4,2";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Catogo de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
	while(!$rs->EOF)
	{
?>
        <tr> 
<?	//******************< Items comunes >*************************
	//¿Que tipo de actividad tiene asociada? (buffer, patron, accion)

		//-- Es un BUFFER?
		if(!(($rs->fields['act_buf']==0) && 
			($rs->fields['act_buf_p']=='admin'))){
				$tipo_actividad = "buffer";
				$estilo = "cat-item-dato5";
        }//--- Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON
		elseif(!(($rs->fields['act_pat']=="especifico") && 
			($rs->fields['act_pat_p']=='admin'))){
            	$tipo_actividad = "patron";
				$estilo = "cat-item-dato4";
        }//--- Es una ACCION. 
        else{
            $tipo_actividad = "accion";
			$estilo = "cat-item-dato1";
        }
?>
          <td  class='cat-arbol-item'  width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/items/propiedades",array( apex_hilo_qs_zona => $rs->fields['item_proyecto'] .apex_qs_separador. $rs->fields["item"]))?>" target="<? echo  apex_frame_centro ?>"  class='cat-item'>
			<? echo recurso::imagen_apl("items/item.gif",true,null,null,"Editar propiedades del ITEM") ?></a>
		  </td>

          <td width="2%" class='cat-item-botones2'>
<?	if($rs->fields["solicitud_tipo"]=="consola"){
		echo recurso::imagen_apl("solic_consola.gif",true,null,null,"Solicitud de CONSOLA");
	}elseif($rs->fields["solicitud_tipo"]=="wddx"){
		echo recurso::imagen_apl("solic_wddx.gif",true,null,null,"Solicitud WDDX");
	}else {
?>
		 	<a href="<? echo $this->vinculador->generar_solicitud($rs->fields['item_proyecto'], $rs->fields["item"]) ?>" target="<? echo  apex_frame_centro ?>">
				<? echo recurso::imagen_apl("items/instanciar.gif",true,null,null,"Ejecutar el ITEM") ?></a>
<? } ?>
		  </td>
          <td  class='<? echo $estilo ?>' colspan='<? echo ($maximo-$nivel + 1)?>'>
		  &nbsp;<? echo $rs->fields["item"];?></td>
          <td  class='<? echo $estilo ?>-nb' width='2%'><?
		if($rs->fields["crono"] == 1){
			  //Se registra la solicitud?
			echo recurso::imagen_apl("cronometro.gif",true,null,null,"El ITEM se cronometra");
		}			
?>
</td>
          <td  class='<? echo $estilo ?>-nb' width='2%' ><?
		if($rs->fields["checkout"] != ""){
			  //El item esta en el portafolio de otro usuario? ?>
			<a href="<? echo $this->vinculador->generar_solicitud('toba','/admin/items/checkouts',array( apex_hilo_qs_zona => 'toba' .apex_qs_separador. $rs->fields["item"])) ?>" target="<? echo  apex_frame_centro ?>">
<?			echo recurso::imagen_apl("contramano.gif",true,null,null,"ITEM utilizado por otro usuario");
		}		
		if($rs->fields["publico"] == 1){
			  //Es un item publico?
			echo recurso::imagen_apl("usuarios/usuario.gif",true,null,null,"ITEM público");
		}			
?></td>
		  <td  class='<? echo $estilo ?>-nb' width='2%' ><?
		if($rs->fields["registrar"] == 1){
			  //Se registra la solicitud?
			echo recurso::imagen_apl("solicitudes.gif",true,null,null,"El ITEM se registra");
		}			
?></td>
		  <td  class='<? echo $estilo ?>-nb' width='2%'><? 
	    if($rs->fields["menu"] == 1){
		  		//Se encuentra en el menu?
			echo recurso::imagen_apl("items/menu.gif",true,null,null,"El ITEM esta incluido en el MENU del PROYECTO");
		}
?></td>
		  <td  class='cat-item-dato3' width='2%' ><? echo $rs->fields["objetos"] ?></td>
		  <td  class='cat-item-botones2' width='2%' ><? echo recurso::imagen_apl("nota.gif",true,null,null,$rs->fields["nombre"]) ?></td>
        </tr>
<?		$rs->movenext();
		flush();
	}
}else{ ?>
	<tr><td class='cat-item-dato3'> No se encuentran ITEMS en su PORTAFOLIO</td></tr>
<?
}
?>
</table>
<?
	$cronometro->marcar('Armo el listado');	
?>
