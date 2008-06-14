<?php

/**
 * 	Interface: Administradorr de persistencia de una estructura de datos tipo tabla, un contenedor de filas de datos
 * 	@package Componentes
 *  @subpackage Persistencia
 */
interface toba_ap_tabla
{
	/**
	 * Mecanismo de recuperación de valores para las columnas externas.
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
	 * Obtiene del medio un conjunto de datos a partir de una definición de sus campos clave
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
	 * Sincroniza los cambios que suponen inserts o updates
	 * @return integer Cantidad de registros modificados
	 */
	function sincronizar_actualizados();	
	
	/**
	 * Sincroniza los cambios que suponen eliminaciones
	 * @return integer Cantidad de registros modificados
	 */
	function sincronizar_eliminados();		
}

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------

/**
 * 	Interface: Administrador de persistencia de un conjunto relacionado de tablas
 * 	@package Componentes
 *  @subpackage Persistencia
 */
interface toba_ap_relacion
{
	/**
	 * Cargar una relación completa a partir de la clave de una de las tablas raiz
	 * @param array $clave Arreglo asociativo campo-valor
	 * @return boolean Falso si no se cargo la tabla raiz
	 */
	function cargar_por_clave($clave);
	
	/**
	 * Sincroniza los cambios en la relacion con el medio de persistencia
	 */
	function sincronizar();
	
	/**
	 * Elimina del medio de persistencia toda la relación cargada 
	 */
	function eliminar_todo();
}
?>