<?php
require_once('lib/toba_manejador_archivos.php');

/**
 *  Permite editar un archivo PHP del sistema
 *  @package Varios
 */
class toba_archivo_php
{
	protected $nombre;
	protected $fp = null;
	protected $contenido = '';
	protected $archivo_abierto = false;

	/**
	 * @param string $nombre Path completo del archivo
	 */
	function __construct($nombre)
	{
		$this->nombre = $nombre;	
	}
	
	/**
	 * Retorna la ruta completa del archivo
	 */
	function nombre()
	{
		return $this->nombre;
	}
	
	/**
	 * Retorna verdadero si el archivo esta creado
	 * @return boolean
	 */
	function existe()
	{
		return file_exists($this->nombre) && is_file($this->nombre);
	}


	/**
	 * Retorna verdadero si el archivo no tiene texto
	 * @return boolean
	 */
	function esta_vacio()
	{
		$this->edicion_inicio();
		if (trim($this->contenido) == '')
			return true;
		else
			return false;
	}

	/**
	 * Retorna verdadero si el archivo contiene algo de codigo php (sin contar los tags de apertura y finalizacion
	 * @return boolean
	 */
	function contiene_codigo_php()
	{
		$macheos = array();
		if (preg_match("/(<\?php|<\?)(.*)\?>/",file_get_contents($this->nombre),$macheos)) {
			return true;
		}
		return false;
	}

	/**
	 * Retorna verdadero si el archivo tiene definido una clase con este nombre
	 * @return boolean
	 */
	function contiene_clase($nombre)
	{
		return self::codigo_tiene_clase(file_get_contents($this->nombre), $nombre);
	}

	/**
	 * Retorna verdadero si el archivo tiene definido un metodo con el nombre especificado
	 * @return boolean
	 */
	function contiene_metodo($nombre)
	{
		return self::codigo_tiene_metodo(file_get_contents($this->nombre), $nombre);
	}


	function get_codigo_php_puro()
	{
		return self::codigo_sacar_tags_php($this->get_codigo());
	}
	
	function get_codigo()
	{
		return self::convertir_formato_interno($this->nombre);
	}
	
	/**
	 * Muestra el código fuente del archivo en formato HTML
	 */
	function mostrar()
	{
		require_once(toba_dir()."/php/3ros/PHP_Highlight.php");
		$h = new PHP_Highlight(false);
		$h->loadFile($this->nombre);		
		$formato_linea = "<span style='background-color:#D4D0C8; color: black; font-size: 10px;".
						" padding-top: 2px; padding-right: 2px; margin-left: -4px; width: 20px; text-align: right;'>".
						"%2d</span>&nbsp;&nbsp;";
		$h->toHtml(false, true, $formato_linea, true);
	}
	
	/**
	 * Utiliza una llamada al sist. operativo para que abra el archivo en un edtiro del sist. de ventanas del servidor
	 */
	function abrir()
	{
		$cmd = toba::instalacion()->get_editor_php();
		if (strlen($cmd) == 0) {
			throw new toba_error_def("No se encuentra definido el editor por defecto a utilizar en la instalación");
		}
		if (toba_manejador_archivos::es_windows()) {
			$archivo = toba_manejador_archivos::path_a_windows($this->nombre);
			$com = "$cmd $archivo";
			toba::logger()->debug("Intentando abrir archivo con comando: '$com'");
			exec($com);
		} else {
			$archivo = toba_manejador_archivos::path_a_unix($this->nombre);
			$archivo = str_replace(" ", "\\ ", $archivo);
			$com = "$cmd $archivo";
			toba::logger()->debug("Intentando abrir archivo con comando: '$com'");
			$fp = popen($com. ' 2>&1', 'r');
			stream_set_blocking($fp, 0);
			sleep(1);
			$salida = fgets($fp, 2096);
			pclose($fp);			
			if ($salida != '') {
				throw new toba_error("Intentando abrir archivo con comando: '$com'<br><br>Mensaje de error:<br>".$salida);
			}

		}
	}
	
	/**
	 *	Incluye en el runtime PHP al archivo
	 */
	function incluir()
	{
		//Verifica que no se haya incluido previamente
		$ya_incluido = false;
		foreach (get_included_files() as $archivo_incluido) {
			$nombre_incluido = str_replace('\\', '/', $archivo_incluido);
			if (strcasecmp($this->nombre, $nombre_incluido) == 0)
				$ya_incluido = true;
		}
		if (!$ya_incluido) {
			include_once($this->nombre);
		}
	}	
	
