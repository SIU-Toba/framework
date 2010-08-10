<?php
class ci_vinculos_servicios extends toba_testing_pers_ci
{
	protected $s__datos;
	protected $s__seleccionado = null;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = array(	'0' => array('columna1' =>'toba', 'columna2' => '1.3.0', 'columna3' => '49410'),
						'1' => array('columna1' => 'pilaga', 'columna2' => '1.9', 'columna3' => '47144'));
		$cuadro->set_datos($datos);
	}

	function servicio__print_columna()
	{
		$valor_clave = toba::memoria()->get_parametro('columna1');
		ei_arbol($valor_clave, 'La clave de la fila es');
		$valor_columna = toba::memoria()->get_parametro('columna3');
		ei_arbol($valor_columna, 'columna3');		
	}

	
	function vista_excel($salida)
	{
		$this->dep('cuadro')->vista_excel($salida);	
	}
	
	function vista_pdf($salida)
	{
		$this->dep('cuadro')->vista_pdf($salida);		
	}
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__formulario(toba_ei_formulario_ml $form_ml)
	{
		if (isset($this->s__datos)){
			$form_ml->set_datos($this->s__datos);
		}
	}

	function evt__formulario__procesar($datos)
	{
		foreach($datos as $klave => $valor)
		{
			if (isset($valor['adjunto'])) {
				$nombre_adjunto = $valor['adjunto']['name'];
				$img = toba::proyecto()->get_www_temp($nombre_adjunto);
				// Mover los adjuntos subidos al servidor del directorio temporal PHP a uno propio.
				move_uploaded_file($valor['adjunto']['tmp_name'], $img['path']);				
			}			
		}		
		
		$this->s__datos = $datos;
	}
	
	function servicio__descargar()
	{
		$encontre = false;
		$seleccionado = toba::memoria()->get_parametro('fila');
		$obj_data = null;
		$index = 0;
		while(!$encontre && $index < count($this->s__datos))
		{
			if ($this->s__datos[$index]['x_dbr_clave'] == $seleccionado){
					$obj_data = $this->s__datos[$index]['adjunto'];
					$encontre = true;
			}
			$index++;
		}
		
		if (! is_null($obj_data)){
			$archivo = toba::proyecto()->get_www_temp($obj_data['name']);			
			header("Content-type:{$obj_data['type']}");
			header("Content-Disposition: attachment; filename=\"{$obj_data['name']}\"");
			$handler = fopen($archivo['path'], 'r');
			if ($handler !== false){
				fpassthru($handler);	
			}				
		}else{
			echo 'No funciono como debia, REVISAME!';
		}
	}

	function servicio__ejecutar()
	{
		echo "ME estoy EJECUTANDO.... te gusta???";
	}
}
?>