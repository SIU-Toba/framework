<?php 
require_once('asistentes/ci_asistente_base.php');

class ci_abms_principal extends ci_asistente_base
{
	protected $cambio_tabla = false;
	
	function posee_informacion_completa()
	{
		$mensajes = array();
		if( parent::posee_informacion_completa() ) {
			$base = $this->dep('datos')->tabla('base')->get();	
			if( !isset($base['tabla'])) {
				return false;	
			}
			if(!isset($base['cuadro_carga_origen'])) {
				$mensajes[] = 'Formulario: Falta indicar el origen de la carga del cuadro';
			}			
			$filas = $this->dep('datos')->tabla('filas')->get_filas();	
			foreach($filas as $fila) {
				if( $fila['asistente_tipo_dato']== toba_catalogo_asistentes::tipo_dato_referencia()) {
					if(!isset($fila['ef_carga_origen'])) {
						$mensajes[] = 'Formulario: Falta indicar el origen de la carga del campo "'.$fila['columna'].'"';
					}
				}
			}
		}
		return empty($mensajes) ? true : $mensajes;
	}
	
	function autocompletar_informacion($refrescar_todo=false)
	{
		$tabla = $this->dep('datos')->tabla('filas');
		if($refrescar_todo) {
			$tabla->eliminar_filas();	
		}
		//--- Recorre las columnas y las rellenas con los nuevos datos
		$actuales =  $tabla->get_filas(null, true);
		$nuevas = toba_catalogo_asistentes::get_lista_filas_tabla($this->get_nombre_tabla_actual(), $this->get_fuente_actual());
		//-- Borra las filas viejas que ya no estan en la tabla
		foreach ($actuales as $id => $actual) {
			$existe = false;
			foreach ($nuevas as $nueva) {
				if ($nueva['columna'] == $actual['columna']) {
					$existe = true;
					break;	
				}
			}
			if (!$existe) {
				$tabla->eliminar_fila($id);
			}
		}
		//-- Agrega las filas nuevas
		foreach ($nuevas as $nueva) {
			$existe = false;
			foreach ($actuales as $id => $actual) {
				if ($nueva['columna'] == $actual['columna']) {
					$existe = true;
					break;	
				}
			}
			if (!$existe) {
				$tabla->nueva_fila($nueva);
			}
		}
		$this->autocompletar_carga_cuadro();
	}
	
	function autocompletar_carga_cuadro()
	{
		$db = toba::db($this->get_fuente_actual(), toba_editor::get_proyecto_cargado());
		$datos = array();
		list($sql, $id) = $db->get_sql_carga_tabla($this->get_nombre_tabla_actual());
		$datos['cuadro_carga_sql'] = $sql;
		$datos['cuadro_id'] = $id;
		$datos['cuadro_carga_origen'] = 'datos_tabla';
		$this->dep('datos')->tabla('base')->set($datos);		
	}
	
	/**
	 * Asume que el dt 'filas' tiene un cursor seteado en la fila actual
	 */
	function autocompletar_carga_combo($columna)
	{
		$nuevas = toba_catalogo_asistentes::get_lista_filas_tabla($this->get_nombre_tabla_actual(), $this->get_fuente_actual());
		$datos = array();
		//-- Busca la fila a actualizar
		foreach ($nuevas as $nueva) {
			if ($nueva['columna'] == $columna) {
				$datos['ef_carga_col_clave'] = $nueva['ef_carga_col_clave'];
				$datos['ef_carga_col_desc'] = $nueva['ef_carga_col_desc'];
				$datos['ef_carga_tabla'] = $nueva['ef_carga_tabla'];
				$datos['ef_carga_sql'] = $nueva['ef_carga_sql'];
				break;
			}
		}
		$this->dep('datos')->tabla('filas')->set($datos);		
	}

	//---------------------------------------------------
	//---- Basico ----------------------------------------

	function get_nombre_tabla_actual()
	{
		return $this->dep('datos')->tabla('base')->get_columna('tabla');		
	}
	
	function get_fuente_actual()
	{
		return $this->dep('datos')->tabla('base')->get_columna('fuente');
	}
	
	function conf()
	{
		$con_filtro = $this->dep('datos')->tabla('base')->get_columna('gen_usa_filtro');	
		if (! $con_filtro) {
			$this->pantalla()->tab('pant_filtro')->ocultar();	
		}
		if ($this->get_nombre_tabla_actual() == '') {
			$this->pantalla()->tab('pant_cuadro')->ocultar();	
			$this->pantalla()->tab('pant_form')->ocultar();	
			$this->pantalla()->tab('pant_filtro')->ocultar();	
			$this->pantalla()->tab('pant_presentacion')->ocultar();	
		}
	}
	
	function conf__pant_basica()
	{
		if ($this->get_nombre_tabla_actual() == '') {
			$this->pantalla()->eliminar_dep('form_filas');
		}
	}

	function evt__form_basico__modificacion($datos)
	{
		if (isset($datos['fuente'])) {
			$this->dep('datos')->tabla('molde')->set(array('fuente' => $datos['fuente']));
		}
		$tabla_vieja = $this->get_nombre_tabla_actual();
		$this->dep('datos')->tabla('base')->set($datos);
		//--- Si cambio de tabla se regenera la información
		if ($tabla_vieja != $this->get_nombre_tabla_actual()) {
			$this->autocompletar_informacion();
			$this->cambio_tabla = true;
		}		
	}

