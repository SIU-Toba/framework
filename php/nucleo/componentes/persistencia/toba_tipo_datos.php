<?php

/**
	Tipos de dato (apex_toba_tipo_datos)

	E    Entero                         
	N    Numero                         
	C    Caracter                       
	F    Fecha                          
	T    Timestamp                      
	L    Logico                         
	X    Caracter largo    
	B    Binario                        
 * @package Componentes
 * @subpackage Persistencia
 */
class toba_tipo_datos
{
	static function numero($tipo){
		return ($tipo == "E") || ($tipo == "N");
	}

	static function fecha($tipo){
		return ($tipo == "F");
	}
}
?>