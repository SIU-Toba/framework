<?php

namespace rest\lib;


class ruteador {


	const COLLECTION_SUFFIX = '_list';

	/**
	 * @var lector_recursos_archivo
	 */
	protected $lector;

	function __construct($lector)
	{
		$this->lector = $lector;
	}


	function buscar_controlador($method, $url){

		$partes_url = explode('/', $url);

		$instanciador = new rest_instanciador();
		//colleccion/id/colleccion...
		$colecciones = array();
		$parametros = array();
		foreach ($partes_url as $key => $valor) {
			if(empty($valor))
			{ //mal formato. / de mas
				throw new \Exception("Formato de ruta incorrecto - $url"); //cambiar
			}
			if($key % 2){
				$parametros[] = $valor;
			}else {
				$colecciones[]= $valor;
			}
		}

		//busco la clase que maneja el recurso
		if(!$clase = $this->lector->get_recurso($colecciones)){
			throw new \Exception("No se encuentra el recurso para $url. Ruta mal formada?"); //cambiar
		}

		$accion = strtolower($method);

		//si hay path faltante lo resuelve en un metodo get_recurso1_recurso2
		$split_by = array_search(basename($clase, '.php'), $colecciones);

		if (false !== $split_by && count($colecciones) > ($split_by +1) ) {
			$path_faltante = array_slice($colecciones, $split_by +1);
			$accion .= '_' . implode('_', $path_faltante);
		}

        $instanciador->clase = $clase;
        //$es_alias = $this->procesar_alias($colecciones, $parametros, $instanciador);

        //Apunta a la coleccion
        if(count($colecciones) > count($parametros)){
            $accion .=  self::COLLECTION_SUFFIX;
        }
		//chequear que exista la accion?

		$instanciador->accion = $accion;
		$instanciador->parametros = $parametros;
		return $instanciador;
	}

    /**
     * @param $colecciones
     * @param $parametros
     * @param $instanciador
     * @return mixed
     */
    protected function procesar_alias($colecciones, $parametros, rest_instanciador $instanciador)
    {
        $posible_accion = $instanciador->accion;
        //Apunta a la coleccion
        if (count($colecciones) > count($parametros) - 1) {
            $posible_accion .= self::COLLECTION_SUFFIX;
        }
        $posible_accion .= '__' . end($parametros);

        if ($instanciador->existe_metodo($posible_accion)) {
            array_pop($parametros);
            $instanciador->accion = $posible_accion;
            $instanciador->parametros = $parametros;
            return true;
        }
        return false;
    }


}