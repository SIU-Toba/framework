<?php
require_once("objeto_ci_me.php");	//Ancestro de todos los OE


class objeto_ci_me_tab extends objeto_ci_me
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
	
	function objeto_ci_me_tab($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto_ci_me($id);
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  SALIDA  HTML --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_barra_navegacion()
/*
 	@@acceso: interno
	@@desc: Genera la INTERFACE de los TABS
*/
	{
		echo "<table width='100%' class='tabla-0'>\n";
		echo "<tr>";
		//echo "<td width='1'  class='tabs-solapa-hueco'>".gif_nulo(3,1)."</td>";
		foreach($this->info_ci_me_etapa as $etapa)
		{
			if($this->etapa_actual == $etapa["posicion"]){
				echo "<td class='tabs-solapa-sel'>";
				echo form::button($etapa["submit"],$etapa["etiqueta"],null,"tabs-boton-sel");
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}else{
				echo "<td class='tabs-solapa'>";
				echo form::submit($etapa["submit"],$etapa["etiqueta"],"tabs-boton");
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}
		}
		echo "<td width='90%'  class='tabs-solapa-hueco'>".gif_nulo()."</td>\n";
		echo "</tr>";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
}
?>
	