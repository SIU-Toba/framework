<?php
/*
*	
*/
class toba_molde_elemento_componente extends toba_molde_elemento
{
	protected $clase_proyecto ='toba';
	protected $subclase;
	protected $molde_php;					// Clase molde de codigo PHP
	
	function ini()
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'clase',$this->clase);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'clase_proyecto',$this->clase_proyecto);
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
		if(!isset($this->subclase)) {
			$this->subclase = $subclase;
			$this->archivo = $archivo;
			$this->carpeta_archivo = $this->asistente->get_carpeta_archivos();
			$this->molde_php = new toba_codigo_clase( $this->subclase, $this->clase );
		}
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
		return $this->molde_php->get_codigo();	
	}

	protected function asociar_archivo()
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'subclase',$this->subclase);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'subclase_archivo',$this->archivo_relativo());
	}
	
	function get_clave_componente_generado()
	{
		$datos = $this->datos->tabla('base')->get_clave_valor(0);
		return array(	'clave' => $datos['objeto'],
						'proyecto' => $datos['proyecto']);
	}
}
?>