<?php
class skins_ci extends toba_ci
{
	protected $proyecto;
	protected $colores = array('fondo', 'frente', 'borde');	
	protected $importado = false;
	protected $template;
	
	function ini()
	{
		$this->template = toba_dir().'/php/modelo/var/toba.css';
		if (toba_editor::acceso_recursivo()) {
			$this->proyecto = 'toba';
		} else {
			$this->proyecto = toba_editor::get_proyecto_cargado();
		}
	}
	
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->dep('datos')->get_listado($this->proyecto));
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->hay_cursor()) {
			$form->set_datos($this->dep('datos')->get());
		} 
		if (! $this->dep('datos')->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$datos['proyecto'] = $this->proyecto;
		$this->dep('datos')->set($datos);
	}

	//---- Importar -------------------------------------------------------------------	

	function conf__form_importar(toba_ei_formulario $form)
	{
		$form->colapsar();
	}
	
	function evt__form_importar__importar($datos)
	{
		if (isset($datos['estilo'])) {
			$sql = 'SELECT paleta FROM apex_estilo
					WHERE estilo='.quote($datos['estilo']).' AND proyecto='. quote($datos['proyecto']);
			$datos = toba::db()->consultar_fila($sql);
			$this->dep('datos')->set($datos);	
			$this->importado = true;		
		}
	}	


	function evt__form_paleta__generar($datos)
	{
		$this->evt__form_paleta__modificacion($datos);
		$datos = $this->dep('datos')->get();
		$paleta = unserialize($datos['paleta']);
		$template = file_get_contents($this->template);
		if ($datos['proyecto'] == 'toba') {
			$dir_salida = toba_dir();
		} else {
			$dir_salida = toba::instancia()->get_path_proyecto($datos['proyecto']);
		}
		$dir_salida = $dir_salida.'/www/skins/'.$datos['estilo'];
		if (! file_exists($dir_salida)) {
			toba_manejador_archivos::crear_arbol_directorios($dir_salida);
		}
		$archivo_salida = $dir_salida.'/toba.css';
		foreach ($paleta as $clave => $valor) {
			$template = str_replace('{$'.$clave.'}', $valor, $template);
		}
		file_put_contents($archivo_salida, $template);
		$this->pantalla()->set_descripcion('Plantilla css generada. Recuerde generar una imagen <b>barra-sup.gif</b> (o copiarlo de un skin existente) '.
											"y guardarla en <b>$dir_salida</b>", 'warning');
		$this->dep('datos')->sincronizar();		
	}	
		
	
	//---- ML -------------------------------------------------------------------

	
	function conf__form_paleta(toba_ei_formulario_ml $form)
	{
		//-- Busca patrones existentes en el template
		$template = file_get_contents($this->template);		
		$inicio = '{$';
		$fin = '}';
		$patron = '/'.preg_quote($inicio).'(.*)'.$fin.'/';
		preg_match_all($patron, $template, $salida);
		$existentes = $salida[1];
		
		$datos = $this->dep('datos')->get();
		if (isset($datos['paleta'])) {
			$arreglo = unserialize($datos['paleta']);
			foreach ($existentes as $clave) {
				if (! isset($arreglo[$clave])) {
					$arreglo[$clave] = '';
				}
			}
			$filas = array();
			foreach ($arreglo as $clave => $valor) {
				$pos = strpos($clave, '_');
				$tipo = substr($clave, 0, $pos);
				$clase = substr($clave, $pos + 1);
				$filas[$clase][$tipo] = $valor;
			}
			$paletas = array();
			foreach ($filas as $clase => $valores) {
				$valores['clave'] = $clase;
				$valores['nombre'] = ucfirst(str_replace('_', ' ', $clase));
				foreach ($this->colores as $color) {
					if (! isset($valores[$color])) {
						$valores[$color] = apex_ef_no_seteado;
					}
				}
				$paletas[] = $valores;
			}
			$paletas = rs_ordenar_por_columna($paletas, 'nombre');
			$form->set_datos($paletas);
		}
	}

	function evt__form_paleta__modificacion($datos)
	{
		if ($this->importado) {
			return;
		}
		$paleta = array();
		foreach ($datos as $tipo) {
			foreach ($this->colores as $color) {
				if (isset($tipo[$color]) && $tipo[$color] != apex_ef_no_seteado) {
					$paleta[$color.'_'.$tipo['clave']] = $tipo[$color];
				}
			}
		}
		$this->dep('datos')->set(array('paleta' => serialize($paleta)));
	}


	
	//---- EVENTOS CI -------------------------------------------------------------------
	
	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}	

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_filas();
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}
}

?>