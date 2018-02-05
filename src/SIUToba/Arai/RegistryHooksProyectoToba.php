<?php
namespace SIUToba\Framework\Arai;

use ParagonIE\Halite\KeyFactory;
use SIU\AraiCli\AraiCli;
use SIU\AraiCli\Services\Registry\HooksInterface;
use SIU\AraiJsonParser\Feature\Consumption;
use SIU\AraiJsonParser\Feature\Provision;
use SIU\AraiJsonParser\Feature\Auth\AbstractAuth;
use SIUToba\SSLCertUtils\SSLCertUtils;


/**
 * Class RegistryHooksProyectoToba
 *
 * Clase que implementa los hooks requeridos por ARAI-cli:Registry para configurar un proyecto tipico Toba
 *
 * Se asumen algunas cosas:
 *   * Solo funciona si estan definidas TOBA_INSTANCIA y TOBA_PROYECTO
 *   * Se configura una unica API cuya url es URL_PROYECTO/rest (redefinir si hay mas APIs o tienen otras URLs).
 *   * Se encripta y envia el primer user/pass que encuentra
 *   * La configuracion del SP de SAML se asume que se usa la autenticacion saml_onelogin
 *
 * @package SIUToba\Framework
 * @subpackage Arai
 */
class RegistryHooksProyectoToba implements HooksInterface
{
    /**
     * @var \toba_modelo_instalacion
     */
    protected $instalacion;

    protected $araiSyncKey;

    function __construct()
    {
        $this->inicializarEntorno();
    }

    public function preConf(\Pimple\Container $container)
    {
    }

    /**
     * Se ejecuta antes de enviar una feature para consumo al registry.
     * @param Consumption $consumption
     */
    public function preConsume(Consumption $feature)
    {
        switch ($feature->getType()) {
            case "api":
                $this->preConsumeApi($feature);
                break;
            case "app":
                $this->preConsumeApp($feature);
                break;
            case "service":
                $this->preConsumeService($feature);
                break;
        }
    }

    /**
     * Se ejecuta cuando vuelve una feature del registry con los datos para su consumo. Este hook es utilizado para
     * configurar el sistema a partir de la informaciÃ³n en registry.
     * @param Consumption $feature
     */
    public function postConsume(Consumption $feature)
    {
        switch ($feature->getType()) {
            case "api":
                $this->postConsumeApi($feature);
                break;
            case "app":
                $this->postConsumeApp($feature);
                break;
            case "service":
                $this->postConsumeService($feature);
                break;
        }
    }

    /**
     * Se ejecuta antes de enviar una feature al registry. En esta ventana se deben configurar las features para incluir
     * los datos dependientes de la instalaciÃ³n.
     * @param Provision $f una feature sin informaciÃ³n de instalaciÃ³n, por ej: url.
     */
    public function preProvide(Provision $provision)
    {
        switch ($provision->getType()) {
            case "api":
                $this->preProvideApi($provision);
                break;
            case "app":
                $this->preProvideApp($provision);
                break;
            case "service":
                $this->preProvideService($provision);
                break;
        }
    }

    public function postProvide(Provision $provision)
    {
        switch ($provision->getType()) {
            case "api":
                $this->postProvideApi($provision);
                break;
            case "app":
                $this->postProvideApp($provision);
                break;
            case "service":
                $this->postProvideService($provision);
                break;
        }
    }


    /**
     * Se ejecuta luego de hacer un add
     */
    public function postAdd()
    {

    }

    /**
     * Se ejecuta luego de hacer un sync
     */
    public function postSync()
    {

    }

    //---------------------------------------------------------------
    //---- PRE - CONSUME
    //---------------------------------------------------------------

    protected function preConsumeApp(Consumption $feature)
    {
    }

    protected function preConsumeApi(Consumption $feature)
    {
        $optionsFijas = $feature->getOptions();
        if (! isset($optionsFijas['toba-rest'])) {
            echo "No se pudo configurar la feature '".$feature->getName()."', falta setearle los identificadores de los accesos REST en el campo 'toba-rest'\n";
            return;
        }
        foreach ($optionsFijas['toba-rest'] as $opciones) {
            if (! isset($opciones['rest-id'])) {
                echo "No se pudo configurar la feature '".$feature->getName()."', falta setearle el identificador del acceso REST en el campo 'toba-rest.rest-id'\n";
                break;
            }

            $this->configurarCliente($feature, $opciones);
        }
    }

    protected function preConsumeService(Consumption $feature)
    {
        if ($feature->getName() == "service:siu/sso-saml-idp") {
            $this->preConsumeSamlIdp($feature);
        }
    }

