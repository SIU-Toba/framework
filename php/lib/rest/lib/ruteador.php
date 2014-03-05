<?php

namespace rest\lib;


class ruteador {


	const COLLECTION_SUFFIX = '_list';

	/**
	 * @var lector_recursos_archivo
	 */
	protected $lector;
    protected $instanciador;

	function __construct($lector, $instanciador)
	{
		$this->lector = $lector;
        $this->instanciador = $instanciador;
	}


	function buscar_controlador($method, $url){

		$partes_url = explode('/', $url);

		$instanciador = $this->instanciador;
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


        $instanciador->clase = $clase;


        //se invoca la accion tipica
        $accion = $this->get_accion_path($method, $clase, $colecciones, $parametros);
        $instanciador->accion = $accion;
        $instanciador->parametros = $parametros;

        if ($instanciador->existe_metodo($accion)){        //chequear que exista la accion?
            return $instanciador;
        }

        // Se checkea si matchea con un alias
        if(count($colecciones) > count($parametros)){
            $alias = array_pop($colecciones);
        }else {
            $alias = array_pop($parametros);
        }

        $posible_accion = $this->get_accion_path($method, $clase, $colecciones, $parametros);;
        $posible_accion .= '__' . $alias;

        if ($instanciador->existe_metodo($posible_accion)) {
            $instanciador->accion = $posible_accion;
            $instanciador->parametros = $parametros;
            return $instanciador;
        }
        throw new \Exception("No se encuentra el recurso para $url. Ruta mal formada?");
	}


    protected function get_accion_path($metodo, $clase, $colecciones, $parametros)
    {
        $accion = strtolower($metodo);
        //si hay path faltante lo resuelve en un metodo get_recurso1_recurso2
        $hay_path_por_resolver = array_search(basename($clase, '.php'), $colecciones);

        if (false !== $hay_path_por_resolver && count($colecciones) > ($hay_path_por_resolver + 1)) {
            $path_faltante = array_slice($colecciones, $hay_path_por_resolver + 1);
            $accion .= '_' . implode('_', $path_faltante);
        }

        //Apunta a la coleccion
        if (count($colecciones) == count($parametros) + 1) {
            $accion .= self::COLLECTION_SUFFIX;
        }
        return $accion;
    }


}