<?php

/**
 * Clase de entrada del usuario, un m�todo por interacci�n
 */
interface toba_aplicacion_comando
{
	
	function set_entorno($manejador_interface, toba_aplicacion_modelo $modelo);	
	
	/**
	 * Toba y los metadatos del proyecto ya est�n instalados
	 * La aplicaci�n puede definir la fuente de datos, crear su estructura y cargarle un set de datos espec�fico
	 */
	function opcion__instalar($parametros);
	
	/**
	 * Ejecuta todos los pasos de migraci�n necesarios para actualizar la estructura y migrar los datos (y posiblemente algo m�s fuera de la fuente de datos)
	 */
	function opcion__migrar($parametros);
	
}

?>