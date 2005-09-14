<?php
/*
	El DEFINE que sigue es para poder usar cada tanto el TRACE posta,
		el tema es que si se dispara desde un evento se va todo al carajo (call_user_func_array)
*/
define("excepcion_toba_display","0");
/**
* Error interno de toba
*/
class excepcion_toba extends Exception
{
	function __construct($mensaje)
	{
		parent::__construct($mensaje);
	}
	
	function agregar_mensaje($mensaje)
	{
		$this->message .= $mensaje;
	}

	function mensaje_web()
	{
		$html = "<div style='text-align: left'>";
		$html .= "<strong style='color:red;'>EXCEPCION:</strong><br>";
		$html .= parent::getMessage()."<br><br>";
		$html .= "Archivo \"".parent::getFile()."\", línea ".parent::getLine()."<br>";
		$html .= "<a href='javascript: var i' onclick=\"o = this.nextSibling; o.style.display = (o.style.display == 'none') ? '' : 'none';\">[detalle]</a>";
		$html .= "<ul style='display: none'>";
		if(!excepcion_toba_display)
		{
			$html .= "-------------------------------------------\n";
			$html .= parent::getTraceAsString() ."\n";
			$html .= "-------------------------------------------\n";
		}else{
			//	ESTO se cuelga en la 5.0.4 si se usa "call_user_func_array"
			//		-> Pasa cuando el error se da a partir de un evento de los EI
			foreach (parent::getTrace() as $paso) {
				$clase = '';
				if (isset($paso['class']))
					$clase .= $paso['class'];
				if (isset($paso['type']))
					$clase .= $paso['type'];				
				$html .= "<li><strong>$clase{$paso['function']}</strong><br>
						Archivo: {$paso['file']}, línea {$paso['line']}<br>";
				if (! empty($paso['args'])) {
					$html .= "Parámetros: <ol>";
					foreach ($paso['args'] as $arg) {
						$html .= "<li>";
						$html .= var_export($arg, true);
						$html .= "</li>";
					}
					$html .= "</ol>";
				} 
				$html .= "</li>";
			}
		}
		$html .= "</ul></div>";
		return $html;
	}
	
	function mensaje_txt()
	{
		$texto = parent::getMessage(). "\r\n";
		$texto .= "Archivo \"".parent::getFile()."\", línea ".parent::getLine()."\n";
		if(!excepcion_toba_display)
		{
			$texto .= "-------------------------------------------\r\n";
			$texto .= parent::getTraceAsString() ."\r\n";
			$texto .= "-------------------------------------------\r\n";
		}else{
			//	ESTO se cuelga en la 5.0.4 si se usa "call_user_func_array"
			//		-> Pasa cuando el error se da a partir de un evento de los EI
			foreach (parent::getTrace() as $paso) {
				$clase = '';
				if (isset($paso['class']))
					$clase .= $paso['class'];
				if (isset($paso['type']))
					$clase .= $paso['type'];				
				$texto .= "$clase{$paso['function']}\r\n
						Archivo: {$paso['file']}, línea {$paso['line']}\r\n";
				if (! empty($paso['args'])) {
					$texto .= "Parámetros: ";
					foreach ($paso['args'] as $arg) {
						$texto .= var_export($arg, true);
					}
				} 
			}
		}
		return $texto;
	}
	
	function mensaje_consola()
	{
		echo $this->__toString();
	}
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

?>