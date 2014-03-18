<?php


use rest\seguridad\autenticacion;
use rest\toba\toba_rest_logger;
use rest\toba as rest_toba;


class toba_rest
{

    const CARPETA_REST = "/rest";
	protected $app;

    static function url_rest()
    {
        return toba_recurso::url_proyecto() . '/rest';
    }

    static function url_api_doc()
    {
        return toba_http::get_protocolo() . toba_http::get_nombre_servidor() . self::url_rest() . '/api-docs';
    }

	
	function conf__inicial()
	{
		if (! $this->es_pedido_documentacion()) {
			$this->app = $this->instanciar_libreria_rest();
			$this->configurar_libreria_rest($this->app);
		}		
	}
	
	function get_instancia_rest()
	{
		return $this->app;
	}
	
    function ejecutar()
    {
        if ($this->es_pedido_documentacion()) {
            $this->rederigir_a_swagger();
            return;
        }

        $this->app->procesar();
    }


    /**
     * @return \rest\rest
     */
    public function instanciar_libreria_rest()
    {
        $ini = $this->get_conf();
        $es_produccion = (boolean) toba::instalacion()->es_produccion();

        $path_controladores = $this->get_path_controladores();
        $url_base = self::url_rest();

        $settings = array(
            'path_controladores' => $path_controladores,
            'url_api' => $url_base,
            'prefijo_api_docs' => 'api-docs',
            'debug' => !$es_produccion,
	        'encoding' => 'latin1'
        );
        $settings = array_merge($settings, $ini->get('settings', null, array(), false));

        include_once 'lib/rest/rest.php';
        $app = new rest\rest($settings);
        return $app;
    }


    /**
     * Configurar la libreria de rest, seteando las dependencias o configuracion que permite la misma
     * @param $app
     * @throws toba_error_modelo si hay errores de configuracion
     */
    public function configurar_libreria_rest($app)
    {
        $app->container->singleton('logger', function () {
            return new toba_rest_logger();
        });

        $conf = $this->get_conf();
        $autenticacion = $conf->get('autenticacion', null, 'basic');
        $modelo_proyecto = $this->get_modelo_proyecto();

        switch($autenticacion){
            case 'basic':
                $app->container->singleton('autenticador', function () use ($modelo_proyecto) {
                    $passwords = new rest_toba\toba_usuarios_rest_conf($modelo_proyecto);
                    return new autenticacion\autenticacion_basic_http($passwords);
                });
                break;
            case 'digest':
                $app->container->singleton('autenticador', function () use ($modelo_proyecto) {
                    $passwords = new rest_toba\toba_usuarios_rest_conf($modelo_proyecto);
                    return new autenticacion\autenticacion_digest_http($passwords);
                });
                break;
            case 'api_key':
                $app->container->singleton('autenticador', function () use ($modelo_proyecto) {
                    $passwords = new rest_toba\toba_usuarios_rest_conf($modelo_proyecto);
                    return new autenticacion\autenticacion_api_key($passwords);
                });
                break;
            case 'toba':
                $app->container->singleton('autenticador', function () use ($modelo_proyecto) {
	                $toba_aut = new toba_autenticacion_basica();
                    $user_prov = new rest_toba\toba_usuarios_rest_bd($toba_aut);
                    return new autenticacion\autenticacion_basic_http($user_prov);
                });
                break;
            default:
                throw new toba_error_modelo("Debe especificar un tipo de autenticacion valido [digest, basic] en el campo 'autenticacion'");
        }

        $app->container->singleton('db', function () {
            return toba::db();
        });

    }

    protected function get_conf()
    {
        if (!isset($this->conf_ini)) {
            $this->conf_ini = toba_modelo_rest::get_ini_server($this->get_modelo_proyecto());
        }
        return $this->conf_ini;
    }

    protected function rederigir_a_swagger()
    {
        $swagger_ui = toba_recurso::url_toba() . '/swagger/index.html';
        $proy = toba_rest::url_api_doc();
        header('Location: ' . $swagger_ui . '?' . $proy);
    }

    /**
     * @return string
     */
    protected function get_path_controladores()
    {
        $path_controladores = toba_proyecto::get_path_php() . self::CARPETA_REST;
        return $path_controladores;
    }

    /**
     * @return bool
     */
    public function es_pedido_documentacion()
    {
        return toba_recurso::url_proyecto() . "/rest" == $_SERVER['REQUEST_URI'];
    }

    protected function get_modelo_proyecto()
    {
        if (!isset($this->modelo_proyecto)) {
            $catalogo = toba_modelo_catalogo::instanciacion();
            $id_instancia = toba::instancia()->get_id();
            $id_proyecto = toba::proyecto()->get_id();
            $this->modelo_proyecto = $catalogo->get_proyecto($id_instancia, $id_proyecto);
        }
        return $this->modelo_proyecto;
    }

}