<?php

	define("apex_pa_nivel_error",3);
	//error_reporting( 0 );//No mostrar errores
	//set_error_handler("error_generico");//Paso el control del display a la funcion de abajo

	// Error generico
	function error_generico($errno, $errmsg, $filename, $linenum, $vars)
	{
	    $momento = date("Y-m-d H:i:s");
   		$tipo_error = array (
       	        1   =>  "Error",
           	    2   =>  "Warning",
               	4   =>  "Parsing Error",
                8   =>  "Notice",
   	            16  =>  "Core Error",
       	        32  =>  "Core Warning",
           	    64  =>  "Compile Error",
                128 =>  "Compile Warning",
   	            256 =>  "User Error",
       	        512 =>  "User Warning",
           	    1024=>  "User Notice"
                );
		$mensaje["momento"] = $momento;
		$mensaje["nro"] = $errno;
		$mensaje["tipo"] = $tipo_error[$errno];
		$mensaje["mensaje"] = $errmsg;
		$mensaje["archivo"] = $filename;
		$mensaje["linea"] = $linenum;

		//Muestro el mensaje
		if ( apex_solicitud_tipo == "browser"){
			require_once("nucleo/browser/interface/ei.php");//puede pasar...
			switch( apex_pa_nivel_error ){
				case 1: 	//TRACE completo de PHP
					ei_arbol(debug_backtrace(),"TRACE");
				case 2: 	//Estado de las variables del contexto
					$mensaje["contexto"] = $vars;
				default: 
							//Mensaje de la excepcion
					enter();
					ei_arbol($mensaje, "ERROR de PHP", 400);
			}
		}else{
			echo "Ha ocurrido una excepcion de PHP";
			print_r($mensaje);
		}
	}
	//-----------------------------------------------------------------

	function error_db_instancia($errno, $errmsg, $filename, $linenum, $vars)
	//No es posible conectarse a la instancia principal
	{
		global $instancia;
		if ( apex_solicitud_tipo == "browser"){
			require_once("nucleo/browser/interface/ei.php");	
			$titulo = "Problemas para conectarse a la instancia";
			ei_html_cabecera($titulo);
			echo "<h2>No es posible contactar la INSTANCIA</h2>";
			//print_r($instancia[apex_pa_instancia]);
			ei_html_pie();
		}else{
			echo "No se creo la conexion a la INSTANCIA";
			print_r($instancia[apex_pa_instancia]);
		}
		exit();//Chau...
	}
	//-----------------------------------------------------------------

	function error_php_actividad($errno, $errmsg, $filename, $linenum, $vars)
	{
		$mensaje = "<b>ATENCION:</b> la ACTIVIDAD solicitada no se encuentra disponible<br>";
		echo ei_mensaje($mensaje . $errmsg,"error");
	}
	//-----------------------------------------------------------------

?>