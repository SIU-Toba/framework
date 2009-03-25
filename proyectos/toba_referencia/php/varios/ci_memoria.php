<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_memoria extends toba_ci
{
	protected $s__interna = null;

	function evt__formulario__modificacion($datos)
	{
		if (isset($datos['interna'])) {
			$this->s__interna = $datos['interna'];
		}
		if (isset($datos['operacion'])) {
			toba::memoria()->set_dato_operacion('temp', $datos['operacion']);
		}
		if (isset($datos['proyecto'])) {
			toba::memoria()->set_dato('temp', $datos['proyecto']);
		}
		if (isset($datos['instancia'])) {
			toba::memoria()->set_dato_instancia('temp', $datos['instancia']);
		}
	}

	function conf__formulario($componente)
	{
		$datos['interna'] = $this->s__interna;
		$datos['operacion'] = toba::memoria()->get_dato_operacion('temp');
		$datos['proyecto'] = toba::memoria()->get_dato('temp');
		$datos['instancia'] = toba::memoria()->get_dato_instancia('temp');
		$componente->set_datos($datos);
	}
	
	function evt__limpiar_proyecto()
	{
		toba::memoria()->eliminar_dato('temp');
	}
	
	function evt__limpiar_instancia()
	{
		toba::memoria()->eliminar_dato_instancia('temp');
	}
}

?>