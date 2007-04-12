<?php
php_referencia::instancia()->agregar(__FILE__);

class extendion_cuadro_estetica extends toba_ei_cuadro
{
	/**
		Redefinir la CABECERA de todo el cuadro
	*/
	function html_cabecera()
	{
		
	}

	/**
		Redefinir la CABECERA del corte de control "B"
	*/
	function html_cabecera_cc_contenido__B(&$nodo)
	{
		
	}

	/**
		Redefinir el PIE del corte de control "B"
	*/
	function html_pie_cc_contenido__B(&$nodo)
	{

	}

	/**
		Redefinir el PIE del corte de control "A"
	*/
	function html_pie_cc_contenido__A(&$nodo)
	{
		//Llamar al procedimiento estandar
		$this->html_pie_cc_contenido();		
	}

	/**
		Redefinir el PIE de todo el cuadro
	*/
	function html_pie()
	{
		
	}
}
?>