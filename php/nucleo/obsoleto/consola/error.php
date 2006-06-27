<?php

	error_reporting(E_ALL);
	set_error_handler("control_de_errores");
	
	function control_de_errores($tipo, $mensaje, $archivo, $linea, $contexto)
	{
		global $solicitud;
		if ($tipo == E_WARNING)
		{
			if(strstr($mensaje,"cargar_php_include")){
				throw new excepcion_toba("ERROR HANDLER: No es posible cargar el ejecutable: 
								<b>{$solicitud->ejecutable['nombre']}</b> 
								porque el archivo <b>{$solicitud->ejecutable['referencia']}</b>
								NO EXISTE!
								<br><br>");
		}
	}

?>