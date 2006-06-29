<?php
//##############################################################################################
//####################################  CABECERA GENERAL #######################################
//##############################################################################################

	//echo "<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">\n";
	ei_html_cabecera($this->info["item_nombre"], recurso::css());
	require_once("javascript.php");


//##############################################################################################
//#####################################  BARRA PRINCIPAL #######################################
//##############################################################################################

	echo "\n\n";
	echo "<table width='100%' class='tabla-0'><tr>";
	echo "<td width='1' class='barra-0'>". gif_nulo(8,22) . "</td>";
	echo "<td width='95%' class='barra-0-tit'>".$this->info["item_nombre"]."</td>";
	echo "<td width='1' class='barra-0'><a href='javascript:window.close()'>". recurso::imagen_apl('finalizar_sesion.gif',true,null,null,'Cerrar Ventana') . "</a></td>";
	echo "</tr></table>\n\n";

//##############################################################################################
?>