    protected function preConsumeSamlIdp(Consumption $feature)
    {
        $url = $this->getProyectoUrl();
        $options = array();

        $options['assertionConsumerService'] = "$url/?acs";
        $options['singleLogoutService'] = "$url/?sls";
        $options['appUniqueId'] = self::getAppUniqueId();
        $feature->setEndpoint($url.'/default-sp');
        $feature->setOptions($options);
    }


    //---------------------------------------------------------------
    //---- POST - CONSUME
    //---------------------------------------------------------------

    protected function postConsumeApp(Consumption $feature)
    {
    }

    protected function postConsumeApi(Consumption $feature)
    {
        $providers = $feature->getProviders();
        if (empty($providers)) return;    //Nada para configurar

        /** @var Provision */
        $optionsFijas = $feature->getOptions();
        $provider = current($providers);  //Asume primer IDP que encuentra!
        if ($provider->getEndpoint() == '') {
            //No configura APIs sin URL
            return;
        }

        if (! isset($optionsFijas['toba-rest'])) {
            echo "No se pudo configurar la feature '".$provider->getName()."', falta setearle los identificadores de los accesos REST en el campo 'toba-rest'\n";
            return;
        }
        foreach ($optionsFijas['toba-rest'] as $acceso) {
            if (! isset($acceso['rest-id'])) {
                echo "No se pudo configurar la feature '".$provider->getName()."', falta setearle el identificador del acceso REST en el campo 'toba-rest.rest-id'\n";
                break;
            }

            $this->actualizarClienteIni($provider, $acceso);
        }


        //Al auto-configurar la API de ARAI-Usuarios, se asume que el ABM de usuarios empieza a trabajar contra esta API
        if ($feature->getName() == "api:siu/arai-usuarios") {
            $iniInstalacion = new \toba_ini($this->instalacion->archivo_info_basica());
            $iniInstalacion->agregar_entrada("vincula_arai_usuarios", "1");
            $iniInstalacion->guardar();
        }
    }



    protected function postConsumeService(Consumption $feature)
    {
        if ($feature->getName() == "service:siu/sso-saml-idp") {
            $this->postConsumeSamlIdp($feature);
        }
    }

    protected function postConsumeSamlIdp(Consumption $feature)
    {
        $providers = $feature->getProviders();
        if (empty($providers)) return;    //Nada para configurar
        /** @var Provision */
        $provider = current($providers);  //Asume primer IDP que encuentra!
        $options = $provider->getOptions();

        $endpoint = $provider->getEndpoint();

        if ($endpoint == '') return; //Nada para configurar
        $iniInstalacion = new \toba_ini($this->instalacion->archivo_info_basica());

        $iniInstalacion->agregar_entrada("autenticacion", "saml_onelogin");
        $iniInstalacion->guardar();

        $iniSaml = new \toba_ini($this->instalacion->dir_base().'/saml_onelogin.ini');
        $basicos = $iniSaml->existe_entrada("basicos") ? $iniSaml->get_datos_entrada("basicos"): array();
        if (! isset($basicos['permite_login_toba'])) $basicos['permite_login_toba'] = 0;
        if (isset($options['attributes']['uid'])) $basicos['atributo_usuario'] = $options['attributes']['uid'];
        $iniSaml->agregar_entrada("basicos", $basicos);

        $sp = $iniSaml->existe_entrada("sp") ? $iniSaml->get_datos_entrada("sp") : array();
        if (! isset($sp['auth_source'])) $sp['auth_source'] = 'default-sp';
        if (! isset($sp['session.phpsession.cookiename'])) $sp['session.phpsession.cookiename'] = 'TOBA_SESSID';
        $sp['idp'] = $endpoint;
        if (! isset($sp['proyecto_login'])) $sp['proyecto_login'] = $this->getProyectoId();
        $iniSaml->agregar_entrada("sp", $sp);

        $idp = $iniSaml->existe_entrada("idp:".$endpoint) ? $iniSaml->get_datos_entrada("idp:".$endpoint) : array();
        if (isset($options['name'])) $idp['name'] = $options['name'];
        if (isset($options['singleSignOnService'])) $idp['SingleSignOnService'] = $options['singleSignOnService'];
        if (isset($options['singleLogoutService'])) $idp['SingleLogoutService'] = $options['singleLogoutService'];
        if (isset($options['certificate'])) {
            $certFile = $this->instalacion->dir_base().'/idp.crt';
            if (false === file_put_contents($certFile, $options['certificate'])) {
                throw new \Exception("No se pudo escribir el archivo $certFile. ¿Problemas de permisos?");
            }
            $idp['certFile'] = $certFile;
        }
        $iniSaml->agregar_entrada("idp:".$endpoint, $idp);
        $iniSaml->guardar();
    }

