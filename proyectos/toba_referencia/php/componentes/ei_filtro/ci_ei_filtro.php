<?php
require_once('condicion_funcion_es_activo.php');
php_referencia::instancia()->agregar(__FILE__);

class ci_ei_filtro extends toba_ci
{
	protected $s__datos;

	function conf__filtro($filtro)
	{
		if (isset($this->s__datos)) {
			$filtro->set_datos($this->s__datos);
		}
		
		$filtro->columna('importe')->set_condicion_default('es_menor_que');		//Seteo la condicion default para la columna importe
		$filtro->columna('deporte')->set_condicion_fija('es_igual_a', true);		//Fijo la condicion de la columna para que no se pueda cambiar
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
		if (isset($datos['nombre'])) {
			$this->dep('filtro')->columna('nombre')->condicion()->set_pre_evaluacion('funcion_x(');
			$this->dep('filtro')->columna('nombre')->condicion()->set_post_evaluacion(')');
		}
		//-----

		//-- Se aplica un condicion totalmente distinta al campo activo, para que llame a una funcion sql con el campo como parametro
		if (isset($datos['activo'])) {
			$this->dep('filtro')->columna('activo')->set_condicion(new condicion_funcion_es_activo());
		}
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


?>