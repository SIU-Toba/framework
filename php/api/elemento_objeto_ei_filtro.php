<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei_filtro extends elemento_objeto
{

	function eventos_predefinidos()
	{
		$eventos = array();
		//Si no selecciono ningun evento, la modificación es por defecto				
		if ($this->hay_filtrar() || !$this->hay_eventos_seleccionados()) 
			$eventos[] = 'filtrar';
		if ($this->hay_cancelar())
			$eventos[] = 'cancelar';
		return $eventos;
	}
	
	function generar_eventos($solo_basicos)
	{
		$eventos = parent::generar_eventos($solo_basicos);
		//¿Esta siendo utilizado por otro? Sino no tiene sentido incluir listeners
		if (!isset($this->rol_en_consumidor['identificador']))
			return $eventos;
		else
			$id = $this->rol_en_consumidor['identificador'];
			
 		$metodos = array();
 		$metodos[] = "\t".
'function evt__'.$id.'__carga()
	!#c3//Cuando no se retorna valor, el filtro muestra unicamente la opción de filtrar
	!#c3//Cuando se retornan valores, el filtro muestra la opción de cancelar
	!#c3//El formato del retorno debe ser array("id_ef" => valor, ...)
	{
		!#c2//if isset($this->datos_'.$id.')
		!#c2//	return $this->datos_'.$id.';
	}
';
 		//Si no selecciono ningun evento, la modificación es por defecto
 		if ($this->hay_filtrar() || !$this->hay_eventos_seleccionados()) {
 			$metodos[] = "\t". 
'function evt__'.$id.'__filtrar($registro)
	!#c3//El formato del dato de entrada es array("id_ef" => valor, ...)
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
		return $eventos;
	}		

	//---Preguntas
	function hay_filtrar() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_agregar'];
	}
	
	function hay_cancelar() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_mod_limpiar'];
	}
	
	function hay_eventos_seleccionados() {
		return $this->hay_filtrar() || $this->hay_cancelar();
	}
}


?>