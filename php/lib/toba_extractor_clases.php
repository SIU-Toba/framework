<?php
/**
 * Description of toba_extractor_clases
 *
 * @author sp14ab
 */
include_once 'toba_manejador_archivos.php';
class toba_extractor_clases
{
	const regexp_eliminar_comentarios = '%(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|(//.*)%i';
	const regexp_extractor = '/.*\b(class|interface)[\t\r\n ]+(\w+)[\t\r\n \{]+(?:[\t\r\n ]*extends[\t\r\n ]*(\w*))?/i';

	/**
	 * @var array Los puntos de montaje de donde se tienen que cargar las clases
	 * Estructura:
	 * array(
	 *		path => array(
	 *			'archivo_salida' => path del archivo salida. Relativo al punto de montaje, no empieza con barra
	 *			'dirs_excluidos' => array() <-- opcional. Los directorios a excluir relativos al punto de montaje
	 *			'extras' => array() <-- opcional. Pares 'nombre_clase' => 'path_relativo_al_punto_de_montaje' se agregan como vienen. Util para agregar un archivo de una carpeta excluída
	 *		)
	 * )
	 */
	protected $puntos_montaje;
	protected $extends_excluidos;

	protected $registro;
	protected $clases_repetidas;

	protected $pms_no_encontrados;

	function  __construct($puntos_montaje)
	{
		$this->pms_no_encontrados = array();
		$this->puntos_montaje = $puntos_montaje;
		$this->extends_excluidos = array();
	}

	/**
	 * Devuelve un arreglo de los puntos de montaje que no fueron encontrados
	 * mientras se construía el archivo de autoload
	 * @return array un arreglo con los paths de los puntos de montaje no
	 * encontrados
	 */
	function get_pms_no_encontrados()
	{
		return $this->pms_no_encontrados;
	}

	/**
	 * Devuelve un arreglo ordenado por punto de montaje que contiene las clases
	 * repetidas que se encontraron para cada pm
	 * @return array
	 */
	function get_clases_repetidas()
	{
		return $this->clases_repetidas;
	}

	/**
	 * Setea los nombres de las clases de las cuales si extienden no van en el autoload
	 * @param array $extends arreglo unidimensional de nombres de clases
	 */
	function set_extends_excluidos($extends)
	{
		$this->extends_excluidos = $extends;
	}

	function generar()
	{
		$this->init_registro();
		foreach ($this->puntos_montaje as $path => $data) {
			if (!is_dir($path)) {
				$this->pms_no_encontrados[] = $path;
				continue;	// simplemente se ignora
			}

			
			$dirs_excluidos = (isset($data['dirs_excluidos'])) ? $data['dirs_excluidos'] : array();
			$archivos  = $this->obtener_archivos($path, $dirs_excluidos);

			$extras = (isset($data['extras'])) ? $data['extras'] : array();
			$arreglo = $this->generar_arreglo($path, $archivos, $extras);
			
			$this->generar_archivo($path.'/'.$data['archivo_salida'], $arreglo, $path);
		}
	}
	
	function generar_vacio()
	{
		$this->init_registro();
		foreach ($this->puntos_montaje as $path => $data) {
			if (!is_dir($path)) {
				$this->pms_no_encontrados[] = $path;
				continue;	// simplemente se ignora
			}

			$this->generar_archivo($path.'/'.$data['archivo_salida'], '', $path);
		}
	}	

	protected function obtener_archivos($path, $excluidos = array())
	{
		$excluidos = $this->preparar_excluidos($path, $excluidos);
		$archivos  = toba_manejador_archivos::get_archivos_directorio($path, '/.*\.php$/', true, $excluidos);
		sort($archivos, SORT_STRING);

		return $archivos;
	}

	protected function preparar_excluidos($path, $excluidos)
	{
		foreach ($excluidos as $key => $excluido) {
			if (!comienza_con($excluido, '/')) {
				$excluidos[$key] = "$path/$excluido";
			} else {
				$excluidos[$key] = "$path$excluido";
			}
		}

		return $excluidos;
	}

