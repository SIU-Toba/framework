<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//--------------------------------------------------------------------
class ci_analizador extends objeto_ci
{
	protected $opciones;
	protected $seleccion;
	protected $archivo;
	protected $cambiar_pantalla = false;
	
	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = 'opciones';
		$estado[] = 'archivo';
		$estado[] = 'seleccion';
		return $estado;	
	}
	
	protected function get_pantalla_inicial()
	{
		if (isset($this->seleccion)) {
			return "visor";
		}
		return parent::get_pantalla_inicial();		
	}	
	
	function get_pantalla_actual()
	{
		if ($this->cambiar_pantalla) {
			return "visor";	
		}
		return parent::get_pantalla_actual();
	}
	
	/**
	 * @todo Se desactiva el logger porque no corre como proyecto toba sino como el de la aplicacion
	 * 		Cuando el admin sea un proyecto hay que sacar la desactivación
	 */
	function evt__inicializar()
	{
		toba::get_logger()->desactivar();	
		if (!isset($this->opciones)) {
			$this->opciones['proyecto'] = toba::get_hilo()->obtener_proyecto();	
			$this->opciones['fuente'] = 'fs';
			$this->archivo = $this->get_logger()->directorio_logs()."/sistema.log";
			$this->seleccion = 'ultima';
		}
	}
	
	function obtener_html_dependencias()
	{
		parent::obtener_html_dependencias();
?>
		<style type="text/css">
		.cuerpo, .ci-cuerpo {
			margin-top: 0px;
			margin-bottom: 0px;
		}
		</style>
<?php
		if ($this->get_pantalla_actual() == 'visor' && isset($this->seleccion)) {
			if (isset($this->opciones['proyecto']) && isset($this->opciones['fuente'])) {
				if ($this->opciones['fuente'] == 'db') {
					$this->obtener_html_db();
				} elseif ($this->opciones['fuente'] == 'fs') {
					$this->obtener_html_fs();
				}
			}
		}
	}
	
	function obtener_html_db()
	{
		echo ei_mensaje("El logger por base de datos no está implementado");
	}
	
	function obtener_html_fs()
	{
		if (!file_exists($this->archivo)) {
			echo ei_mensaje("No hay logs registrados para el proyecto ".
							"<strong>{$this->opciones['proyecto']}</strong>");
			return;
		}			
		
		$res = $this->obtener_pedido($this->seleccion);
		$encabezado = $this->analizar_encabezado($res);
		$cuerpo = $this->analizar_cuerpo($res);
		echo "<div style='clear:both;width:100%;height:100%;overflow:auto;'>";
		echo "<ul>";
		echo "<li>Encabezado: <ul>";
		foreach ($encabezado as $clave => $valor) {
			echo "<li><strong>".ucfirst($clave)."</strong>: $valor</li>\n";
		}
		echo "</ul></li>";
		echo "</ul>";
		echo "<ol>";
		foreach ($cuerpo as $linea) {
			$img =recurso::imagen_apl('logger/'.strtolower($linea['nivel']).'.png', true, null, null, $linea['nivel']);
			echo "<li>";
			echo "$img ".$linea['mensaje'];
			echo "</li>";	
		}
		echo "</ol>";
		echo "</div>";
	}
	
	protected function analizar_encabezado($log)
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
	
	protected function analizar_cuerpo($log)
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
			$cuerpo[] = array('nivel' => $res[$i], 'mensaje' => $res[$i+1]);
		}
		return $cuerpo;
	}

	protected function obtener_pedido($seleccion)
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
	protected function obtener_ultimo_pedido()
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
	
	protected function get_logs_archivo()
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
	
	function get_logger()
	{
		return logger::instancia($this->opciones['proyecto']);
	}
	

	function evt__pre_cargar_datos_dependencias()
	{
		if (isset($this->opciones)) {
			$this->dependencia('filtro')->colapsar();
		}
	}
	
	//---- Eventos CI -------------------------------------------------------
	
	function evt__refrescar()
	{
	}
	
	function evt__borrar()
	{
		$this->get_logger()->borrar_archivos_logs();	
	}
	
	//---- Eventos Filtro -------------------------------------------------------
	
	function evt__filtro__filtrar($opciones)
	{
		$this->opciones = $opciones;		
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->opciones);	
	}
	
	function evt__filtro__carga()
	{
		if (isset($this->opciones)) {
			return $this->opciones;	
		}
	}
	
	//---- Eventos Cuadro -------------------------------------------------------
	
	function evt__pedidos__carga()
	{
		$logs = $this->get_logs_archivo();
		$logs = array_reverse($logs);		
		$pedidos = array();
		$numero = count($logs);
		foreach ($logs as $log) {
			$log = trim($log);
			$basicos = $this->analizar_encabezado($log);
			$basicos['numero'] = $numero;
			$pedidos[] = $basicos; 
			$numero--;
		}
		return $pedidos;
	}
	
	function evt__pedidos__seleccion($id)
	{
		$this->seleccion = $id['numero'];
		$this->cambiar_pantalla = true;
	}
	
	function evt__pedidos__ultima()
	{
		$this->seleccion = 'ultima';
		$this->cambiar_pantalla = true;		
	}
		
}

?>