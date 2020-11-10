<?php
/**
 * Esta clase es un registro de los conflictos de una personalizaci�n
 * @package Centrales
 * @subpackage Personalizacion
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
			if (false === fwrite($handle, "Conflictos del archivo $nombre\n")) {
                            throw new Exception('No se pueden agregar los conflictos del archivo ' . $nombre . ' al log');
                        }
			if (isset($this->conflictos[$nombre][$fatal])) {
                            $this->generar_linea_log($handle, $this->conflictos[$nombre][$fatal], "\tErrores fatales\n");
			}
			if (isset($this->conflictos[$nombre][$warning])) {
                            $this->generar_linea_log($handle, $this->conflictos[$nombre][$warning], "\tErrores recuperables\n");
			}
		}

		fclose($handle);
	}

        protected function generar_linea_log($handle, $conflictos, $descripcion_grupo)
        {
            if (false === fwrite($handle, $descripcion_grupo)) {
                throw new Exception('No se pudo escribir el grupo '. $descripcion_grupo . ' al archivo de log');
            }
            foreach ($conflictos as $conflicto) {
                    $desc = $conflicto->get_descripcion();
                    if (false === fwrite($handle, "\t\t$desc\n")) {
                        throw new Exception('No se pudo escribir el conflicto '. $desc . ' al archivo de log');
                    }
            }

        }

	function get_reporte($path = null)
	{
		$log = '';
		if (!is_null($path)) {
			$this->generar_log($path);
			$log = "\nConsulte el log para m�s detalles en $path";
		}

		$total_f = $this->get_error_count(toba_registro_conflicto::fatal);
		$total_w = $this->get_error_count(toba_registro_conflicto::warning);

		$total_f_mensaje = $this->get_mensaje_irresoluble($total_f);
		$total_w_mensaje = $this->get_mensaje_soluble($total_w);

		if ($total_f + $total_w > 0) {
			$reporte = "Hubo un total de $total_f_mensaje y $total_w_mensaje. $log";
		} else {
			$reporte = "No se detect� ning�n conflicto.";
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
