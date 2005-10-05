<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class ci_prueba extends objeto_ci
{
	protected $form1;// = array();

	//Generales

	function destruir()
	{
		parent::destruir();
		ei_arbol( $this->get_estado_sesion() );
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "form1";
		return $propiedades;	
	}

	function evt__procesar()
	{
		//parent::procesar();
		//$datos = consulta::get_jurisdicciones();
		//ei_arbol($datos);
	}

	function evt__pre_cargar_datos_dependencias__uno() 
	{
		$this->dependencias['form1']->colapsar();
	}


	function evt__cancelar()
	{
		dump_SESSION();
	}


	function obtener_html_contenido__uno()
	{
		$this->obtener_html_dependencias();
		echo "<h1>Contenido AD-HOC</h1>";	
	}

	function obtener_html_contenido__tres()
	{
		echo "<h1>Contenido AD-HOC</h1>";	
	}
/*
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		unset($eventos['cancelar']);
		return $eventos;
	}
	
	function get_lista_tabs()
	{
		$eventos = parent::get_lista_tabs();
		unset($eventos['dos']);
		return $eventos;
	}
*/
	//Dependencias

	function get_lista_ei__uno()
	{
		return array('form1','form_ml');	
	}
	
	function evt__form1__modificacion($datos)
	{
		
		$this->form1 = $datos;
	}	

	function evt__form1__carga()
	{
		return $this->form1;
	}		
}
?>