<?php 
class ci_sesiones extends toba_ci
{
	protected $s__filtro;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
		
	}

	//-----------------------------------------------------------------------------------
	//---- Config. ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- filtro -----------------------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	function conf__filtro($componente)
	{
		if(isset($this->s__filtro)) $componente->set_datos($this->s__filtro);
	}

	//---- sesiones ---------------------------------------------------------------------

	function evt__sesiones__seleccion($seleccion)
	{
	}

	function conf__sesiones($componente)
	{
		if(isset($this->s__filtro)) {
			//$componente->set_datos( admin_instancia:: )
		}
	}

	//---- solicitudes ------------------------------------------------------------------

	function evt__solicitudes__obs($seleccion)
	{
	}

	function conf__solicitudes($componente)
	{
	}
}

?>