<?php

	function toba_dir()
	{
		return toba_nucleo::toba_dir();
	}

	/**
	* Convierte el string a UTF-8 a menos que ya se encuentre en dicho encoding.
	* @param string $s
	* @return string $s en utf8
	*/
	function utf8_e_seguro($s)
	{
		if (mb_detect_encoding($s, "UTF-8", true) == "UTF-8") {
			return $s;
		}

		return utf8_encode($s);
	}

	/**
	* Convierte a LATIN-1 un string UTF-8, a menos que no este en ese encoding.
	* @param string $s
	* @return string $s en latin1
	*/
	function utf8_d_seguro($s)
	{
		if (mb_detect_encoding($s, "UTF-8", true) == "UTF-8") {
			return utf8_decode($s);
		}

		return $s;
	}

	/**
	 * comienza_con
	 * Testea si el string $haystack comienza con $needle
	 *
	 * @param     string
	 * @param     string
	 * @return    bool
	 */
	function comienza_con($haystack, $needle){
		return strpos($haystack, $needle) === 0;
	}

	/**
	 * Si $var está seteada la devuelve, sino devuelve el valor por defecto
	 * de la misma
	 * @param mixed $var
	 * @param mixed $default
	 * @return mixed
	 */
	function get_var(&$var, $default = null) {
		if (isset($var)) {
			return $var;
		}
		return $default;
	}

	/**
	 * Devuelve el nombre de la clase de acuerdo a la extensión de proyecto
	 * que se indique
	 * @param string $nombre
	 * @param string $proyecto
	 * @param array $extensiones arreglo asociativo con 2 parámetros: el 1ro es
	 * extension_toba y el 2do extension_proyecto. Se obtiene de
	 * toba::proyecto()->get_clases_extendidas()
	 */
	function get_nombre_clase_extendida($nombre, $proyecto, $extensiones)
	{
		$res = substr($nombre, strlen('toba_'));
		if ($extensiones[1]) {
			return $proyecto.'_pers_'.$res;
		} else if ($extensiones[0]) {
			return $proyecto.'_'.$res;
		}

		return $nombre;
	}

	function array_elem_limitrofes($arreglo, $elem)
	{
		$arreglo = array_values($arreglo);
		$anterior = false;
		$siguiente = false;
		$pos = array_search($elem, $arreglo);
		if ($pos !== false) {
			if ($pos > 0) {
				$anterior = $arreglo[$pos - 1];	
			}
			if ($pos < count($arreglo) -1) {
				$siguiente = $arreglo[$pos + 1];
			}
		}
		return array($anterior, $siguiente);
	}
	
	function array_no_nulo($array)
	//Controla que el array tiene todas sus entradas en NULL
	{
		$x = false;
		foreach( array_keys($array) as $id ){
			if( isset($array[$id]) ) return true;
		}
		return $x;
	}

	/**
	 * Renombra todas las llaves de primer nivel de $arreglo por la $nueva_llave
	 */
	function array_renombrar_llave($arreglo,$nueva_llave)
	{
		$llaves = array_keys($arreglo);
		$cambios = array();
		foreach ($llaves as $llave) {
			$cambios += array($llave => $nueva_llave);
		}
		return array_renombrar_llaves($arreglo, $cambios, false);
	}
	
	function array_cambiar_prefijo_claves($arreglo, $prefijo, $quitar)
	{
		$salida = array();
		foreach ($arreglo as $id => $dato) {
			if ($quitar && substr($id, 0, strlen($prefijo)) == $prefijo) {
				$id = substr($id, strlen($prefijo));
			}
			if (! $quitar) {
				$id = $prefijo.$id;
			}
			$salida[$id] = $dato;
		}		
		return $salida;
	}	
	
	/**
	 * Recorre un arreglo dejando solo aquellas entradas pasadas por parametro
	 */
	function array_dejar_llaves($arreglo, $llaves)
	{
		$nuevo = array();
		foreach ($arreglo as $clave => $valor) {
			if (in_array($clave, $llaves)) {
				$nuevo[$clave] = $valor;	
			}
		}
		return $nuevo;
	}
	
	function array_renombrar_llaves($arreglo, $cambios, $recursivo = true)
	//Toma un conjunto de $cambios ("original" => "reemplazo") y los aplica a $arreglo
	{
		if (is_array($arreglo)) {
			foreach($arreglo as $k => $v) {
				if (isset($cambios[$k]) && strlen($cambios[$k])>0) {
					unset($arreglo[$k]);
					$k=$cambios[$k];
				}
				if (is_array($v) && $recursivo) {
					$arreglo[$k]= array_renombrar_llaves($v, $cambios);
				} else {
					$arreglo[$k]=$v;
				}
			}
		}
		return $arreglo;
	}	
	
	/**
	 * Determina si alguna componente recursiva del arreglo es un objeto php
	 * @param array $variable
	 * @return boolean
	 */
	function array_posee_objetos($variable)
	{
		foreach($variable as $elemento) {
			if(is_object($elemento)) {
				return true;	
			}
			if(is_array($elemento)) {
				return array_posee_objetos($elemento);
			}
		}
		return false;
	}	
	
	
	function array_a_latin1($arreglo)
	{
		$salida = array();
		foreach ($arreglo as $clave => $valor) {
			if (is_array($valor)) {
				$salida[$clave] = array_a_latin1($valor);
			} elseif (is_string($valor)) {
				$salida[$clave] = utf8_decode($valor);
			} else {
				$salida[$clave] = $valor;
			}
		}		
		return $salida;
	}

	function array_a_utf8($datos){
		if (is_string($datos)) {
			return utf8_encode($datos);
		}
		if (!is_array($datos)) {
			return $datos;
		}
		$ret = array();
		foreach ($datos as $i => $d) {
			$ret[$i] = array_a_utf8($d);
		}
		return $ret;
	}

	/** Transforma un json o arreglo en utf8 a un arreglo en latin1 */
	function rest_decode($datos)
	{
		if(is_string($datos) ){
			$datos = json_decode($datos);
		} //es un json ya decodificada guzzle->response->json
		return array_a_latin1($datos);
	}

	/** Transforma un arreglo en latin1 a un json en utf8 */
	function rest_encode($datos)
	{
		$array = array_a_utf8($datos);
		return json_encode($array);
	}

	/**
	 * Elimina los campos del array con valor null. No se modifica el arreglo
	 * pasado por parámetro, se devuelve uno nuevo con las componentes vacías
	 * eliminadas
	 * @param array $array
	 * @return array
	 */
	function array_eliminar_nulls(&$array)
	{
		$nuevo_array = array();

		foreach ($array as $columna => $valor) {
			if (!is_null($valor)) {
				$nuevo_array[$columna] = $valor;
			}
		}

		return $nuevo_array;
	}

	/**
	 * Borra todos los subarrays vacíos de $array. Modifica la variable de entrada
	 * @param array $array
	 */
	function array_borrar_subarrays_vacios(&$array)
	{
		foreach ($array as $key => $data) {
			if (empty($data)) {
				unset($array[$key]);
			}
		}
	}

	/**
	 * Si el parámetro no es un arreglo o es un arreglo sin la componente 0 mete
	 * el parámetro dentro de un arreglo
	 * @param mixed $elem
	 * @return array
	 */
	function array_wrap($elem)
	{
		if (!is_array($elem) || !isset($elem[0])) {
			return array($elem);
		}
		return $elem;
	}

	function rs_ordenar_por_columna($rs, $columna, $tipo = SORT_ASC)
	{
		if (empty($rs)) {
			return $rs;
		}
		$rs_columna = array();
		foreach ($rs as $registro) {
			$rs_columna[] = $registro[$columna];
		}
		array_multisort($rs_columna, $tipo, $rs);
		return $rs;
	}

	function rs_ordenar_por_columnas( $rs, $columnas, $tipo = SORT_ASC )
	{
		if (empty($rs)) {
			return $rs;
		}
		$sentido_default = SORT_ASC;			//-- Lo necesito para pasar por referencia al call_user_func_array
		// Armo los arrays utilizados para ORDENAR
		$orden = array();
		for ( $a=0; $a < count( $rs ) ; $a++ ) {
			foreach ( $columnas as $id => $col ) {
				$orden[$id][$a] = $rs[$a][$col];
			}
		}
		// Armo los parametros del mutisort
		foreach ( $columnas as $id => $col ) {
			$parametros[] =& $orden[$id];
			if (! is_array($tipo)) {		//Valor comun 
				$parametros[] = &$tipo;
			} elseif (isset($tipo[$col])) {		//Es arreglo asociativo por columna
					$parametros[] =  &$tipo[$col];
			} else {
				$parametros[] =  &$sentido_default;
			}
		}
		$parametros[] =& $rs;
		// Como la funcion trabaja por referencia, tomo la posicion del array que me interesa ordenar
		$indice_resultado = count( $parametros ) - 1;
		call_user_func_array( 'array_multisort', $parametros );
		return $parametros[ $indice_resultado ];
	}

	function pasar_a_unica_linea($string)
	{
		return  preg_replace("/\r\n|\n/", "\\n", $string);
	}

	function salto_linea()
	//Salto de linea dependiente de la plataforma
	{
		return PHP_EOL;
	}

	function sl(){ return salto_linea(); }

	function tecla_acceso($etiqueta)
	//Toma una etiqueta e intenta extraer el caracter de acceso rápido
	// Ej: Proce&sar retornar array('<u>P</u>rocesar', 'P')
	{
		$pos_guia = strpos($etiqueta, '&');
		if ($pos_guia === false || ($pos_guia ==  strlen($etiqueta) - 1)) {
			$etiqueta = htmlspecialchars($etiqueta, null, 'ISO-8859-1');
			return array($etiqueta, null);
		} else {
			$tecla = $etiqueta{$pos_guia + 1};
			$nueva_etiqueta = str_replace("&$tecla", "%_%$tecla%_%", $etiqueta);
			$nueva_etiqueta = htmlspecialchars($nueva_etiqueta, null, 'ISO-8859-1');
			$nueva_etiqueta = str_replace("%_%$tecla%_%", "<u>$tecla</u>", $nueva_etiqueta);			
			return array($nueva_etiqueta, $tecla);
		}
	}
	
	function array_borrar_valor(& $arreglo, $valor)
	{
		$pos = array_search($valor, $arreglo);
		if ($pos !== false) {
			array_splice($arreglo, $pos, 1);
		}
	}
	
	function aplanar_matriz($matriz, $campo = null)
	//Toma una matriz y lo aplana a una sola dimension, si no se especifica un campo, se elige el primero
	//Util para aplanar recordset de consultas de un solo campo
	//Ej: array(0 => array('campo' => 'cero'), 1 => array('campo' => 'uno'))  --->  array('cero', 'uno')
	{
		$aplanado = array();
		foreach ($matriz as $clave => $arreglo) {
			if ($campo === null) {
				$aplanado[$clave] = current($arreglo);
			}elseif (isset($arreglo[$campo])) {
				$aplanado[$clave] = $arreglo[$campo];
			}			
		}
		return $aplanado;
	}
	

	/**
	 * Toma una matriz en formato recordset y retorna un arreglo asociativo clave => valor
	 *
	 * @param array $datos_recordset Matriz en formato recordset
	 * @param array $claves Campos (asociativos o numericos) claves de cada registro
	 * @param string $valor Campo valor (asociativo o numerico) de cada registro
	 * @return array 
	 */
    function rs_convertir_asociativo($datos_recordset, $claves=array(0), $valor=1)
    {
    	if (!isset($datos_recordset)) {
    		return array();	
    	}
		$valores = array();
		foreach ($datos_recordset as $fila){
			$valores_clave = array();
			foreach($claves as $clave) {
				if (isset($fila[$clave])) {
					$valores_clave[] = $fila[$clave];
				} else {
					throw new toba_error_def("La fila del recordset no contiene la clave '$clave'. ".var_export($fila, true));
				}
			}

			if (! isset($fila[$valor])){
				throw new toba_error_def("La fila del recordset no contiene la columna '$valor'. ".var_export($fila, true));
			}else{
				$valores[implode(apex_qs_separador, $valores_clave)] = $fila[$valor];
			}
		}
        return $valores;
    }	
    
	//-----------------------------------------------------------------	
	    
	/**
	 * Toma una matriz en formato recordset y retorna la misma matriz pero con la primer componente asociativa
	 *
	 * @param array $datos_recordset Matriz en formato recordset
	 * @param array $claves Campos (asociativos o numericos) claves de cada registro
	 * @param string $valores Campos valor (asociativo o numerico) de cada registro, se asumen todos los campos
	 * @return array 
	 */
    function rs_convertir_asociativo_matriz($datos_recordset, $claves, $valores=null)
    {
    	if (!isset($datos_recordset)) {
    		return array();	
    	}
		$salida = array();
		foreach ($datos_recordset as $fila){
			$valores_clave = array();
			foreach($claves as $clave) {
				$valores_clave[] = $fila[$clave];
			}
			if (isset($valores)) {
				foreach ($valores as $valor) {
	            	$salida[implode(apex_qs_separador, $valores_clave)][$valor] = $fila[$valor];
				}
			} else {
				$clave_temp = implode(apex_qs_separador, $valores_clave);
				if ($clave_temp != '') {
					$salida[$clave_temp] = $fila;
				} else {
					$salida[''] = $fila;
				}
			}
		}
        return $salida;
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
				$valor = str_replace("\"","'",$valor);
				if ($es_objeto)
					$valor_js = $valor;
				else
					$valor_js = "\"$valor\"";
				$js .= "$nombre"."['$id'] = $valor_js;\n";	
			}
		}
		return $js;
	}
	//-----------------------------------------------------------------

	function revision_svn($dir, $usar_comando=false)
	//Busca la revision de a la que corresponde el TOBA
	{
		if (! $usar_comando) {
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
		} else {
			if (file_exists($dir.'/.svn')) {
				$cmd = "svn info \"$dir\" --xml";
				$xml = @simplexml_load_string(`$cmd`);
				if (isset($xml->entry)) {
					return (string) $xml->entry['revision'];
				} else {
					return "DESCONOCIDA";
				}
			}
			return "DESCONOCIDA";
		}
	}

	/**
	 * Clase que otorga rangos para asignación de tabs
	 * @package Varios
	 */
	class toba_manejador_tabs 
	{
		static private $instancia;
		static function instancia() {
			if (! toba_manejador_tabs::$instancia) { 
				toba_manejador_tabs::$instancia = new toba_manejador_tabs(); 
			}
			return toba_manejador_tabs::$instancia;
		}		

		protected $proximo_tab = 1;
		
		function reservar($cantidad) {
			$reserva = array($this->proximo_tab, $this->proximo_tab + $cantidad - 1);
			$this->proximo_tab = $this->proximo_tab + $cantidad;
			return $reserva;
		}
		
		function siguiente()
		{
			return $this->proximo_tab++;
		}
	}
	
	/**
	*	El objeto_de_mentira intenta superar su ejecución sin causar ningun error ni warning
	*	Util para simulaciones
	* @ignore 
	*/
	class toba_objeto_de_mentira
	{
		function __call($m, $a)
		{
			return new toba_objeto_de_mentira();	
		}	
		
		function __set($p, $v)
		{
		}
		
		function __get($p)
		{
			return new toba_objeto_de_mentira();
		}
	}

	function convertir_a_medida_tabla($ancho, $medida='width')
	{
		//El ancho de una tabla no puede tener 'px'			
		$ancho = str_replace('px', '', $ancho);
		if ($ancho != '') {
			$ancho =  "$medida='$ancho'";
		}
		return $ancho;
	}
	
	function sumar_medida($original, $agregado)
	{
		$numero = intval($original);
		return str_replace($numero, $numero + $agregado, $original);
	}

	function acceso_post()
	//Devuelde TRUE si la hoja se accedio por POST
	{
		return ($_SERVER["REQUEST_METHOD"]=="POST");
	}

	function acceso_get()
	//Devuelve TRUE si el acceso se dio por GET
	{
		return ($_SERVER["REQUEST_METHOD"]=="GET");
	}	
	
	function set_tiempo_maximo($tiempo="30")
	{
		ini_set("max_execution_time",$tiempo);
	}

	function reflexion_buscar_propiedades($obj, $patron)
	{
		$ref = new ReflectionClass($obj);
		$props = array();
		foreach ($ref->getProperties() as $prop) {
			$nombre = $prop->getName();
			if (strpos($nombre, $patron) === 0) {
				$props[] = $nombre;
			}
		}
		return $props;		
	}
	
	function reflexion_buscar_metodos($obj, $patron)
	{
		$ref = new ReflectionClass($obj);
		$props = array();
		foreach ($ref->getMethods() as $prop) {
			$nombre = $prop->getName();
			if (strpos($nombre, $patron) === 0) {
				$props[] = $nombre;
			}
		}
		return $props;		
	}	
	
	function agregar_dir_include_path($dir)
	{
		$i_path = ini_get("include_path");
		if (substr(PHP_OS, 0, 3) == 'WIN'){
			ini_set("include_path", $i_path . ";.;" . $dir );
		}else{
			ini_set("include_path", $i_path . ":.:" . $dir);
		}
	}
	
	function get_url_desarrollos($forzar_alternativo=false)
	{
		return "http://repositorio.siu.edu.ar";
	}	
	
	function comparar($valor1, $operador, $valor2)
	{
		switch ($operador) {
			case '==':
				return $valor1 == $valor2;
				break;
			case '===':
				return $valor1 === $valor2;
				break;
			case '<':
				return $valor1 < $valor2;
				break;
			case '<=':
				return $valor1 <= $valor2;
				break;
			case '>':
				return $valor1 > $valor2;
				break;																
			case '>=':
				return $valor1 >= $valor2;
				break;
			case '!=':
				return $valor1 != $valor2;
				break;
			case '!==':
				return $valor1 !== $valor2;
				break;
			case '~':
				return preg_match($valor2, $valor1);		//$valor2 es el pattern
				break;
			default:
				throw new toba_error("El operador $operador no está soportado");
		}
	}
	
	function encriptar_con_sal($clave, $metodo, $sal=null)
	{		
		if (version_compare(PHP_VERSION, '5.3.2') >= 0 || $metodo == 'bcrypt') {
			$hasher = new toba_hash($metodo);			
			if (is_null($sal)) {									//Hash nuevo
				return $hasher->hash($clave);
			} else {											//Verificacion
				$resultado = $hasher->get_hash_verificador($clave, $sal);
				if (strlen($resultado) > 13) {	//Si es menor a 13 hubo error, puede ser que el hash 
					return $resultado;		//se hubiera generado con el metodo anterior
				}				
			}
		}
		
		if (is_null($sal)) {
			$sal = get_salt();
		} else {
			$sal = substr($sal, 0, 10);
		}
		return $sal . hash($metodo, $sal . $clave);		
	}
	
	function get_salt()
	{
		return substr(md5(uniqid(rand(), true)), 0, 10);
	}
	
	function dormir($tiempo)
	{
		if (class_exists('inst_timer')) {
			$timer = new inst_timer();
			$timer->wait($tiempo);
		} else {
			usleep(1000);	
		}		
	}
	
	function ejecutar_consola($cmd, &$stdout, &$stderr)
	{
	    $outfile = tempnam(".", "cmd");
	    $errfile = tempnam(".", "cmd");
	    $descriptorspec = array(
	        0 => array("pipe", "r"),
	        1 => array("file", $outfile, "w"),
	        2 => array("file", $errfile, "w")
	    );
	    $proc = proc_open($cmd, $descriptorspec, $pipes);
	   
	    if (!is_resource($proc)) return 255;
	
	    fclose($pipes[0]);    //Don't really want to give any input
	
	    $exit = proc_close($proc);
	    $stdout = file($outfile);
	    $stderr = file($errfile);
	
	    unlink($outfile);
	    unlink($errfile);
	    return $exit;
	}	

	function cambiar_fecha($fecha,$sep_actual,$sep_nuevo, $buscar_hora=false){
		if (isset($fecha) && trim($fecha)!='') {
			$f = explode($sep_actual,$fecha);
			if(count($f) < 3){
				toba::logger()->notice("Formateador: se recibio una fecha invalida. [$fecha]");
				return '';	
			}
			$extra = explode(' ',$f[2]);
			$dia = str_pad($f[0],2,0,STR_PAD_LEFT);
			$mes = str_pad($f[1],2,0,STR_PAD_LEFT);
			$salida = $extra[0] . $sep_nuevo . $mes . $sep_nuevo .$dia;
			if ($buscar_hora && isset($extra[1])) {
				$hora = explode('.', $extra[1]);
				$salida .= ' '.$hora[0];
			}
			return $salida;
		}
	}	
	
	/**
	 * Convierte una hora de formato 24 a 12
	 * @param string $hora Cadena representando la hora que se quiere convertir
	 */
	function cambiar_hora_formato_12($hora_original)
	{
		if (isset($hora_original) && $hora_original != '') {
			$valores = explode(':', $hora_original);
			if ($valores !== false && ! empty($valores)) {
				$hora = ($valores[0] < 12) ? $valores[0] : $valores[0]  - 12;
				$am_pm = ($valores[0] < 12) ? 'AM' : 'PM';
				$minutos = isset($valores[1]) ? $valores[1]: '00';
				return "$hora:$minutos $am_pm";
			}
		}
		return null;
	}
		
	/**
	 * Purifica una cadena a incluir en la salida html, previniendo ataques XSS 
	 * @param string $texto
	 * @return string
	 */
	function texto_plano($texto)
	{
		return htmlentities($texto, ENT_QUOTES, 'ISO-8859-1');
	}


	function file_size($size, $decimales=2)
	{
		$filesizename = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), $decimales) . $filesizename[$i] : '0 Bytes';
	}

	function checktime($horas, $minutos, $segundos = null)
	{
		$valida = ($horas >= 0 && $horas < 24);							//Inicializa validando las horas 0-23
		$valida = $valida && ($minutos >= 0 && $minutos < 60);  //chequea los minutos
		if (! is_null($segundos)) {													// Si estan disponibles los segundos entonces los verifica tambien
			$valida = $valida && ($segundos >= 0 && $segundos < 60);
		}
		return $valida;
	}
	
	
	function xml_encode($valor) {
		return toba_xml_tablas::encode($valor);
	}
	
	function xml_decode($valor) {
		return toba_xml_tablas::decode($valor);
	}	

?>
