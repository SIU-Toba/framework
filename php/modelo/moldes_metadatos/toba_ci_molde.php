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
	protected $deps_pantallas_asoc = array();

	function ini()
	{
		parent::ini();
		$this->datos->tabla('prop_basicas')->set(array());
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
		$lista_dep = explode(',', $dep);
		foreach($lista_dep as $nombre_dep){
			$this->deps_pantallas_asoc[$pantalla][] = $nombre_dep;
		}
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
		$lista_evt = explode(',', $evento);
		foreach($lista_evt as $nombre_evt){
			$this->pantallas_evt_asoc[$pantalla][] = $nombre_evt;
		}
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
		$this->asociar_dependencias_a_pantalla();
		parent::generar();
	}
	
	function asociar_dependencia($id, $clave)
	{
		$datos = array('proyecto'=>$this->proyecto, 'objeto_proveedor'=>$clave, 'identificador'=> $id);
		$this->datos->tabla('dependencias')->nueva_fila( $datos );
	}

	function asociar_dependencias_a_pantalla()
	{
		//Doy de alta la asociacion entre pantallas y objetos_ei, primero ciclo por las pantallas
		foreach($this->deps_pantallas_asoc as $pantalla => $dependencias){
			//Seteo el cursor en la pantalla
			$id_fila = $this->mapeo_pantallas[$pantalla];
			$this->datos->tabla('pantallas')->set_cursor($id_fila);

			//Ciclo por las dependencias de la pantalla
			foreach($dependencias as $orden => $nombre_dep){
				$id = $this->datos->tabla('dependencias')->get_id_fila_condicion(array('identificador' => $nombre_dep));
				$this->datos->tabla('dependencias')->set_cursor(current($id));
				$this->datos->tabla('objetos_pantalla')->nueva_fila(array('orden' => $orden, 'dependencia' => $nombre_dep));
			}
		}
		$this->datos->tabla('pantallas')->resetear_cursor();
		$this->datos->tabla('dependencias')->resetear_cursor();
	}

	function asociar_eventos_a_pantallas()
	{
		//Doy de alta la asociacion entre pantallas y eventos
		foreach($this->pantallas_evt_asoc as $pantalla => $eventos){
			//Seteo el cursor en la pantalla
			$id_fila = $this->mapeo_pantallas[$pantalla];
			$this->datos->tabla('pantallas')->set_cursor($id_fila);

			foreach($eventos as $nombre_evt){
				$id_ev = $this->datos->tabla('eventos')->get_id_fila_condicion(array('identificador' => $nombre_evt));
				$this->datos->tabla('eventos')->set_cursor(current($id_ev));
				$this->datos->tabla('eventos_pantalla')->nueva_fila(array('identificador' => $nombre_evt));
			}
		}
		$this->datos->tabla('eventos')->resetear_cursor();
		$this->datos->tabla('pantallas')->resetear_cursor();
	}
}
?>