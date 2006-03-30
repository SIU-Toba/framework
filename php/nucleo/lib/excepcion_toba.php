<?php

/**
* Error interno de toba
*/
class excepcion_toba extends Exception
{

}

/**
* Excepción producida en tiempo de ejecución producidas por alguna interacción del usuario
*/
class excepcion_toba_usuario extends excepcion_toba
{

}

/**
* Excepción producida en tiempo de definición producidas por error del desarrollo
*/
class excepcion_toba_def extends excepcion_toba
{

}

/**
 * Excepción producida en tiempo de ejecución cuando el usuario no tiene permitido algún derecho
 */
class excepcion_toba_permisos extends excepcion_toba 
{
	
}

/**
 * Excepción producida por un login incorrecto
 */
class excepcion_toba_login extends excepcion_toba
{

}


?>