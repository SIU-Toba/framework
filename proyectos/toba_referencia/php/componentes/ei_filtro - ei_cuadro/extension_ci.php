<?php
php_referencia::instancia()->agregar(__FILE__);

class formateo_proyecto extends toba_formateo
{
	
	/**
		* @param integer $valor Cantidad total de segundos
		* @return cadena en formato H:M:S
		*/
	function formato_hora($valor)
	{
		 $segundos = str_pad($valor % 60, 2, 0, STR_PAD_LEFT);        
		$minutos = floor($valor / 60);
		$horas = floor($minutos / 60);
		$minutos = str_pad($minutos % 60, 2, 0, STR_PAD_LEFT);	 
		
		$desc = "$horas:$minutos:$segundos";
		 if ($this->tipo_salida != 'excel') {	
			return $desc;
		} else {
			return parent::formato_hora($desc);
		}
	}
}


class extension_ci extends toba_ci
{
	protected $s__filtro;
	protected $datos_estaticos = array(
			array( 'fecha' => '2004-05-20', 'importe' => 12500, 'hora' => '200', 'texto' => 'Llamada a Mamá'),
			array( 'fecha' => '2004-05-21', 'importe' => 22200, 'hora' => '200','texto' => 'Llamada a 0600'),
			array( 'fecha' => '2004-05-22', 'importe' => 4500, 'hora' => '200','texto' => 'Llamada a celular'),
			array( 'fecha' => '2005-05-20', 'importe' => 12500, 'hora' => '200','texto' => 'Llamada al exterior'),
			array( 'fecha' => '2005-05-21', 'importe' => 22200, 'hora' => 10000,'texto' => 'Llamada al teatro'),
			array( 'fecha' => '2005-05-22', 'importe' => 4500, 'hora' => '300','texto' => 'Llamada de atención')
		);
	
	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}
	
	function conf__filtro()
	{
		if (isset($this->s__filtro)) {
			return $this->s__filtro;
		}
	}
	

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_formateo_columna('hora', 'hora', 'formateo_proyecto');
		$cuadro->evento('seleccion')->set_alineacion_pre_columnas(true);    //Defino que el evento de seleccion se graficara antes de las columnas de datos
		$cuadro->evento('baja')->set_alineacion_pre_columnas(true);    //Defino que el evento de seleccion se graficara antes de las columnas de datos
		if (!isset($this->s__filtro) || $this->s__filtro['metodo'] == 'estatica') {
			return $this->datos_estaticos;
		} else {
			return $this->filtrar_importes();
		}
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		toba::notificacion()->agregar("Se seleccionó la fecha {$seleccion['fecha']}", 'info');
	}
	
	function evt__cuadro__baja($seleccion)
	{
		toba::notificacion()->agregar("Se quiere borrar la fecha {$seleccion['fecha']}", 'info');
	}
	
	function evt__cuadro__ordenar($param)
	{
		$columna = $param['columna']; 
		$sentido = $param['sentido']; 
		toba::notificacion()->agregar("Evento escuchado en php: Se quiere ordenar la columna $columna en orden $sentido", 'info'); 
		return true;
	}
	
	function evt__mi_evento()
	{
		$this->dependencia('cuadro')->deseleccionar();
		toba::notificacion()->agregar('Este evento borra la selección del cuadro (si es que la hay)', 'info');
	}

	function filtrar_importes()
	{
		//Esto normalmente se haría utilizando SQL...
		$retorno = array();
		foreach ($this->datos_estaticos as $dato) {
			if ($this->s__filtro['importe'] == '' || $this->s__filtro['importe'] < $dato['importe']) {
				$retorno[] = $dato;
			}
		}
		return $retorno;
	}

}
?>