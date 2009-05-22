<?php

/**
 * Clase de entrada del usuario, un método por interacción
 */
interface toba_aplicacion_comando
{
	
	function set_entorno($manejador_interface, toba_aplicacion_modelo $modelo);	
	
	/**
	 * Toba y los metadatos del proyecto ya están instalados
	 * La aplicación puede definir la fuente de datos, crear su estructura y cargarle un set de datos específico
	 */
	function opcion__instalar($parametros);
	
	/**
	 * Ejecuta todos los pasos de migración necesarios para actualizar la estructura y migrar los datos (y posiblemente algo más fuera de la fuente de datos)
	 */
	function opcion__migrar($parametros);
	
}

?>