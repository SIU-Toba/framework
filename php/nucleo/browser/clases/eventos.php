<?php
/**
*	Clase estática que sabe construir eventos básicos para los objetos
**/
class eventos
{
	/**
	* 	Toma la definición de un evento y la pasa a su homonimo js
	**/
	static function a_javascript($id, $evento)
	{
		$js_confirm = isset( $evento['confirmacion'] ) ? "'{$evento['confirmacion']}'" : "''";
		$js_validar = isset( $evento['maneja_datos'] ) ? js::bool($evento['maneja_datos']) : "true";
		return "new evento_ei('$id', $js_validar, $js_confirm)";
	}
	
	static function evento_estandar($id, $etiqueta = null, $en_botonera = true)
	{
		$evento[$id]['etiqueta'] = $etiqueta;
		$evento[$id]['maneja_datos'] = true;
		$evento[$id]['confirmacion'] = '';
		$evento[$id]['estilo'] = "";
		$evento[$id]['en_botonera'] = $en_botonera;	
		return $evento;
	}
	
	//-------------------------------------------------
	//---EVENTOS De CI
	static function ci_cancelar($etiqueta=null, $en_botonera=true)
	{
		$evento = self::evento_estandar('cancelar', isset($etiqueta) ? $etiqueta : "&Cancelar", $en_botonera);
		$evento['cancelar']['maneja_datos'] = false;
		return $evento;
	}

	static function ci_procesar($etiqueta=null, $en_botonera=true)
	{
		return self::evento_estandar('procesar', isset($etiqueta) ? $etiqueta : "Proce&sar", $en_botonera);	
	}
	
	static function ci_cambiar_tab($id)
	{
		return self::evento_estandar('cambiar_tab_'.$id, '', false);		
	}
	
	//---------------------------------------------------
	//---Eventos de FORMULARIO
	static function alta($etiqueta=null, $en_botonera=true)
	{
		$evento = self::evento_estandar('alta', isset($etiqueta) ? $etiqueta : "&Agregar", $en_botonera);
		$evento['alta']['estilo'] = "abm-input";
		return $evento;
	}
	
	static function modificacion($etiqueta=null, $en_botonera=true)
	{
		$evento = self::evento_estandar('modificacion', isset($etiqueta) ? $etiqueta : "&Modificar", $en_botonera);
		$evento['modificacion']['estilo'] = "abm-input";
		return $evento;
	}	
	
	static function baja($etiqueta=null, $en_botonera=true)
	{
		$evento = self::evento_estandar('baja', isset($etiqueta) ? $etiqueta : "&Eliminar", $en_botonera);	
		$evento['baja']['maneja_datos'] = false;
		$evento['baja']['confirmacion'] = "¿Desea ELIMINAR el registro?";
		$evento['baja']['estilo'] = "abm-input-eliminar";
		return $evento;
	}


	static function cancelar($etiqueta=null, $en_botonera=true)
	{
		$evento = self::evento_estandar('cancelar', isset($etiqueta) ? $etiqueta : "&Cancelar", $en_botonera);
		$evento['cancelar']['estilo'] = "abm-input";
		$evento['cancelar']['maneja_datos'] = false;
		return $evento;	
	}
	
	//---------------------------------------------------
	//---Eventos de FILTRO	
	static function filtrar($etiqueta=null, $en_botonera=true)
	{
		$evento = self::evento_estandar('filtrar', isset($etiqueta) ? $etiqueta : "&Filtrar", $en_botonera);
		$evento['filtrar']['estilo'] = "abm-input-eliminar";
		return $evento;	
	}

}



?>