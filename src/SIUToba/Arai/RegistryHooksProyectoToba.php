<?php
namespace SIUToba\Framework\Arai;

use SIU\AraiCli\Services\Registry\HooksInterface;
use SIU\AraiJsonParser\Feature\Consumption;
use SIU\AraiJsonParser\Feature\Feature;
use SIU\AraiJsonParser\Feature\Provision;


/**
 * Class RegistryHooksProyectoToba
 * @package SIUToba\Framework\Arai
 *
 * Clase que implementa los hooks requeridos por ARAI-cli:Registry para configurar un proyecto típico Toba
 *
 * Se asumen algunas cosas:
 *   * Solo funciona si estan definidas TOBA_INSTANCIA y TOBA_PROYECTO
 *   * Se configura una unica API cuya url es URL_PROYECTO/rest (redefinir si hay mas APIs o tienen otras URLs).
 *     Se envia el primer user/pass que encuentra (inseguro, se va a migrar a un modelo clave publica/crt)
 *   * La configuracion del SP de SAML se asume que se usa la autenticacion saml_onelogin
 *
 */
class RegistryHooksProyectoToba implements HooksInterface
{
    /**
     * @var \toba_modelo_instalacion
     */
    protected $instalacion;

    function __construct()
    {
        $this->instalacion = $this->cargarToba();
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
     * configurar el sistema a partir de la información en registry.
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
     * los datos dependientes de la instalación.
     * @param Provision $f una feature sin información de instalación, por ej: url.
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
//        $iniInstalacion = new \toba_ini($this->instalacion->archivo_info_basica());
//        if ($iniInstalacion->existe_entrada("autenticacion") && $iniInstalacion->get_datos_entrada('autenticacion') == 'saml_onelogin') {

        $options['assertionConsumerService'] = "$url/?acs";
        $options['singleLogoutService'] = "$url/?sls";
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

        $options = $provider->getOptions();
        if (! isset($optionsFijas['toba-rest'])) {
            echo "No se pudo configurar la feature '".$provider->getName()."', falta setearle los identificadores de los accesos REST en el campo 'toba-rest'\n";
            return;
        }
        foreach ($optionsFijas['toba-rest'] as $acceso) {
            if (! isset($acceso['rest-id'])) {
                echo "No se pudo configurar la feature '".$provider->getName()."', falta setearle el identificador del acceso REST en el campo 'toba-rest.rest-id'\n";
                break;
            }
            $apiId = $acceso['rest-id'];
            $proyecto = isset($acceso['proyecto']) ? $acceso['proyecto'] : $this->getProyectoId();
            $modeloProyecto = $this->instalacion->get_instancia($this->getInstanciaId())->get_proyecto($proyecto);
            $dirIni = $modeloProyecto->get_dir_instalacion_proyecto()."/rest/$apiId/";
            if (! file_exists($dirIni)) {
                if (mkdir($dirIni) === false) {
                    throw \Exception("No se pudo crear la carpeta $dirIni. Problemas de permisos?");
                }
            }
            $ini = new \toba_ini($dirIni."/cliente.ini");
            $datosApi = array();
            $datosApi['to'] = $provider->getEndpoint();
            if (isset($options['auth']['type'])) $datosApi['auth_tipo'] = $options['auth']['type'];
            if (isset($options['auth']['userId'])) $datosApi['auth_usuario'] = $options['auth']['userId'];
            if (isset($options['auth']['userPass'])) $datosApi['auth_password'] = $options['auth']['userPass'];
            $ini->agregar_entrada("conexion", $datosApi);
            $ini->guardar();

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
        $options = array();
        $modeloProyecto = $this->getModeloProyecto();
        $iniServer = \toba_modelo_rest::get_ini_server($modeloProyecto);
        $iniUsuarios = \toba_modelo_rest::get_ini_usuarios($modeloProyecto);

        if ($autoconfigurar && ! $iniServer->existe_entrada("autenticacion")) {
            echo "Autoconfigurando API...";
            $iniServer->agregar_entrada("autenticacion", "digest");
            $iniServer->guardar();
        }
        if ($autoconfigurar && empty($iniUsuarios->get_entradas())) {
            $iniUsuarios->agregar_entrada($this->getProyectoId(), array("password" => md5(uniqid(rand(), true))));
            $iniUsuarios->guardar();
        }

        if ($iniServer->existe_entrada("autenticacion")) {
            $options['auth']['type'] = $iniServer->get_datos_entrada("autenticacion");
        }
        //TODO: esta tomando el primer usuario y lo manda. Es totalmente inseguro, esto tiene que ir hacia un modelo clave privada/crt
        $usuarios = $iniUsuarios->get_entradas();
        if (! empty($usuarios)) {
            foreach ($usuarios as $usuario => $datos) {
               $options['auth']['userId'] = $usuario;
               $options['auth']['userPass'] = $datos['password'];
               break;
            }
        }
        $endpoint = $this->getProyectoUrl() . '/rest/';
        if (isset($_SERVER['DOCKER_NAME'])) {
            //HACK: en el caso de docker la IP interna difiere de la externa. Se trata de sacar con la variable DOCKER_NAME
            $parts = parse_url($endpoint);
            if (isset($parts['host'])) $parts['host'] = $_SERVER['DOCKER_NAME'];
            if (isset($parts['port'])) unset($parts['port']);
            $endpoint = unparse_url($parts);
        }
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
}