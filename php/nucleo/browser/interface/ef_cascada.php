<?php
include_once("nucleo/browser/interface/ef.php");// Elementos de interface

class ef_cascada_fl extends ef
{
	function ef_cascada_fl($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function obtener_input()
	{
		//Funcion que recarga al combo
		$html = "
<script>
function {$this->id_form}_cargar_combo(datos)
{
	//Busco la referencia al SELECT
	s_ = document.{$this->nombre_formulario}.{$this->id_form};
	s_.options.length = 0;//Borro las opciones que existan
	//Creo los OPTIONS recuperados
	for (id in datos){
		s_.options[s_.options.length] = new Option(datos[id], id);
	}
	//Escondo el GIF de buscando DATOS...
	if (ie||ns6)
	var div_img=document.all? document.all['{$this->id_form}_div'] : document.getElementById? document.getElementById('{$this->id_form}_div') : '';
	div_img.style.visibility='hidden';
} 	
function {$this->id_form}_buscar_datos()
{
	var parametros = '';
	consultar_info('toba','/basicos/ef/cascada_fl',parametros,'{$this->id_form}_cargar_combo');
	//Descubro el GIF de buscando DATOS...
	if (ie||ns6)
	var div_img=document.all? document.all['{$this->id_form}_div'] : document.getElementById? document.getElementById('{$this->id_form}_div') : '';
	div_img.style.visibility='visible';
}
</script>";
		$html .= "<table class='tabla-0'><tr><td>";
		$html .= form::select($this->id_form, null, null);
		$html .= "</td><td>";
		$html .= form::button("cargar","Cargar","onclick=\"{$this->id_form}_buscar_datos()\"");
		$html .= "</td><td>";
		$html .= "<div id='{$this->id_form}_div' style='visibility: hidden'>";
		$html .= recurso::imagen_apl('cargando',true,15,15);
		$html .= "</div>";
		$html .= "</td></tr></table>";
		return $html;
	}

}
//########################################################################################################
//########################################################################################################
?>