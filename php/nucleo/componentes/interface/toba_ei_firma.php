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
	protected $_mostrar_pdf = true;
	protected $_pdf_altura = "500px";
	protected $_multiple = false;
	protected $_url_pdf_embebido = null;
	
	final function __construct($id)
	{	
		parent::__construct($id);
		$this->_ancho = get_var($this->_info_firma['ancho'], '500px');
		$this->_alto = get_var($this->_info_firma['alto'], '120px');
		if (isset($this->_memoria['dir_actual'])) {
			$this->_multiple = $this->_memoria['multiple'];
		}		
	}

		/**
	 * Destructor del componente
	 */
	function destruir()
	{
		$this->_memoria['multiple'] = $this->_multiple;
		parent::destruir();
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
		$valor_cross_token = toba::memoria()->get_dato_operacion(apex_sesion_csrt);

		$destino = array($this->_id);
		
		$url_enviar = $this->get_url_enviar_pdf(true);
		$url_base = $this->get_url_base_actual();
		$url_recibir = toba::vinculador()->get_url(null, null, array('accion' => 'recibir'),array('servicio' => 'ejecutar',
														 'objetos_destino' => $destino), true);
		$url_recibir = $url_base.$url_recibir;

		echo "<applet  id='AppletFirmador'  code='ar/gob/onti/firmador/view/FirmaApplet' scriptable='true''
				archive='$url_jar' width='{$this->_ancho}' height='{$this->_alto}' >\n";
		if (! $this->_multiple) {
			echo "<param  name='URL_DESCARGA'	 value='$url_enviar' />\n";
		} else {
			echo "<param  name='MULTIPLE'	 value='true' />\n";
		}
		echo "<param  name='URL_SUBIR'	value='$url_recibir' />
			  <param  name='MOTIVO'  value='{$this->_motivo_firma}' />
			  <param  name='CODIGO'  value='$sesion' />
			  <param  name='PREGUNTAS' value='{ \"preguntasRespuestas\": []}' />
			  <param  name='COOKIE' value='$cookie' />
			  <param name='classloader_cache' value='false' />
			  <param name='codebase_lookup' value='false' />
			  <param name='TOKID'  value='".apex_sesion_csrt."'/>
			  <param name='TOKVAL'  value='$valor_cross_token'/>
		</applet>
		";
		if ($this->_mostrar_pdf) {
			$this->_url_pdf_embebido = $this->get_url_enviar_pdf(false);
			$this->_url_pdf_embebido .= '&codigo='.$sesion;
			if ($this->_multiple) {
				$texto_alternativo = 'Haga click en los documentos para visualizarlos.';
			} else {
				$texto_alternativo = "Parece que no tiene Adobe Reader o soporte PDF en este navegador.</br>Para configurar correctamente instale Adobe Reader y siga <a href='http://helpx.adobe.com/acrobat/using/display-pdf-browser-acrobat-xi.html'>estas instrucciones</a>.";
			}
			echo "<div id='pdf' style='display: none; height: {$this->_pdf_altura}; text-align: center'><div style='margin-top: 40px; color: gray'>$texto_alternativo</div></div>";
		}
	}
	
	/**
	 * 
	 * @param boolean $usa_url_encode El Applet necesita que la URL este encodeada (por el infame caracter || que usa toba), mientras que JS no lo necesita
	 * @return string
	 */
	function get_url_enviar_pdf($usa_url_encode)
	{
		$destino = array($this->_id);
		$url_base = $this->get_url_base_actual();
		$url_enviar = toba::vinculador()->get_url(null, null, array('accion' => 'enviar'),array('servicio' => 'ejecutar',
														 'objetos_destino' => $destino), $usa_url_encode);
		$url_enviar = $url_base.$url_enviar;
		return $url_enviar;
	}

	function get_url_base_actual() 
	{
		$url = toba_http::get_url_actual();
		return $url;
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

	 function set_multiple($multiple)
	 {
		 $this->_multiple = $multiple;
	 }

	 function set_dimension($ancho, $alto)
	 {
		 $this->_ancho = $ancho;
		 $this->_alto = $alto;
	 }

	 function set_mostrar_pdf($mostrar) 
	 {
		 $this->_mostrar_pdf = $mostrar;
	 }

	 function set_alto_pdf($alto)
	 {
		 $this->_pdf_altura = $alto;
	 }
	 
	 /**
	 * Servicio que se ejecuta cuando el applet busca/envia el PDF
	 * @param <type> $parametros
	 * @ignore
	 */
	function servicio__ejecutar($parametros = null)
	{
		toba::memoria()->desactivar_reciclado();
		$accion = toba::memoria()->get_parametro('accion');
		switch ($accion) {
			case 'enviar': 
				$this->accion_enviar($parametros);
				break;
			case 'recibir':
				$this->accion_recibir($parametros);
				break;
			default:
				return;
		}
	}
	
	protected function accion_enviar($parametros)
	{
		$codigo = toba::memoria()->get_parametro('codigo');
		if (! isset($codigo) || is_null($codigo)) {
			header('HTTP/1.1 500 Internal Server Error');
			throw new toba_error_seguridad("Falta indicar el codigo");
		}
		if ($this->_memoria['token']  != $codigo) {
			header('HTTP/1.1 500 Internal Server Error');
			throw new toba_error_seguridad("Codigo invalido");   
		}	
		$numero_documento = null;
		if ($this->_multiple) {
			$test_val = toba::memoria()->get_parametro('id');
			if (! isset($test_val)) {
				header('HTTP/1.1 500 Internal Server Error');
				throw new toba_error_seguridad("Falta indicar el ID del documento a enviar");   
			}
			$numero_documento = (int) toba::memoria()->get_parametro('id');
		}
		//Reportar evento al padre
		$pdf = $this->reportar_evento_interno('enviar_pdf', $this->_memoria['token'], $numero_documento);
		if (isset($pdf) && $pdf !== apex_ei_evt_sin_rpta) {
			//Enviar PDF
			header("Cache-Control: private");
			header("Content-type: application/pdf");
			header("Pragma: no-cache");
			header("Expires: 0");				
			echo $pdf;
		} else {
			toba::logger()->error("toba_ei_fimar: No se atrapo el evento enviar_pdf!");
		}			
		return;
	}

	protected function accion_recibir($parametros) 
	{
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
			throw new toba_error_seguridad("Error: ".$_FILES["md5_fileSigned"]["error"]);
		}	
		$numero_documento = null;
		if ($this->_multiple) {
			if (! isset($_POST['id'])) {
				header('HTTP/1.1 500 Internal Server Error');
				throw new toba_error_seguridad("Falta indicar el ID del documento a recibir");   
			}
			$numero_documento = (int) $_POST['id'];
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
			$this->reportar_evento_interno('recibir_pdf_firmado', $destino, $this->_memoria['token'], $numero_documento);			
		} catch (Exception $e) {
			error_log("Error al atender el evento recibir_pdf_firmado");
			header('HTTP/1.1 500 Internal Server Error');				
			throw $e;
		}
		if (file_exists($destino)) {
			unlink($destino);
		}
		return;
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

		echo $identado."window.{$this->objeto_js} = new ei_firma($id, 
													'{$this->_submit}', 
													".($this->_multiple ? 'true' : 'false').");\n";

		echo "
			function appletLoaded() {
				{$this->objeto_js}.applet_cargado();
			}
			function firmaOk() {
				{$this->objeto_js}.firma_ok();
			}
			if (! {$this->objeto_js}._multiple) {
				window.onload = function () {
					{$this->objeto_js}.ver_pdf_inline('{$this->_url_pdf_embebido}');
				};
			}			
		";
	}
	
	/**
	 * @ignore
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_firma';
		$consumo[] = 'utilidades/pdfobject.min';
		return $consumo;
	}

}
?>
