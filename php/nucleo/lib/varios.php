<?php

	function array_no_nulo($array)
	//Controla que el array tiene todas sus entradas en NULL
	{
		$x = false;
		foreach( array_keys($array) as $id ){
			if( isset($array[$id]) ) return true;
		}
		return $x;
	}
	//-----------------------------------------------------------------

	function salto_linea()
	//Salto de linea dependiente de la plataforma
	{
		if (substr(PHP_OS, 0, 3) == 'WIN'){
			return "\r\n";
		}else{
			return "\n";
		}	
	}
	//-----------------------------------------------------------------

	function sl(){ return salto_linea(); }
	//-----------------------------------------------------------------

	function tecla_acceso($etiqueta)
	//Toma una etiqueta e intenta extraer el caracter de acceso rápido
	// Ej: Proce&sar retornar array('<u>P</u>rocesar', 'P')
	{
		$pos_guia = strpos($etiqueta, '&');
		if ($pos_guia === false || ($pos_guia ==  strlen($etiqueta) - 1))
			return array($etiqueta, null);
		else {
			//ATENCION!! creo que esta forma de acceder un string esta deprecada!
			$tecla = $etiqueta[$pos_guia + 1];
			$nueva_etiqueta = str_replace("&$tecla", "<u>$tecla</u>", $etiqueta);
			return array($nueva_etiqueta, $tecla);
		}
	}
	//-----------------------------------------------------------------	
	
	function aplanar_matriz($matriz, $campo = null)
	//Toma una matriz y lo aplana a una sola dimension, si no se especifica un campo, se elige el primero
	//Util para aplanar recordset de consultas de un solo campo
	//Ej: array(0 => array('campo' => 'cero'), 1 => array('campo' => 'uno'))  --->  array('cero', 'uno')
	{
		$aplanado = array();
		foreach ($matriz as $clave => $arreglo) {
			$aplanado[$clave] = ($campo === null) ? current($arreglo) : $arreglo[$campo];
		}
		return $aplanado;
	}
	//-----------------------------------------------------------------	

	function dump_array_php($array, $nombre="array",$html=false)
	//Dumpea un array como sintaxis de definicion de array de PHP
	{
		if(is_array($array)){
			$temp = dump_array_nivel($array);
		}else{
			return;
		}
		//concateno el nombre de la variable
		$linea = explode("\n",$temp);
		$php = "";
		for($a=0;$a<count($linea)-1;$a++){
			$php .=	$nombre . $linea[$a] . "\n";
		}
		if($html){
			return "<pre>$php</pre>";	
		}else{
			return $php;
		}
	}
	
	function dump_array_nivel($array)
	{
		$php ="";
		static $prefijo = "";
		foreach($array as $clave => $valor){
			if(is_numeric($clave)){
				$php_x = "[$clave]";
			}else{
				$php_x = "['$clave']";
			}
			$php_l = strlen($php_x);
			//Agrando el prefijo
			$prefijo .= $php_x;
			if(is_array($valor)){
				$php .= dump_array_nivel($valor);
			}else{
				$php .= "$prefijo='$valor';\n";
			}
			//Achico el prefijo
			$prefijo = substr($prefijo,0,strlen($prefijo)-$php_l);
		}
		return $php;
	}
	//-----------------------------------------------------------------

	function dump_array_javascript($array, $nombre, $es_objeto=false)
	{
		$js = "";
		if(is_array($array)){
			$js .= " $nombre = new Object();\n";
			foreach($array as $id => $valor)
			{
				if ($es_objeto)
					$valor_js = $valor;
				else
					$valor_js = "'$valor'";
				$js .= "$nombre"."['$id'] = $valor_js;\n";	
			}
		}
		return $js;
	}
	//-----------------------------------------------------------------

	function revision_svn($dir)
	//Busca la revision de a la que corresponde el TOBA
	{
		$archivo = "$dir/.svn/entries";
		if(file_exists($archivo))
		{
			//$fd = fopen($archivo, "r");
	   		//$contenido = fread($fd, filesize($archivo));
			$contenido = file_get_contents ( $archivo );
			$captura = array();
			if(preg_match("/revision=\"(.*)\"/", $contenido, $captura)){
				//ei_arbol($captura);	
				return $captura[1];
			}else{
				return "DESCONOCIDA";	
			}
		}else{
			return "DESCONOCIDA";	
		}
	}
	//-----------------------------------------------------------------


	//Clase que otorga rangos para asignación de tabs
	class manejador_tabs 
	{
		static private $instancia;
		static function instancia() {
			if(! manejador_tabs::$instancia) { 
				manejador_tabs::$instancia = new manejador_tabs(); 
			}
			return manejador_tabs::$instancia;
		}		

		protected $proximo_tab = 1;
		
		function reservar($cantidad) {
			$reserva = array($this->proximo_tab, $this->proximo_tab + $cantidad - 1);
			$this->proximo_tab = $this->proximo_tab + $cantidad;
			return $reserva;
		}
	}
	
?>