<?php
//##############################################################################################
//####################################  CABECERA GENERAL #######################################
//##############################################################################################

	//echo "<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">\n";
	ei_html_cabecera($this->info["item_nombre"], recurso::css());
	require_once("nucleo/browser/includes/javascript.php");

//##############################################################################################
//#####################################  Retorno al ABM ########################################
//##############################################################################################

    $ef_popup = $this->hilo->obtener_parametro('ef_popup');
    if ($ef_popup == null)
    {
        $ef_popup = $this->hilo->recuperar_dato('ef_popup');
    }
	$this->hilo->persistir_dato('ef_popup', $ef_popup);

	echo "
<script language='javascript'>
function seleccionar(clave, descripcion)
{
	window.opener.popup_callback('". $ef_popup ."', clave, descripcion);
	window.close();
}
</script>";

//##############################################################################################
//#####################################  BARRA PRINCIPAL #######################################
//##############################################################################################

	echo "\n\n";
	echo "<table width='100%' class='tabla-0'><tr>";
	echo "<td width='1' class='barra-0'>". gif_nulo(8,22) . "</td>";
	echo "<td width='95%' class='barra-0-tit'>".$this->info["item_nombre"]."</td>";
	echo "</tr></table>\n\n";

//##############################################################################################
?>