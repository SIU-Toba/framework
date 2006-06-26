<?php
/**
*	Clase estática que sabe construir eventos básicos para los objetos
*	@deprecated Los eventos se definen utilizando el administrador
**/
class eventos
{
	static function evento_estandar($id, $etiqueta=null, $en_botonera=true, $imagen = '', $ayuda = '', $maneja_datos=true)
	//Retorna un evento estandar listo para modificar
	{
		toba::get_logger()->obsoleto(__CLASS__, "", "0.8.3", "Utilizar la definición de eventos en el administrador", 'toba');
		$evento[$id]['etiqueta'] = $etiqueta;
		$evento[$id]['maneja_datos'] = $maneja_datos;
		$evento[$id]['sobre_fila'] = false;			//Propiedad particular que entiende el cuadro (y el ML debería tambien)
		$evento[$id]['confirmacion'] = '';
		$evento[$id]['estilo'] = "";
		$evento[$id]['imagen'] = $imagen;		
		$evento[$id]['en_botonera'] = $en_botonera;	
		$evento[$id]['ayuda'] = $ayuda;
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
	
	static function ci_pantalla_siguiente()
	{
		return self::evento_estandar('cambiar_tab__siguiente', '&Siguiente >', true);
	}
	
	static function ci_pantalla_anterior()
	{
		return self::evento_estandar('cambiar_tab__anterior', '< &Anterior', true);
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
	//---Eventos del ML
	static function ml_registro_nuevo()
	{
		$evento = self::evento_estandar('pedido_registro_nuevo', "", false);
		return $evento;	
	}	

	//---------------------------------------------------
	//---Eventos sobre datos tabulares
	static function seleccion($maneja_datos = false)
	{
		$evento = self::evento_estandar('seleccion', null, false);
		$evento['seleccion']['maneja_datos'] = $maneja_datos;
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
	
	static function cambiar_pagina()
	{
		$evento = self::evento_estandar('cambiar_pagina', null, false);
		$evento['cambiar_pagina']['maneja_datos'] = false;
		return $evento;	
	}

	//---------------------------------------------------
	//---Eventos de Calendario	
	static function seleccionar_dia()
	{
		$evento = self::evento_estandar('seleccionar_dia', null, false);
		$evento['seleccionar_dia']['maneja_datos'] = false;
		$evento['seleccionar_dia']['ayuda'] = 'Seleccionar el día';
		return $evento;	
	}	
	
	static function seleccionar_semana()
	{
		$evento = self::evento_estandar('seleccionar_semana', null, false);
		$evento['seleccionar_semana']['maneja_datos'] = false;
		$evento['seleccionar_semana']['ayuda'] = 'Seleccionar la semana';
		return $evento;	
	}	
	
	static function cambiar_mes()
	{
		$evento = self::evento_estandar('cambiar_mes', null, false);
		$evento['cambiar_mes']['maneja_datos'] = false;
		$evento['cambiar_mes']['ayuda'] = 'Cambiar de mes';
		return $evento;	
	}	
	
	//---------------------------------------------------
	//---Eventos del ei_Arbol
	static function ver_propiedades()
	{
		$evento = self::evento_estandar('ver_propiedades', null, false);
		return $evento;	
	}

	//----------------------------------------------------
	//---Varios
	static function refrescar($etiqueta = "&Refrescar")
	{
		$refrescar = eventos::duplicar(eventos::ci_procesar($etiqueta), 'refrescar');	
		$refrescar['refrescar']['imagen'] = recurso::imagen_apl('refrescar.gif');	
		$refrescar['refrescar']['ayuda'] = 'Refrescar';
		return $refrescar;
	}
	
}

?>