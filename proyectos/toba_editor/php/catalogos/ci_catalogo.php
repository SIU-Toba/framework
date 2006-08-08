<?php
require_once('catalogos/album_fotos.php');

define('apex_foto_inicial', '-- Completo --');
//----------------------------------------------------------------
/**
*	Una clase general para el manejo de catalogo de item/objetos
*/
abstract class ci_catalogo extends objeto_ci
{
	protected $opciones;
	protected $apertura;			//Ultima apertura creada
	protected $apertura_selecc;		//Seleccion explicita de apertura
	protected $album_fotos;
	
	function __construct($id)
	{
		parent::__construct($id);
	}
		
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "apertura";
		$propiedades[] = "opciones";
		return $propiedades;
	}	
	
	function obtener_html_dependencias()
	{
		foreach($this->dependencias_gi as $dep)
		{
			$this->dependencia($dep)->obtener_html();	
		}
	}	
	

	/*
	*	Agrega al evento sacar_foto una pregunta acerca del nombre de la misma
	*/
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__sacar_foto = function() {
				this._parametros = prompt('Nombre de la foto','nombre de la foto');
				if (this._parametros != '' && this._parametros != null) {
					return true;
				}
				return false;
			}
		";
	}	
		
	
	//-------------------------------
	//---- Filtro de opciones ----
	//-------------------------------
	
	function conf__filtro()
	{
		$this->dependencia('filtro')->colapsar();
		if (isset($this->opciones))
			return $this->opciones;
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->opciones);
		$this->dependencia('fotos')->deseleccionar();
	}
	
	function evt__filtro__filtrar($datos)
	{
		$this->opciones = $datos;
	}	
	
	//-------------------------------
	//---- Cuadro de fotos ----
	//-------------------------------
	
	function conf__fotos()
	{
		$fotos = $this->album_fotos->fotos();
		$this->dependencia('fotos')->colapsar();
		//Se incluyen la imagen de predeterminada
		foreach ($fotos as $id => $foto) {
			if ($foto['foto_nombre'] == apex_foto_inicial) {
				$esta_la_inicial = true;
			}
			if ($foto['predeterminada'] == 1) {
				$fotos[$id]['defecto'] = "home.gif";
				//Carga la por defecto
				if (!isset($this->opciones) && !isset($this->apertura)) { 
					$this->apertura = $foto['foto_nodos_visibles'];
					$this->apertura_selecc = $this->apertura;
					$this->opciones = $foto['foto_opciones'];
				}
			}
			else 
				$fotos[$id]['defecto'] = 'nulo.gif';
		}
		return $fotos;
	}
	
	
	function evt__fotos__seleccion($nombre)
	{
		$foto_nombre = $nombre['foto_nombre'];
		$foto = $this->album_fotos->foto($foto_nombre);
		if ($foto !== false) {
			$this->apertura = $foto['foto_nodos_visibles'];
			$this->apertura_selecc = $this->apertura;
			$this->opciones = $foto['foto_opciones'];
		}

	}
	
	function evt__fotos__baja($nombre)
	{
		$this->album_fotos->borrar_foto($nombre['foto_nombre']);
	}	
	
	function evt__fotos__defecto($nombre)
	{
		$this->album_fotos->cambiar_predeterminada($nombre['foto_nombre']);	
	}
	
	function evt__sacar_foto($nombre)
	{
		$this->album_fotos->agregar_foto($nombre, $this->apertura, $this->opciones);
		$this->evt__fotos__seleccion($nombre);
	}	
}

?>