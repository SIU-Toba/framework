<?php

/**
* Error interno de toba
*/
class excepcion_toba extends Exception
{

}

/**
* Excepci�n producida en tiempo de ejecuci�n producidas por alguna interacci�n del usuario
*/
class excepcion_toba_usuario extends excepcion_toba
{

}

/**
* Excepci�n producida en tiempo de definici�n producidas por error del desarrollo
*/
class excepcion_toba_def extends excepcion_toba
{

}

/**
 * Excepci�n producida en tiempo de ejecuci�n cuando el usuario no tiene permitido alg�n derecho
 */
class excepcion_toba_permisos extends excepcion_toba 
{
	
}

/**
 * Excepci�n producida por un login incorrecto
 */
class excepcion_toba_login extends excepcion_toba
{

}


?>