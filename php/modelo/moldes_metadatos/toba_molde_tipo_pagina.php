<?php
class toba_molde_tipo_pagina extends toba_molde_elemento
{
	protected $clase = 'toba_tipo_pagina';

	function __construct($asistente)
	{
		$this->id = $this->asistente->get_id_elemento();
		$this->proyecto = $this->asistente->get_proyecto();
		//Busco el datos relacion correspondientes al componente
		$id = toba_info_editores::get_dr_de_clase($this->clase);			
		$componente = array('proyecto' => $id[0], 'componente' => $id[1]);
		$this->datos = toba_constructor::get_runtime($componente);
		$this->datos->inicializar();
		$datos = array(	'nombre'=>$this->clase.' generado automaticamente',
						'proyecto'=>$this->proyecto);
		$this->ini();
	}

	function ini()
	{
		$this->datos->set_fila_columna_valor(0,'proyecto',$this->proyecto);
	}

	function cargar($tipo)
	{
		$this->datos->cargar(array('proyecto' => $this->proyecto, 'pagina_tipo' => $tipo));
	}

	//--------------------------------------------------------------------------------------------------------
	function set_tipo_pagina($tipo)
	{
		$this->datos->set_fila_columna_valor(0, 'pagina_tipo', $tipo);
	}

	function set_descripcion($descripcion)
	{
		$this->datos->set_fila_columna_valor(0, 'descripcion', $descripcion);
	}

	function set_clase_nombre($clase)
	{
		$this->datos->set_fila_columna_valor(0, 'clase_nombre', $clase);
	}

	function set_clase_archivo($archivo)
	{
		$this->datos->set_fila_columna_valor(0, 'clase_archivo', $archivo);
	}

	function set_punto_montaje($pm)
	{
		$this->datos->set_fila_columna_valor(0, 'punto_montaje', $pm);						
	}
	
	//-----------------------------------------------------------------------------------------------------------
	function get_clave_componente_generado()
	{
		$datos = $this->datos->get_clave_valor(0);
		return array('tipo_pagina' => $datos['tipo_pagina'],
						'proyecto' => $datos['proyecto']);
	}
}
?>
