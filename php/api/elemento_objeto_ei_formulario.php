<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei_formulario extends elemento_objeto
{
	
	//---Preguntas
	function hay_alta() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_agregar'];
	}
	
	function hay_baja() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_mod_eliminar'];
	}
	
	function hay_modificacion() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_mod_modificar'];
	}
	
	function hay_cancelar() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_mod_limpiar'];
	}
	
	function hay_eventos_seleccionados() {
		return $this->hay_alta() || $this->hay_baja() || $this->hay_modificacion() || $this->hay_cancelar();
	}

	function eventos_predefinidos()
	{
		$eventos = array('carga');
		//Si no selecciono ningun evento, la modificación es por defecto				
		if ($this->hay_modificacion() || !$this->hay_eventos_seleccionados()) 
			$eventos[] = 'modificacion';
		if ($this->hay_alta())
			$eventos[] = 'alta';
		if ($this->hay_baja())
			$eventos[] = 'baja';
		if ($this->hay_cancelar())
			$eventos[] = 'cancelar';
		return $eventos;
	}
	
	
	//---- Generación de código	
	function generar_eventos($solo_basicos)
	{
		$eventos = parent::generar_eventos($solo_basicos);
		//¿Esta siendo utilizado por otro? Sino no tiene sentido incluir listeners
		if (isset($this->rol_en_consumidor['identificador'])) {
			$id = $this->rol_en_consumidor['identificador'];
			$metodos = array();
			$metodos[] = "\t".
'function evt__'.$id.'__carga()
	!#c3//Cuando no se retorna valor, el formulario muestra unicamente la opción de alta
	!#c3//Cuando se retornan valores, el formulario muestra las opciones de modificacion, baja, cancelar
	!#c3//El formato del retorno debe ser array("id_ef" => valor, ...)
	{
		!#c2//if isset($this->datos_'.$id.')
		!#c2//	return $this->datos_'.$id.';
	}
';
			//Si no selecciono ningun evento, la modificación es por defecto
			if ($this->hay_modificacion() || !$this->hay_eventos_seleccionados()) {
				$metodos[] = "\t".
'function evt__'.$id.'__modificacion($registro)
	!#c3//El formato del dato de entrada es array("id_ef" => valor, ...)
	{
		!#c2//$this->datos_'.$id.' = $registro;	
	}
';
			}
			if ($this->hay_alta()) {
				$metodos[] = "\t".
'function evt__'.$id.'__alta($registro)
	!#c3//El formato del dato de entrada es array("id_ef" => valor, ...)
	{
	}
';
			}
			if ($this->hay_baja()) {
				$metodos[] = "\t".
'function evt__'.$id.'__baja()
	{
	}
';
			}			
			if ($this->hay_cancelar()) {
				$metodos[] = "\t".
'function evt__'.$id.'__cancelar()
	{
	}
';
			}			
			$eventos[$id] = $this->filtrar_comentarios($metodos);
		}
		return $eventos;
	}		

	function generar_metodos_basicos()
	{
		$basicos = parent::generar_metodos_basicos();

		return $this->filtrar_comentarios($basicos);
	}
	
}
?>