	function conf__form_basico(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();		
		$datos_molde = $this->dep('datos')->tabla('molde')->get();
		if (! isset($datos_molde['fuente'])) {
			$datos['fuente'] = toba_info_editores::get_fuente_datos_defecto(toba_editor::get_proyecto_cargado());
		} else {
			$datos['fuente'] = $datos_molde['fuente'];
		}
		return $datos;
	}
	
	function conf__form_filas(toba_ei_formulario_ml $ml)
	{
		$tabla = $this->dep('datos')->tabla('filas');
		//$ml->set_ordenar_en_linea(true);
		$ml->set_proximo_id($tabla->get_proximo_id());
		$ml->set_datos( $tabla->get_filas(null, true));		
	}
	
	function evt__form_filas__modificacion($datos)
	{
		//--- Este ml cuando se cambio el combo de la tabla a usar ya no tiene datos necesarios
		if (! $this->cambio_tabla) {
			$this->dep('datos')->tabla('filas')->procesar_filas($datos);
		}
	}

	function evt__form_filas__refrescar($datos)
	{
		$this->evt__form_filas__modificacion($datos);
		$this->autocompletar_informacion();
	}
	

	//---- CUADRO - Prop. basicas ----------------------------------------
	
	function evt__form_cuadro__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_cuadro(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();
		$form->set_datos($datos);
	}
	
	function evt__form_cuadro_carga__modificacion($datos)
	{
		$datos = array_cambiar_prefijo_claves($datos, 'cuadro_', false);		
		if (!isset($datos['cuadro_carga_origen'])) {
			$datos['cuadro_carga_origen'] = 'datos_tabla';
		}
		if (!isset($datos['cuadro_carga_php_metodo']) && isset($datos['cuadro_carga_php_metodo_nuevo'])) {
			$datos['cuadro_carga_php_metodo'] = $datos['cuadro_carga_php_metodo_nuevo'];
		}
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_cuadro_carga(toba_ei_formulario $form)
	{
		$form->desactivar_efs(array('carga_col_clave', 'carga_col_desc'));
		$datos = $this->dep('datos')->tabla('base')->get();
		$datos = array_cambiar_prefijo_claves($datos, 'cuadro_', true);
		if (isset($datos['carga_php_metodo'])) {
			$datos['carga_php_metodo_nuevo'] = $datos['carga_php_metodo'];
		}		
		$form->set_datos($datos);
	}	
	
	/**
	 * Modifica los datos y regenera la SQL de carga del cuadro
	 */
	function evt__form_cuadro_carga__regenerar($datos)
	{
		$this->evt__form_cuadro_carga__modificacion($datos);
		$this->autocompletar_carga_cuadro();
	}
	
	//---- FORM ----------------------------------------
	
	function conf__pant_form()
	{
		if (! $this->dep('datos')->tabla('filas')->hay_cursor()) {
			$this->pantalla()->eliminar_dep('form_form_fila');
		}
	}
	
	function evt__cuadro_form_filas__seleccion($seleccion)
	{
		$this->dep('datos')->tabla('filas')->set_cursor($seleccion);
	}

	function conf__cuadro_form_filas(toba_ei_cuadro $cuadro)
	{
		//--Retorna sola las columnas con referencias
		$cuadro->set_datos($this->dep('datos')->tabla('filas')->get_filas(array('asistente_tipo_dato' => '1000008')));
	}	
	
	function evt__form_form_fila__modificacion($datos, $resetear=true)
	{
		$datos = array_cambiar_prefijo_claves($datos, 'ef_', false);		
		if (!isset($datos['ef_carga_php_metodo']) && isset($datos['ef_carga_php_metodo_nuevo'])) {
			$datos['ef_carga_php_metodo'] = $datos['ef_carga_php_metodo_nuevo'];
		}		
		$this->dep('datos')->tabla('filas')->set($datos);
		if ($resetear) {
			$this->dep('datos')->tabla('filas')->resetear_cursor();
		}
	}
	
	function evt__form_form_fila__regenerar($datos)
	{
		$fila = $this->dep('datos')->tabla('filas')->get();
		$this->evt__form_form_fila__modificacion($datos, false);		
		$this->autocompletar_carga_combo($fila['columna']);
	}
	
	function conf__form_form_fila(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('filas')->get();
		$datos = array_cambiar_prefijo_claves($datos, 'ef_', true);
		if (isset($datos['carga_php_metodo'])) {
			$datos['carga_php_metodo_nuevo'] = $datos['carga_php_metodo'];
		}		
		$form->set_datos($datos);
	}
	
	
	//---- FILTRO ----------------------------------------

	function evt__pant_filtro__entrada()
	{
		$datos = $this->dep('datos')->tabla('filas')->get_filas(array('en_filtro' => 1));
		if (empty($datos)) {
			throw new toba_error('Para poder utilizar el filtro es necesario definir que columnas se muestran en el mismo');
		}
	}
	
	function evt__form_filtro__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}	
	
	function conf__form_filtro(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('base')->get();
		$form->set_datos($datos);	
	}
	
	function evt__ml_filtro_filas__modificacion($datos)
	{
		$this->dep('datos')->tabla('filas')->procesar_filas($datos);
	}
	
	function conf__ml_filtro_filas(toba_ei_formulario_ml $ml)
	{
		$datos = $this->dep('datos')->tabla('filas')->get_filas(array('en_filtro' => 1));
		$ml->set_datos($datos);
	}
	
	
}

?>