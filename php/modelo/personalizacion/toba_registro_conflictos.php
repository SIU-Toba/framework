<?php
/**
 * Esta clase es un registro de los conflictos de una personalización
 */
class toba_registro_conflictos
{
    protected $conflictos = array();

	// Una lista de todos los conflictos sin metadata adicional
	protected $raw_conflictos = array();

	/**
	 * @param toba_registro_conflicto $conflicto
	 * @param $path el path del archivo donde se encuentra el registro conflictivo
	 */
	function add_conflicto($conflicto, $path)
	{
		$tipo = $conflicto->get_tipo();
		$archivo = basename($path);
		$this->conflictos[$archivo][$tipo][] = $conflicto;
		$this->raw_conflictos[] = $conflicto;
	}

	function get_conflictos()
	{
		return $this->raw_conflictos;
	}

	protected function get_error_count($tipo)
	{
		$error_count = 0;

		foreach ($this->conflictos as $por_archivo) {
			if (isset($por_archivo[$tipo])) {
				$error_count += count($por_archivo[$tipo]);	
			}
		}

		return $error_count;
	}

	protected function generar_log($path)
	{
		$handle = fopen($path, 'w');

		$fatal		= toba_registro_conflicto::fatal;
		$warning	= toba_registro_conflicto::warning;

		foreach (array_keys($this->conflictos) as $nombre) {
			fwrite($handle, "Conflictos del archivo $nombre\n");
			if (isset($this->conflictos[$nombre][$fatal])) {
				fwrite($handle, "\tErrores fatales\n");
				foreach ($this->conflictos[$nombre][$fatal] as $conflicto) {
					$desc = $conflicto->get_descripcion();
					fwrite($handle, "\t\t$desc\n");
				}
			}

			if (isset($this->conflictos[$nombre][$warning])) {
				fwrite($handle, "\tErrores recuperables\n");
				foreach ($this->conflictos[$nombre][$warning] as $conflicto) {
					$desc = $conflicto->get_descripcion();
					fwrite($handle, "\t\t$desc\n");
				}
			}
		}

		fclose($handle);
	}

	function get_reporte($path = null)
	{
		$log = '';
		if (!is_null($path)) {
			$this->generar_log($path);
			$log = "\nConsulte el log para más detalles en $path";
		}

		$total_f = $this->get_error_count(toba_registro_conflicto::fatal);
		$total_w = $this->get_error_count(toba_registro_conflicto::warning);

		$total_f_mensaje = $this->get_mensaje_irresoluble($total_f);
		$total_w_mensaje = $this->get_mensaje_soluble($total_w);

		if ($total_f + $total_w > 0) {
			$reporte = "Hubo un total de $total_f_mensaje y $total_w_mensaje. $log";
		} else {
			$reporte = "No se detectó ningún conflicto.";
		}

		return $reporte;
	}

	protected function get_mensaje_irresoluble($total)
	{
		if ($total == 0) {
			return "(0) errores irresolubles";
		} elseif ($total == 1) {
			return "(1) error irresoluble";
		} else {
			return "($total) errores irresolubles";
		}
	}

	protected function get_mensaje_soluble($total)
	{
		if ($total == 0) {
			return "(0) errores resolubles";
		} elseif ($total == 1) {
			return "(1) error resoluble";
		} else {
			return "($total) errores resolubles";
		}
	}

}
?>
