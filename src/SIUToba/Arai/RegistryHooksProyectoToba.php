<?php
namespace SIUToba\Framework\Arai;

use SIU\AraiCli\AraiCli;
use SIU\AraiCli\Services\Registry\HooksInterface;
use SIU\AraiJsonParser\Feature\Consumption;
use SIU\AraiJsonParser\Feature\Provision;

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
		$cant = count($feature->getProviders());
		echo "Detectando proveedores de api '{$feature->getName()}': {$cant}\n";
		
		$providers = $feature->getProviders();
		if (empty($providers)) return;    //Nada para configurar

		/** @var Provision */
		$optionsFijas = $feature->getOptions();
		$provider = current($providers);  //Asume primer IDP que encuentra!
		
		echo "Procesando proveedor '{$provider->getName()}': ";
		
		if ($provider->getEndpoint() == '') {
			echo "no posee configurado 'endpoint'\n";
			return;
		}

		if (! isset($optionsFijas['toba-rest'])) {
			echo "falta setearle los identificadores de los accesos REST en el campo 'toba-rest'\n";
			return;
		}
		foreach ($optionsFijas['toba-rest'] as $acceso) {
			if (! isset($acceso['rest-id'])) {
				echo "falta setearle el identificador del acceso REST en el campo 'toba-rest.rest-id'\n";
				break;
			}

			$this->actualizarClienteIni($provider, $acceso);
		}


		//Al auto-configurar la API de ARAI-Usuarios, se asume que el ABM de usuarios empieza a trabajar contra esta API
		if ($feature->getName() == "api:siu/arai-usuarios") {
			$iniInstalacion = new \toba_ini($this->instalacion->archivo_info_basica());
			$iniInstalacion->agregar_entrada("vincula_arai_usuarios", "1");
			$iniInstalacion->guardar();
			
			echo "Activando vínculo entre 'toba_usuarios' y 'Araí' para la gestión de cuentas\n";
		}		
	}



	protected function postConsumeService(Consumption $feature)
	{
		$cant = count($feature->getProviders());
		echo "Detectando proveedores de servicio '{$feature->getName()}': {$cant}\n";
		
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

		echo "Procesando proveedor '{$provider->getName()}': ";
		
		$endpoint = $provider->getEndpoint();
		if ($endpoint == '') {
			echo "no posee configurado 'endpoint'\n";
			return;
		}
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
		
		echo "configurado con '{$provider->getEndpoint()}'\n";
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

		$cant = count($feature->getConsumers());
		echo "Detectando clientes de api '{$feature->getName()}': {$cant}\n";
		
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
	if (substr($fullUrl, -1) == '/') {
		$fullUrl = substr($fullUrl, 0, -1);
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
		//--Carga nucleo para registrar todos los autoloaders y tener acceso a las clases del modelo / funciones globales desde arai-cli
		$dir = realpath($this->getTobaDir()."/php");
		require_once("$dir/nucleo/toba_nucleo.php");
		//Inicio desde consola para procesar el contexto de ejecucion (puede ser necesario para JWT)
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
			 throw new \Exception("Debe configurar en el archivo 'instalacion.ini' la entrada 'arai_sync_key_file' con la ruta al certificado para sincronizar con SIU-Arai");
		}

		$araiSyncKeyFile = $iniInstalacion->get_datos_entrada("arai_sync_key_file");

		if (!is_readable($araiSyncKeyFile)) {
			throw new \Exception("No se puede leer el certificado '$araiSyncKeyFile' para sincronizar con SIU-Arai configurado en el archivo 'instalacion.ini'");
		}
		
		return $araiSyncKeyFile;
	}

	protected function getAraiSyncKeyPublic()
	{
		return AraiCli::getCryptoService()->getPublicFromSyncKey($this->araiSyncKey);
	}

	protected function getAraiSyncKeyPrivate()
	{
		return AraiCli::getCryptoService()->getPrivateFromSyncKey($this->araiSyncKey);
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
			return;
		}

		return \toba_modelo_rest::get_ini_cliente($modeloProyecto, $apiId);
	}

	protected function agregarServidorUsuariosIni($authCliente, $iniServidorUsuarios)
	{
		 if (in_array($authCliente['type'], array('basic', 'digest'))) {
			$credentials = $this->getCredencialesClienteSimple($authCliente);
		}

		if ($credentials){
			$iniServidorUsuarios->agregar_entrada($credentials['user'], $credentials['data']);
		}
	}

	protected function getCredencialesClienteSimple($auth)
	{
		echo "Procesando cliente con usuario '{$auth['credentials']['user']}':";
		if (!isset($auth['credentials']['cert'])) {
			return;
		}

		$privateKey = $this->getAraiSyncKeyPrivate();

		// la clave publica es del cliente de la api
		$certPublic = $auth['credentials']['cert'];

		$encrypted = $auth['credentials']['password'];

		$decryptedCredentials = false;
		try {
			// se desencripta la clave del cliente con el certificado privado del server y el publico del cliente
			$decryptedCredentials = AraiCli::getCryptoService()->decrypt($encrypted, $privateKey, $certPublic);
		} catch (\Exception $e) {
			echo " {$e->getMessage()}\n";
		}

		if (!$decryptedCredentials){
			return;
		}		
		echo " Desencriptado correcto de la clave\n";
		$credentials = [
			'user' => $auth['credentials']['user'],
			'data' => array('password' => $decryptedCredentials)
		];

		return $credentials;
	}

	protected function actualizarClienteIni($provider, $acceso)
	{
		$apiId = $acceso['rest-id'];
		$iniCliente = $this->getIniClienteRest($apiId, $acceso);

		if (!$iniCliente) {
			echo "el cliente de la api '$apiId' no esta correctamente configurado (no posee el cliente.ini)\n";
			return;
		}

		if (!$iniCliente->existe_entrada("conexion")) {
			echo "el cliente de la api '$apiId' no esta correctamente configurado (no posee la entrada conexion)\n";
			return;
		}
		$datos = $iniCliente->get_datos_entrada('conexion');

		$datos['to'] = $provider->getEndpoint();

		$datos['auth_tipo'] = $provider->getOptions()['auth']['type'];

		$iniCliente->agregar_entrada("conexion", $datos);
		$iniCliente->guardar();
		
		echo "configurado con '{$provider->getEndpoint()}'\n";
	}

	protected function configurarCliente($feature, $opciones)
	{
		$apiId = $opciones['rest-id'];
		$iniCliente = $this->getIniClienteRest($apiId, $opciones);
		if (!$iniCliente) {
			echo "el cliente de la api '$apiId' no esta correctamente configurado (no posee el cliente.ini)\n";
			return;
		}

		 if (!$iniCliente->existe_entrada("conexion")) {
			echo "el cliente de la api '$apiId' no esta correctamente configurado (no posee la entrada conexion)\n";
			return;
		}
		
		$provider = current($feature->getProviders());
		if (empty($provider)){
			return;
		}

		$authServer = $provider->getOptions()['auth'];
		$authCliente = $iniCliente->get_datos_entrada('conexion');
		
		$authType = $authCliente['auth_tipo'];
		
		if (in_array($authType, array('basic', 'digest'))) {
			$credentials = $this->configurarClienteSimple($feature, $authServer, $authCliente);
		}

		if ($credentials){
			$feature->addAuth($authType, $credentials);
		}
	}

	protected function configurarClienteSimple($feature, $authServer, $authCliente)
	{
		if (empty($authServer['credentials']['cert'])){
			echo "Se intenta enviar los datos de conexion a la api '{$feature->getName()}' pero no se encuentra definida la propiedad 'cert' del servidor\n";
			return null;
		}

		$theirPublic = $authServer['credentials']['cert'];
		$ourPrivate = $this->getAraiSyncKeyPrivate();
		$sendPublic = $this->getAraiSyncKeyPublic();

		$encrypted = AraiCli::getCryptoService()->encrypt($authCliente['auth_password'], $ourPrivate, $theirPublic);

		$credentials = [
			'user' => $authCliente['auth_usuario'],
			'password' => $encrypted,
		         'cert' => $sendPublic,
		];

		 return $credentials;
	}
	
	//-----------------------------------------------------------------------------//
	public static function checkVersionCompatible()
	{
		$version = new \toba_version(AraiCli::getVersion());

		//Recupero los topes inferior-superior de la config de toba
		$instalacion = \toba_modelo_catalogo::instanciacion()->get_instalacion(null);
		$limites = $instalacion->get_compatibilidad_arai_cli();
		$inferior = new \toba_version($limites[0]);
		$techo = new \toba_version($limites[1]);

		return ($inferior->es_menor_igual($version) && $techo->es_mayor($version));
	}
}
