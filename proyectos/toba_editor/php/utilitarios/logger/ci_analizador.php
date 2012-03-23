<?php

class ci_analizador extends toba_ci
{
	protected $s__opciones;
	public $s__seleccion;
	protected $archivo;
	protected $cambiar_pantalla = false;
	protected $analizador;
	protected $s__mostrar_encabezados = false;
	protected $s__seleccion_modo_detalle = 'no_seteado';
	
	/**
	 * @todo Se desactiva el logger porque no corre como proyecto toba sino como el de la aplicacion
	 * 		Cuando el admin sea un proyecto hay que sacar la desactivación
	 */
	function ini()
	{
		toba::logger()->desactivar();	
		if (!isset($this->s__opciones)) {
			$this->s__opciones['proyecto'] = toba_editor::get_proyecto_cargado();
			$this->s__opciones['fuente'] = 'fs';
			$this->s__seleccion = 'ultima';
		}
		$this->cargar_analizador();
	}
		
	function get_analizador()
	{
		return $this->analizador;
	}
	
	function ajax__get_datos_logger($anterior_mod, toba_ajax_respuesta $respuesta)
	{
		$res = $this->analizador->get_pedido($this->s__seleccion);
		$info_operacion = $this->pantalla()->generar_html_info_operacion($res);
		$encabezado = $this->pantalla()->generar_html_encabezado($res);
		list($detalle, $cant_por_nivel) = $this->pantalla()->generar_html_detalles($res);
		$ultima_mod = $this->timestamp_archivo();
		if ($anterior_mod != $ultima_mod) {
			$salida['ultima_mod'] = $ultima_mod;		
			$salida['encabezado'] = $encabezado;
			$salida['info_op'] = $info_operacion;
			$salida['detalle'] = $detalle;	
			$salida['cant_por_nivel'] = $cant_por_nivel;
			$respuesta->set($salida);
		}
	}

	function ajax__set_estado_encabezados($estado)
	{
		$this->s__mostrar_encabezados = ($estado == 'true')? true: false;
		return $estado;
	}

	function ajax__set_modo_detalle_seleccionado($modo)
	{
		$this->s__seleccion_modo_detalle = $modo;
	}
	//---- Consultas varias ----------------------------------------------------	
	
	function get_logger()
	{
		return toba_logger::instancia($this->s__opciones['proyecto']);
	}
	
	function get_proyecto()
	{
		return $this->s__opciones['proyecto'];
	}

	function cargar_analizador()
	{
		if (isset($this->s__opciones)) {
			$this->archivo = $this->get_logger()->directorio_logs().'/sistema.log';		
			$this->analizador = new toba_analizador_logger_fs($this->archivo);
			$this->analizador->set_filtro($this->s__opciones);
		}
	}
	
	function debe_mostrar_visor()
	{
		if ($this->get_id_pantalla() == 'visor' && isset($this->s__seleccion)) {
			if (isset($this->s__opciones['proyecto']) && isset($this->s__opciones['fuente'])) {
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
	
	function get_estado_encabezados()
	{
		return $this->s__mostrar_encabezados;
	}

	function get_seleccion_modo_detalle()
	{
		return $this->s__seleccion_modo_detalle;
	}
	//---- Eventos CI -------------------------------------------------------
	
	function evt__refrescar()
	{
	}
	
	function evt__borrar()
	{
		$this->get_logger()->borrar_archivos_logs();	
	}
	
	function evt__anterior()
	{
		if (isset($this->s__seleccion)) {
			if ($this->s__seleccion == 'ultima') {
				$this->s__seleccion = $this->analizador->get_cantidad_pedidos() - 1;
			} else {
				$this->s__seleccion--;
			}
		}
	}

	function evt__siguiente()
	{
		if (isset($this->s__seleccion)) {
			$ultima = $this->analizador->get_cantidad_pedidos();
			if ($this->s__seleccion == ($ultima - 1) ) {
				$this->s__seleccion = 'ultima';
			} else {
				$this->s__seleccion++;				
			}
		}
	}

	function evt__ultima()
	{
		$this->s__seleccion = 'ultima';
	}
	//---- Eventos Filtro -------------------------------------------------------
	
	function evt__filtro__filtrar($opciones)
	{
		$this->s__opciones = $opciones;		
		$this->s__opciones['fuente'] = 'fs';
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__opciones);	
	}
	
	function conf__filtro($filtro)
	{
		if (isset($this->s__opciones)) {
			$filtro->set_datos($this->s__opciones);	
		}
	}
	
	//---- Eventos Cuadro -------------------------------------------------------
	
	function conf__pedidos()
	{
		if (isset($this->s__opciones)) {
			$this->analizador->set_filtro($this->s__opciones);
		}
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
		$this->s__seleccion = $id['numero'];
		$this->set_pantalla('visor');
	}
	
	function evt__pedidos__ultima()
	{
		$this->s__seleccion = 'ultima';
		$this->set_pantalla('visor');
	}
}

?>