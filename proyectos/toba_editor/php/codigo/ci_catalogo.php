<?php
class ci_catalogo extends toba_ci
{
	protected $s__filtro;
	protected $s__datos;
	protected $filtrar = false;

	function conf__pant_inicial()
	{
		if (! isset($this->s__filtro)) {
			$this->pantalla()->eliminar_dep('cuadro');
		}
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $form)
	{
		if (isset($this->s__filtro)) {
			$form->set_datos($this->s__filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->filtrar = true;
	}

	function evt__filtro__cancelar()
	{
		if (isset($this->s__filtro)) {
			unset($this->s__filtro);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (! isset($this->s__datos) || $this->filtrar) {
			$carpeta = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . '/php';
			$carpeta = toba_manejador_archivos::path_a_plataforma($carpeta);
			$extra = '';
			if (isset($this->s__filtro['nombre'])) {
				$extra = "{$this->s__filtro['nombre']}.*";
			}
			$archivos = toba_manejador_archivos::get_archivos_directorio($carpeta, "/$extra\\.php$/", true);
					$modelo = toba_editor::get_modelo_proyecto();
					$estandar = $modelo->get_estandar_convenciones();

			$datos = array();
			foreach ($archivos as $archivo) {
				$path_relativo = substr($archivo, strlen($carpeta) + 1);
				$nombre = basename($archivo);
				if (strlen($nombre) > 30) {
					$nombre = substr($nombre, 0, 30).'...';
				}
				$info = array('archivo' => $nombre, 'path' => $path_relativo);
				if (isset($this->s__filtro['convenciones']) && $this->s__filtro['convenciones']) {
					$errores = $estandar->validar(array($archivo));
					$info['errores'] = $errores['totals']['errors'];
					$info['warnings'] = $errores['totals']['warnings'];

				}
				$datos[] = $info;
			}
			$this->s__datos = rs_ordenar_por_columna($datos, 'archivo');
		}
		if (isset($this->s__filtro['convenciones']) && $this->s__filtro['convenciones']) {
			$columnas = array();
			$columnas[0]['clave'] = 'errores';
			$columnas[0]['titulo'] = toba_recurso::imagen_toba('error.gif', true);
			$columnas[0]['estilo'] = 'col-num-p1';
			$columnas[1]['clave'] = 'warnings';
			$columnas[1]['titulo'] = toba_recurso::imagen_toba('warning.gif', true);
			$columnas[1]['estilo'] = 'col-num-p1';
			$cuadro->agregar_columnas($columnas);
		}
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos($this->s__datos);
	}


}
?>
