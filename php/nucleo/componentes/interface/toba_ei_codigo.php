<?php
/**
 * Genera un editor de código
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei ei
 * @wiki Referencia/Objetos/ei_codigo
*/ 
 
class toba_ei_codigo extends toba_ei
{
	protected $_contenido;
	protected $_ancho;
	protected $_alto;
	protected $_id_post_codigo;

	final function __construct($id)
	{	
		parent::__construct($id);
		$this->_ancho = get_var($this->_info_codigo['ancho'], '600px');
		$this->_alto = get_var($this->_info_codigo['alto'], '300px');
		$this->_id_post_codigo = $this->_submit.'_params';
	}

	//-------------------------------------------------------------------------------
	//---- UTIL PARA LA CONSTRUCCIÓN DEL COMPONENTE ---------------------------------
	//-------------------------------------------------------------------------------

	protected function get_datos()
	{
		return get_var($this->_contenido, '');
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
		if (isset($datos)) {
			$this->_contenido = $datos;
		}
	}

	function generar_html()
	{
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI CODIGO (	".	$this->_id[1] ." )	***********	-->\n\n";
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_id_post_codigo, ''); // Aca viaja el codigo
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-form-barra-sup");

		echo "<textarea id='code'>";
		echo $this->get_datos();
		echo "</textarea>";
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
		$dim = toba_js::arreglo($this->get_dimensiones(), false);

		echo $identado."window.{$this->objeto_js} = new ei_codigo($id, $dim, '{$this->_submit}', '{$this->_id_post_codigo}');\n";
	}

	/**
	 * @ignore
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'codemirror/codemirror';
		$consumo[] = 'componentes/ei_codigo';
		return $consumo;
	}

}

?>
