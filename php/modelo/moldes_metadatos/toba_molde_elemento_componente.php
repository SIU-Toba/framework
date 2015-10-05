<?php
/*
*	
*/
class toba_molde_elemento_componente extends toba_molde_elemento
{
	protected $clase_proyecto ='toba';
	protected $subclase;
	protected $molde_php = null;					// Clase molde de codigo PHP
	protected $carpeta_archivo;
	
	function ini()
	{
		//Averiguo cual es el punto de montaje /php
		$db = toba_contexto_info::get_db(); 
		$datos = $this->datos->tabla('base')->get_clave_valor(0);		
		$sql = 'SELECT pm_contexto FROM apex_proyecto WHERE proyecto = '.$db->quote($datos['proyecto']);
		$rs = $db->consultar_fila($sql);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'punto_montaje', $rs['pm_contexto']);						
		$this->datos->tabla('base')->set_fila_columna_valor(0,'clase',$this->clase);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'clase_proyecto',$this->clase_proyecto);
		$this->carpeta_archivo = $this->asistente->get_carpeta_archivos();		
	}
	
	function get_clase()
	{
		if (isset($this->clase)) {
			return $this->clase;
		}
	}
	
	//---------------------------------------------------
	//-- Extension de clases
	//---------------------------------------------------	

	/**
	*	Declara la extension del archivo, despues de su invocacion se puede usar
	*	el metodo php() para acceder al molde de la clase
	*/
	function extender($subclase, $archivo)
	{
		if(!isset($this->molde_php)) {
			$this->subclase = $subclase;
			$this->archivo = $archivo;
			$extensiones = toba_info_editores::get_clases_extendidas_proyecto($this->proyecto);
			$nombre_clase = get_nombre_clase_extendida($this->clase, $this->proyecto, $extensiones);			
			$this->molde_php = new toba_codigo_clase( $this->subclase, $nombre_clase);
			//Dejo la marca
			if (file_exists($this->archivo_absoluto())) {
				/*$txt = "Reemplazar archivo: " . $this->archivo_relativo();
				$ayuda = "Si no desea reemplazar el archivo, modifique el molde especificando otra carpeta de destino u otro prefijo para la generacion de clases.";
				$this->asistente->agregar_opcion_generacion( $this->get_id_opcion_archivo(), $txt, $ayuda );*/
			}
		}
	}
	
	/**
	 * Tiene una subclase?
	 * @return boolean
	 */
	function extendido()
	{
		return $this->datos->tabla('base')->get_columna('subclase') != '';
	}

	function php()
	{
		return $this->molde_php;	
	}
		
	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------	

	protected function get_codigo_php()
	{
		$existente = null;
		if (!$this->pisar_archivo && file_exists($this->archivo_absoluto())) {
			$existente = toba_archivo_php::codigo_sacar_tags_php(file_get_contents($this->archivo_absoluto()));
		}
		return $this->molde_php->get_codigo($existente);	
	}

	protected function asociar_archivo()
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'subclase',$this->subclase);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'subclase_archivo',$this->archivo_relativo());
	}
	
	function get_clave_componente_generado()
	{
		$datos = $this->datos->tabla('base')->get_clave_valor(0);
		return array('clave' => $datos['objeto'], 'proyecto' => $datos['proyecto']);
	}
	
	//----------------------------------------------------------
	//	Ubicacion mediante punto de montaje
	//----------------------------------------------------------
	function directorio_absoluto()
	{
		$datos = $this->datos->tabla('base')->get_fila(0);		
		if (!is_null($datos['punto_montaje']) && ($datos['punto_montaje'] !== 0)) { 	
			$punto_montaje = toba_pms::instancia()->get_instancia_pm_proyecto($datos['proyecto'], $datos['punto_montaje']);
			return $punto_montaje->get_path_absoluto(). '/' . $this->directorio_relativo();
		} else {
			return parent::directorio_absoluto();
		}
	}
}
?>