<?php

/**
 * 	Interface: Administradorr de persistencia de una estructura de datos tipo tabla, un contenedor de filas de datos
 * 	@package Objetos
 *  @subpackage Persistencia
 */
interface ap_tabla
{
	/**
	 * Mecanismo de recuperaci�n de valores para las columnas externas.
	 * @param array $fila Fila que toma de referencia la carga externa
	 * @param string $evento 
	 * @return array Se devuelven los valores recuperados del medio de persistencia
	 */
	function completar_campos_externos_fila($fila, $evento=null );
	
	/**
	 * Permite que las modificaciones puedan cambiar las claves de una fila
	 */	
	function activar_modificacion_clave();
	
	/**
	 * Obtiene del medio un conjunto de datos a partir de una definici�n de sus campos clave
	 * @param array $clave Arreglo asociativo campo-valor
	 * @return boolean Falso si no se encontro ninguna fila
	 */
	function cargar_por_clave($clave);

	/**
	 * Sincroniza los cambios en la tabla con el medio de persistencia
	 * @return integer Cantidad de registros modificados
	 */
	function sincronizar();

	/**
	 * Elimina f�sicamente las filas de esta tabla
	 */
	function eliminar();
}

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------

/**
 * 	Interface: Administrador de persistencia de un conjunto relacionado de tablas
 * 	@package Objetos
 *  @subpackage Persistencia
 */
interface ap_relacion
{
	/**
	 * Cargar una relaci�n completa a partir de la clave de una de las tablas raiz
	 * @param array $clave Arreglo asociativo campo-valor
	 * @return boolean Falso si no se cargo la tabla raiz
	 */
	function cargar_por_clave($clave);
	
	/**
	 * Sincroniza los cambios en la relacion con el medio de persistencia
	 */
	function sincronizar();
	
	/**
	 * Elimina del medio de persistencia toda la relaci�n cargada 
	 *
	 */
	function eliminar();
}
?>