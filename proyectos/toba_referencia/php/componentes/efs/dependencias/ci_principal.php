<?php 
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{
	protected $s__datos = array();
	protected $s__i = 0;
	
	function conf__ml($componente)
	{
		$componente->set_datos($this->s__datos);
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
		$this->s__datos = $datos;
	}
	
	function ajax__combo_edit_get_opciones($parametros, toba_ajax_respuesta $respuesta)
	{
		$deportes = consultas::get_deportes($parametros);
		$respuesta->set($deportes);
	}	
	
}

?>