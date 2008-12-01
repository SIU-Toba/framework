<?php 

class ci_subclases extends toba_ci
{
	protected $s__id_componente;
	protected $s__path_relativo;
	protected $s__datos_nombre;
	protected $s__datos_metodos;
	protected $s__datos_opciones;
	
	protected $clase_php;
	protected $archivo_php;
	protected $previsualizacion;
	
	function ini()
	{
		$this->s__id_componente = array('proyecto' => 'toba_referencia', 'componente'=> '2292');
	}
	
	/**
	 * @return toba_componente_info
	 */
	function get_metaclase()
	{
		return toba_constructor::get_info($this->s__id_componente);
	}
	
	function get_prefijo_clase()
	{
		return $this->get_metaclase()->get_nombre_instancia_abreviado().'_';	
	}
	

	
	//------------------------------------------------------------------
	//--------	UBICACION
	//------------------------------------------------------------------
		
	function conf__carpetas(toba_ei_archivos $archivos)
	{
		$archivos->set_solo_carpetas(true);
		$absoluto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado())."/php/";
		$archivos->set_path_absoluto($absoluto);
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');
		if ($inicial != null) {
			$archivos->set_path(dirname($inicial));
		}		
	}
	
	function get_path_relativo()
	{
		return $this->s__path_relativo;
	}
	
	function get_path_absoluto()
	{
		$relativo = $this->get_path_relativo();
		if ($relativo != '') {
			$relativo = '/'.$relativo;
		}
		return toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()).'/php'.$relativo;
	}
	
	function evt__pant_ubicacion__salida()
	{
		$this->s__path_relativo = $this->dep('carpetas')->get_path_relativo();
	}	
	
	
	//------------------------------------------------------------------
	//--------	FORM NOMBRE
	//------------------------------------------------------------------
	
	function conf__form_nombre(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_nombre)) {
			$form->set_datos($this->s__datos_nombre);
		} else {
			$datos['nombre'] = $this->get_prefijo_clase();
			$form->set_datos($datos);
		}

	}
	
	function evt__form_nombre__modificacion($datos)
	{
		$this->s__datos_nombre = $datos;
	}
	
	function get_path_archivo()
	{
		return $this->get_path_absoluto().'/'.$this->s__datos_nombre['nombre'].'.php';
	}
	
	
	//-----------------------------------------------------------------
	//---------- GENERACION
	//----------------------------------------------------------------
	
	function conf__form_opciones(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_opciones)) {
			$form->set_datos($this->s__datos_opciones);
		}
	}
	
	function evt__form_opciones__modificacion($datos)
	{
		$this->s__datos_opciones = $datos;
	}
	
	
	function conf__pant_generacion()
	{
		$molde_clase = $this->get_metaclase()->get_molde_subclase();
		$metodos = $molde_clase->get_lista_metodos();
		
		$grupos = array();
		//-- Agrupamos los metodos segun dependencia y tipo
		foreach ($metodos as $metodo) {
			$elemento = $metodo['elemento'];
			$id = $metodo['id'];
			$grupo = $elemento->get_grupo();
			if ($elemento instanceof toba_codigo_metodo_js) {
				$grupo = 'Javascript';	
			}
			$grupos[$grupo][$id] = $elemento;
		}
		
		//-- Agrupamos por javascript
		
		// Se definen los EFs del formulario en runtime.
		$clave = array('componente' => '2291', 'proyecto' => 'toba_editor');
        $metadatos = toba_cargador::instancia()->get_metadatos_extendidos($clave, 'toba_ei_formulario');
        
        $ef_base = array(
			'obligatorio' => 0,
        	'elemento_formulario' => 'ef_checkbox',
        	'descripcion' => '',
        	'colapsado' => 0,
        	'oculto_relaja_obligatorio' => 0
        );
        $metadatos['_info_formulario_ef'] = array();
       
        
        $i = 0;
        foreach ($grupos as $id_grupo => $metodos) {
        	if ($id_grupo == '') {
        		$id_grupo = 'Propios';
        	}
        	$nombre_grupo = "$id_grupo <div><a href='javascript: cambiar_grupo(\"$id_grupo\", true);'>todos</a> / <a href='javascript: cambiar_grupo(\"$id_grupo\", false);''>ninguno</a></div>";
        	$separador = $ef_base;
        	$separador['identificador'] = "sep_$i";
        	$separador['columnas'] = $separador['identificador'];
        	$separador['etiqueta'] = $nombre_grupo;
        	$separador['elemento_formulario'] = 'ef_barra_divisora';        	
        	$metadatos['_info_formulario_ef'][] = $separador;
        	
        	foreach ($metodos as $id => $metodo) {
	        	$ef = $ef_base;
		       	$ef['identificador'] = "metodo_".$id_grupo."_$id";
	        	$ef['columnas'] = $ef['identificador'];
	        	$ef['etiqueta'] = $metodo->get_descripcion();
	        	$ef['descripcion'] = $metodo->get_doc();
	        	$metadatos['_info_formulario_ef'][] = $ef;
	        	
        	}
        	$i++;
        }
		toba_cargador::instancia()->set_metadatos_extendidos($metadatos, $clave);		
	}
	
	function conf__form_metodos(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_metodos)) {
			$form->set_datos($this->s__datos_metodos);
		}
	}
	
	function evt__form_metodos__modificacion($datos)
	{
		$this->s__datos_metodos = $datos;
	}
	
	function evt__vista_previa()
	{
		$incluir_comentarios = true;
		$metodos = $this->get_metodos_a_generar();
		$archivo_php = $this->get_path_archivo();
		$clase_php = new toba_clase_php($archivo_php, $this->get_metaclase());
		
		$codigo = "<?php" . salto_linea() . $clase_php->get_codigo($metodos, $incluir_comentarios) . "?>" . salto_linea() ;
		require_once(toba_dir()."/php/3ros/PHP_Highlight.php");
		$h = new PHP_Highlight(false);
		$h->loadString($codigo);
		$formato_linea = "<span style='background-color:#D4D0C8; color: black; font-size: 10px;".
						" padding-top: 2px; padding-right: 2px; margin-left: -4px; width: 20px; text-align: right;'>".
						"%2d</span>&nbsp;&nbsp;";
		$this->previsualizacion = $h->toHtml(true, true, $formato_linea, true);
		if(count($clase_php->get_lista_metodos_posibles())>5) {
			$this->dep('form_metodos')->colapsar();
		}		
	}
	
	function get_metodos_a_generar()
	{
		$metodos = array();
		foreach ($this->s__datos_metodos as $clave => $valor) {
			if ($valor) {
				$clave = explode('_', $clave);
				$metodos[] = end($clave);
			}
		}		
		return $metodos;
	}
	
	function get_previsualizacion()
	{
		return $this->previsualizacion;	
	}
	
	function evt__generar()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		$archivo_php->crear_basico();
		$clase_php = new toba_clase_php($this->get_path_archivo(), $this->get_metaclase());
		$this->clase_php->generar($opciones, $incluir_comentarios);
		
	}
	

}

?>