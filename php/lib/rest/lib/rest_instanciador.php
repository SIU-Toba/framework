<?php

namespace rest\lib;

/**
 * Sirva para pasa almacenar el controlador, accion y parametros que se decodifica de la ruta
 * y para encapusular eventuales diferencias entre instanciar clases con namespace o globales.
 *
 * Class recurso_rest
 *
 * @package rest\rutas
 */
class rest_instanciador {

	public $clase;
	public $accion;
	public $parametros;

	protected $objeto;

	public function get_instancia($autoinclude = true){
		if(! $this->objeto){
			if($autoinclude){
				require_once $this->clase;
			}
			$clase = $this->get_clase_de_ruta($this->clase);
			$this->objeto = new  $clase();
		}
		return $this->objeto;
	}

	public function ejecutar_accion(){

		if(empty($this->parametros)){
			$this->parametros = array();
		}
		return call_user_func_array(array($this->get_instancia(), $this->accion), $this->parametros);
	}

    public function existe_metodo($nombre){
        $clase = $this->get_clase_de_ruta($this->clase);
//        require_once $this->clase;
        return method_exists($clase, $nombre);
    }

	private function get_clase_de_ruta($clase)
	{
		return basename($clase, ".php");
	}

}