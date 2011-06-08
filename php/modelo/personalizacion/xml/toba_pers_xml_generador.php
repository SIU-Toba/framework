<?php

abstract class toba_pers_xml_generador
{
	/**
	 * @var toba_xml
	 */
    protected $plan;

	function init_plan($path)
	{
		$this->plan =  new toba_xml($path);
		$this->plan->abrir_elemento(toba_pers_xml_elementos::plan);
	}

	function finalizar_plan()
	{
		$this->plan->cerrar_elemento();	// </plan>
		$this->plan->cerrar_documento();
	}

	protected function agregar_al_plan($id, $path = null)
	{
		$this->plan->abrir_elemento(toba_pers_xml_elementos::tarea);
		$this->plan->add_atributo(toba_pers_xml_atributos::id, $id, true);

		if (!is_null($path)) {
			$this->plan->add_atributo(toba_pers_xml_atributos::path, $path, true);
		}

		$this->plan->cerrar_elemento();
	}

	protected function grabo_clave($estado)
	{
		return $estado == toba_personalizacion::registro_deleted
		       || $estado == toba_personalizacion::registro_updated;
	}
}
?>
