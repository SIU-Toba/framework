<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_ei_filtro extends toba_ci
{
	protected $s__datos;

	function conf__filtro(toba_ei_filtro $filtro)
	{
		if (isset($this->s__datos)) {
			$filtro->set_datos($this->s__datos);
		}
	}
	
	function evt__filtro__where($datos)
	{
		$this->s__datos = $datos;
		$where = $this->dep('filtro')->get_sql_where();
		$this->pantalla()->set_descripcion('Cláusula where generada: <pre>'.$where.'</pre>');
	}
	
	function evt__filtro__where_particular($datos)
	{
		$this->s__datos = $datos;
		
		//-- Se cambia la condición de la cadena para que invoque una funcion durante la evaluacion
		$this->dep('filtro')->columna('nombre')->condicion()->set_pre_evaluacion('masajear_nombre(');
		$this->dep('filtro')->columna('nombre')->condicion()->set_post_evaluacion(')');
		//-----

		//-- Se aplica un condicion totalmente distinta al campo activo, para que llame a una funcion sql con el campo como parametro
		$this->dep('filtro')->columna('activo')->set_condicion(new condicion_funcion_es_activo());
		//-----
		
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
	
	
}

/**
 * Se crea una nueva condicion para el filtro de la columna 'activo'
 */
class condicion_funcion_es_activo extends toba_filtro_condicion 
{
	function get_sql($campo, $valor)
	{
		$valor = toba::db()->quote($valor);
		return "es_activo($campo) = $valor";
	}	
}

?>