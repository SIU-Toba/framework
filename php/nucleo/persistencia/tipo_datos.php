<?
/*
	Tipos de dato (apex_tipo_datos)

	E    Entero                         
	N    Numero                         
	C    Caracter                       
	F    Fecha                          
	T    Timestamp                      
	L    Logico                         
	X    Caracter largo    
	B    Binario                        
*/
class tipo_datos
{
	static function numero($tipo){
		return ($tipo == "E") || ($tipo == "N");
	}

	static function fecha($tipo){
		return ($tipo == "F");
	}
}
?>