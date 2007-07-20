<?php 
class ci_asistente extends toba_ci
{
	protected $s__tipo;
	protected $datos_tipo_operacion;
	
	function ini()
	{
		if (isset($this->s__tipo)) {
			$this->cargar_editor_molde();
		}	
	}
	
	function cargar_editor_molde()
	{
		$info = toba_info_editores::get_lista_tipo_molde($this->s__tipo['tipo']);
		$ci = $info['ci'];
		$this->agregar_dependencia('asistente', 'toba_editor', $ci);	
		$this->dep('asistente')->set_molde_nuevo();
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
		$this->cargar_editor_molde();
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
	
	function evt__generar()
	{
		$this->dep('asistente')->sincronizar();
	}
	
	function evt__volver()
	{
		$this->set_pantalla('pant_tipo_operacion');	
	}	
}

?>