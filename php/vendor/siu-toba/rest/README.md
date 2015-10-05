
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
Para los casos en los que se requiera recuperar un conjunto de recursos o dar de alta un recurso en particular, se utiliza el sufijo list (para hacer referencia que es sobre la lista de valores y no sobre uno puntual):

``` php
    // Equivale a GET /rest: retorna el recurso como un conjunto
    function get_list() ...
    
    // Equivale a POST /rest: da de alta un nuevo recurso
    function post_list($id) ...
```

``` php
<?php
class recurso_personas
{

    function post_list()
    {
        $datos = rest::request()->get_body_json();
        $nuevo = modelo_persona::insert($datos);
        $fila = array('id' => $nuevo);
        rest::response()->post($fila);
    }

    function get_list()
    {
        $personas = modelo_persona::get_personas($where);
        rest::response()->get($personas);
    }
```

Si se quiere enviar respuestas que no sean JSON o con headers especificos, se puede hacer cambiando la **vista** y configurando la respuesta de la siguiente manera:
``` php
<?php
class recurso_documento
{

    function get_list()
    {
        $pdf = documentos::get_pdf();

        $vista_pdf = new \SIUToba\rest\http\vista_raw(rest::response());
        $vista_pdf->set_content_type("application/pdf");
        rest::app()->set_vista($vista_pdf);

        rest::response()->set_data($pdf);
        rest::response()->set_status(200);
        rest::response()->add_headers(array(
            "Content-Disposition" => "attachment; filename=Mi_documento.pdf"
        ));
    }
```


###Sub APIs

La librería permite agrupar recursos en subcarpetas, con **hasta dos niveles** de profundidad, permitiendo asi, definir sub APIs y lograr una mejor división semántica que facilite la aplicación de distintas configuraciones según el caso. Además estas subcarpetas sirven de prefijo de acceso en la URL, por ejemplo _/personas/deportes/_. 

Por ejemplo, una API que brinda servicios al usuario actual, puede tener las subdivisiones `admin` y `me`. Para esto se deberá crear una carpeta _/rest/me_ y _/rest/admin_ sin ningún recurso dentro. Si se quieren conocer las `mascotas` del usuario actual, se debe crear un recurso `mascotas` en _/rest/me/mascotas/recurso_mascotas.php_ y luego, se podrá acceder por medio de la url _/rest/me/mascotas_. La alternativa, mas compleja, sin utilizar sub APIs, es accediendo a _/rest/usuarios/{usuario_actual}/mascotas_.

##Links relacionados
* [**Testing de APIs REST**](https://github.com/SIU-Toba/rest/wiki/Testing-de-APIs-REST)
* [**Documentación de APIs REST**](https://github.com/SIU-Toba/rest/wiki/Documentaci%C3%B3n-de-APIs-REST)
* [**Convenciones en la creación de APIs REST**](https://github.com/SIU-Toba/rest/wiki/Convenciones-en-la-creaci%C3%B3n-de-APIs-REST)
* [**Uso de la libreria REST standalone**](https://github.com/SIU-Toba/rest/wiki/Uso-de-la-libreria-REST-standalone)
