<?
require_once("nucleo/browser/clases/objeto.php");						//Ancestro de todos los OE

class objeto_esquema extends objeto
/*
	@@acceso: publico
	@@desc: Permite representar planeamientos en el eje del tiempo
*/
{
	var $debug = false;
	var $formato_salida = "-Tgif";
	var $modelo_ejecucion = "cache";
	var $alto;
	var $ancho;
	
	
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
    	
	function objeto_esquema($id)
/*
	@@acceso: publico
	@@desc: Constructor de la clase
*/
	{
		parent::objeto($id);
		$this->alto = isset($this->info_esquema['alto']) ?  $this->info_esquema['alto'] : 400;
		$this->ancho = isset($this->info_esquema['ancho']) ?  $this->info_esquema['ancho'] : 500;
		$this->tipo_incrustacion = isset($this->info_esquema['tipo_incrustacion']) ?  $this->info_esquema['tipo_incrustacion'] : 'iframe';
	}

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//---- Plan -----------------------
		$sql["info_esquema"]["sql"] = "SELECT	parser,
												descripcion,
												dot,
												debug,
												formato,					
												modelo_ejecucion,			
												modelo_ejecucion_cache,	
												tipo_incrustacion,		
												ancho,					
												alto,
												sql						
									FROM	apex_objeto_esquema
									WHERE	objeto_esquema_proyecto='".$this->id[0]."'
               				AND     objeto_esquema='".$this->id[1]."';";
		$sql["info_esquema"]["tipo"]="1";
		$sql["info_esquema"]["estricto"]="1";
		return $sql;
	}
//---------------------------------------------------------------

	function obtener_dot()
	{
		return $this->info_esquema['dot'];
	}
//---------------------------------------------------------------

	function controlar_sintaxis_dot()
	{
		return true;
		//Esto deberia setear el estado de DEBUG en true...	
	}
//---------------------------------------------------------------

	function generar_sentencia_incrustacion($url)
	{
		if( $this->tipo_incrustacion =="iframe" ){
			//La imagen incrusta como IFRAME
			echo "\n<iframe align='center' width='{$this->ancho}' height='{$this->alto}' src='$url'></iframe>";
		}elseif( $this->tipo_incrustacion == "img" ){
			//La imagen se incrusta como IMG
			echo "<img src='$url' border='0'>";
		}else{
			//La imagen se incrusta como IMG
			echo "<img src='$url' border='0'>";
		}
	}
//---------------------------------------------------------------

	function generar_imagen_archivo()
	//Modelo de ejecucion de tipo CACHE
	{
		$carpeta = "temp/";
		$base_archivo = "img_" . $this->id[1];
		$path_relativo = $carpeta . $base_archivo;
		$formato = "gif";
		$path = $this->solicitud->hilo->obtener_proyecto_path_www($path_relativo);
		include_once("nucleo/lib/esquema.php");
		$status = esquema::generar_i($this->obtener_dot(), $path["real"], $formato, $this->info_esquema['parser']);
		if( $status[0] ){
			echo $this->generar_sentencia_incrustacion( $path["browser"].".".$formato );
		}else{
			if($this->info_esquema['debug']=="1"){
				echo ei_mensaje($status[1]);	
			}else{
				echo $this->generar_sentencia_incrustacion( $path["browser"].".".$formato );
			}
		}
	}
//---------------------------------------------------------------

	function obtener_imagen()
	//Modelo de ejecucion en el AIRE. Esto se llama desde el item
	//generador /basicos/generar_esquema
	{
		//Busco el texto escrito en DOT
		$dot = $this->obtener_dot();
		//Habro un proceso, obtengo handler a stdin y stdout del mismo
		$descriptorspec = array(
		   0 => array("pipe", "r"),  	// stdin
		   1 => array("pipe", "w"),  	// stdout
		   2 => array("pipe", "w"));	// stderr
		//Habro el proceso hijo
		$proceso_hijo = proc_open("dot {$this->formato_salida}", $descriptorspec, $pipes);
		if (is_resource($proceso_hijo))
		{
			ob_start();
			//header("Content-type: image/gif");			
						
			//Escribo en STDIN el grafico
		    //echo $dot;
		    fwrite($pipes[0], $dot );
		    fclose($pipes[0]);
		
			//Busco ERRORES
			$error = "";
		    while(!feof($pipes[2])) {
		        $error .= fgets($pipes[2], 1024);
		    }
		    fclose($pipes[2]);

		    if(trim($error)==""){
				
				$ok = fpassthru($pipes[1]);
				if(!$ok){
					echo "ocurrio un error generando la imagen";	
				}
				
			    /*while(!feof($pipes[1])) {
					echo fread($pipes[1], 4096);
			    }
			    fclose($pipes[1]);*/

		    }else{
		    	echo $error;
			}
		    
		    $return_value = proc_close($proceso_hijo);
		 	//Falta el mecanismo para controlar errores...
		 	//echo "command returned $return_value\n";
		 	
		 	ob_end_flush();
		}
	}

//################################################################################
//###########################                         ############################
//###########################         INTERFACE       ############################
//###########################                         ############################
//################################################################################

	function obtener_html($cabecera=true)
/*
	@@acceso: publico
	@@desc: Genera la interface de este elemento
*/
	{
		echo "<div align='center'>\n";			
		echo "<table class='objeto-base'>\n";

		if($cabecera){
			echo "<tr><td>";
			$this->barra_superior($this->info["titulo"]);
			echo "</td></tr>\n";
		}

		echo "<tr><td>";
		echo "<TABLE width='100%' class='tabla-0'>";
		echo "<tr><td>\n";

		if( $this->modelo_ejecucion =="cache" ){	//-- MODO CACHE
			$this->generar_imagen_archivo();
		}else{										//-- MODO RUNTIME
    	    $id_prop_objeto = array( 'proyecto'=> $this->id[0], 'objeto'=>$this->id[1] );
			$url = $this->solicitud->vinculador->obtener_vinculo_a_item("toba","/basicos/generador_esquema", $id_prop_objeto, false);
			$this->generar_sentencia_incrustacion($url);
		}

		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	}
}
//################################################################################
?>
