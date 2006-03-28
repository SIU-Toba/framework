<?php

class analizador_logger_fs
{
	protected $archivo;
	
	function __construct($archivo)
	{
		$this->archivo = $archivo;
	}
	
	function analizar_cuerpo($log)
	{
		$cuerpo = array();
		$niveles = toba::get_logger()->get_niveles();
		$texto = trim(substr($log, strpos($log, logger::fin_encabezado) + strlen(logger::fin_encabezado), strlen($log)));
		$patron = "/\[(";
		$patron .= implode("|", $niveles);
		$patron .= ")\]/";
		
		$res = preg_split($patron, $texto, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		//Se mezclan el nivel y el mensaje en un arreglo
		for ($i = 0; $i < count($res); $i+=2) {
			$mensaje = $res[$i+1];
			$bracket_cierra = strpos($mensaje, ']');
			$proy = substr($mensaje, 1, $bracket_cierra-1);
			$mens = substr($mensaje, $bracket_cierra+1);
			$cuerpo[] = array('nivel' => $res[$i], 'proyecto'=>$proy, 'mensaje' => $mens);
			
		}
		return $cuerpo;
	}
	
	function analizar_encabezado($log)
	{
		$encabezado = substr($log, 0, strpos($log, logger::fin_encabezado));
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
		

	function obtener_pedido($seleccion)
	{
		//Pedir el ultimo es un caso especial porque se trata con mas eficiencia
		if ($seleccion == 'ultima') {
			return $this->obtener_ultimo_pedido();	
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
	function obtener_ultimo_pedido()
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
			$ocurrencia = strrpos($acumulado, logger::separador);
			if ($ocurrencia !== false) {
				//Se encontro el separador, una parte del acumulado pertenece a este pedido
				$encontrado = true;
				$acumulado = substr($acumulado, $ocurrencia + strlen(logger::separador));
				$hay_algo_antes =  $hay_mas_para_leer || ($ocurrencia !== 0);
			}
			$franja_acum += $franja;
		} while (!$encontrado && $hay_mas_para_leer);
		
		fclose($fp);
		return $acumulado;
	}
	
	function get_logs_archivo()
	{
		if (!file_exists($this->archivo)) {
			return array();	
		}
		$texto = trim(file_get_contents($this->archivo));
		$logs = explode(logger::separador , $texto);
		if (count($logs) > 0) {
			//Borra el primer elemento que siempre esta vacio
			array_splice($logs, 0 ,1);
		}
		return $logs;
	}
}
?>