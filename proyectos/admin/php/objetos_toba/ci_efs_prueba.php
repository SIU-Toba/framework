<?php 
//--------------------------------------------------------------------
class ci_efs_prueba extends objeto_ci
{
	protected $parametros = array();
	protected $modificado = false;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos';
		return $propiedades;
	}

	function get_mecanismos_carga()
	{
		$tipos = array(
			array('php', 'Método PHP'),
			array('sql', 'Consulta SQL'),
		);
		//--- Si es un editable, sacar la lista
		if (strpos($this->get_tipo_ef(), 'ef_editable') === false) {
			$tipos[] = array('lista', 'Lista de Valores');
		}
		return $tipos;
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- carga -------------------------------------------------------

	function get_tipo_ef()
	{
		return 'ef';
	}
	
	
/*	function evt__popup__carga()
	{
		$datos = array();
		$datos['item_proyecto'] = editor::get_proyecto_cargado();
		return $datos;		
	}*/
	
	
	///--------------

	function evt__efs__carga()
	{
		$datos = array(
			'columnas' => 'columnas',
			'estado_defecto' => 'Defecto!',
			'solo_lectura' => 1
		);	
		return $datos;
	}
	
	function evt__param_carga__carga()
	{
		$datos = array(
			'mecanismo' => 'php',
			'estatico' => 1,
			'carga_include' => 'algun/path/archivo.php',
			'carga_clase' => 'nombre_clase',
			'carga_metodo' => 'Método!!',
			'carga_sql' => "SELECT *\nFROM\n ALGO",
			'carga_fuente' => 'instancia',
			'carga_col_clave' => 'columna_clave',
			'carga_col_desc' => 'columna_desc',
			'carga_lista' => 'a/A, b/B, c/C',
			'carga_maestros' => array('a','b','c'),
			'carga_cascada_relaj' => 1,
			'carga_no_seteado' => '---Valor No seteado!!--'
		);
		return $datos;
	}
	
	function evt__param_varios__carga()
	{
		$datos = array(
			'edit_columnas' => 12,
			'edit_tamano' => 100,
			'edit_maximo' => 255,
			'edit_mascara' => '###.###,00',
			'edit_unidad' => 'peras',
			'edit_rango' => '[0..100),Este es el mensaje de error',
			'edit_wrap' => 'virtual',
			'edit_ajustable' => 1,
			'edit_resaltar' => 1,
			'popup_proyecto' => 'admin',
			'popup_carpeta' => '/admin/utilidades',
			'popup_item' => '1242',
			'popup_ventana' => 'widht:300,height:100',
			'popup_editable' => 1,
			'editor_ancho' => '100px',
			'editor_alto' => '150px',
			'editor_botonera' => 'toba',
			'selec_cant_minima' => '3',
			'selec_cant_maxima' => 5,
			'selec_utilidades' => 1,
			'selec_tamano' => 4
		);
		return $datos;
	}
	
	function evt__param_carga__modificacion($datos)
	{
		$this->modificado = true;		
		$mecanismos = array( 'php' => 'carga_metodo', 'sql' => 'carga_sql', 'lista' => 'carga_lista');
		$actual = $datos['mecanismo'];
		foreach ($mecanismos as $id_mec => $valor_mec) {
			if ($id_mec != $actual && isset($datos[$valor_mec])) {
				unset($datos[$valor_mec]);
			}
		}
		if ($datos['mecanismo'] != null) {
			unset($datos['mecanismo']);
			unset($datos['estatico']);
		} else {
			$datos = array();	
		}
		$this->parametros = array_merge($this->parametros, $datos);	}
	
	function evt__param_varios__modificacion($datos)
	{
		$this->modificado = true;
		$this->parametros = array_merge($this->parametros, $datos);
	}
	
	function evt__post_recuperar_interaccion()
	{
		if ($this->modificado) {
			ei_arbol($this->parametros);	
		}
		$salida = array();
		foreach ($this->parametros as $clave => $valor) {
			if (is_array($valor)) {
				$valor = implode(', ', $valor);	
			}
		}
	}	
	
}

?>