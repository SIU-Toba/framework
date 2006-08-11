<?php

class ci_relacion_ml_dt extends objeto_ci
{
	protected $s__id_fila_uno;
	protected $s__id_fila_dos;
	
	function ini__operacion()
	{
		$this->s__id_fila_uno = $this->dep('dt')->nueva_fila(array('id' => 2, 'descripcion' => 'Dos', 'orden' => 2));		
		$this->s__id_fila_dos = $this->dep('dt')->nueva_fila(array('id' => 1, 'descripcion' => 'Uno', 'orden' => 1));
	}
	
	function evt__ml__modificacion($datos)
	{
		if (count($datos) != 2) {
			throw new excepcion_toba('Se esperaban dos registros');
		}
		$primera = current($datos);
		if ($primera[apex_datos_clave_fila] != $this->s__id_fila_uno) {
			throw new excepcion_toba('Se esperaban que la PRIMERA fila tenga la clave '.
										$this->s__id_fila_uno . '. Pero se encontro la clave '.
										$primera[apex_datos_clave_fila]);
		}
		$segunda = next($datos);
		if ($segunda[apex_datos_clave_fila] != $this->s__id_fila_dos) {
			throw new excepcion_toba('Se esperaban que la SEGUNDA fila tenga la clave '.
										$this->s__id_fila_dos . '. Pero se encontro la clave '.
										$segunda[apex_datos_clave_fila]);
		}		
		$this->dep('dt')->procesar_filas($datos);
	}
	
	function conf__ml($ml)
	{
		return $this->dep('dt')->get_filas();	
	}
	
	function conf__cuadro($cuadro)
	{
		return $this->dep('dt')->get_filas();		
	}
	
	function evt__cancelar()
	{
		$this->dep('dt')->resetear();
		parent::evt__cancelar();
	}
	
	function evt__procesar()
	{
		echo ei_mensaje("OK");
		$this->dep('dt')->resetear();
	}
	
}

?>