<?php

class ci_relacion_ml_dt extends toba_testing_pers_ci
{
	protected $s__id_fila_uno;
	protected $s__id_fila_dos;
	const id_siguiente = 2000;
	
	function ini__operacion()
	{
		$this->s__id_fila_uno = $this->dep('dt')->nueva_fila(array('id' => 2, 'descripcion' => 'Dos', 'orden' => 2));		
		$this->s__id_fila_dos = $this->dep('dt')->nueva_fila(array('id' => 1, 'descripcion' => 'Uno', 'orden' => 1));
	}
	
	//------- PRIMERA PANTALLA

	function evt__ml__modificacion($datos)
	{
		$this->dep('dt')->procesar_filas($datos);
		
		$datos = $this->dep('dt')->get_filas();
		if (count($datos) != 2) {
			throw new toba_error('Se esperaban dos registros');
		}
		$primera = current($datos);
		if ($primera[apex_datos_clave_fila] != $this->s__id_fila_uno) {
			throw new toba_error('Se esperaban que la PRIMERA fila tenga la clave '.
										$this->s__id_fila_uno . '. Pero se encontro la clave '.
										$primera[apex_datos_clave_fila]);
		}
		$segunda = next($datos);
		if ($segunda[apex_datos_clave_fila] != $this->s__id_fila_dos) {
			throw new toba_error('Se esperaban que la SEGUNDA fila tenga la clave '.
										$this->s__id_fila_dos . '. Pero se encontro la clave '.
										$segunda[apex_datos_clave_fila]);
		}		
	}
	
	function conf__ml($ml)
	{
		return $this->dep('dt')->get_filas();	
	}
	
	//------- SEGUNDA PANTALLA

	function evt__ml2__modificacion($datos)
	{
		//--- En el procesamiento el DT debe respetar los IDS que vienen del ML
		$this->dep('dt')->procesar_filas($datos);
		
		$datos = $this->dep('dt')->get_filas();
		if (count($datos) != 4) {
			throw new toba_error('Se esperaban cuatro registros');
		}
		$primera = $datos[2];
		if ($primera[apex_datos_clave_fila] != self::id_siguiente ) {
			throw new toba_error('Se esperaba que el id de la tercera fila fuera '.self::id_siguiente );
		}
		$segunda = $datos[3];
		if ($segunda[apex_datos_clave_fila] != self::id_siguiente + 1) {
			throw new toba_error('Se esperaba que el id de la cuarta fila fuera '.self::id_siguiente + 1);
		}			
	}
	
	function conf__ml2($ml)
	{
		$ml->set_proximo_id(self::id_siguiente);	
	}

}

?>