    //---------------------------------------------------------------
    //---- PRE - PROVIDE
    //---------------------------------------------------------------


    protected function preProvideApp(Provision $feature)
    {
        $feature->setEndpoint($this->getProyectoUrl());
    }

    protected function preProvideApi(Provision $feature)
    {
        $optionsFijas = $feature->getOptions();
        $autoconfigurar = isset($optionsFijas) && isset($optionsFijas['auto-configurar']) && $optionsFijas['auto-configurar'];
        $options = $optionsFijas;
        $modeloProyecto = $this->getModeloProyecto();
        $iniServer = \toba_modelo_rest::get_ini_server($modeloProyecto);

        if ($autoconfigurar && ! $iniServer->existe_entrada("autenticacion")) {
            echo "Autoconfigurando API...\n";
            $iniServer->agregar_entrada("autenticacion", "basic");
            $iniServer->guardar();
        }

        if ($iniServer->existe_entrada("autenticacion")) {
            $options['auth']['type'] = $iniServer->get_datos_entrada("autenticacion");
        }

	    $publicKey = $this->getAraiSyncKeyPublic();
        $options['auth']['credentials']['cert'] = $publicKey;

        $endpoint = $this->getProyectoUrl();
        if (isset($_SERVER['ARAI_REGISTRY_ENDPOINT_BASE'])) {
            if (filter_var($_SERVER['ARAI_REGISTRY_ENDPOINT_BASE'], FILTER_VALIDATE_URL)) {
                $fixed_endpoint_parts = parse_url($_SERVER['ARAI_REGISTRY_ENDPOINT_BASE']);
                $endpoint_parts = parse_url($endpoint);
                if (isset($fixed_endpoint_parts['scheme'])) $endpoint_parts['scheme'] = $fixed_endpoint_parts['scheme'];
                if (isset($fixed_endpoint_parts['host'])) $endpoint_parts['host'] = $fixed_endpoint_parts['host'];
                if (isset($fixed_endpoint_parts['port'])) $endpoint_parts['port'] = $fixed_endpoint_parts['port'];
                $endpoint = unparse_url($endpoint_parts);
            } else {
                echo "La URL especificada en env 'ARAI_REGISTRY_ENDPOINT_BASE' no es vÃ¡lida\n";
            }
        }
        $endpoint = $endpoint . '/rest/';
        $feature->setEndpoint($endpoint);
        $feature->setOptions($options);
    }

    protected function preProvideService(Provision $feature)
    {

    }

    //---------------------------------------------------------------
    //---- POST - PROVIDE
    //---------------------------------------------------------------

    protected function postProvideApp(Provision $feature)
    {
    }

    protected function postProvideApi(Provision $feature)
    {
        $optionsFijas = $feature->getOptions();
        $autoconfigurar = isset($optionsFijas) && isset($optionsFijas['auto-configurar']) && $optionsFijas['auto-configurar'];
        if (!$autoconfigurar) {
            return;
        }
        $modeloProyecto = $this->getModeloProyecto();
        $iniServidorUsuarios = \toba_modelo_rest::get_ini_usuarios($modeloProyecto);
        $iniServidorUsuarios->vaciar();

        foreach ($feature->getConsumers() as $consumer) {
            $authInfo = $consumer->getAuth();
            if (empty($authInfo)) continue;
            foreach ($authInfo as $authCliente) {
                $this->agregarServidorUsuariosIni($authCliente, $iniServidorUsuarios);
            }
        }
        $iniServidorUsuarios->guardar();
    }

    protected function postProvideService(Provision $feature)
    {

    }

    //---------------------------------------------------------------
    //---- Puente: Registry <---> proyecto <---> TOBA
    //---------------------------------------------------------------

    protected function getProyectoUrl()
    {
        $fullUrl = $this->instalacion->get_instancia($this->getInstanciaId())->get_parametro_seccion_proyecto($this->getProyectoId(), "full_url");
        if (!isset($fullUrl)) {
            $proyecto = $this->getProyectoId();
            $archivo = $this->instalacion->get_instancia($this->getInstanciaId())->get_dir()."/instancia.ini";
            throw new \Exception("Es necesario especificar la URL completa del sistema en el atributo 'full_url' de la seccion [$proyecto] del archivo $archivo");
        }
        return $fullUrl;
    }

