<?php
/**
 * Clase base para los comandos.
 * 
 * Class \comando.
 * @package consola
 */
class comando
{	
	protected $consola;
	protected $argumentos;

	/**
	 * Constructor
	 * 
	 * @param mixed $manejador_interface
	 */
	function __construct( $manejador_interface )
	{
		$this->consola = $manejador_interface;
	}

	/**
	 * Devuelve info del comando
	 * 
	 * @abstract
	 */
	static function get_info(){}

	/**
	 * Muestra help de uso
	 * 
	 * @abstract
	 */
	function mostrar_observaciones(){}

	/**
	 * Devuelve el nombre del comando
	 * 
	 * @return string
	 */
	function get_nombre()
	{
		$nombre = get_class( $this );
		$temp = explode('_', $nombre);
		return $temp[1];
	}
	
	/**
	 * Devuelve un string con los argumentos
	 * 
	 * @return string
	 */
	function get_argumentos_string()
	{
		$salida = '';
		foreach ($this->get_parametros() as $id => $valor) {
			$salida .= "$id $valor ";
		}
		return $salida;
	}
	
	/**
	 * Permite fijar los argumentos del comando
	 * 
	 * @param array $argumentos
	 */
	function set_argumentos( $argumentos )
	{
		$this->argumentos = $argumentos;
	}

	/**
	 * Ubica el metodo solicitado y los ejecuta
	 * 
	 * @param string $opcion
	 * @param array $argumentos
	 * @return null
	 */
	function procesar($opcion=null, $argumentos=null)
	{
		if (! isset($opcion)) {
			if ( count( $this->argumentos ) == 0 ) {
				$this->mostrar_ayuda();
				return;
			} else {
				$opcion = 'opcion__' . $this->argumentos[0];
			}
		}
		$this->ejecutar_opcion($opcion, $argumentos);
	}
	
	/**
	 * Ejecuta la opcion solicitada
	 * 
	 * @param string $opcion
	 * @param array $argumentos
	 */
	protected function ejecutar_opcion($opcion, $argumentos)
	{
		if( method_exists( $this, $opcion ) ) {
			$this->$opcion($argumentos);
		} else {
			$this->consola->mensaje("La opcion '".$this->argumentos[0]."' no existe");
			$this->mostrar_ayuda();
		}
	}

	/**
	 * Muestra la ayuda del comando en base a una lista de opciones
	 * 
	 */
	function mostrar_ayuda()
	{
		$this->consola->titulo( $this->get_info() );
		$this->mostrar_observaciones();
		$this->consola->subtitulo( 'Lista de opciones' );
		$opciones = $this->inspeccionar_opciones();
		$salida = array();
		$i=0;
		foreach ($opciones as $id => $opcion) {
			if (!isset($opcion['tags']['consola_no_mostrar'])) {
				$salida[$id] = $opcion['ayuda'];
				if (isset($opcion['tags']['consola_parametros'])) {
					$salida[$id] .= "\n".$opcion['tags']['consola_parametros'];
				}
				if (isset($opcion['tags']['consola_separador']) && $i+1 < count($opciones)) {
					$salida [$id] .= "\n_________________________________\n";
				}
				
			}
			$i++;
		}
		$this->consola->coleccion($salida);
	}
	
	/**
	 * Recupera todas las opciones para una clase dada, es decir los metodos ejecutables
	 * 
	 * @param string $clase
	 * @return array
	 */
	function inspeccionar_opciones($clase = null)
	{
		if (!isset($clase)) {
			$clase = get_class($this);
		}
		$opciones = array();
		$clase = new ReflectionClass($clase);

		//-----
		//Hace 2 pasadas para poder ordenar los metodos según si son propios del proyecto o son definidos en toba (util para ver comandos propios custom al final de la lista )
		for ($pasada = 1; $pasada <= 2; $pasada++) {
			foreach ($clase->getMethods() as $metodo) {
				if ($metodo->getDeclaringClass()->getName() == $clase->getName() && $pasada == 1) {
					continue;
				}
				if ($metodo->getDeclaringClass()->getName() != $clase->getName() && $pasada == 2) {
					continue;
				}
				if (substr($metodo->getName(), 0, 8) == 'opcion__') {
					$temp = explode('__', $metodo->getName());
					$nombre = $temp[1];
					$comentario = $metodo->getDocComment();
					$opciones[$nombre] = array(
						'ayuda' => parsear_doc_comment($comentario),
						'tags' => parsear_doc_tags($comentario),
					);

				}
			}
		}
		return $opciones;
	}

	/**
	 * Parseo de parametros
	 * 
	 * @return array
	 */
	protected function get_parametros()
	{
		
		$params = array();
		for ($i=0; $i < count( $this->argumentos ); $i++)
		{
			if ($this->es_parametro($this->argumentos[$i])) {
				if (strtolower(trim($this->argumentos[$i])) == '--help') {
					   return array('help' => 1);
				}
				if (strlen($this->argumentos[$i]) == 1) {

				 } elseif ( strlen($this->argumentos[$i]) == 2) {

					$paramName = $this->argumentos[$i];
					$paramVal = $this->get_param_val($i);
				 } elseif (strlen($this->argumentos[$i]) > 2) {
					if (substr($this->argumentos[$i], 0, 2) == '--') {
						$paramName = $this->argumentos[$i];
						$paramVal = $this->get_param_val($i);
					} else {
						$paramName = substr($this->argumentos[$i], 0, 2);
						$paramVal = substr($this->argumentos[$i], 2);
					}
				 }
				 $params[ $paramName ] = $paramVal;
			}
		}
		return $params;
	}

	/**
	 * Devuelve el valor de un parametro que fue pasado como argumento
	 * 
	 * @param integer $i
	 * @return mixed
	 */
	private function get_param_val($i) {
		$paramVal = '';
		$y=1;
		while (isset($this->argumentos[ $i + $y ]) &&
			!$this->es_parametro( $this->argumentos[ $i + $y ] )) {
			$paramVal .=  $this->argumentos[ $i + $y ] . ' ';
			$y++;
		}
		return trim($paramVal);
	}

	/**
	 * Determina si el texto pertenece a un argumento
	 * 
	 * @param string $texto
	 * @return integer 0|1 
	 */
	private function es_parametro( $texto ) {
		 return (substr($texto, 0, 1) == "-") ? 1: 0;
	}
}
?>