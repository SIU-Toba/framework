<?php
require_once('3ros/PHP_CodeSniffer/CodeSniffer.php');

class toba_estandar_convenciones
{
	protected $estandar;
	protected $sniffer;
	protected $warnings;

	function __construct($estandar='Toba', $warnings=true)
	{
		$this->warnings = $warnings;
		if ($estandar == 'Toba') {
			$this->estandar = toba_dir().'/php/convenciones/Toba';
			$this->estandar = toba_manejador_archivos::path_a_plataforma($this->estandar);
		} else {
			$this->estandar = $estandar;
		}
		$this->sniffer = new PHP_CodeSniffer(0);
		if (!$this->sniffer->isInstalledStandard($this->estandar)) {
			throw new toba_error("El estandar '{$this->estandar}' no esta instalado");
		}
	}

	function validar($paths)
	{
		@$this->sniffer->process($paths, $this->estandar, array(), false);
		return $this->sniffer->prepareErrorReport();
	}

	function get_consola_reporte()
	{
		ob_start();
		$this->sniffer->printErrorReport($this->warnings);
		$contenido = ob_get_contents();
		ob_get_clean();
		return $contenido;
	}

	function get_consola_sumario($path_relativo=null)
	{
		ob_start();
		$this->sniffer->printErrorReportSummary($this->warnings, $path_relativo);
		$contenido = ob_get_contents();
		ob_get_clean();
		return $contenido;
	}

	/**
	 * Cambia el id de una convencion por su URL 
	 */
	function parsear_mensaje($mensaje)
	{
		$resultado = array();
		$expresion = '/^\[(.*)\](.*)/';
		if (preg_match_all($expresion, $mensaje, $resultado)) {
			$pagina = $resultado[1][0];
			$despues = $resultado[2][0];
			$url = get_url_desarrollos().'/trac/siu/wiki/Convenciones/Codigo/'.$pagina;
			$mensaje = "$despues <br><br><a href=$url target=wiki>Ver detalles de la convención</a>";
			return $mensaje;
		} else {
			return $mensaje;
		}
	}
	
	
}
?>
