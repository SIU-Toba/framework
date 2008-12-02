<?php 
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{
	protected $s__datos;
	protected $s__datos_ml;
	protected $s__i = 0;

	//---------------------------------------------------
	//-- FORM COMUN
	//---------------------------------------------------
	
	function conf__formulario($componente) 
	{
		if (isset($this->s__datos)) {
			$componente->set_datos($this->s__datos);
		}		
	}
	
	
	function evt__formulario__modificacion($datos)
	{
		$this->s__datos = $datos;
	}
	
	//---------------------------------------------------
	//-- ML
	//---------------------------------------------------
	
	function conf__ml($componente)
	{
		if (isset($this->s__datos_ml)) {
			$componente->set_datos($this->s__datos_ml);
		}
	}
	
	function evt__ml__pedido_registro_nuevo()
	{
		$fila = array();
		$fila['oculto'] = "Oculto: {$this->s__i}";
		$this->dep('ml')->set_registro_nuevo($fila);
		$this->s__i++;
	}

	function evt__ml__modificacion($datos)
	{
		$this->s__datos_ml = $datos;
	}
	
	function ajax__combo_edit_get_opciones($parametros, toba_ajax_respuesta $respuesta)
	{
		$parametros['nombre'] = $parametros[0];
		$deportes = consultas::get_deportes($parametros);
		$salida = array();
		foreach ($deportes as $dep) {
			$otro[0] = $dep['id'];
			$otro[1] = $dep['nombre'];
			$salida[] = $otro;
		}
		$respuesta->set($salida);
	}	
}

?>