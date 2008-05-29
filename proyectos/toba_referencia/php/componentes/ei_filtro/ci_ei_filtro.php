<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_ei_filtro extends toba_ci
{
	protected $s__datos;
	
	function get_opciones()
	{
		return array(
			array('clave' => 'opcion_a', 'valor'=> 'Opción A'),
			array('clave' => 'opcion_b', 'valor'=> 'Opción B'),
			array('clave' => 'opcion_c', 'valor'=> 'Opción C')
		);
	}
	
	function evt__filtro__where($datos)
	{
		$this->s__datos = $datos;
		$where = $this->dep('filtro')->get_sql_where();
		$this->pantalla()->set_descripcion('Cláusula where generada: <pre>'.$where.'</pre>');
	}
	
	function evt__filtro__clausulas($datos)
	{
		$this->s__datos = $datos;
		$clausulas = $this->dep('filtro')->get_sql_clausulas();
		$this->pantalla()->set_descripcion('Cláusulas: <pre>'.print_r($clausulas, true).'</pre>');
	}

	function evt__filtro__datos($datos)
	{
	$this->pantalla()->set_descripcion('Datos: <pre>'.print_r($datos, true).'</pre>');		
		$this->s__datos = $datos;
	}
	
	
	
	function conf__filtro(toba_ei_filtro $filtro)
	{
		if (isset($this->s__datos)) {
			$filtro->set_datos($this->s__datos);
		}
	}
}

?>