	/**
	 * Crea el archivo con los tags php básicos
	 */
	function crear_basico()
	{
		$this->edicion_inicio();
		$this->contenido = "<?php ?>";
		$this->edicion_fin();
	}
	
	//--------------------------------------------------------------------------------
	//-------------------------EDITAR EL ARCHIVO -------------------------------------
	//--------------------------------------------------------------------------------
	
	/**
	 * Abre el archivo para edición
	 */
	function edicion_inicio()
	{
		if( ! $this->archivo_abierto ) {
			if (file_exists($this->nombre)) {
				$this->contenido = $this->get_codigo();
			} else {
				$this->contenido = '';
			}
			$this->archivo_abierto = true;
		}
	}
	
	/**
	 * Cierra el archivo
	 */
	function edicion_fin()
	{
/*		echo "Contenido: <pre>";
		echo htmlentities($this->contenido);
		echo "</pre>";
*/		
		toba_manejador_archivos::crear_arbol_directorios(dirname($this->nombre));
		$contenido = self::convertir_formato_so($this->contenido);
		file_put_contents($this->nombre, $contenido);
	}	

	/**
	 * Retorna el contenido del archivo
	 */
	function contenido()
	{
		return $this->contenido;
	}
	
	/**
	 * Agrega codigo al inicio del archivo (dentro del tag php)
	 */
	function insertar_al_inicio($codigo)
	{
		$pos = strpos($this->contenido, '<?php');
		if ($pos !== false) {
			$inicio = "<?php";
			$final = substr($this->contenido, $pos + 5);
		} else {
			$pos = strpos($this->contenido, '<?');
			if ($pos !== false) {
				$inicio = "<?";
				$final = substr($this->contenido, $pos + 2);
			} else {
				throw new toba_error("El archivo no contiene las marcas PHP de inicio de archivo");
			}
		}
		$this->contenido = $inicio."\n".$codigo.$final;
	}

	/**
	 * Agrega codigo al final del archivo (dentro del tag php)
	 */	
	function insertar_al_final($codigo)
	{
		$pos = strrpos($this->contenido, '?>');
		if ($pos !== false) {
			$final = "?>";
			$inicio = substr($this->contenido, 0, $pos);
		} else {
			throw new toba_error("El archivo no contiene las marcas PHP de fin de archivo");
		}
		$this->contenido = $inicio."\n".$codigo."\n".$final;
	}
	
	function insertar($codigo)
	{
		$this->contenido = "<?php\n";
		$this->contenido .= $codigo;
		$this->contenido .= "\n?>";
	}

	/**
	*	Dado un codigo PHP, extrae un metodo y los sustituye por codigo nuevo
	*/
	static function reemplazar_metodo($codigo, $nombre_metodo_a_extraer, $codigo_a_insertar)
	{
		$contenido = explode("\n",$codigo);
		$codigo_a_insertar = explode("\n", $codigo_a_insertar);
		$encontrado = false;
		$comenzo_cuerpo = false;
		$balance = 0;
		$linea_i = null;
		$linea_f = null;
		//Busco la region en donde se encuentra el metodo
		foreach($contenido as $linea => $codigo) {
			if(	!$encontrado && preg_match("/(protected|public|private)*\s*function\s+$nombre_metodo_a_extraer\s*?\(/",$codigo)) {
				$encontrado = true;
				$linea_i = $linea;
			}
			if( $encontrado ) {
				if(!$comenzo_cuerpo && (strpos($codigo,'{')!==false) ) $comenzo_cuerpo = true;
				$balance += substr_count($codigo, '{');
				$balance -= substr_count($codigo, '}');
				if($comenzo_cuerpo && $balance==0) {
					$linea_f = $linea;
					break;
				}
			}
		}
		//Reemplazo la region por el codigo nuevo
		if( $linea_i && $linea_f ) {
			$inicio = array_splice($contenido, 0, $linea_i);
			$recorte = array_splice($contenido, 0, ($linea_f-$linea_i)+1);
			$fin = $contenido;
			$contenido = array_merge($inicio, $codigo_a_insertar, $fin);
			return implode("\n", $contenido);
		} else {
			//Lanzar una excepcion?
			throw new toba_error("toba_archivo_php: Error reemplazando el metodo '$nombre_metodo_a_extraer': no existe!");	
		}
	}
	
