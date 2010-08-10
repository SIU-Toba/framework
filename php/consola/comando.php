<?php

class comando
{
	/**
	 * @var consola
	 */
	protected $consola;
	protected $argumentos;

	function __construct( $manejador_interface )
	{
		$this->consola = $manejador_interface;
	}

	static function get_info(){}

	function mostrar_observaciones(){}

	function get_nombre()
	{
		$nombre = get_class( $this );
		$temp = explode('_', $nombre);
		return $temp[1];
	}
	
	function get_argumentos_string()
	{
		$salida = '';
		foreach ($this->get_parametros() as $id => $valor) {
			$salida .= "$id $valor ";
		}
		return $salida;
	}
	
	function set_argumentos( $argumentos )
	{
		$this->argumentos = $argumentos;
	}

	/**
	*	Ubica el metodo solicitado y los ejecuta
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
	
	protected function ejecutar_opcion($opcion, $argumentos)
	{
		if( method_exists( $this, $opcion ) ) {
			$this->$opcion($argumentos);
		} else {
			$this->consola->mensaje("La opcion '".$this->argumentos[0]."' no existe");
			$this->mostrar_ayuda();
		}
	}

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

	function inspeccionar_opciones($clase = null)
	{
		if (!isset($clase)) {
			$clase = get_class($this);
		}
		$opciones = array();
		$clase = new ReflectionClass($clase);
		foreach ($clase->getMethods() as $metodo){
			if (substr($metodo->getName(), 0, 8) == 'opcion__'){
				$temp = explode('__', $metodo->getName());
				$nombre = $temp[1];
				$comentario = $metodo->getDocComment();
				$opciones[ $nombre ] = array(
					'ayuda' => parsear_doc_comment($comentario),
					'tags' => parsear_doc_tags($comentario)
				);
				
			}
		}
		return $opciones;		
	}

	/*
	*	Parseo de parametros
	*/
	protected function get_parametros()
	{
		
	   $params = array();
	   for ($i=0; $i < count( $this->argumentos ); $i++){
	
	       if ( $this->es_parametro($this->argumentos[$i]) ){
	           if (strlen($this->argumentos[$i]) == 1){
	
	           }elseif ( strlen($this->argumentos[$i]) == 2){
					$paramName = $this->argumentos[$i];
					$y=1;
					$paramVal = '';	               
					while (isset($this->argumentos[ $i + $y ]) && 
					  			!$this->es_parametro( $this->argumentos[ $i + $y ] )) {
						$paramVal .=  $this->argumentos[ $i + $y ] . ' ';
						$y++;
					}
					$paramVal = trim($paramVal);
	           }elseif ( strlen($this->argumentos[$i]) > 2){
	               $paramName = substr($this->argumentos[$i],0,2);
	               $paramVal = substr($this->argumentos[$i],2);
	           }
	
	           $params[ $paramName ] = $paramVal;
	
	       }
	   }
		return $params;
	}
	
	private function es_parametro( $texto ) {
       return (substr($texto, 0, 1) == "-") ? 1: 0;
	}
}
?>