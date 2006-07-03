<?php 
//--------------------------------------------------------------------
class ci_efs_prueba extends objeto_ci
{
	protected $modificado = false;
	protected $tipo_ef;
	protected $mecanismos_carga = array('carga_metodo', 'carga_sql', 'carga_lista');

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'tipo_ef';
		return $propiedades;
	}
	
	function get_definicion_parametros($carga = false)
	//Recupero la informacion de los parametros de un EF puntual
	{
		$ef = $this->get_tipo_ef();
		$metodo = ($carga) ? "get_lista_parametros_carga" : "get_lista_parametros";
		$parametros = call_user_func(array($ef, $metodo));
		return $parametros;
	}
	
	
	function get_mecanismos_carga()
	{
		$param = $this->get_definicion_parametros(true);		
		$tipos = array();
		if (in_array('carga_metodo', $param)) {
			$tipos[] = array('carga_metodo', 'Método PHP');
		}
		if (in_array('carga_sql', $param)) {		
			$tipos[] = array('carga_sql', 'Consulta SQL');
		}
		if (in_array('carga_lista', $param)) {
			$tipos[] = array('carga_lista', 'Lista de Valores');
		}
		return $tipos;
	}
	
	function get_lista_ei()
	{
		$eis = array('tipo_ef');
		if (isset($this->tipo_ef['tipo'])) {
			$eis[] = 'efs';
			$param_carga = $this->get_definicion_parametros(true);			
			$param_varios = $this->get_definicion_parametros();
			if (! empty($param_varios)) {			
				$eis[] = 'param_varios';
			}
			if (! empty($param_carga)) {
				$eis[] = 'param_carga';
			}
		}
		return $eis;
	}
	
	function get_tipo_ef()
	{
		return $this->tipo_ef['tipo'];
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	
/*	function evt__popup__carga()
	{
		$datos = array();
		$datos['item_proyecto'] = editor::get_proyecto_cargado();
		return $datos;		
	}*/
	
	
	///--------------
	
	function evt__tipo_ef__carga()
	{
		if (isset($this->tipo_ef)) {
			return $this->tipo_ef;
		}
	}
	
	function evt__tipo_ef__modificacion($tipo)
	{
		$this->tipo_ef = $tipo;
	}

	function evt__efs__modificacion($datos)
	{
		$this->get_tabla()->set($datos);
	}
	
	function evt__efs__carga()
	{
		return $this->get_tabla()->get();
	}
	
	function evt__param_carga__carga()
	{
		$lista_param = $this->get_definicion_parametros(true);
		$parametros=  $this->get_tabla()->get();
		$todos = $this->dependencia('param_carga')->get_nombres_ef();
		foreach ($todos as $disponible) {
			if (! in_array($disponible, $lista_param) &&
					$disponible != 'mecanismo' &&
					$disponible != 'estatico') {
				if (isset($parametros[$disponible])) {
					unset($parametros[$disponible]);	
				}						
				$this->dependencia('param_carga')->desactivar_efs($disponible);
			}
		}
		
		//---Determina el mecanismo
		foreach ($this->mecanismos_carga as $mec) {
			if (isset($paramametros[$mec])) {
				$parametros['mecanismo'] = $mec;
				break;
			}
		}
		return $parametros;
	}
	
	function evt__param_varios__carga()
	{
		$param = $this->get_definicion_parametros();
		$todos = $this->dependencia('param_varios')->get_nombres_ef();
		$efs_a_desactivar = array();
		foreach ($todos as $disponible) {
			if (! in_array($disponible, $param) ) {
				$efs_a_desactivar[] = $disponible;
				if (isset($this->parametros[$disponible])) {
					unset($this->parametros[$disponible]);	
				}
			}
		}
		//-- Si es un popup no eliminar la carpeta (es cosmetico)
		if (! in_array('popup_item', $efs_a_desactivar)) {
			array_borrar_valor($efs_a_desactivar, 'popup_carpeta');	
		}
		$this->dependencia('param_varios')->desactivar_efs($efs_a_desactivar);
		return $this->get_tabla()->get();
	}
	
	function evt__param_carga__modificacion($datos)
	{
		$this->modificado = true;		
		$actual = $datos['mecanismo'];
		foreach ($this->mecanismos_carga as $valor_mec) {
			if ($valor_mec != $actual && isset($datos[$valor_mec])) {
				unset($datos[$valor_mec]);
			}
		}
		if ($datos['mecanismo'] != null) {
			unset($datos['mecanismo']);
			unset($datos['estatico']);
		} else {
			//--- Limpia los valores
			$datos = array();	
			foreach ($this->mecanismos_carga as $mec) {
				$datos[$mec] = null;
			}
		}
		$this->get_tabla()->set($datos);
	}
	
	function evt__param_varios__modificacion($datos)
	{
		$this->modificado = true;
		$this->get_tabla()->set($datos);
	}
	
	function evt__cargar_datos()
	{
		$this->parametros = array(
			'columnas' => 'columnas',
			'estado_defecto' => 'Defecto!',
			'solo_lectura' => 1,
					
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
			'carga_no_seteado' => '---Valor No seteado!!--',
					
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
	}
	
	function evt__post_recuperar_interaccion()
	{
		if (isset($this->parametros)) {
			$salida = array();
			foreach ($this->parametros as $clave => $valor) {
				if (is_array($valor)) {
					$valor = implode(', ', $valor);	
				}
				$salida[$clave] = $valor;
			}
			//--- Inserto los nuevos
			$this->get_tabla()->set($salida);
		}
	}
	
	
	function get_tabla()
	{
		return $this->dependencia('tabla');	
	}
	
	function evt__procesar()
	{
		ei_arbol($this->get_tabla()->get());
	}
}

?>