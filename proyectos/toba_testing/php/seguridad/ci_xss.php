<?php 

class ci_xss extends toba_testing_pers_ci
{
	protected $prueba1;
	protected $prueba2;
	
	function ini()
	{
		$this->prueba1 = <<<EOT
';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>
EOT;
		$this->prueba2 = <<<EOT
</textarea>';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>
EOT;
	}
	
	function conf__form($form)
	{
		$datos = array(
			'editable' => $this->prueba1,
			'clave' => $this->prueba1,
			'fecha' => $this->prueba1,
			'moneda' => $this->prueba1,
			'numero' => $this->prueba1,
			'textarea' => $this->prueba2,
			'popup' => $this->prueba1,
			'popup_editable' => $this->prueba1,
			'upload' => $this->prueba1,
			'cuit' => $this->prueba1,
			'fijo' => $this->prueba1
		);
		$form->set_datos($datos);
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = array(
			array('a' => $this->prueba1, 'b' => "Esta columna permite ingresar <b>HTML</b>")
		);
		$cuadro->set_datos($datos);
	}
	
	function get_opciones_combo()
	{
		return array(
			array($this->prueba1, $this->prueba1)
		);	
	}
}

?>