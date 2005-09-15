<?php

class elemento_objeto_ci_pantalla implements recorrible_como_arbol
{
	protected $dependencias = array();
	protected $datos;
	
	function __construct($datos, $dependencias_posibles)
	{
		//etiqueta, posicion
		$this->datos = $datos;
		$this->asociar_dependencias($dependencias_posibles);
	}
	
	protected function asociar_dependencias($posibles)
	{
		$eis = explode(',', $this->datos['objetos']);
		$eis = array_map('trim', $eis);
		foreach ($posibles as $posible) {
			if (in_array($posible->rol_en_consumidor(), $eis)) {
				$this->dependencias[] = $posible;
			}
		}
	}
	
	public function tiene_dependencia($dep)
	{
		return in_array($dep, $this->dependencias);
	}

	//---- Recorrido como arbol
	function hijos()
	{
		return $this->dependencias;
	}
	
	function es_hoja()
	{
		return (count($this->dependencias) == 0);
	}
	
	function tiene_propiedades()
	{
		return false;
	}	
	
	function nombre_corto()
	{
		if ($this->datos['etiqueta'] != '')
			return str_replace('&', '', $this->datos['etiqueta']);
		else
			return $this->id();
	}
	
	function nombre_largo()
	{
		if (trim($this->datos['descripcion']) != '')
			return $this->datos['descripcion'];
		else
			return $this->nombre_corto();
	}
	
	function id()
	{
		return $this->datos['identificador'];
	}
	
	function iconos()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => recurso::imagen_apl('objetos/pantalla.gif', false),
			'ayuda' => 'Pantalla dentro del CI'
			);	
		return $iconos;
	}
	
	function utilerias()
	{
		$param_editores = array(apex_hilo_qs_zona=> $this->datos['objeto_ci_proyecto']. apex_qs_separador.
													$this->datos['objeto_ci'],
								"pantalla" => $this->datos['identificador']);	
		$iconos = array();
		$iconos[] = array(
			'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado a la pantalla",
			'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos_toba/crear",
								array('destino_tipo' => 'ci_pantalla', 
										'destino_proyecto' => $this->datos['objeto_ci_proyecto'],
										'destino_id' => $this->datos['objeto_ci'],
										'destino_pantalla' => $this->datos['pantalla']),
										false, false, null, true, "central")
		);

		$iconos[] = array(
				'imagen' => recurso::imagen_apl("objetos/editar.gif", false),
				'ayuda' => "Editar esta pantalla",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba", "/admin/objetos_toba/editores/ci", $param_editores,
																		false, false, null, true, "central")
		);
		return $iconos;	
	}	
	

}


?>