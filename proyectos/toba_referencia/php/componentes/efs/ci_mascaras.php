<?php 

class ci_mascaras extends toba_ci
{
	protected $s__datos;

	
	function ini()
	{
		if (! isset($this->datos)) {	
			$this->s__datos = array(
						'numero_sin' => '123456.789',
						'fecha_sin' => '2006-10-26',
						
						'numero_original' => '123456.789',
						'fecha_original' => '2006-10-26',
						'moneda_original' => '123456.789',
						
						'numero_personal' => '123456.78',
						'moneda_personal' => '123456.789',
						'fecha_personal' => '2006-10-26'
					);
		}
	}
	
	function conf__form($componente)
	{
		return $this->s__datos;
	}
	
	function evt__form__modificacion($datos)
	{
		$this->s__datos = $datos;	
	}
}

?>