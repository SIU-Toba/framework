<?
//------------------------------------------------
//	Funciones MIME para envio de E-mails
//------------------------------------------------

class correo
{
	private $mime;
	private $hdrs;
	private $cuentas_email;

	function __construct ()
	{
		include('Mail.php');
		include('Mail/mime.php');
		$this->mime = new Mail_mime();
	}

	function set_txt_body($txt)
	{
		$this->mime->setTxtBody($txt);
	}
	
	function set_html_body($html)
	{
		$this->mime->setHtmlBody($html);
	}
	
	function set_adjunto($attachment)
	{
		$this->mime->addAttachment($attachment);
	}
	
	function set_cabeceras($hdrs)
	{
		$this->hdrs = $hdrs;
	}
	
	function set_cuentas($cuentas_email)
	{
		//SEPARO CON COMAS (,) LAS DIFERENTES CUENTAS DE EMAIL
		//IMPLODE!!!
		$cuentas = null;
		for($i=0;$i<count($cuentas_email); $i++)
		{
			if(count($cuentas_email) >= $i)
			{
				$cuentas .= $cuentas_email[$i]['email'] . " , ";
			}else{
				$cuentas .= $cuentas_email[$i]['email'];
			}	
		}

		$this->cuentas_email = $cuentas;
	}
	
	function enviar ()
	{
		if(isset($this->cuentas_email))
		{
			$body = $this->mime->get(); 
			$hdrs = $this->mime->headers($this->hdrs);
			$mail = &Mail::factory('mail'); 
			$ok = $mail->send($this->cuentas_email, $hdrs, $body); 
			return $ok;
		}else{
			return "No se setearon la/s direccion/es de destino";
		}
	}
}

?>