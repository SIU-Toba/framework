<?php
class ci_firma_digital extends toba_ci
{
	protected $s__seleccion;
	protected $s__datos_persona;
	
	function evt__volver()
	{
		unset($this->s__seleccion);
		$this->set_pantalla("pant_listado");
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro) 
	{
		$sql = "SELECT 
					id, 
					nombre, 
					planilla_pdf_firmada 
				FROM 
					ref_persona
				ORDER BY id ASC
		";
		$rs = toba::db()->consultar($sql);
		$cuadro->set_datos($rs);
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__seleccion = $seleccion;
		$this->set_pantalla("pant_firma");
	}
	
	function conf__pant_firma(toba_ei_pantalla $pant)
	{
		$sql = "SELECT * FROM ref_persona WHERE id = ".quote($this->s__seleccion['id']);
		$this->s__datos_persona = toba::db()->consultar_fila($sql);
		$pant->set_descripcion($this->s__datos_persona['nombre']);
	}
	
	function conf__firmador(toba_ei_firma $firmador)
	{
	}
	
}

?>