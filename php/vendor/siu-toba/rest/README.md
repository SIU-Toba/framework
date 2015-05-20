
[![Build Status](https://travis-ci.org/SIU-Toba/rest.svg?branch=master)](https://travis-ci.org/SIU-Toba/rest)

## rest
Esta librería permite servir APIs rest de forma simple pero estructurada. Se toman algunas decisiones en pos de eliminar el mayor boilerplate posible.


### Instalacion

composer require siu-toba/rest

Se deben proveer algunas dependencias para configurar y adaptar la librería. Por ejemplo:

```php
	$settings = array(
	    'path_controladores' => $path_controladores,
	    'url_api' => $url_base,
	    'prefijo_api_docs' => 'api-docs',
	    'debug' => !$es_produccion,
	        'encoding' => 'latin1'
	);
	$app = new SIUToba\rest\rest($settings);
	
	$app->container->singleton('logger', function () {
	    return new toba_rest_logger();
	});
	//ver otros mecanismos de autenticacion, aca usamo 'oauth2' que es el mas complejo:
	$app->container->singleton('autenticador', function () use ($conf) {
	    $conf_auth = $conf->get('oauth2');
	    $cliente = new \GuzzleHttp\Client(array('base_url' => $conf_auth['endpoint_decodificador_url']));
	    $decoder = new oauth_token_decoder_web($cliente);
	    $decoder->set_cache_manager(new \Doctrine\Common\Cache\ApcCache());
	    $decoder->set_tokeninfo_translation_helper(new autenticacion\oauth2\tokeninfo_translation_helper_arai());
	}
	$app->container->singleton('autorizador', function () use ($conf) {
	    $conf_auth = $conf->get('oauth2');
	    if (!isset($conf_auth['scopes'])) {
	        die("es necesario definir el parámetro 'scopes' en el bloque oauth2 de la configuración");
	    }
	    $auth = new autorizacion_scopes();
	    $auth->set_scopes_requeridos(array_map('trim', explode(',', $conf_auth['scopes'])));
	    return $auth;
	});
	
	$app->container->singleton('db', function () {
	    return toba::db();
	});
	
	
	$app->run(); //le damos control

```
