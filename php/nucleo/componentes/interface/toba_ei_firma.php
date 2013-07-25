<?php
/**
 * Genera un editor de código
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei ei
 * @wiki Referencia/Objetos/ei_firma
*/ 
 
class toba_ei_firma extends toba_ei
{
	protected $_ancho;
	protected $_alto;
	protected $_motivo_firma = "";

	final function __construct($id)
	{	
		parent::__construct($id);
		$this->_ancho = get_var($this->_info_firma['ancho'], '600px');
		$this->_alto = get_var($this->_info_firma['alto'], '300px');
	}

	//-------------------------------------------------------------------------------
	//---- UTIL PARA LA CONSTRUCCIÓN DEL COMPONENTE ---------------------------------
	//-------------------------------------------------------------------------------

	protected function get_datos()
	{
		return array();
	}

	protected function get_dimensiones()
	{
		return array($this->_ancho, $this->_alto);
	}

	function  disparar_eventos()
	{
		if (isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];
			if (isset($this->_memoria['eventos'][$evento])) {
				//Me fijo si el evento requiere validacion
				$maneja_datos = ($this->_memoria['eventos'][$evento] == apex_ei_evt_maneja_datos);
				if($maneja_datos) {
					$parametros = $_POST[$this->_id_post_codigo];
				} else {
					$parametros = null;
				}
				//El evento es valido, lo reporto al contenedor
				$this->reportar_evento( $evento, $parametros );
			}
		}
	}

	function set_datos($datos)
	{

	}

	function generar_html()
	{
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI FIRMA (	".	$this->_id[1] ." )	***********	-->\n\n";
		echo toba_form::hidden($this->_submit, '');
		//echo toba_form::hidden($this->_id_post_codigo, ''); // Aca viaja el codigo
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-form-barra-sup");

		$this->generar_applet();
	}

	function generar_applet()
	{
	    $sesion = $this->generar_sesion();
		$cookie = session_name()."=".session_id();
		$url_jar = toba_recurso::url_toba()."/firmador_pdf/firmador.jar";
		
		$destino = array($this->_id);
		$url = toba::vinculador()->get_url(null, null, array(),array('servicio' => 'ejecutar',
														 'objetos_destino' => $destino));

		$url_base = $this->get_url_base_actual();
		$url_descarga = toba::vinculador()->get_url(null, null, array('accion' => 'descargar'),array('servicio' => 'ejecutar',
														 'objetos_destino' => $destino), true);
		$url_descarga = $url_base.$url_descarga;
		
		$url_subir = toba::vinculador()->get_url(null, null, array('accion' => 'subir'),array('servicio' => 'ejecutar',
														 'objetos_destino' => $destino), true);
		$url_subir = $url_base.$url_subir;

        echo "<applet  code='ar/gob/onti/firmador/view/FirmaApplet'
           archive='$url_jar' width='{$this->_ancho}' height='{$this->_alto}' >
         <param  name='URL_DESCARGA'	 value='$url_descarga' />
         <param  name='URL_SUBIR'	value='$url_subir' />
         <param  name='MOTIVO'  value='{$this->_motivo_firma}' />
         <param  name='CODIGO'  value='$sesion' />
         <param  name='PREGUNTAS' value='{ \"preguntasRespuestas\": []}' />
		 <param  name='COOKIE' value='$cookie' />
		 <param name='codebase_lookup' value='false' />			 
		</applet>";
	}
	
	function get_url_base_actual() 
	{
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port;
	}
   
	
	function generar_sesion()
	{
		if (! isset($this->_memoria['token'])) {
			$this->_memoria['token'] = hash('sha256', uniqid(mt_rand(), true));
		}
        return $this->_memoria['token'];
     }
	
	 function set_motivo_firma($motivo)
	 {
		 $this->_motivo_firma;
	 }
	 
	 function set_dimension($ancho, $alto)
	 {
		 $this->_ancho = $ancho;
		 $this->_alto = $alto;
	 }
	 
	 /**
	 * Servicio que se ejecuta cuando el applet busca/envia el PDF
	 * @param <type> $parametros
	 * @ignore
	 */
	function servicio__ejecutar($parametros = null)
	{
		toba::memoria()->desactivar_reciclado();
		
		//-- DESCARGAR
		if ($_GET['accion'] == 'descargar') {
			if (! isset($_GET['codigo'])) {
				header('HTTP/1.1 500 Internal Server Error');
				throw new toba_error_seguridad("Falta indicar el codigo");
			}
			if ($this->_memoria['token']  != $_GET['codigo']) {
				header('HTTP/1.1 500 Internal Server Error');
				throw new toba_error_seguridad("Codigo invalido");   
			}	
			
			//Reportar evento al padre
			$pdf = $this->reportar_evento_interno('enviar_pdf', $this->_memoria['token']);
			if (isset($pdf) && $nodo !== apex_ei_evt_sin_rpta) {
				//Enviar PDF
				header("Cache-Control: private");
				header("Content-type: application/pdf");
				header("Pragma: no-cache");
				header("Expires: 0");				
				echo $pdf;
			} else {
				toba::logger()->error("toba_ei_fimar: No se atrapo el evento get_pdf!");
			}			
			return;
		}

		//-- SUBIR
		if ($_GET['accion'] == 'subir') {
			if (! isset($_POST['codigo'])) {
				header('HTTP/1.1 500 Internal Server Error');
				throw new toba_error_seguridad("Falta indicar el codigo");
			}
			if ($this->_memoria['token'] != $_POST['codigo']) {
				header('HTTP/1.1 500 Internal Server Error');
				throw new toba_error_seguridad("Codigo invalido");   
			}
			if ($_FILES["md5_fileSigned"]["error"] != UPLOAD_ERR_OK) {
				error_log("Error uploading file");
				header('HTTP/1.1 500 Internal Server Error');
				die;
			}	
			$temp = rand();
			$destino = toba::proyecto()->get_path_temp()."/$temp.pdf";
			$path = $_FILES['md5_fileSigned']['tmp_name'];
			if (! move_uploaded_file($path, $destino)) {
				error_log("Error uploading file");
				header('HTTP/1.1 500 Internal Server Error');
				return;
			}			
			try {
				$this->reportar_evento_interno('recibir_pdf_firmado', $destino, $this->_memoria['token']);			
			} catch (Exception $e) {
				error_log("Error al atender el evento recibir_pdf_firmado");
				header('HTTP/1.1 500 Internal Server Error');				
				throw $e;
			}
			unlink($destino);
			return;
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @ignore
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$id = toba_js::arreglo($this->_id, false);
		//$dim = toba_js::arreglo($this->get_dimensiones(), false);

		echo $identado."window.{$this->objeto_js} = new ei_firma($id,  '{$this->_submit}');\n";
	}

	/**
	 * @ignore
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_firma';
		return $consumo;
	}

}
?>