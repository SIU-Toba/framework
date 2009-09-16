<?php

class toba_analizador_logger_fs
{
	protected $archivo;
	protected $filtros;
	protected $ultimo_lugar;
	protected $procesar_entidades_html = true;
	
	function __construct($archivo)
	{
		$this->archivo = $archivo;
	}

	function procesar_entidades_html($activar=true)
	{
		$this->procesar_entidades_html = $activar;
	}
		
	function analizar_cuerpo($log)
	{
		$cuerpo = array();
		$niveles = toba::logger()->get_niveles();
		$texto = trim(substr($log, strpos($log, toba_logger::fin_encabezado) + strlen(toba_logger::fin_encabezado), strlen($log)));
		$patron = "/\[(";
		$patron .= implode("|", $niveles);
		$patron .= ")\]/";
		
		$res = preg_split($patron, $texto, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		//Se mezclan el nivel y el mensaje en un arreglo
		for ($i = 0; $i < count($res); $i+=2) {
			$mensaje = $res[$i+1];
			$bracket_cierra = strpos($mensaje, ']');
			$proy = substr($mensaje, 1, $bracket_cierra-1);
			$mens = trim(substr($mensaje, $bracket_cierra+1));
			$cuerpo[] = array('nivel' => $res[$i], 'proyecto'=>$proy, 'mensaje' => $mens);
			
		}
		return $cuerpo;
	}
	
	function analizar_encabezado($log)
	{
		$encabezado = substr($log, 0, strpos($log, toba_logger::fin_encabezado));
		$pares = explode("\r\n", trim($encabezado));
		$basicos = array();
		foreach ($pares as $texto) {
			$pos = strpos($texto, ":");
			$clave = substr($texto,0, $pos);
			$valor = substr($texto, $pos+1, strlen($texto));
			$basicos[strtolower(trim($clave))] = trim($valor);
		}
		return $basicos;
	}	
		

	function get_pedido($seleccion)
	{
		//Pedir el ultimo es un caso especial porque se trata con mas eficiencia, a menos que haya filtros por lo cual el ultimo pedido puede estar a mitad del archivo
		if ($seleccion == 'ultima') {
			if (! $this->existen_filtros_extra()){
				return $this->get_ultimo_pedido();
			}else{
				if (! isset($this->ultimo_lugar)){
					$this->ultimo_lugar = $this->get_cantidad_pedidos();
				}
				$seleccion = $this->ultimo_lugar;
			}
		}
		//Trata de encontrar el n-esimo pedido en el archivo
		//Este metodo es mucha mas ineficiente que obtener el ultimo
		$logs = $this->get_logs_archivo();
		return $logs[$seleccion-1];
	}
	
	/**
	 * Recorre en inversa el archivo tratando de encontrar el limite de la ultima seccion
	 * @return array Texto del ultimo pedido, ¿Queda algo antes?
	 */
	function get_ultimo_pedido()
	{
		$total = filesize($this->archivo);
		$fp = fopen($this->archivo, "rb");
		$franja = 50 * 1024; //Se leen los ultimos 50 KB en reversa
		$franja_acum = $franja;
		$pos = 0;
		$encontrado = false;
		$hay_algo_antes = false;
		do {
			$pos = (abs($pos - $franja) > $total) ? -$total : $pos-$franja;
			fseek($fp, $pos, SEEK_END);
			$hay_mas_para_leer = (abs($pos) < $total);
			$acumulado = fread($fp, $franja_acum);
			$ocurrencia = strrpos($acumulado, toba_logger::separador);
			if ($ocurrencia !== false) {
				//Se encontro el separador, una parte del acumulado pertenece a este pedido
				$encontrado = true;
				$acumulado = substr($acumulado, $ocurrencia + strlen(toba_logger::separador));
				$hay_algo_antes =  $hay_mas_para_leer || ($ocurrencia !== 0);
			}
			$franja_acum += $franja;
		} while (!$encontrado && $hay_mas_para_leer);
		
		fclose($fp);
		if (isset($this->filtros) && (!empty($this->filtros))){
			if (! $this->cumple_criterio_filtro($acumulado)){
					return null;
			}
		}		
		return $acumulado;
	}
	
	function get_logs_archivo()
	{
		if (!file_exists($this->archivo)) {
			return array();	
		}
		$texto = trim(file_get_contents($this->archivo));
		$logs = explode(toba_logger::separador , $texto);
		if (count($logs) > 0) {
			//Borra el primer elemento que siempre esta vacio
			array_splice($logs, 0 ,1);
		}

		$logs_filtrados = array();
		$klaves = array_keys($logs);
		foreach($klaves as $id)
		{
				if (isset($this->filtros) && (!empty($this->filtros))){
					if ($this->cumple_criterio_filtro($logs[$id])){
							$texto = $this->procesar_entidades_html ? texto_plano($logs[$id]) : $logs[$id];
							$logs_filtrados[] = $texto;
					}
				}else{
					$texto = $this->procesar_entidades_html ? texto_plano($logs[$id]) : $logs[$id];
					$logs[$id] = $texto;
				}
		}
		if (isset($this->filtros) && (!empty($this->filtros))){
					$logs = $logs_filtrados;
		}				
		return $logs;
	}
	
	function get_cantidad_pedidos()
	{
		$logs = $this->get_logs_archivo();
		$this->ultimo_lugar = count($logs);
		return $this->ultimo_lugar;
	}
	
	function get_archivo_nombre()
	{
		return $this->archivo;	
	}

	function set_filtro($filtro)
	{
		//Quito los filtros implicitos en la creacion del logger
		if (isset($filtro['fuente'] )){
			unset($filtro['fuente']);
		}
		if (isset($filtro['proyecto'])){
			unset($filtro['proyecto' ]);
		}
		foreach($filtro as $klave => $valor)
		{
			if (is_null($valor)){
				unset($filtro[$klave]);
			}
		}
		$this->filtros = $filtro;
	}

	function cumple_criterio_filtro($datos)
	{
			$basicos = $this->analizar_encabezado($datos);
			$cumple = true;
			foreach($this->filtros as $klave => $valor)
			{
				$index = strtolower(trim($klave));
				if (isset($basicos[$index])){
					$cumple = $cumple && (trim($basicos[$index]) == trim($valor));
				}
			}
			return $cumple;
	}

	function existen_filtros_extra()
	{
		$filtros_base = array('proyecto' => 1, 'fuente' => 1);
		$sobrante = array_diff_key($this->filtros, $filtros_base);
		return (!empty($sobrante));
	}
}
?>