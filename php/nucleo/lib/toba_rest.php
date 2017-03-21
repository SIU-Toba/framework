<?php

use SIUToba\rest\seguridad\autenticacion;
use SIUToba\rest\seguridad\autenticacion\autenticacion_basic_http;
use SIUToba\rest\seguridad\autenticacion\oauth2\oauth_token_decoder_web;
use SIUToba\rest\seguridad\autenticacion\usuarios_usuario_password;
use SIUToba\rest\seguridad\autorizacion\autorizacion_scopes;

/**
 * Clase que instancia la libreria necesaria para atender un pedido REST
 * @package Centrales
 */
class toba_rest
{
	const CARPETA_REST = "/rest";
	protected $app;

	static function url_rest()
	{
		return toba_recurso::url_proyecto() . self::CARPETA_REST;
	}

	static function url_api_doc()
	{
		return toba_http::get_protocolo() . toba_http::get_nombre_servidor() . self::url_rest() . '/api-docs';
	}


	function conf__inicial($api='')
	{
		if (! $this->es_pedido_documentacion()) {
			$this->app = $this->instanciar_libreria_rest($api);
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
	 * @return SIUToba\rest\rest
	 */
	public function instanciar_libreria_rest($api='')
	{
		$ini = $this->get_conf($api);
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
		$datos_ini_proyecto = $this->get_ini_proyecto();
		if (!empty($datos_ini_proyecto) && isset($datos_ini_proyecto['version'])) {
			$settings['api_version'] = $datos_ini_proyecto['version'];
		}
		$settings = array_merge($settings, $ini->get('settings', null, array(), false));

		//        include_once 'lib/rest/rest.php';
		$app = new SIUToba\rest\rest($settings);
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

		$autenticaciones = $this->get_metodos_autenticacion();
		$modelo_proyecto = $this->get_modelo_proyecto();

        $metodos = array();
        foreach ($autenticaciones as $autenticacion){
            switch($autenticacion) {
                case 'basic':
                    $metodos[] = function () use ($modelo_proyecto) {
                        $passwords = new toba_usuarios_rest_conf($modelo_proyecto);
                        return new autenticacion\autenticacion_basic_http($passwords);
                    };
                    break;
                case 'digest':
                    $metodos[] = function () use ($modelo_proyecto) {
                        $passwords = new toba_usuarios_rest_conf($modelo_proyecto);
                        return new autenticacion\autenticacion_digest_http($passwords);
                    };
                    break;
                case 'api_key':
                    $metodos[] = function () use ($modelo_proyecto) {
                        $passwords = new toba_usuarios_rest_conf($modelo_proyecto);
                        return new autenticacion\autenticacion_api_key($passwords);
                    };
                    break;
                case 'ssl':
                    $metodos[] = function () use ($modelo_proyecto) {
                        $certificados = new toba_usuarios_rest_ssl($modelo_proyecto);
                        return new autenticacion\autenticacion_ssl($certificados);
                    };
                    break;
                case 'jwt':
                    $metodos[] = function () use ($modelo_proyecto) {
                        $certificados = new toba_usuarios_rest_jwt($modelo_proyecto);
                        return new autenticacion\autenticacion_jwt($certificados);
                    };
                    break;
                case 'oauth2':
                    $metodos[] = function () use ($conf) {
                        $conf_auth = $conf->get('oauth2');
                        $decoder = null;
                        switch ($conf_auth['decodificador_tokens']) {
                            case 'local':
                                die('not implemented');
                                break;
                            case 'web':
                                $cliente = new \GuzzleHttp\Client(array('base_url' => $conf_auth['endpoint_decodificador_url']));
                                $decoder = new oauth_token_decoder_web($cliente);
                                $decoder->set_cache_manager(new \Doctrine\Common\Cache\ApcCache());
                                $decoder->set_tokeninfo_translation_helper(new autenticacion\oauth2\tokeninfo_translation_helper_arai());
                                break;
                        }

                        $auth = new autenticacion\autenticacion_oauth2();
                        $auth->set_decoder($decoder);
                        return $auth;
                    };
                    $app->container->singleton('autorizador', function () use ($conf) {
                        $conf_auth = $conf->get('oauth2');
                        if (!isset($conf_auth['scopes'])) {
                            die("es necesario definir el par�metro 'scopes' en el bloque oauth2 de la configuraci�n");
                        }
                        $auth = new autorizacion_scopes();
                        $auth->set_scopes_requeridos(array_map('trim', explode(',', $conf_auth['scopes'])));
                        return $auth;
                    });
                    break;
                case 'toba':
                    $metodos[] = function () use ($modelo_proyecto) {
                        $toba_aut = new toba_autenticacion_basica();
                        $user_prov = new toba_usuarios_rest_bd($toba_aut);
                        return new autenticacion\autenticacion_basic_http($user_prov);
                    };
                    break;
                default:
                    throw new toba_error_modelo("Debe especificar un tipo de autenticacion valido [digest, basic] en el campo 'autenticacion'");
            }
        }
        $app->container->singleton('autenticador', function () use ($metodos) {
			return $metodos;
		});

		$app->container->singleton('rest_quoter', function () {
			return toba::db();
		});
	}

    protected function get_metodos_autenticacion()
    {
        $conf = $this->get_conf();
		$autenticaciones = explode(',', str_replace(' ', '', $conf->get('autenticacion', null, 'basic')));

        // jwt y oauth usan el mismo header
        if (in_array('jwt', $autenticaciones) && in_array('oauth', $autenticaciones)){
            throw new toba_error_modelo("No se puede especificar en simultaneo el tipo de autenticacion 'jwt' y 'oauth' en el campo 'autenticacion'");
        }

        // basic y digest, se procesan al final y hacen redirect para pedir datos
        if (in_array('digest', $autenticaciones) && in_array('basic', $autenticaciones)){
            throw new toba_error_modelo("No se puede especificar en simultaneo el tipo de autenticacion 'digest' y 'basic ' en el campo 'autenticacion'");
        }

        // hay que priorizar, basic y digest (si existe alguno) hacen redirect primero
        $order = array('ssl', 'jwt', 'api_key', 'toba', 'digest', 'basic');
        $autenticaciones = array_intersect($order, $autenticaciones);

        return $autenticaciones;
    }

	protected function get_conf($api='')
	{
		if (!isset($this->conf_ini)) {
			$this->conf_ini = toba_modelo_rest::get_ini_server($this->get_modelo_proyecto(), $api);
		}
		return $this->conf_ini;
	}

	protected function rederigir_a_swagger()
	{
		$swagger_ui = toba_recurso::url_toba() . '/swagger/index.html';
		$proy = toba_rest::url_api_doc();
		header('Location: ' . $swagger_ui . '?url=' . $proy);
	}

	/**
	 * @return string
	 */
	protected function get_path_controladores()
	{
		$api_base = toba_proyecto::get_path_php() . self::CARPETA_REST;
		$api_pers = toba_proyecto::get_path_pers_php() . self::CARPETA_REST;			
			
		$path_controladores = array($api_base, $api_pers);
		return $path_controladores;
	}

	/**
	 * @return bool
	 */
	public function es_pedido_documentacion()
	{
		return toba_recurso::url_proyecto() . self::CARPETA_REST == rtrim( $_SERVER['REQUEST_URI'], '/');
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
	
	protected function get_ini_proyecto()
	{
		$resultado = array();
		$path_ini = toba_proyecto::get_path().'/proyecto.ini';
		if (file_exists($path_ini)) {
			$ini = new toba_ini($path_ini);
			if ($ini->existe_entrada('proyecto')) {
				$resultado = $ini->get_datos_entrada('proyecto');
			}
		}
		return $resultado;
	}
		
}
