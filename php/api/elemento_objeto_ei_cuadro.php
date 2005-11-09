<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei_cuadro extends elemento_objeto
{

	function eventos_predefinidos()
	{
		$eventos = array('carga');
		//Si no selecciono ningun evento, la modificación es por defecto				
		if ($this->hay_seleccion()) 
			$eventos[] = 'seleccion';
		if ($this->hay_baja()) 
			$eventos[] = 'baja';		
		if ($this->hay_ordenar())
			$eventos[] = 'ordenar';
		return $eventos;
	}
	
	function generar_eventos($solo_basicos)
	{
		$eventos = parent::generar_eventos($solo_basicos);
		//¿Esta siendo utilizado por otro? Sino no tiene sentido incluir listeners
		if (isset($this->rol_en_consumidor['identificador'])) {
			$id = $this->rol_en_consumidor['identificador'];
			$metodos = array();
			$metodos[] = "\t".
'function evt__'.$id.'__carga()
	!#c3//El formato del retorno debe ser array( array("columna" => valor, ...), ...)
	{
		!#c3//	return $this->datos_'.$id.';
	}
';
			if ($this->hay_seleccion()) {
				$metodos[] = "\t".
'function evt__'.$id.'__seleccion($seleccion)
	!#c3//Formato del dato de entrada 
	!#c3//	- Clave única: $seleccion contiene el valor de la columna
	!#c3//	- Clave multiple: array("col_clave1" => valor, "col_clave2" => valor, ...)
	!#c3//Recordar hacer un $this->dependencias["'.$id.'"]->deseleccionar() cuando se cancela la edición 
	!#c3//para eliminar el feedback en la fila seleccionada
	{
	}
';
			}
			if ($this->hay_baja()) {
				$metodos[] = "\t".
'function evt__'.$id.'__baja($seleccion)
	!#c3//Formato del dato de entrada 
	!#c3//	- Clave única: $seleccion contiene el valor de la columna
	!#c3//	- Clave multiple: array("col_clave1" => valor, "col_clave2" => valor, ...)
	{
	}
';
			}
			if (!$solo_basicos && $this->hay_ordenar()) {
				$metodos[] = "\t".
'function evt__'.$id.'__ordenar($columna, $sentido)
	!#c3//$sentido puede ser "des" o "asc"
	{
	}
';			
			}
			$eventos[$id] = $this->filtrar_comentarios($metodos);
		}
		return $eventos;
	}		

	//---Preguntas
	function hay_seleccion() {
		return $this->hay_evento('seleccion');	
	}
	
	function hay_baja() {
		return $this->hay_evento('baja');	
	}
	
	function hay_ordenar() {
		return $this->datos['apex_objeto_cuadro'][0]['ordenar'];
	}

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'seleccion';
		$modelo[0]['nombre'] = 'Seleccion';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'seleccion':
				$evento[0]['identificador'] = "seleccion";
				$evento[0]['etiqueta'] = "";
				$evento[0]['orden'] = 1;
				$evento[0]['sobre_fila'] = 1;
				$evento[0]['en_botonera'] = 0;
				$evento[0]['imagen_recurso_origen'] = "apex";
				$evento[0]['imagen'] = "doc.gif";	
				break;
		}
		return $evento;
	}
}
?>