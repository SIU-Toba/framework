<?php
class ci_instituciones extends toba_ci
{
	
	protected $s__filtro;
	
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)){
			$cuadro->set_datos(soe_consultas::reporte_instituciones($this->s__filtro));
		}
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $form)
	{
		if (isset($this->s__filtro)){
			$form->set_datos( $this->s__filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

}
?>