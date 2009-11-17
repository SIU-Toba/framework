<?php
class ci_servicios extends toba_ci
{
	protected $s__echo_input;
	//-----------------------------------------------------------------------------------
	//---- form_echo --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_echo(toba_ei_formulario $form)
	{
		if (isset($this->s__echo_input)) {
			$form->set_datos($this->s__echo_input);
		}
	}

	function evt__form_echo__enviar($datos)
	{
		$this->s__echo_input = $datos;
		$payload = '<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia"><text>';
		$payload .= $this->s__echo_input['texto'];
		$payload .= '</text></ns1:eco>';
		$servicio = toba::servicio_web('pruebas');
		$respuesta = $servicio->request($payload);
		toba::notificacion()->info($respuesta->str);
	}

}

?>
