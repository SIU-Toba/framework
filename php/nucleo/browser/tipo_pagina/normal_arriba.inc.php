<?php

	//echo "<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">\n";
	ei_html_cabecera($this->info["item_nombre"], recurso::css());
	
	//Cargo el menu milonic, si el punto de acceso lo solicita
	if(apex_pa_menu == "milonic") require_once("nucleo/browser/includes/menu_inferior.php");
	//Incluyo el javascript STANDART	
	require_once("nucleo/browser/includes/javascript.php");

	//----------------------------------------------------------------
	//------------------------> BARRA SUPERIOR -----------------------
	//----------------------------------------------------------------

	echo "\n\n";
	echo "<table width='100%' class='tabla-0'><tr>";

	//- 1 - LINK a el editor del ITEM
	echo "<td  class='barra-0-edit' width='1'>";
	$parametros = array(apex_hilo_qs_zona=> $this->info["item_proyecto"] . apex_qs_separador . $this->info["item"]);
	echo $this->vinculador->obtener_vinculo_a_item_cp("toba","/admin/items/propiedades",$parametros,true);
	echo "&nbsp;</td>";
	
	//- 2 - Link al editor de la ayuda del item
	echo "<td  class='barra-0-edit' width='1'>";
	$parametros = array(apex_hilo_qs_zona=> $this->info["item_proyecto"] . apex_qs_separador . $this->info["item"]);
	echo $this->vinculador->obtener_vinculo_a_item_cp("toba","/admin/items/info",$parametros,true);
	echo "&nbsp;</td>";


	echo "<td  class='barra-0-edit' width='1'>";
	$parametros = array('plantilla' => recurso::css());
	echo "<a href='".$this->vinculador->generar_solicitud('toba','/admin/objetos/editores/editor_estilos', $parametros)."' target='".apex_frame_lista."'>".
	recurso::imagen_apl("css.gif",true,null,null,"Editar los estilos del ítem.")."</a>";
	echo "&nbsp;</td>";
	
	//- 3 - Boton que dispara la cronometracion
	if(!isset($this->zona)){
	//SI existe una zona que todavia no se cargo, el vinculo no va a propagar al EDITABLE
	//En ese caso, el cronometrador tiene que posicionarse sobre la barra de la ZONA
		if($this->vinculador->consultar_vinculo("toba","/basicos/cronometro",true))
		{
			echo "<td  class='barra-0-edit' width='1'>";
			echo "<a href='".$this->vinculador->generar_solicitud(null,null,null,true,true)."'>".
			recurso::imagen_apl("cronometro.gif",true,null,null,"Cronometrar la ejecución del ITEM")."</a>";
			echo "&nbsp;</td>";
		}
	}
	echo "<td width='1' class='barra-0'>". gif_nulo(8,22) . "</td>";

	echo "<td width='99%' class='barra-0-tit'>".$this->info["item_nombre"]."&nbsp;&nbsp;</td>";


	if($this->cronometrar){
		$parametros = array("solicitud"=>$this->id);
		echo "<td  class='barra-0-tit' width='1'>&nbsp;";
		echo $this->vinculador->obtener_vinculo_a_item("toba","/basicos/cronometro",$parametros,true);
		echo "&nbsp;</td>";
	}
	
	if($this->existe_ayuda()){
		$parametros = array("item"=>$this->info["item"],"proyecto"=>$this->info["item_proyecto"]);
		echo "<td  class='barra-0-tit' width='1'>&nbsp;";
		echo $this->vinculador->obtener_vinculo_a_item("toba","/basicos/ayuda",$parametros,true);
		echo "&nbsp;</td>";
	}
	echo "</tr></table>\n\n";

	//----------------------------------------------------------------
?>
