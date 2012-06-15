<?php
class toba_molde_zona extends toba_molde_elemento
{
	protected $clase = 'toba_zona';

	function __construct($asistente)
	{
		$this->asistente = $asistente;
		$this->asistente->registrar_molde($this);
		$this->id = $this->asistente->get_id_elemento();
		$this->proyecto = $this->asistente->get_proyecto();
		//Busco el datos relacion correspondientes al componente
		$id = toba_info_editores::get_dr_de_clase($this->clase);			//TODO:Esto quizas no lo encuentre
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

	function cargar($zona)
	{
		$this->datos->cargar(array('proyecto' => $this->proyecto, 'zona' => $zona));
	}

	//--------------------------------------------------------------------------------------------------------
	function set_identificador_zona($zona)
	{
		$this->datos->set_fila_columna_valor(0, 'zona', $zona);
	}

	function set_descripcion($descripcion)
	{
		$this->datos->set_fila_columna_valor(0, 'nombre', $descripcion);
	}

	function set_archivo($archivo)
	{
		$this->datos->set_fila_columna_valor(0, 'archivo', $archivo);
	}

	function set_clase_consulta($clase)
	{
		$this->datos->set_fila_columna_valor(0, 'consulta_clase', $clase);
	}

	function set_archivo_consulta($archivo)
	{
		$this->datos->set_fila_columna_valor(0, 'consulta_archivo', $archivo);
	}

	function set_metodo_consulta($metodo)
	{
		$this->datos->set_fila_columna_valor(0, 'consulta_metodo', $metodo);
	}

	function set_punto_montaje($pm)
	{
		$this->datos->set_fila_columna_valor(0, 'punto_montaje', $pm);						
	}
	
	//-----------------------------------------------------------------------------------------------------------
	function get_clave_componente_generado()
	{
		$datos = $this->datos->get_clave_valor(0);
		return array('zona' => $datos['zona'],
						'proyecto' => $datos['proyecto']);
	}
}
?>
