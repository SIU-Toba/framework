<?php 
class ci_ei_filtro_ml extends toba_ci
{
	protected $s__datos;
	
	function evt__filtro__actualizar($datos)
	{
		$this->s__datos = $datos;
		$where = $this->dep('filtro')->get_where();
		$this->pantalla()->set_descripcion('Cláusula where generada: <pre>'.$where.'</pre>');
	}
	

	function conf__filtro(toba_ei_filtro_ml $filtro_ml)
	{
		if (isset($this->s__datos)) {
			$filtro_ml->set_datos($this->s__datos);
		}
	}
}

?>