	protected function generar_arreglo($path_montaje, &$archivos, $extras = array())
	{
		$clases = '';

		foreach ($archivos as $archivo) {
			$contenido = file_get_contents($archivo);
			
			$contenido = preg_replace(self::regexp_eliminar_comentarios, '', $contenido);	// removemos comentarios

			// matches[1]: cada elemento acá trae 'class', 'interface' o nada
			// matches[2]: cada elemento acá trae el nombre de la clase o interfaz
			// matches[3]: cada elemento acá trae de que clase extiende
			preg_match_all(self::regexp_extractor, $contenido, $matches);
			
			if (empty($matches[1])) continue;	// No es una clase o una interfaz. No hay que incluirla

			foreach ($matches[1] as $key => $tipo) {
				if ($tipo == 'class') {
					if (in_array($matches[3][$key], $this->extends_excluidos)) {
						continue;
					}
				}
				$clase = $matches[2][$key];
				$this->registrar_clase($path_montaje, $clase, $archivo);
				$path = substr(str_replace($path_montaje, '', $archivo), 1); // Sacamos el $path_montaje para que quede relativo al mismo

				if (! $this->es_clase_repetida($path_montaje, $clase)) {
					$clases .= sprintf("\t\t'%s' => '%s',\n", $clase, $path);
				}
			}
		}

		foreach ($extras as $clase => $path) {
			if (! $this->es_clase_repetida($path_montaje, $clase)) {
				$clases .= sprintf("\t\t'%s' => '%s',\n", $clase, $path);
			}
		}

		return $clases;
	}

	protected function init_registro()
	{
		unset($this->registro);
		unset($this->clases_repetidas);
		$this->registro = array();
		$this->clases_repetidas = array();
	}

	protected function registrar_clase($montaje, $clase, $path)
	{
		if (isset($this->registro[$montaje][$clase])) {	// La clase con nombre $clase ya existe
			if (!isset($this->clases_repetidas[$montaje][$clase])) {	// La clase $nombre no se había registrado como repetida
				$this->clases_repetidas[$montaje][$clase][] = $this->registro[$montaje][$clase];
			}
			$this->clases_repetidas[$montaje][$clase][] = $path;
		} else {
			$this->registro[$montaje][$clase] = $path;
		}
	}

	protected function generar_archivo($path, $contenido, $punto_montaje)
	{
		$nombre_clase = basename($path, '.php');
		$comentario = "/**\n * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.\n * @ignore\n */";
		$arreglo = sprintf("\tstatic protected \$clases = array(\n%s\t);", $contenido);
		$metodo_consultor = "\tstatic function existe_clase(\$nombre)\n\t{\n\t\treturn isset(self::\$clases[\$nombre]);\n\t}\n";
		$metodo_cargador = "\tstatic function cargar(\$nombre)\n\t{\n\t\tif (self::existe_clase(\$nombre)) { \n\t\t\t require_once(dirname(__FILE__) .'/'. self::\$clases[\$nombre]); \n\t\t}\n\t}\n";
		$clase = sprintf("<?php\n%s\nclass %s \n{\n%s\n%s\n%s\n}\n?>", $comentario, $nombre_clase, $metodo_consultor, $metodo_cargador, $arreglo);

		file_put_contents($path, $clase);
	}

	protected function mostrar_clases_repetidas()
	{
		foreach ($this->clases_repetidas as $montaje => $clase) {
			$this->mensajes[] = "\n[$montaje] Existen clases repetidas, la única
					que se cargará en el autoload será la última de cada lista";
			foreach ($clase as $key => $paths) {
				$this->consola->mensaje("\n[$key]");
				foreach ($paths as $path) {
					$this->consola->mensaje($path, true);
				}
			}
		}
	}
	
	protected function es_clase_repetida($montaje, $clase)
	{
		 return (isset($this->clases_repetidas[$montaje][$clase]));
	}
}
?>
