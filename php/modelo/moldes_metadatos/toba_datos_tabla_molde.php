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
		$this->carpeta_archivo = $this->asistente->get_carpeta_archivos_datos();		
	}
	
	function crear($tabla)
	{
		$this->datos->tabla('prop_basicas')->nueva_fila(array('ap'=>1));	//Admin persistencia por defecto
		$this->datos->tabla('prop_basicas')->set_cursor(0);
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