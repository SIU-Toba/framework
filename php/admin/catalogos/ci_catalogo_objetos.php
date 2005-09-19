<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class ci_catalogo_objetos extends objeto_ci
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}

	function obtener_html_dependencias()
	{
		foreach($this->dependencias_gi as $dep)
		{
			$this->dependencias[$dep]->obtener_html();	
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
	
	function evt__fotos__carga()
	{
		$fotos = $this->catalogador->fotos();
		if (count($fotos) > 0) {
/*			if (! $this->dependencias['fotos']->hay_seleccion()) {
				$this->dependencias['fotos']->seleccionar($this->nombre_ultima_foto);
			}*/
			$this->dependencias['fotos']->colapsar();
			return $fotos;
		}
	}
		
	
	
	

}

?>