    /**
     * @throws \Exception
     */
    protected function inicializarEntorno()
    {
        $this->instalacion = $this->cargarToba();

        $this->araiSyncKey = $this->cargarAraiSyncKey();
    }

    /**
     * @return toba_modelo_instalacion
     * @throws \Exception
     */
    protected function cargarToba()
    {
        //--Carga nucleo toba
        $dir = $this->getTobaDir()."/php";
        $separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ';.;' : ':.:';
        ini_set('include_path', ini_get('include_path'). $separador . $dir);
        require_once("nucleo/toba_nucleo.php");
        \toba_nucleo::instancia()->iniciar_contexto_desde_consola($this->getInstanciaId(), $this->getProyectoId());
        return \toba_modelo_catalogo::instanciacion()->get_instalacion(null);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function cargarAraiSyncKey()
    {
        $iniInstalacion = new \toba_ini( \toba::nucleo()->toba_instalacion_dir() . '/instalacion.ini');

        if (!$iniInstalacion->existe_entrada("arai_sync_key_file")) {
            throw new \Exception("Debe configurar en el archivo 'instalacion.ini' la entrada 'arai_sync_key_file' con la ruta al certificado para sincronizar con Arai-Registry");
        }

        $araiSyncKeyFile = $iniInstalacion->get_datos_entrada("arai_sync_key_file");

        if (!is_readable($araiSyncKeyFile)) {
            throw new \Exception("No se puede leer el certificado '$araiSyncKeyFile' para sincronizar con Arai-Registry configurado en el archivo 'instalacion.ini'");
        }

        try {
            $keyPair = KeyFactory::loadEncryptionKeyPair($araiSyncKeyFile);

            return $keyPair;
        } catch (\Exception $exc) {
            $msg = $exc->getTraceAsString();
            throw new \Exception("El certificado para sincronizar con Arai-Registry configurado en el archivo 'instalacion.ini' con la entrada 'arai_sync_key_file' no es una clave de sincronización válida");
        }
    }

    protected function getAraiSyncKeyPublic()
    {
	    $publicKey = $this->araiSyncKey->getPublicKey();

        return sodium_bin2hex($publicKey->getRawKeyMaterial());
    }

    protected function getAraiSyncKeyPrivate()
    {
	    $secretKey = $this->araiSyncKey->getSecretKey();

        return sodium_bin2hex($secretKey->getRawKeyMaterial());
    }

    /**
     * @return \toba_modelo_proyecto
     * @throws \Exception
     */
    protected function getModeloProyecto()
    {
        return $this->instalacion->get_instancia($this->getInstanciaId())->get_proyecto($this->getProyectoId());
    }


    protected function getTobaDir()
    {
        return realpath(__DIR__.'/../../../');
    }

    protected function getInstanciaId()
    {
        if (! isset($_SERVER['TOBA_INSTANCIA'])) {
            throw new \Exception("Es necesario definir la instancia toba en la variable de entorno TOBA_INSTANCIA");
        }
        return $_SERVER['TOBA_INSTANCIA'];
    }

    protected function getProyectoId()
    {
        if (! isset($_SERVER['TOBA_PROYECTO'])) {
            throw new \Exception("Es necesario definir el proyecto toba en la variable de entorno TOBA_PROYECTO");
        }
        return $_SERVER['TOBA_PROYECTO'];
    }

    public static function getAppUniqueId()
    {
         try {
            $registry = AraiCli::getRegistryService();
            $providers = $registry->getPackage()->getProvideList();
            if (empty($providers)) return null;

            $enc = false;
            $i = 0;
            $cantidad = count($providers);
            While ($i < $cantidad && !$enc) {
                if ($providers[$i]->getType() == 'app') {
                    $appName = $providers[$i]->getName();
                    $enc = true;
                }
                $i++;
            }
            if ($enc) {
                return $registry->generateAppUniqueId($appName);
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getIniClienteRest($apiId, $acceso)
    {
        $proyecto = isset($acceso['proyecto']) ? $acceso['proyecto'] : $this->getProyectoId();
        $modeloProyecto = $this->instalacion->get_instancia($this->getInstanciaId())->get_proyecto($proyecto);

        $dirIni = \toba_modelo_rest::get_dir_consumidor($modeloProyecto->get_dir_instalacion_proyecto(), $apiId);
        if (! \toba_modelo_rest::existe_ini_cliente($modeloProyecto, $apiId)) {
            throw new \Exception("No se puden enviar las credenciales de la api porque no estÃ¡n definidas en el archivo '$dirIni'");
        }

        return \toba_modelo_rest::get_ini_cliente($modeloProyecto, $apiId);
    }

    protected function agregarServidorUsuariosIni($authCliente, $iniServidorUsuarios)
    {
        if ($authCliente['type'] == 'ssl'){
            $credentials = $this->getCredencialesClienteSSL($authCliente);
        } elseif (in_array($authCliente['type'], array('basic', 'digest'))){
            $credentials = $this->getCredencialesClienteSimple($authCliente);
        }

        if ($credentials){
            $iniServidorUsuarios->agregar_entrada($credentials['user'], $credentials['data']);
        }
    }

    protected function getCredencialesClienteSSL($auth)
    {
        $sslUtils = new SSLCertUtils();
        if (!isset($auth['credentials']['cert'])) {
            throw new \Exception("Se intenta configurar auth de tipo ssl pero no se provee certificado\n");
        }

        $sslUtils->loadCert($auth['credentials']['cert']);

        $user = $sslUtils->getCN();
        $fingerprint = $sslUtils->getFingerprint();

        $credentials = [
            'user' => $user,
            'data' => array('fingerprint' => $fingerprint)
        ];

        return $credentials;
    }

    protected function getCredencialesClienteSimple($auth)
    {
        if (!isset($auth['credentials']['cert'])) {
            throw new \Exception("Se intenta configurar el cliente '{$auth['credentials']['user']}' pero no provee el certificado\n");
        }

        $privateKey = $this->getAraiSyncKeyPrivate();

        // la clave publica es del cliente de la api
        $certPublic = $auth['credentials']['cert'];

        $encryptedAuth = AbstractAuth::getInstance($auth['type'], $auth['credentials'], true, $privateKey, $certPublic);

        // se desencripta la clave del cliente con el certificado privado del server y el publico del cliente
        $decryptedCredentials = $encryptedAuth->getDecryptedCredentials();

        $credentials = [
            'user' => $auth['credentials']['user'],
            'data' => array('password' => $decryptedCredentials['password'])
        ];

        return $credentials;
    }

    protected function actualizarClienteIni($provider, $acceso)
    {
        $iniCliente = $this->getIniClienteRest($acceso['rest-id'], $acceso);

        $datos = $iniCliente->get_datos_entrada('conexion');

        $datos['to'] = $provider->getEndpoint();

        $datos['auth_tipo'] = $provider->getOptions()['auth']['type'];

        $iniCliente->agregar_entrada("conexion", $datos);
        $iniCliente->guardar();
    }

    protected function configurarCliente($feature, $opciones)
    {
        $iniCliente = $this->getIniClienteRest($opciones['rest-id'], $opciones);

        $provider = current($feature->getProviders());
        if (!empty($provider)){
            $authServer = $provider->getOptions()['auth'];
        }

        $authCliente = $iniCliente->get_datos_entrada('conexion');

        if ($authCliente['auth_tipo'] == 'ssl'){
            $credentials = $this->configurarClienteSSL($feature, $authServer, $authCliente);
        } elseif (in_array($authCliente['auth_tipo'], array('basic', 'digest'))){
            $credentials = $this->configurarClienteSimple($feature, $authServer, $authCliente);
        }

        if ($credentials){
            $feature->addAuth($credentials->getType(), $credentials->getEncryptedCredentials());
        }
    }

    protected function configurarClienteSSL($feature, $authServer, $authCliente)
    {
        if (!isset($authCliente['cert_file'])) {
            throw new \Exception("Se intenta enviar los datos de conexion para {$feature->getName()} pero no se seteó la propiedad 'cert_file'");
        }

        $pathCert = $authCliente['cert_file'];
        if (!file_exists($pathCert)) {
            throw new \Exception("El certificado para {$feature->getName()} no se encuentra en el path '$pathCert'");
        }

        $credentials = [
            'cert' => file_get_contents($pathCert)
        ];

        return AbstractAuth::getInstance('ssl', $credentials, true, null, null);
    }

    protected function configurarClienteSimple($feature, $authServer, $authCliente)
    {
        if (empty($authServer['credentials']['cert'])){
            throw new \Exception("Se intenta enviar los datos de conexion para '{$feature->getName()}' pero no se seteó el certificado del servidor");
        }

        $credentials = [
            'user' => $authCliente['auth_usuario'],
            'password' => $authCliente['auth_password'],
        ];

        $certServer = $authServer['credentials']['cert'];

        $sendPrivate = $this->getAraiSyncKeyPrivate();
        $sendPublic = $this->getAraiSyncKeyPublic();

        $credentials['cert'] = $sendPublic;

        return AbstractAuth::getInstance($authServer['type'], $credentials, true, $sendPrivate, $certServer);
    }
}
