<?php 
//--------------------------------------------------------------------
class ci_efs_prueba extends objeto_ci
{
	protected $parametros = array();

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
			'estado' => 'estado por defecto',
			'solo_lectura' => 1
		);
		return $datos;
	}
	
	function evt__carga__carga()
	{
		$datos = array(
			'mecanismo' => 'php',
			'estatico' => 1,
			'include' => 'algun/path/archivo.php',
			'clase' => 'nombre_clase',
			'dao' => 'Método!!',
			'sql' => "SELECT *\nFROM\n ALGO",
			'fuente' => 'instancia',
			'clave' => 'columna_clave',
			'valor' => 'columna_desc',
			'lista' => 'a/A, b/B, c/C',
			'dependencias' => array('a','b','c'),
			'cascada_relajada' => 1
		);
		return $datos;		
	}
	
	function evt__editable__carga()
	{
		$datos = array(
			'tamano' => 100,
			'maximo' => 255,
			'mascara' => '###.###,00',
			'unidad' => 'peras',
			'rango' => '[0..100),Este es el mensaje de error'
		);	
		return $datos;
	}
	
	function evt__textarea__carga()
	{
		$datos = array(
			'maximo' => 100,
			'wrap' => 'virtual',
			'resaltar' => 1,
		);
		return $datos;
	}
	
	function evt__popup__carga()
	{
		$datos = array(
			'item_proyecto' => 'admin',
			'item_carpeta' => '/admin/utilidades',
			'item_id' => '1242',
			'ventana' => 'widht:300,height:100',
			'editable' => 1
		);
		
		return $datos;
	}
	
	//----
	function evt__efs__modificacion($todos)
	{
		$datos = array('estado' => $todos['estado'], 'solo_lectura' => $todos['solo_lectura']);
		$this->parametros = array_merge($this->parametros, $datos);
	}
	
	function evt__carga__modificacion($datos)
	{
		$mecanismos = array( 'php' => 'dao', 'sql' => 'sql', 'lista' => 'lista');
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
	
	function evt__editable__modificacion($datos)
	{
		$this->parametros = array_merge($this->parametros, $datos);
	}
	
	function evt__textarea__modificacion($datos)
	{
		$this->parametros = array_merge($this->parametros, $datos);
	}

	function evt__popup__modificacion($datos)
	{
		$this->parametros = array_merge($this->parametros, $datos);
	}
	
	function evt__checkbox__modificacion($datos)
	{
		$this->parametros = array_merge($this->parametros, $datos);
	}
	
	function evt__pre_cargar_datos_dependencias()
	{
		ei_arbol($this->parametros);
		$salida = array();
		foreach ($this->parametros as $clave => $valor) {
			if (is_array($valor)) {
				$valor = implode(', ', $valor);	
			}
			
			
		}
	}	
	
}

?>