<?php 
class ci_editor extends toba_ci
{
	protected $s__proyecto;
	protected $s__dimension;
	protected $s__elementos = array();
	protected $datos_dimension;
	
	function datos()
	{
		return $this->controlador()->dep('datos');	
	}
	
	function set_proyecto($proyecto)
	{
		$this->s__proyecto = $proyecto;
	}
	
	//-----------------------------------------------------------------------------------
	//---- Pantalla 1 -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- perfil -----------------------------------------------------------------------

	function evt__perfil__modificacion($datos)
	{
		$datos['proyecto'] = $this->s__proyecto;
		$this->datos()->tabla('perfil')->set($datos);
	}

	function conf__perfil(toba_ei_formulario $form)
	{
		$datos = $this->datos()->tabla('perfil')->get();
		$form->set_datos($datos);
	}
	
	//---- dimensiones ------------------------------------------------------------------

	function evt__dimensiones__seleccion($seleccion)
	{
		$this->s__dimension = $seleccion['dimension'];
		$this->set_pantalla('pant_dimensiones');
	}

	function conf__dimensiones(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_dimensiones($this->s__proyecto);
		foreach (array_keys($datos) as $id ) {
			$elementos = $this->datos()->tabla('dims')->get_id_fila_condicion(array('dimension'=> $datos[$id]['dimension']));
			switch (count($elementos)) {
				case 0:
					$txt = 'No hay restricciones.';
					break;
				case 1:
					$txt = '1 elemento seleccionado.';
					break;
				default:
					$txt = count($elementos) . ' elementos seleccionados.';
			}
			$datos[$id]['estado'] = $txt;
		}
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Pantalla 2 -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__aceptar()
	{
		$this->set_pantalla('pant_perfil');
	}

	function evt__volver()
	{
		$this->set_pantalla('pant_perfil');
	}

	function conf__pant_dimensiones($pantalla)
	{
		$this->datos_dimension = toba_info_editores::get_datos_dimension($this->s__proyecto, $this->s__dimension);
		//Saco los botones del CI de afuera
		$pantalla_externa = $this->controlador->pantalla();
		$pantalla_externa->eliminar_evento('guardar');
		$pantalla_externa->eliminar_evento('cancelar');
		$pantalla_externa->eliminar_evento('eliminar');
		$perfil = $this->datos()->tabla('perfil')->get();
		$txt = "Perfil de datos: <strong>{$perfil['nombre']}</strong>.<br>";
		$txt .= "Dimensión: <strong>{$this->datos_dimension['nombre']}</strong>.<br>";
		$txt .= 'Seleccione los elementos que desea habilitar para el perfil.';
		$this->pantalla()->set_descripcion($txt);
	}

	//---- FORM elementos --------------------------------------------------------------------

	function conf__elementos(toba_ei_formulario_ml $form_ml)
	{
		//Lista completa de elementos de la dimension
		$datos = $this->get_elementos_dimension();
		//Tomo los elementos de la dimension seleccionada
		$datos_habilitados = $this->datos()->tabla('dims')->get_filas(array('dimension'=>$this->s__dimension));
		//Los guardo para comparar
		$this->s__elementos = array();
		//ei_arbol($datos_habilitados,'contenido Dt');
		foreach ($datos_habilitados as $elemento) {
			$this->s__elementos[$elemento['clave']] = $elemento[apex_datos_clave_fila];
		}
		foreach (array_keys($datos) as $id) {
			if (isset($this->s__elementos[$datos[$id]['clave']])) {
				$datos[$id]['habilitar'] = 1;
			} else {
				$datos[$id]['habilitar'] = 0;
			}
		}
		$form_ml->set_datos($datos);
	}

	function evt__elementos__modificacion($datos)
	{
		foreach (array_keys($datos) as $id) {
			if (trim($datos[$id]['clave']) == '') {						//Atrapa el caso de una clave vacia.
					continue;				
			}
			if (isset($this->s__elementos[$datos[$id]['clave']])) {
				if (!($datos[$id]['habilitar'] == 1)) {
					$this->datos()->tabla('dims')->eliminar_fila($this->s__elementos[$datos[$id]['clave']]);
				}
			} else {
				if (($datos[$id]['habilitar'] == 1)) {
					$datos[$id]['dimension'] = $this->s__dimension;
					$this->datos()->tabla('dims')->nueva_fila($datos[$id]);
				}
			}
		}
	}

	//---- Buscar data de la dimension --------------------------------------------------------------------
	
	function get_elementos_dimension()
	{
		$id = explode(',', $this->datos_dimension['col_id']);
		$desc = explode(',', $this->datos_dimension['col_desc']);
		$sql = 'SELECT ' . implode(" || '".toba_perfil_datos::separador_multicol_db."' || ", $id) . ' as clave, ' 
						. implode("  || ' - ' ||  ", $desc) . " as descripcion
				FROM {$this->datos_dimension['tabla']}
				ORDER BY descripcion";
		toba::logger()->debug($sql);
		$datos = $this->get_db($this->datos_dimension['fuente_datos'])->consultar($sql);
		return $datos;
	}

	function get_db($id)
	{		
		$fuente_datos = $this->get_fuente_proyecto_alterno($id, $this->s__proyecto);
		return  $fuente_datos->get_db();		
	}
	
	function get_fuente_proyecto_alterno($id, $proyecto)
	{
		$parametros = toba_proyecto_db::get_info_fuente_datos($proyecto, $id);
		if (isset($parametros['subclase_archivo'])) {
			if (toba::proyecto()->get_id() != $proyecto) {
				//Si la fuente esta extendida, puede necesitar otros archivos del proyecto, agregar el include path				
				$path_proyecto = toba::instancia()->get_path_proyecto($proyecto) . '/php';
				agregar_dir_include_path($path_proyecto);
			}				
			$archivo = $parametros['subclase_archivo'];
		} else {
			$archivo = 'nucleo/lib/toba_fuente_datos.php';
		}
		if (isset($parametros['subclase_nombre'])) {
			$clase = $parametros['subclase_nombre'];
		} else {
			$clase = "toba_fuente_datos";
		}
		require_once($archivo);
		return new $clase($parametros);		
	}
}
?>