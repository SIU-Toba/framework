<?php

//Libreria de exportacion

	function exportar_a_archivo($archivo_destino, $datos, $separador_campos="|")
	{
	/*
 	@@acceso: actividad
	@@desc: Exporta un array a un archivo
	@@param: string | Archivo destino
	@@param: array | Datos a exportar
	@@param: string | Separador de campos | pipe
	@@retorno: boolean | Estado del proceso
*/
		$archivo = fopen($archivo_destino,"wb");
		if(is_resource($archivo))
		{
			$estado = true;
			for($a=0;$a<count($datos);$a++)
			{
				$linea = implode($separador_campos, $datos[$a]) . salto_linea();
				if(fwrite($archivo, $linea)===false){
					$estado=false;
					break;
				}
			}
			fclose($archivo);
			return $estado;
		}else{
			return false;
		}
	}
	//----------------------------------------------------------------------------

?>