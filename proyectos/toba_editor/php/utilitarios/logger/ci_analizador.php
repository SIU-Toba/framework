<?php
require_once('modelo/lib/analizador_logger.php');
//--------------------------------------------------------------------

class ci_analizador extends toba_ci
{
	protected $opciones;
	public $seleccion;
	protected $archivo;
	protected $cambiar_pantalla = false;
	protected $analizador;
	
	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = 'opciones';
		$estado[] = 'seleccion';
		return $estado;	
	}
	
	/**
	 * @todo Se desactiva el logger porque no corre como proyecto toba sino como el de la aplicacion
	 * 		Cuando el admin sea un proyecto hay que sacar la desactivación
	 */
	function ini()
	{
		toba::get_logger()->desactivar();	
		if (!isset($this->opciones)) {
			$this->opciones['proyecto'] = editor::get_proyecto_cargado();	
			$this->opciones['fuente'] = 'fs';
			$this->seleccion = 'ultima';
		}
		$this->cargar_analizador();
	}
		
	function conf()
	{
		$this->cargar_analizador();
	}

	function conf__visor()
	{
		$this->pantalla()->analizador = $this->analizador;		
	}
	
	function servicio__ejecutar()
	{
		$res = $this->analizador->obtener_pedido($this->seleccion);
		$encabezado = $this->pantalla()->generar_html_encabezado($res);
		list($detalle, $cant_por_nivel) = $this->pantalla()->generar_html_detalles($res);
		$anterior_mod = toba::get_hilo()->obtener_parametro('mtime');
		$ultima_mod = $this->timestamp_archivo();
		if ($anterior_mod != $ultima_mod) {
			echo $ultima_mod;		
			echo "<--toba-->";			
			echo $encabezado;
			echo "<--toba-->";		
			echo $detalle;
			echo "<--toba-->";
			echo js::arreglo($cant_por_nivel, true);
		}
	}
	
	//---- Consultas varias ----------------------------------------------------	
	
	function get_logger()
	{
		return logger::instancia($this->opciones['proyecto']);
	}
	
	function get_proyecto()
	{
		return $this->opciones['proyecto'];
	}

	function cargar_analizador()
	{
		if (isset($this->opciones)) {
			$this->archivo = $this->get_logger()->directorio_logs()."/sistema.log";		
			$this->analizador = new analizador_logger_fs($this->archivo);
		}
	}
	
	function debe_mostrar_visor()
	{
		if ($this->get_id_pantalla() == 'visor' && isset($this->seleccion)) {
			if (isset($this->opciones['proyecto']) && isset($this->opciones['fuente'])) {
				return true;
			}
		}
		return false;
	}
	
	function timestamp_archivo()
	{
		return filemtime($this->archivo);
	}
	
	function existe_archivo_log()
	{
		return file_exists($this->archivo);
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
		$this->opciones['fuente'] = 'fs';
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->opciones);	
	}
	
	function conf__filtro()
	{
		if (isset($this->opciones)) {
			return $this->opciones;	
		}
	}
	
	//---- Eventos Cuadro -------------------------------------------------------
	
	function conf__pedidos()
	{
		$logs = $this->analizador->get_logs_archivo();
		$logs = array_reverse($logs);		
		$pedidos = array();
		$numero = count($logs);
		foreach ($logs as $log) {
			$log = trim($log);
			$basicos = $this->analizador->analizar_encabezado($log);
			$basicos['numero'] = $numero;
			$pedidos[] = $basicos; 
			$numero--;
		}
		return $pedidos;
	}
	
	function evt__pedidos__seleccion($id)
	{
		$this->seleccion = $id['numero'];
		$this->set_pantalla("visor");
	}
	
	function evt__pedidos__ultima()
	{
		$this->seleccion = 'ultima';
		$this->set_pantalla("visor");
	}
		
}

?>