<?php
require_once('catalogos/album_fotos.php');

define('apex_foto_inicial', '-- Completo --');
/**
*	Una clase general para el manejo de catalogo de item/objetos
*/
abstract class ci_catalogo extends toba_ci
{
	protected $s__opciones;
	protected $s__apertura;			//Ultima apertura creada
	protected $apertura_selecc;		//Seleccion explicita de apertura
	protected $album_fotos;
	
	/*
	*	Agrega al evento sacar_foto una pregunta acerca del nombre de la misma
	*/
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__fotos__foto = function() {
				var nombre = prompt('Nombre de la foto','nombre de la foto');
				if (trim(nombre) != '' && nombre != null) {
					this.dep('fotos')._evento.parametros_extra = nombre;
					return true;
				}else{
					notificacion.agregar('Toda Foto requiere un nombre, especifiquelo por favor', 'error');
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
		if (isset($this->s__opciones)) {
			return $this->s__opciones;
		}
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__opciones);
		$this->dependencia('fotos')->deseleccionar();
	}
	
	function evt__filtro__filtrar($datos)
	{
		$this->s__opciones = $datos;
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
				$fotos[$id]['defecto'] = 'home.png';
				//Carga la por defecto
				if (!isset($this->s__opciones) && !isset($this->s__apertura)) { 
					$this->s__apertura = $foto['foto_nodos_visibles'];
					$this->apertura_selecc = $this->s__apertura;
					$this->s__opciones = $foto['foto_opciones'];
				}
			} else {
				$fotos[$id]['defecto'] = 'nulo.gif';
			}
		}
		return $fotos;
	}
	
	function evt__fotos__seleccion($nombre)
	{
		$foto_nombre = $nombre['foto_nombre'];
		$foto = $this->album_fotos->foto($foto_nombre);
		if ($foto !== false) {
			$this->s__apertura = $foto['foto_nodos_visibles'];
			$this->apertura_selecc = $this->s__apertura;
			$this->s__opciones = $foto['foto_opciones'];
		}

	}
	
	function evt__fotos__baja($nombre)
	{
		$this->album_fotos->borrar_foto($nombre['foto_nombre']);
	}	
	
	function evt__fotos__defecto($nombre)
	{
		$this->album_fotos->cambiar_predeterminada($nombre['foto_nombre']);
		$this->evt__fotos__seleccion($nombre);
	}
	

	function evt__fotos__foto($nombre)
	{
		if (trim($nombre) !== '') {
			$this->album_fotos->agregar_foto($nombre, $this->s__apertura, $this->s__opciones);
			$this->evt__fotos__seleccion($nombre);
		} else {
			throw new toba_error('Toda Foto requiere un nombre, especifiquelo por favor');
		}
	}		
	
	function evt__sacar_foto($nombre)
	{
		$this->evt__fotos__foto($nombre);
	}	
}

?>