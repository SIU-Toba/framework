<?php 
class ci_asistente extends toba_ci
{
	protected $s__tipo;

	function ini()
	{
		if (isset($this->s__tipo)) {
			$info = toba_info_editores::get_lista_tipo_molde($this->s__tipo['tipo']);
			$ci = $info['ci'];
			$this->agregar_dependencia('asistente', 'toba_editor', $ci);
		}	
	}
	
	//-----------------------------------------------------------------------------------
	//---- Elegir tipo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	function conf__form_tipo_operacion()
	{
		if (isset($this->s__tipo)) {
			return $this->s__tipo;
		}
	}
	
	function evt__form_tipo_operacion__modificacion($datos)
	{
		$this->s__tipo = $datos;
	}	

	//-----------------------------------------------------------------------------------
	//---- Editar ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	function conf__pant_edicion()
	{

		$this->pantalla()->agregar_dep('asistente');		
	}
	
	function evt__siguiente()
	{
		$this->set_pantalla('pant_edicion');	
	}
	
	function evt__volver()
	{
		$this->set_pantalla('pant_tipo_operacion');	
	}	
}

?>