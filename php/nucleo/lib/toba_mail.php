<?php

/**
 * Clase para crear un mail en texto plano o html. Encapsula a la librería phpmailer
 * @package Centrales  
 */
class toba_mail implements toba_tarea
{
	protected $desde;
	protected $hacia;
	protected $asunto;
	protected $cuerpo;
	protected $desde_nombre;
	protected $html = false;
	protected $cc = array();
	protected $bcc = array();
	protected $datos_configuracion;
	protected $adjuntos = array();
	protected $debug = false;
	protected $timeout = 30;
	protected $reply_to;
	protected $confirmacion;
	protected $nombre_conf = null;
	
	/**
	 * Constructor de la clase
	 * @param string $hacia  Direccion de email a la cual se enviará
	 * @param string $asunto
	 * @param string $cuerpo Contenido del email
	 * @param string $desde Direccion de email desde la cual se envia (opcionalmente se obtiene desde los parametros)
	 */
	function __construct($hacia, $asunto, $cuerpo, $desde=null)
	{
		$this->hacia = $hacia;
		$this->asunto = $asunto;
		$this->cuerpo = $cuerpo;
		$this->desde = $desde;
	}

	/**
	 * Permite modificar en runtime el nombre de la configuracion smtp a ser utilizada
	 * @param string $nombre_conf  Nombre de la configuracion en el archivo smtp.ini
	 */
	function set_configuracion_smtp($nombre_conf = null)
	{
		if (! is_null($nombre_conf)) {
			$this->nombre_conf = $nombre_conf;
		}
	}

	/**
	 *  Método para obtener la configuración del servidor SMTP
	 */
	function get_datos_configuracion_smtp()
	{
		//Pide a la instalacion la configuración del SMTP
		return toba::instalacion()->get_datos_smtp($this->nombre_conf);
	}

	/**
	 *  Servicio que dispara el envio del email
	 */
	function ejecutar()
	{
		$this->enviar();
	}

	/**
	 *  Realiza el envio del email propiamente dicho
	 */
	function enviar()
	{
		require_once('3ros/phpmailer/class.phpmailer.php');
		
		//Se obtiene la configuración del SMTP
		$this->datos_configuracion = $this->get_datos_configuracion_smtp();
		if (! isset($this->desde)) {
			$this->desde = $this->datos_configuracion['from'];
		}
		
		//Construye y envia el mail
	   	$mail = new PHPMailer();
	   	$mail->IsSMTP();
	   	if ($this->debug) {
	   		$mail->SMTPDebug = true;
	   	}
		$mail->Timeout  = $this->timeout;
		$host = trim($this->datos_configuracion['host']);
		if (isset($this->datos_configuracion['seguridad']) && trim($this->datos_configuracion['seguridad']) != '') {
			if ($this->datos_configuracion['seguridad'] == 'ssl') {
				if (! extension_loaded('openssl')) {
					throw new toba_error('Para usar un SMTP con encriptación SSL es necesario activar la extensión "openssl" en php.ini');
				}
			}
			$mail->set('SMTPSecure', $this->datos_configuracion['seguridad']);
		}
		
		if (isset($this->datos_configuracion['puerto']) && trim($this->datos_configuracion['puerto']) != '') {
			$mail->set('Port', $this->datos_configuracion['puerto']);
		}
		$mail->Host = trim($host);
		if (isset($this->datos_configuracion['auth']) && $this->datos_configuracion['auth']) {
			$mail->SMTPAuth = true;
			$mail->Username = trim($this->datos_configuracion['usuario']);
			$mail->Password = trim($this->datos_configuracion['clave']);
		}		
		$mail->From     = $this->desde;
		if (isset($this->datos_configuracion['nombre_from']) && trim($this->datos_configuracion['nombre_from']) != '') {
			$this->desde_nombre = $this->datos_configuracion['nombre_from'];
		}		
		if (isset($this->desde_nombre)){
			$mail->FromName = $this->desde_nombre;
		} else {
			$mail->FromName = $this->desde;			
		}
		$mail->AddAddress($this->hacia);
		foreach($this->cc as $copia){
			$mail->AddCC($copia);
		}
		
		foreach($this->bcc as $copia){
			$mail->AddBCC($copia);
		}
		
		if (isset($this->reply_to)){
			$mail->AddReplyTo($this->reply_to);
		}
		
		if (isset($this->confirmacion)){
			$mail->ConfirmReadingTo = $this->confirmacion;
		}
			
		$mail->Subject  = $this->asunto;
		$mail->Body     = $this->cuerpo;
		$mail->IsHTML($this->html);
		$temporales = array();
		$dir_temp = toba::proyecto()->get_path_temp();
		foreach (array_keys($this->adjuntos) as $id_adjunto) {
			$archivo = tempnam($dir_temp, 'adjunto');
			file_put_contents($archivo, $this->adjuntos[$id_adjunto]['archivo']);
			$temporales[] = $archivo;
		   	$tipo = $mail->_mime_types($this->adjuntos[$id_adjunto]['tipo']);
			$mail->AddAttachment($archivo, $this->adjuntos[$id_adjunto]['nombre'], 'base64', $tipo);
		}
		
		$exito = $mail->Send();
		toba::logger()->debug("Enviado mail con asunto {$this->asunto} a {$this->hacia}");
		
		//Elimina los temporales creado para los attachments
		foreach ($temporales as $temp) {
			unlink($temp);
		}
		if (!$exito) {
			throw new toba_error("Imposible enviar mail. Mensaje de error: {$mail->ErrorInfo}");
		}			
		
	}

	/**
	 * Configura las direcciones a las que se enviara copia carbonica
	 * @param array $direcciones Arreglo de direcciones de email
	 */
	function set_cc($direcciones = array())
	{
		$this->cc = $direcciones;
	}
	
	/**
	 * Configura las direcciones a las que se enviara copia carbónica oculta
	 * @param array $direcciones Arreglo de direcciones de email
	 */
	function set_bcc($direcciones = array())
	{
		$this->bcc = $direcciones;
	}
	

	/**
	 * Indica que el cuerpo del email contiene codigo HTML
	 * @param boolean $html
	 */
	function set_html($html=true)
	{
		$this->html = true;
	}

	/**
	 * Configura la direccion de email a la cual se debe responder
	 * @param string $reply
	 */
	function set_reply($reply)
	{
		$this->reply_to = $reply;
	}

	/**
	 * Configura el FromName para el envío del mail
	 * @param string $from_name
	 */
	function set_remitente($from_name)
	{
		$this->desde_nombre = $from_name;
	}
	
	/**
	 * Indica la direccion de email a la cual debe llegar la confirmacion
	 * @param string $confirm
	 */
	function set_confirmacion($confirm)
	{
		$this->confirmacion = $confirm;
	}
	
	/**
	 * Agrega un archivo adjunto al mail
	 * @param string $nombre Nombre del archivo a mostrarse en el correo
	 * @param string $path_archivo Path al archivo en el disco
	 * @param string $encoding Encoding del archivo
   	 * @param string $tipo File extension (MIME) type
	 */
	function agregar_adjunto($nombre, $path_archivo, $encoding = 'base64', $tipo = '')
	{
		$this->adjuntos[] = array(
			'nombre' => $nombre,
			'archivo' => file_get_contents($path_archivo),
			'encoding' => $encoding,
			'tipo' => $tipo
		);
	}	
}

?>