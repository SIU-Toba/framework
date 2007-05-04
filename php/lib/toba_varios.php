<?php

	function toba_dir()
	{
		return toba_nucleo::toba_dir();
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

	function array_renombrar_llave($arreglo,$nueva_llave)
	//Renombra todas las llaves de primer nivel de $arreglo por la $nueva_llave
	{
		$llaves = array_keys($arreglo);
		$cambios = array();
		foreach ($llaves as $llave) {
			$cambios += array($llave => $nueva_llave);
		}
		return array_renombrar_llaves($arreglo, $cambios, false);
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

	function rs_ordenar_por_columna($rs, $columna, $tipo = SORT_ASC)
	{
		$rs_columna = array();
		foreach ($rs as $registro) {
			$rs_columna[] = $registro[$columna];
		}
		array_multisort($rs_columna, $tipo, $rs);
		return $rs;
	}

	function rs_ordenar_por_columnas( $rs, $columnas, $tipo = SORT_ASC )
	{
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
			$parametros[] = $tipo;
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
		if (substr(PHP_OS, 0, 3) == 'WIN'){
			return "\r\n";
		}else{
			return "\n";
		}	
	}

	function sl(){ return salto_linea(); }

	function tecla_acceso($etiqueta)
	//Toma una etiqueta e intenta extraer el caracter de acceso rápido
	// Ej: Proce&sar retornar array('<u>P</u>rocesar', 'P')
	{
		$pos_guia = strpos($etiqueta, '&');
		if ($pos_guia === false || ($pos_guia ==  strlen($etiqueta) - 1)) {
			$etiqueta = htmlspecialchars($etiqueta);
			return array($etiqueta, null);
		} else {
			$tecla = $etiqueta{$pos_guia + 1};
			$nueva_etiqueta = str_replace("&$tecla", "%_%$tecla%_%", $etiqueta);
			$nueva_etiqueta = htmlspecialchars($nueva_etiqueta);
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
			$aplanado[$clave] = ($campo === null) ? current($arreglo) : $arreglo[$campo];
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
					throw new toba_error("La fila del recordset no contiene la clave '$clave'. ".var_export($fila, true));
				}
			}
            $valores[implode(apex_qs_separador, $valores_clave)] = $fila[$valor];
		}
        return $valores;
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
	
	function agregar_dir_include_path($dir)
	{
		$i_path = ini_get("include_path");
		if (substr(PHP_OS, 0, 3) == 'WIN'){
			ini_set("include_path", $i_path . ";.;" . $dir );
		}else{
			ini_set("include_path", $i_path . ":.:" . $dir);
		}
	}
	
	function get_url_desarrollos()
	{
		$host = (toba::instalacion()->get_id_grupo_desarrollo() != 0) ? "desarrollos2" : "desarrollos";
		return "http://$host.siu.edu.ar";
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
			default:
				throw new toba_error("El operador $operador no está soportado");
		}
	}
	
	function encriptar_con_sal($clave, $metodo, $sal=null)
	{
	    if ($sal === null) {
	        $sal = substr(md5(uniqid(rand(), true)), 0, 10);
	    } else {
	        $sal = substr($sal, 0, 10);
	    }
	    return $sal . hash($metodo, $sal . $clave);		
	}
?>