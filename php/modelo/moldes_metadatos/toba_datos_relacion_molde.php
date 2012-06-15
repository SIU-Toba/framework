<?php

/*
*	
*/
class toba_datos_relacion_molde extends toba_molde_elemento_componente_datos
{
	protected $clase = 'toba_datos_relacion';
	protected $tablas= array();
	protected $definiciones = array();
	protected $relaciones = array();
	protected $columnas_relacionadas = array();
	protected $pre_relacion_columnas = array();
	
	function ini()
	{
		parent::ini();
		$this->pisar_archivo = false;
		$this->carpeta_archivo = $this->asistente->get_carpeta_archivos_datos();
	}

	function cargar($id)
	{
		$this->datos->cargar(array('proyecto' => $this->proyecto, 'objeto' => $id));
	}

	function crear($nombre)
	{
		//Lanzo la generacion de los datos tabla, esto aun no genero los metadatos, solo las estructuras internas
		foreach($this->tablas as $tabla => $molde_dt){
			$this->asistente->generar_datos_tabla($this->tablas[$tabla], $tabla, $this->definiciones[$tabla]);
		}

		//Creo las lineas correspondientes en el datos relacion del molde.
		$datos = array('nombre' => $nombre . ' - DR ');
		$this->datos->tabla('base')->set($datos);
		$predeterminados = array(
			'ap' => 2,
			'sinc_lock_optimista' => 1,
			'sinc_orden_automatico' => 1
		);
		$this->datos->tabla('prop_basicas')->set($predeterminados);
	}

	function crear_relaciones()
	{
		$tablas_involucradas = array_keys($this->pre_relacion_columnas);
		foreach($tablas_involucradas as $par){
			list($tabla_padre, $tabla_hija) = explode('|', $par);
			if (! isset($this->relaciones[$tabla_padre])){
				throw new toba_error_asistentes("Molde DR, asociando tablas: La tabla $tabla_padre no existe en el conjunto de tablas padres.");
			}elseif (! in_array($tabla_hija , $this->relaciones[$tabla_padre])){
				throw new toba_error_asistentes("Molde DR, asociando tablas: La tabla $tabla_hija no existe entre el conjunto de tablas hijas de $tabla_padre.");
			}

			$this->columnas_relacionadas[$tabla_padre][$tabla_hija] = $this->pre_relacion_columnas[$par];
		}
	}

	function crear_metodo_consulta($metodo, $sql, $parametros=null)
	{
		foreach($this->tablas as $tabla => $molde_dt){
			 $molde_dt->crear_metodo_consulta($metodo, $sql, $parametros);
		}
	}

	//----------------------------------------------------------------------------------------------------------------//
	function set_ap($ap_clase, $ap_archivo)
	{
		//Recupero el punto de montaje del componente y lo coloco para el AP.
		$pm = $this->datos->tabla('base')->get_fila_columna(0, 'punto_montaje');		
		
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'ap_clase',$ap_clase);
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'ap_archivo',$ap_archivo);
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0, 'punto_montaje', $pm);
	}
	
	//----------------------------------------------------------------------------------------------------------------//
	function agregar_tabla($nombre_tabla)
	{
		$pm = $this->datos->tabla('base')->get_fila_columna(0, 'punto_montaje');		//Seteo el punto de montaje en la tabla nueva		
		$this->tablas[$nombre_tabla] = new toba_datos_tabla_molde($this->asistente);
		$this->tablas[$nombre_tabla]->set_punto_montaje($pm);
	}

	function agregar_relacion_tablas($tabla_padre, $tabla_hija)
	{
		$this->relaciones[$tabla_padre][] = $tabla_hija;
	}

	/**
	 * Funcion que agrega las columnas que relacionan 2 tablas
	 * @param mixed $tabla_padre
	 * @param mixed $tabla_hija
	 * @param mixed $columna_padre
	 * @param mixed $columna_hija
	 */
	function agregar_columnas_relacionadas($tabla_padre, $tabla_hija, $columna_padre, $columna_hija)
	{
		$index = $tabla_padre . '|' . $tabla_hija;
		$valor = array('columna_padre' => $columna_padre, 'columna_hija' => $columna_hija);
		$this->pre_relacion_columnas[$index][] = $valor;
	}

	function agregar_definicion_tabla($tabla, $def_fila)
	{
		$this->definiciones[$tabla] = $def_fila;
	}

	//----------------------------------------------------------------------------------------------------------------//
	function generar()
	{
		//Aca tengo que generar los datos tabla y la relacion
		foreach($this->tablas as $tabla => $molde_dt){
			$molde_dt->generar();
			$datos = $molde_dt->get_clave_componente_generado();
			$this->datos->tabla('dependencias')->nueva_fila(array('identificador' => $tabla, 'objeto_proveedor' => $datos['clave'], 'proyecto' => $datos['proyecto'], 'parametros_a' => 1, 'parametros_b' => 1));
		}

		//Alpha testing required..
		$fuente = $this->asistente->get_fuente();
		foreach($this->relaciones  as $tabla_padre => $tabla_hija){
			$dt_padre = toba_info_editores::get_dt_de_tabla_fuente($tabla_padre, $fuente, $this->proyecto);
			$dt_hijo = toba_info_editores::get_dt_de_tabla_fuente($tabla_hija, $fuente, $this->proyecto);
			$this->datos->tabla('relaciones')->nueva_fila(array('identificador' =>  "$tabla_padre -> $tabla_hija", 'padre_id' => $tabla_padre , 'hijo_id' => $tabla_hija, 'padre_objeto' => $padre_obj['id'], 'hijo_objeto' => $hijo_obj['id']));
			if (isset($this->columnas_relacionadas[$tabla_padre][$tabla_hija])){
				foreach($this->columnas_relacionadas[$tabla_padre][$tabla_hija] as $columnas){
						$this->datos->tabla('columnas_relacion')->nueva_fila(array('padre_clave' => $columnas['columna_padre'], 'hijo_clave' => $columnas['columna_hija']));
				}
			}
		}
		
		parent::generar();
	}	
}
?>