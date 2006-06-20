<?php
require_once("nucleo/browser/clases/objeto_ci_me_tab.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");
require_once("nucleo/browser/clases/objeto_ei_formulario_ml.php");


//-------------------------------------------------------------------------------
//----  Controlador de INTERFACE
//-------------------------------------------------------------------------------

class objeto_ci_me_tab_p2 extends objeto_ci_me_tab
{
	function __construct($id)
	{
		parent::__construct($id);
	}

	function obtener_interface_3()
	{
		$this->cn->debug();
	}
}

//-------------------------------------------------------------------------------
//----  Formulario ML 1
//-------------------------------------------------------------------------------

class objeto_ei_formulario_ml_asignar extends objeto_ei_formulario_ml
//Este es el formulario que permite asignar
{
	function __construct($id)
	{
		parent::__construct($id);
	}
}

//-------------------------------------------------------------------------------
//----  Formulario 2
//-------------------------------------------------------------------------------

class objeto_ei_formulario_p2 extends objeto_ei_formulario
{
	function obtener_funciones_javascript()
	//Devuelve las funciones javascript del formulario
	//ATENCION: Falta la validacion de TOPES
	{
		parent::obtener_funciones_javascript();
		//Recupero los nombres de los EF
		$ef = $this->obtener_nombres_ef(); //ei_arbol($ef);
		echo " <script>\nfunction calcular_total(formulario) {\n";
		//Controlo que el porcentaje restado no sea superior al valor
		echo " if(parseFloat(formulario.{$ef['deuda_condona']}.value) > parseFloat(formulario.{$ef['deuda']}.value)){\n";
		echo " alert('Valor incorrecto');\n";
		echo " formulario.{$ef['deuda_condona']}.value='0';\n";
		echo " formulario.{$ef['deuda_condona']}.focus();\n";
		echo " }\n";
		echo " if(parseFloat(formulario.{$ef['interes_condona']}.value) > parseFloat(formulario.{$ef['interes']}.value)){\n";
		echo " alert('Valor incorrecto');\n";
		echo " formulario.{$ef['interes_condona']}.value='0';\n";
		echo " formulario.{$ef['interes_condona']}.focus();\n";
		echo " }\n";
		echo "formulario.{$ef['deuda_total']}.value = 
				parseFloat(formulario.{$ef['deuda']}.value) -
				parseFloat(formulario.{$ef['deuda_condona']}.value)\n";
		echo "formulario.{$ef['interes_total']}.value = 
				parseFloat(formulario.{$ef['interes']}.value) -
				parseFloat(formulario.{$ef['interes_condona']}.value)\n";
		echo "formulario.{$ef['total']}.value = 
				parseFloat(formulario.{$ef['deuda_total']}.value) +
				parseFloat(formulario.{$ef['interes_total']}.value)\n";
		echo " }\n";
		echo "calcular_total(document.{$this->nombre_formulario});\n";
		echo "</script>\n";
	}
}

//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
?>