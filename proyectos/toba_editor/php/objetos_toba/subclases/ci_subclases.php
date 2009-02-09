<?php 

class ci_subclases extends toba_ci
{
	protected $s__id_componente;
	protected $s__path_relativo;
	protected $s__datos_nombre;
	protected $s__subcomponente;
	
	protected $clase_php;
	protected $archivo_php;
	
	
	function ini()
	{
		$datos = toba::zona()->get_info();
		if(!isset($datos)){
			throw new toba_error('Necesita seleccionar un componente para poder extenderlo');	
		}
		$this->s__id_componente = array('componente'=>$datos['objeto'], 'proyecto'=>$datos['proyecto'] );		
		$info = $this->get_metaclase();
		
		if ($info->get_subclase_archivo() != '' &&  $info->get_subclase_nombre() != '') {
			$this->s__path_relativo = dirname($info->get_subclase_archivo());
			if ($this->s__path_relativo == '.') {
				$this->s__path_relativo = '';
			}
			$this->s__datos_nombre = array('nombre' => basename($info->get_subclase_archivo(), '.php'));
			$this->set_pantalla('pant_generacion');
		}
	}
	
	/**
	 * @return toba_componente_info
	 */
	function get_metaclase()
	{
		//-- Acceso a un SUBCOMPONENTE
		if(isset($this->s__subcomponente)){ //Cargue un subcomponente en un request anterior.
			$subcomponente = $this->s__subcomponente;
		} else {
			$subcomponente = toba::memoria()->get_parametro('subcomponente');
		}
		
		$info = toba_constructor::get_info($this->s__id_componente);
		if (isset($subcomponente)) {
			$info = $info->get_metaclase_subcomponente($subcomponente);
			if ($info) {
				$this->s__subcomponente = $subcomponente;
			}else{
				throw new toba_error('ERROR cargando el SUBCOMPONENTE: No es posible acceder a la definicion del mismo.');
			}
		}	
		return $info;
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
			$datos = $this->s__datos_nombre;
		} else {
			$datos = array();
			$datos['nombre'] = $this->get_prefijo_clase();
		}
		$form->set_datos($datos);
	}
	
	function conf__archivos(toba_ei_archivos $archivos)
	{
		$archivos->set_extensiones_validas(array('php'));
		$archivos->set_path_absoluto($this->get_path_absoluto());
		$archivos->set_crear_archivos(false);
		$archivos->set_crear_carpetas(false);
		$archivos->set_titulo('Extensiones existentes en php/'.$this->get_path_relativo());
	}
	
	function evt__form_nombre__modificacion($datos)
	{
		$this->s__datos_nombre = $datos;
		
		//-- Sincroniza el cambio con la base
		$path_relativo = $this->get_path_relativo();
		if ($path_relativo != '') {
			$path_relativo.= '/';
		}
		$datos['subclase_archivo'] = $path_relativo.$this->s__datos_nombre['nombre'].'.php';
		$this->get_metaclase()->set_subclase($this->s__datos_nombre['nombre'], $datos['subclase_archivo']);
		toba_constructor::set_refresco_forzado(true);
	}
	
	function get_path_archivo()
	{
		return $this->get_path_absoluto().'/'.$this->s__datos_nombre['nombre'].'.php';
	}
	
	//-----------------------------------------------------------------
	//---------- GENERACION
	//----------------------------------------------------------------
	
	function conf__pant_generacion()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		$codigo_existente = null; 
		if (! $archivo_php->esta_vacio()) {
			$codigo_existente = $archivo_php->get_codigo();
		}
		$molde_clase = $this->get_metaclase()->get_molde_subclase();
		$metodos = $molde_clase->get_lista_metodos($codigo_existente);
		
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

	
	function evt__generar()
	{
		$opciones = $this->dep('ci_generacion')->get_opciones();
		$metodos = $this->dep('ci_generacion')->get_metodos_a_generar();		
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		$clase_php = new toba_clase_php($archivo_php, $this->get_metaclase());
	
		$clase_php->generar($metodos, $opciones['incluir_comentarios'], $opciones['incluir_separadores']);
		$this->pantalla()->set_descripcion("Clase generada correctamente");
		$this->dep('ci_generacion')->set_pantalla('pant_vista_previa');
		
		//Resetea los métodos para que fuerze al usuario a elegir otros si quiere generar nuevamente la clase
		$this->dep('ci_generacion')->resetear_metodos();
	}
	
	//-------------------------------------------------------------------------------
	//-- Apertura de archivos por AJAX ----------------------------------------------
	//-------------------------------------------------------------------------------

	function servicio__ejecutar()
	{ 
		$this->abrir_archivo();
	}

	function abrir_archivo()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		if( !$archivo_php->existe() ) {
			throw new toba_error('Se solicito la apertura de un archivo inexistente (\'' . $archivo_php->nombre() . '\').');	
		}
		$archivo_php->abrir();		
	}
		

}

?>