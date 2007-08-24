<?php
/*
*	
*/
class toba_ci_molde extends toba_molde_elemento_componente_ei
{
	protected $clase = 'toba_ci';
	protected $deps = array();
	protected $mapeo_pantallas;
	protected $orden_pantalla = 0;

	function ini()
	{
		parent::ini();
		$this->datos->tabla('prop_basicas')->nueva_fila(array());
		$this->datos->tabla('prop_basicas')->set_cursor(0);
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	

	function agregar_pantalla($identificador, $etiqueta=null)
	{
		$datos = array('identificador'=>$identificador, 'etiqueta'=>$etiqueta, 'orden'=>$this->orden_pantalla);
		$id = $this->datos->tabla('pantallas')->nueva_fila($datos);
		$this->mapeo_pantallas[$identificador] = $id;
		$this->orden_pantalla++;
	}
	
	function asociar_pantalla_dep($pantalla, $dep)
	{
		if(!isset($this->mapeo_pantallas[$pantalla])){
			throw new toba_error_asistentes('Molde CI, asociando a pantallas: La pantalla solicitada no existe.');
		}
		if(is_object($dep)){
			foreach($this->deps as $id => $d) {
				if ($d === $dep) {
					$dep = $id;	
					continue;
				}
			}
		} else {
			if(!isset($this->deps[$dep])){
				throw new toba_error_asistentes('Molde CI, asociando a pantallas: La dependencia solicitada no existe.');
			}
		}
		$id_fila = $this->mapeo_pantallas[$pantalla];
		$this->datos->tabla('pantallas')->agregar_dependencia_pantalla($id_fila,$dep);
	}
	
	function asociar_pantalla_evento($pantalla, $evento)
	{
		if(!isset($this->mapeo_pantallas[$pantalla])){
			throw new toba_error_asistentes('Molde CI, asociando a pantallas: La pantalla solicitada no existe.');
		}
		if(is_object($evento)){
			$evento = $evento->get_identificador();	
		} else {
			if(!isset($this->eventos[$evento])){
				throw new toba_error_asistentes('Molde CI, asociando a pantallas: El evento solicitado no existe.');
			}
		}
		$id_fila = $this->mapeo_pantallas[$pantalla];
		$this->datos->tabla('pantallas')->agregar_evento_pantalla($id_fila,$evento);
	}

	function set_alto($alto)
	{
		if((strpos($alto,'%')===false) && (strpos($alto,'px')===false)) {
			throw new toba_error_asistentes("MOLDE CUADRO: El alto debe definirse con el tipo de medida asociado ('%' o 'px'). Definido: $alto");
		}
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'alto',$alto);
	}
	
	//---------------------------------------------------
	//-- Manejo de SUBCOMPONENTES
	//---------------------------------------------------	

	function agregar_dep($tipo, $id)
	{
		$clase = $tipo . '_molde';
		$this->deps[$id] = new $clase($this->asistente);
		//Asignacion a pantallas
		return $this->deps[$id];
	}

	function dep($id)
	{
		if(!isset($this->deps[$id])){
			throw new toba_error_asistentes("La dependencia '$id' no existe");
		}
		return $this->deps[$id];
	}

	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------
		
	function generar()
	{
		foreach($this->deps as $id => $dep) {
			$dep->generar();
			$clave = $dep->get_clave_componente_generado();
			$this->asociar_dependencia($id, $clave['clave']);
		}
		parent::generar();
	}
	
	function asociar_dependencia($id, $clave)
	{
		$datos = array('proyecto'=>$this->proyecto, 'objeto_proveedor'=>$clave, 'identificador'=> $id);
		$this->datos->tabla('dependencias')->nueva_fila( $datos );
	}
}
?>