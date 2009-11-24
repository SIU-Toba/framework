<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{
	protected $s__datos_form;
	protected $s__datos_form_cascada;
	
	function evt__form__modificacion($datos)
	{
		$this->s__datos_form = $datos;	
	}
	
	function conf__form()
	{
		return $this->s__datos_form;	
	}

	//--------- CASCADAS

	function get_persona_con_deporte($deporte)
	{
		$deporte = quote($deporte);
		$sql = "SELECT p.id, p.nombre 
				FROM 
					ref_persona p,
					ref_persona_deportes d
				WHERE 
					p.id = d.persona AND
					d.deporte = $deporte
				ORDER BY p.nombre
					
		";
		$fila = toba::db()->consultar_fila($sql, toba_db_fetch_num);
		return $fila;
	}

	function get_persona_nombre($id)
	{
		return consultas::get_persona_nombre($id);
	}

	function evt__form_cascada__modificacion($datos)
	{
		$this->s__datos_form_cascada = $datos;	
	}
	
	function conf__form_cascada()
	{
		return $this->s__datos_form_cascada;
	}

	
}

?>