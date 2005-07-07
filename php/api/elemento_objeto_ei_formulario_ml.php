<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei_formulario_ml extends elemento_objeto
{
	function eventos_predefinidos()
	{
		$eventos = array('carga');
		//Si no selecciono ningun evento, la modificación es por defecto				
		if ($this->hay_seleccion()) 
			$eventos[] = 'seleccion';
		if ($this->tipo_analisis() == 'EVENTOS') {
			$eventos[] ='registro_alta';
			$eventos[] ='registro_baja';
			$eventos[] ='registro_modificacion';
		} else {
			$eventos[] = 'modificacion';
		}
		return $eventos;
	}
	
	function generar_eventos($solo_basicos)
	{
		$eventos = parent::generar_eventos($solo_basicos);
		if (! isset($this->rol_en_consumidor['identificador']))
			return $eventos;
		else
			$id = $this->rol_en_consumidor['identificador'];
			
		$metodos[] = "\t".
'function evt__'.$id.'__carga()
	!#c3//El formato debe ser una matriz array("id_fila" => array("id_ef" => valor, ...), ...);
	!#c3//Si no se retorna valor, se toma la cantidad de líneas definidas en el administrador
	{
		!#c2//if isset($this->datos_'.$id.')
		!#c2//	return $this->datos_'.$id.';
	}
';		
		if ($this->hay_seleccion()) {
			$metodos[] = "\t".
'function evt__'.$id.'__seleccion($id_fila)
	!#c3//El $id_fila es la clave de la fila en el arreglo asociativo retornado en la modificación
	{
		!#c2//if isset($this->datos_'.$id.')
		!#c2//	return $this->datos_'.$id.';
	}
';
		}
		switch($this->tipo_analisis()) {
			case 'EVENTOS':
				$metodos[] = "\t".
'function evt__'.$id.'__registro_alta($id_fila, $datos)
	!#c3//El formato de datos es array("id_ef" => valor, ...)
	{
		!#c2//$this->datos_'.$id.'[$id_fila] = $datos;	
	}
';
				$metodos[] = "\t".
'function evt__'.$id.'__registro_baja($id_fila)
	{
		!#c2//unset($this->datos_'.$id.'[$id_fila]);
	}
';
				$metodos[] = "\t".
'function evt__'.$id.'__registro_modificacion($id_fila, $datos)
	!#c3//El formato de datos es array("id_ef" => valor, ...)
	{
		!#c2//$this->datos_'.$id.'[$id_fila] = $datos;
	}
';						
				break;
			case "LINEA":
				$metodos[] = "\t".
'function evt__'.$id.'__modificacion($registros)
	!#c3//El formato del dato de entrada es una matriz array("id_fila" => array("id_ef" => valor, ...), ...);
	!#c2//En cada fila existe una columna apex_ei_analisis_fila que indica el movimiento sobre la fila:
	!#c2//	A: Alta, M: Modificación, B:Baja
	{
		!#c2//$this->datos_'.$id.' = $registros;	
	}
';			
				break;
			case "NO":
				$metodos[] = "\t".
'function evt__'.$id.'__modificacion($registros)
	!#c3//El formato del dato de entrada es una matriz array("id_fila" => array("id_ef" => valor, ...), ...);
	{
		!#c2//$this->datos_'.$id.' = $registros;	
	}
';
				break;
		}
		$eventos[$id] = $this->filtrar_comentarios($metodos);
		return $eventos;
	}	
	
	//---Preguntas
	function hay_seleccion() {
		return $this->datos['apex_objeto_ut_formulario'][0]['ev_seleccion'];
	}	
	
	function tipo_analisis() {
		return $this->datos['apex_objeto_ut_formulario'][0]['analisis_cambios'];
	}
}


?>