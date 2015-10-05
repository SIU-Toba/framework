<?php
namespace SIUToba\Framework\Arai;

use Siu\AraiCli\Services\Registry\HooksInterface;
use Siu\AraiJsonParser\Feature\Consumption;
use Siu\AraiJsonParser\Feature\Feature;
use Siu\AraiJsonParser\Feature\Provision;


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

    }

    /**
     * Se ejecuta antes de enviar una feature para consumo al registry.
     * @param Consumption $consumption
     */
    public function preConsume(Consumption $consumption)
    {

    }

    /**
     * Se ejecuta cuando vuelve una feature del registry con los datos para su consumo. Este hook es utilizado para
     * configurar el sistema a partir de la información en registry.
     * @param Consumption $feature
     */
    public function postConsume(Consumption $feature)
    {

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
    //---- Extensiones propias
    //---------------------------------------------------------------

    protected function preProvideApp(Feature $feature)
    {
        $feature->setEndpoint($this->getProyectoUrl());
    }

    protected function preProvideApi(Feature $feature)
    {
        $options = array();
        $modeloProyecto = $this->instalacion->get_instancia($this->getInstanciaId())->get_proyecto($this->getProyectoId());
        $iniServer = \toba_modelo_rest::get_ini_server($modeloProyecto);
        $iniUsuarios = \toba_modelo_rest::get_ini_usuarios($modeloProyecto);

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
        $feature->setEndpoint($this->getProyectoUrl().'/rest');
        $feature->setOptions($options);
    }

    protected function preProvideService(Feature $feature)
    {
        if ($feature->getName() == "service:siu/sso-saml-sp") {
            $this->preProvideSAMLSP($feature);
        }
    }

    protected function preProvideSAMLSP(Feature $feature)
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