	/**
	 * Retorna el cuerpo de un método
	 */
	static function codigo_get_metodo($codigo, $nombre_metodo_a_extraer)
	{
		$contenido = explode("\n",$codigo);
		$encontrado = false;
		$comenzo_cuerpo = false;
		$balance = 0;
		$linea_i = null;
		$linea_f = null;
		//Busco la region en donde se encuentra el metodo
		foreach($contenido as $linea => $codigo) {
			if(	!$encontrado && preg_match("/(protected|public|private)*\s*function\s+$nombre_metodo_a_extraer\s*?\(/",$codigo)) {
				$encontrado = true;
				$linea_i = $linea;
			}
			if( $encontrado ) {
				if(!$comenzo_cuerpo && (strpos($codigo,'{')!==false) ) $comenzo_cuerpo = true;
				$balance += substr_count($codigo, '{');
				$balance -= substr_count($codigo, '}');
				if($comenzo_cuerpo && $balance==0) {
					$linea_f = $linea;
					break;
				}
			}
		}
		if( $linea_i && $linea_f ) {
			$salida = '';
			$cantidad = 1;
			$contenido[$linea_i+1] = str_replace('{', '', $contenido[$linea_i+1], $cantidad);
			for ($i = $linea_i+1; $i < $linea_f; $i++) {
				$salida .= $contenido[$i]."\n";
			}
			return trim($salida);
		} else {
			//Lanzar una excepcion?
			throw new toba_error("toba_archivo_php: Error reemplazando el metodo '$nombre_metodo_a_extraer': no existe!");	
		}		
		
	}

	/**
	 * Determina si una porción de código tiene un método específico
	 */
	static function codigo_tiene_metodo($codigo, $nombre)
	{
		return preg_match("/function\s+$nombre\s*\(/", $codigo);		
	}
	
	/**
	 * Determina si una porción de código tiene un método específico
	 */
	static function codigo_tiene_metodo_js($codigo, $nombre)
	{
		return preg_match("/\.$nombre = function\s*\(/", $codigo);		
	}	

	/**
	 * Determina si una porción de código tiene un método específico
	 */
	static function codigo_tiene_codigo($actual, $codigo)
	{
		return strpos($actual, $codigo) !== false;
	}	
	
	/**
	 * Determina si una porción de código tiene una clase específica definida
	 */	
	static function codigo_tiene_clase($codigo, $nombre)
	{
		return preg_match("/class\s+$nombre/", $codigo);
	}

	/**
	 * Retorna los métodos definidos en una porción de código
	 */	
	static function codigo_get_nombre_metodos($codigo, $solo_publicos=true)
	{
		$no_publicos = '';
		if ($solo_publicos) {
			$no_publicos = '';
		}
		$patron = "/(protected|private|public|)[\s]+function[\s]+([\w]+)\s*\(/";
		$metodos = array();
		preg_match_all($patron, $codigo, $macheos);
		if (count($macheos) == 3) {
			foreach ($macheos[2] as $id => $metodo) {
				if (!$solo_publicos || ($macheos[1][$id] == 'public' || $macheos[1][$id] == '')) {
					$metodos[] = $metodo;
				}
			}
		}
		sort($metodos);
		return $metodos;
	}
	
	/**
	 * Toma una porción de código y agrega un nuevo método
	 */	
	static function codigo_agregar_metodo($codigo_actual, $metodo)
	{
		$pos = strrpos($codigo_actual, '}');
		if ($pos !== false) {
			return substr($codigo_actual, 0, $pos-1)."\n".
								$metodo."\n".substr($codigo_actual, $pos);
		} else {
			throw new toba_error("El codigo no contiene una llave de fin de clase");
		}

	}	
	
	static function codigo_quitar_identacion($codigo, $identacion=1)
	{
		$salida = array();
		foreach(explode("\n", $codigo) as $linea) {
			$linea_salida = '';
			$quitar = true;
			for($i=0; $i<$identacion; $i++) {
				if (!isset($linea[$i]) || $linea[$i] != "\t") {
					$quitar = false;
				}
			}
			if ($quitar) {
				$linea = substr($linea, $identacion);
			}
			$salida[] = $linea;
		}
		return implode("\n", $salida);
	}
	
	
	/**
	 * Toma una porción de código y le quita los tags php si los tiene
	 */		
	static function codigo_sacar_tags_php($codigo)
	{
		$codigo = str_replace('<?php', '', $codigo);
		return trim(str_replace('?>', '', $codigo));
	}

	static function convertir_formato_interno($nombre_archivo)
	{
		$codigo =  file_get_contents($nombre_archivo);
		$codigo_interno = str_replace("\r\n", "\n", $codigo);
		return $codigo_interno;
	}

	static function convertir_formato_so($contenido_archivo)
	{
		return str_replace("\n", PHP_EOL, $contenido_archivo);
	}
}
?>