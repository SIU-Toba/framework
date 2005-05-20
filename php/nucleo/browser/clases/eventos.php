<?php
/**
*	Clase estática que sabe construir eventos básicos para los objetos
**/
class eventos
{
	static function evento_estandar($id, $etiqueta = null, $en_botonera = true)
	//Retorna un evento estandar listo para modificar
	{
		$evento[$id]['etiqueta'] = $etiqueta;
		$evento[$id]['maneja_datos'] = true;
		$evento[$id]['sobre_fila'] = false;			//Propiedad particular que entiende el cuadro (y el ML debería tambien)
		$evento[$id]['confirmacion'] = '';
		$evento[$id]['estilo'] = "";
		$evento[$id]['imagen'] = "";		
		$evento[$id]['en_botonera'] = $en_botonera;	
		$evento[$id]['ayuda'] = '';
		return $evento;
	}
	
	static function duplicar($evento, $nuevo_id)
	{
		return array_renombrar_llave($evento, $nuevo_id);
	}

	static function a_javascript($id, $evento, $parametros = null)
	// Toma la definición de un evento y la pasa a su homonimo js
	{
		$js_confirm = isset( $evento['confirmacion'] ) ? "'{$evento['confirmacion']}'" : "''";
		$js_validar = isset( $evento['maneja_datos'] ) ? js::bool($evento['maneja_datos']) : "true";
		if (is_array($parametros))
			$param = ", ".js::arreglo($parametros, true);
		else		
			$param = (isset($parametros)) ? ", '$parametros'" : '';
		return "new evento_ei('$id', $js_validar, $js_confirm $param)";
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
	
	//---------------------------------------------------
	//---Eventos de Cuadro	
	static function seleccion()
	{
		$evento = self::evento_estandar('seleccion', null, false);
		$evento['seleccion']['maneja_datos'] = false;
		$evento['seleccion']['sobre_fila'] = true;					//Propiedad particular que entiende el cuadro (y el ML debería tambien)
		$evento['seleccion']['ayuda'] = 'Seleccionar la fila';
		$evento['seleccion']['imagen'] = recurso::imagen_apl('doc.gif');
		return $evento;	
	}	
	
	static function ordenar()
	{
		$evento = self::evento_estandar('ordenar', null, false);
		$evento['ordenar']['maneja_datos'] = false;
		return $evento;	
	}

}



?>