<?php

class toba_servicio_web_cliente_soap extends toba_servicio_web_cliente
{
	protected $wsf;

	
	function __construct($opciones, $id_servicio, $proyecto = null) 
	{
        parent::__construct($opciones, $id_servicio, $proyecto);
		$this->wsf = new WSClient($this->opciones);
	}

	/**
     * @return toba_servicio_web_cliente_soap
     */
    static function conectar($id_servicio, $opciones=array(), $proyecto = null)
    {
        if (! isset($proyecto)) {
            $proyecto = toba_editor::activado() ? toba_editor::get_proyecto_cargado() : toba::proyecto()->get_id();
        }
        $info = toba::proyecto()->get_info_servicios_web_acc($id_servicio, $proyecto);
        $opciones_ini = $info['parametros'];

        self::get_modelo_proyecto($proyecto);
        $ini = toba_modelo_servicio_web::get_ini_cliente(self::$modelo_proyecto, $id_servicio);
        if ($ini->existe_entrada('conexion')) {
            $opciones_ini = array_merge($opciones_ini, $ini->get_datos_entrada('conexion'));
        }

        //--2- Arma el servicio indicando certificado del server y clave privada del cliente
        $security_token = self::get_ws_token($proyecto, $id_servicio);
        if (isset($security_token)) {
            $seguridad = array(
                "sign" => true,
                "encrypt" => true,
                "algorithmSuite" => "Basic256Rsa15",
                "securityTokenReference" => "IssuerSerial");

            $policy = new WSPolicy(array("security" => $seguridad));
            $opciones_ini['policy'] = $policy;
            $opciones_ini['securityToken'] = $security_token;
        }

        //Convierte todos los '1' de texto en true
        foreach (array_keys($opciones_ini) as $id_opcion) {
            if ($opciones_ini[$id_opcion] === '1' || $opciones_ini[$id_opcion] === 1) {
                $opciones_ini[$id_opcion] = true;
            }
        }

        //-- Mezcla con las opciones recibidas y crea el objeto
        $opciones = array_merge($opciones_ini, $opciones);
        if (! isset($opciones['to'])) {
            throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");
        }
        if (isset($opciones['seguro']) && $opciones['seguro'] && ! isset($seguridad)) {
            throw new toba_error("El servicio web esta configurado para requerir firma, sin embargo no se <a target='_blank' href='http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb/Seguridad#configuracion'>configuro correctamente</a> el servicio importando el certificado del servidor.");
        }
        toba::logger()->debug("Invocando servicio $id_servicio. Opciones:<br>". var_export($opciones, true));
        $servicio = new toba_servicio_web_cliente_soap($opciones, $id_servicio);
        return $servicio;
    }

    /**
     * Devuelve un objeto WSF con la configuracion de certificados ssl existente o null
     * @param string $proyecto
     * @param string $servicio
     * @throws toba_error
     * @return WSSecurityToken
     */
    static function get_ws_token($proyecto, $servicio)
    {
        $security_token = null;
        self::get_modelo_proyecto($proyecto);
        $ini_conf = toba_modelo_servicio_web::get_ini_cliente(self::$modelo_proyecto, $servicio);
        $directorio = toba_instancia::instancia()->get_path_instalacion_proyecto($proyecto). "/servicios_cli/$servicio";		//Directorio perteneciente al servicio

        //Busco los datos para los certificados en el archivo perteneciente al servicio
        if (! is_null($ini_conf) && $ini_conf->existe_entrada('certificado')) {
            chdir($directorio);
            $config = $ini_conf->get_datos_entrada('certificado');

            //Cargo las claves y armo el objeto WSF
            if (! file_exists($config['clave_cliente'])) {
                throw new toba_error("El archivo ".$config['clave_cliente']." no existe");
            }
            $clave_cliente = ws_get_key_from_file($config['clave_cliente']);

            if (! file_exists($config['cert_cliente'])) {
                throw new toba_error("El archivo ".$config['cert_cliente']." no existe");
            }
            $cert_cliente = ws_get_cert_from_file($config['cert_cliente']);

            if (! file_exists($config['cert_servidor'])) {
                throw new toba_error("El archivo ".$config['cert_servidor']." no existe");
            }
            $cert_server = ws_get_cert_from_file($config['cert_servidor']);

            $security_token = new WSSecurityToken(array("privateKey" => $clave_cliente,
                    "receiverCertificate" => $cert_server,
                    "certificate" 		=> $cert_cliente
                )
            );
        }
        return $security_token;
    }

	/**
	 * @return WSClient
	 */
	function wsf()
	{
		return $this->wsf;
	}

	/**
	 * Envia un mensaje al servicio web y espera la respuesta
	 * @param toba_servicio_web_mensaje $mensaje
	 * @return toba_servicio_web_mensaje
	 */
	function request(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$message = $this->wsf->request($mensaje->wsf());
			if (! toba::instalacion()->es_produccion()) {
				toba::logger()->debug("Request: " . var_export($this->wsf->getLastRequest(), true));
				toba::logger()->debug("Response: " . var_export($this->wsf->getLastResponse(), true));
				toba::logger()->var_dump($this->wsf->getLastResponseHeaders());
			}

			//-- INICIO PARCHE: Intenta parsear un Fault por bug en libreria WSF con esquema de seguridad..
			if (is_a($message, 'WSMessage')) {
				$inicio = "<soapenv:Fault";
				if (substr($message->str, 0, strlen($inicio)) == $inicio) {
					$xml = new SimpleXMLElement($message->str);
					$ns = $xml->getDocNamespaces(true);
					$childrens = $xml->children($ns['soapenv']);
					$code = @(string) $childrens->Code->Value;
					$reason = @(string) $childrens->Reason->Text;
					$detail = @(string) $childrens->Detail->children($ns['soapenv']->children, true)->error;
					throw new WSFault(str_replace("soapenv:", "", $code), $reason, null, $detail);
				}
			}
			//--- FIN PARCHE

			return new toba_servicio_web_mensaje($message);
		} catch (WSFault $fault) {
			if (! toba::instalacion()->es_produccion()) {
				toba::logger()->debug("Request: " . var_export($this->wsf->getLastRequest(), true));
				toba::logger()->debug("Response: " . var_export($this->wsf->getLastResponse(), true));
				toba::logger()->var_dump($this->wsf->getLastResponseHeaders());
			}
			$detalle = (isset($fault->Detail)) ? $fault->Detail: '';
			$code = (isset($fault->Code)) ? $fault->Code: '';
			self::get_modelo_proyecto($this->proyecto);
			throw new toba_error_servicio_web($fault->Reason, $detalle, $code);
		} catch (Exception $e) {
			if (! toba::instalacion()->es_produccion()) {
				toba::logger()->debug("Request: " . var_export($this->wsf->getLastRequest(), true));
				toba::logger()->debug("Response: " . var_export($this->wsf->getLastResponse(), true));
				toba::logger()->var_dump($this->wsf->getLastResponseHeaders());
			}
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());
		}
	}

	function send(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$this->wsf->send($mensaje->wsf());
		} catch (WSFault $fault) {
			self::get_modelo_proyecto($this->proyecto);
			toba::logger()->debug("Request: " .$this->wsf->getLastRequest());
			toba::logger()->debug("Response: " .$this->wsf->getLastResponse());
			$detalle = (isset($fault->Detail)) ? $fault->Detail: '';
			throw new toba_error_servicio_web($fault->Reason, $fault->Code, $detalle);
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());
		}
	}

}
?>