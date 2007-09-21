<?php
/*
*	
*/
class toba_datos_tabla_molde extends toba_molde_elemento_componente_datos
{
	protected $clase = 'toba_datos_tabla';
	protected $columnas = array();
	
	function ini()
	{
		parent::ini();
		$this->pisar_archivo = false;
		$this->carpeta_archivo = $this->asistente->get_carpeta_archivos_datos();	
	}
	
	function crear($tabla)
	{
		$this->datos->tabla('prop_basicas')->set(array('ap'=>1));	//Admin persistencia por defecto
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'tabla',$tabla);		
	}
	
	function cargar($id)
	{
		$this->datos->cargar(array('proyecto' => $this->proyecto, 'objeto' => $id));
	}
	
	function get_tabla_nombre()
	{
		return $this->datos->tabla('prop_basicas')->get_columna('tabla');	
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------		
	
	function actualizar_campos()
	{
		$this->datos->actualizar_campos();
	}

	function set_ap($subclase, $archivo)
	{
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'ap',0);
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'ap_clase',$subclase);
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'ap_archivo',$archivo);
	}

	function permitir_modificar_pks()
	{
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'modificar_claves',1);		
	}

	//-- Columnas ---------------------------------------

	function agregar_columna($identificador, $tipo)
	{
		$this->columnas[$identificador] = new toba_molde_datos_tabla_col($identificador, $tipo);
		return $this->columnas[$identificador];
	}

	function columna($identificador)
	{
		if(!isset($this->columnas[$identificador])) {
			throw new toba_error_asistentes('Molde formulario: El ef solicitado no existe');	
		}
		return $this->columnas[$identificador];
	}

	//---------------------------------------------------
	//-- API de subclase
	//---------------------------------------------------		

	function archivo_relativo()
	{
		return $this->archivo;		
	}

	function directorio_absoluto()
	{
		$path_proyecto = toba::instancia()->get_path_proyecto($this->proyecto);
		return  $path_proyecto . '/php/';
	}
	
	/**
	 * Para el datos tabla, al querer acceder a la extension se crea sola ya que es global al proyecto
	 *
	 * @return unknown
	 */
	function php()
	{
		if (! isset($this->molde_php)) {
			if (! $this->extendido()) {
				$clase = $this->get_tabla_nombre();
				$archivo = $this->carpeta_archivo.'/'.$clase.'.php';
			} else {
				//-- Ya estaba extendido previamente, se carga la clase
				$clase = $this->datos->tabla('base')->get_columna('subclase');
				$archivo = $this->datos->tabla('base')->get_columna('subclase_archivo');
			}
			$this->extender($clase, $archivo);			
		}
		return $this->molde_php;	
	}	
	
	
	function crear_metodo_consulta($metodo, $sql, $parametros=null)
	{
		$param_metodo = isset($parametros)? array('$filtro=array()') : null;
		$metodo = $this->asistente->crear_metodo_consulta($metodo, $sql, $param_metodo);
		$this->php()->agregar($metodo);
	}

	
	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------

	
	function generar()
	{
		foreach($this->columnas as $columna) {
		 	$this->datos->tabla('columnas')->nueva_fila($columna->get_datos());
		}
		parent::generar();
	}
}
?>