<?php
require_once("nucleo/negocio/objeto_cn.php");	//Ancestro de todos los CN
require_once("nucleo/lib/correo.php"); //Clase correo


class objeto_cn_correo extends objeto_cn
{

	private $mensaje;
	private $usuarios;
	private $attach;
	
	private  $correo;

	function __construct($id)
	{
		parent::__construct($id);
		$this->correo = new correo();
	}

//-------------------------------------------------------------------------------
// Proveer a la interface
//-------------------------------------------------------------------------------

	function obtener_usuarios()
	{
		try{
			$sql = "SELECT usuario, email, '0' as envio FROM apex_usuario ORDER BY usuario ASC;";
			$usuarios = consultar_fuente($sql);
			return $usuarios;
		}catch(excepcion_toba $e){
			echo "error";
		}
	}

//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------

	function set_mensaje($mensaje)
	{
		$this->mensaje = $mensaje;
		
	}

	function set_usuarios($usuarios)
	{
		$this->usuarios = $usuarios;
	}
	
	function set_adjunto($file)
	{
		$this->attach = $file;
	}

//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------

	function procesar()
	{
		//Recupero mensaje y asunto

			$this->correo->set_txt_body($this->mensaje['mensaje']);
			$this->correo->set_html_body($this->mensaje['mensaje']);
			$cabecera['Subject'] = $this->mensaje['asunto'];
			$this->correo->set_cabeceras($cabecera);

	
		//Recupero adjuntos si existen

			if(isset($this->mensaje['adjuntos']))
			{
				$this->correo->set_adjunto($this->mensaje['adjuntos']);
			}

		
		//Recupero Usuarios con checkbox 'enviar' seteado
			$inc = 0;
			for($i=0;$i<count($this->usuarios);$i++)
			{
				if($this->usuarios[$i]['envio'] == 1)
				{
					$usuarios[$inc] = $this->usuarios[$i];
					$inc++;
				}
			}
			
			$this->correo->set_cuentas($usuarios);
			//ei_arbol($this->usuarios, "USUARIOS");

		//Envio
			$ok = $this->correo->enviar();
			//ei_arbol($ok, "Enviar Mail");
			//echo ei_mensaje("Enviar MAIL");

	}
}
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
?>