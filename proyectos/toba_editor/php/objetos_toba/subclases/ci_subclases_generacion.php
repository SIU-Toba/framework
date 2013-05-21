<?php
/**
	* Esta subclase puede trabajar de dos maneras:
	*  - Esclava de un wizard que edita la sublcases de componentes
	*  - Usada directamente como visor/editor php
	*/
class ci_subclases_generacion extends toba_ci
{
	protected $s__datos_opciones;    
	protected $s__datos_metodos;
	protected $s__path_archivo;
	protected $s__es_esclavo;
	protected $previsualizacion;
	protected $info_archivo;
	protected $comando_svn;
	
	function ini()
	{
		//Si viene 'archivo' asume la carpeta php
		//Si viene 'path' no asume carpeta php del proyecto (puede ser www u otra)
		$asume_php = true;
		$archivo = toba::memoria()->get_parametro('archivo');
		$path = toba::memoria()->get_parametro('path');
		if (!isset($archivo) && isset($path)) {
			$archivo = $path;
			$asume_php = false;
		}
		if (isset($archivo)) {
			if (strpos($archivo, '..') !== false) {
				//Evita que se pasen ../ en la url
				throw new toba_error_seguridad("El parámetro '$archivo' no es un path válido");
			}
			$pm_id = toba::memoria()->get_parametro('punto_montaje');
			if (! is_null($pm_id)) {
				$pm_obj =  toba_modelo_pms::get_pm($pm_id, toba_editor::get_proyecto_cargado());
				$this->s__path_archivo = $pm_obj->get_path_absoluto() . '/' . $archivo;
			} else {			
				$path_proyecto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado());
				if (! $asume_php) {
					$this->s__path_archivo = $path_proyecto.'/'.$archivo;
				} else {
					$this->s__path_archivo = $path_proyecto.'/php/'.$archivo;
				}
			}
			$this->s__es_esclavo = false;
		}		
		if (! isset($this->s__es_esclavo)) {
			//Es un esclavo
			$this->s__path_archivo = $this->controlador()->get_path_archivo();
			$this->s__es_esclavo = true;
		}
	}

	function conf()
	{    
		$metodos = $this->get_metodos_a_generar();
		$archivo_php = new toba_archivo_php($this->s__path_archivo);
		
		//-- Se va a modificar algo?        
		if (! empty($metodos) || $archivo_php->esta_vacio()) {
			$this->pantalla()->tab('pant_vista_previa')->set_etiqueta('Vista Previa');
		}

		//-- Puede generar código?
		if (! $this->s__es_esclavo) {
			$this->pantalla()->eliminar_tab('pant_edicion');
			$this->pantalla()->eliminar_tab('pant_opciones');
		}
	}    
	
	function conf__pant_opciones()
	{
		$efs = $this->dep('form_metodos')->get_nombres_ef();
		if (empty($efs)) {
			$this->pantalla()->eliminar_dep('form_metodos');
			$this->pantalla()->eliminar_dep('form_opciones');
			$this->pantalla()->set_descripcion('No hay métodos nuevos para generar en la subclase');
		}
	}
	
	//-----------------------------------------------------------------
	//---------- OPCIONES 
	//-----------------------------------------------------------------    
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
	
	function get_opciones()
	{
		return $this->s__datos_opciones;        
	}
	
	//-----------------------------------------------------------------
	//---------- METODOS 
	//-----------------------------------------------------------------
	
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

	//-----------------------------------------------------------------
	//---------- EDITOR DE CODIGO
	//-----------------------------------------------------------------

	function conf__codigo(toba_ei_codigo $codigo)
	{
		$codigo->set_datos($this->get_codigo_vista_previa());
	}

	function evt__codigo__modificacion($datos)
	{
		file_put_contents($this->s__path_archivo, $datos);
	}

	//-----------------------------------------------------------------
	//---------- VISTA PREVIA 
	//-----------------------------------------------------------------    
	
	function evt__eliminar()
	{
		unlink($this->s__path_archivo);
	}    


	function evt__svn_blame()
	{
		$this->comando_svn = 'blame';
	}
	
	function evt__svn_revert()
	{
		$this->comando_svn = 'revert';
	}    
	
	function evt__svn_diff()
	{
		$this->comando_svn = 'diff';
	}    
	
	function evt__svn_add()
	{
		$this->comando_svn = 'add';
	}        
	
	function get_previsualizacion()
	{
		return $this->previsualizacion;    
	}    
	
	function get_info_archivo()
	{
		return $this->info_archivo;
	}
	
	function conf__pant_vista_previa()
	{
		$path = $this->s__path_archivo;
		$svn = new toba_svn();        
		if (! isset($this->comando_svn)) {
			$codigo = $this->get_codigo_vista_previa();
		} else {
			switch ($this->comando_svn) {
				case 'blame':
					$codigo = $svn->blame($path);
					if ($codigo == '') {
						toba::notificacion()->error('No se pudo realizar el blame, posiblemente porque el repositorio tiene restricciones de permisos y el usuario de apache no tiene cargadas las credenciales');
					}
					break;
				case 'diff':
					$codigo = $svn->diff($path);
					break;            
				case 'revert':
					$svn->revert($path);
					$codigo = $this->get_codigo_vista_previa();
					break;                
				case 'add':
					$svn->add($path);
					$codigo = $this->get_codigo_vista_previa();
					break;                                    
			}
		}

		//-- Info del archivo
		$estado = $svn->get_estado($path);
		$svn_blame = true;
		$svn_diff = false;
		$svn_add = false;
		switch ($estado) {
			case 'unversioned':
				$svn_blame = false;
				$svn_add = true;
				$nombre = 'sin versionar';
				$img = toba_recurso::imagen_proyecto('svn/unversioned.png', true, 16, 16, $nombre);
				break;
			case 'modified':
			case 'missing':
				$svn_diff = true;
				$nombre = 'modificado';
				$img = toba_recurso::imagen_proyecto('svn/modified.png', true, 16, 16, $nombre);
				break;
			case 'added':
				$svn_blame = false;
				$nombre = 'agregado';
				$img = toba_recurso::imagen_proyecto('svn/added.png', true, 16, 16, $nombre);
				break;
			case 'normal':
				$nombre = 'sin modificar';
				$img = toba_recurso::imagen_proyecto('svn/normal.png', true, 16, 16, $nombre);
				break;        
			case 'conflicted':
				$nombre = 'En Conflicto';
				$img = toba_recurso::imagen_proyecto('svn/conflict.png', true, 16, 16, $nombre);
				break;    
			case 'deleted':
				$nombre = 'borrado';
				$img = toba_recurso::imagen_proyecto('svn/deleted.png', true, 16, 16, $nombre);
				break;
			case 'locked':
				$nombre = 'locked';
				$img = toba_recurso::imagen_proyecto('svn/locked.png', true, 16, 16, $nombre);
				break;
			case 'ignored':
				$svn_add = true;
				$svn_blame = false;
				$nombre = 'ignorado';
				$img = toba_recurso::imagen_proyecto('svn/ignored.png', true, 16, 16, $nombre);
				break;    
		}
		$this->info_archivo = "$path <span style='font-size: 9px; color:gray'>($nombre)</span>";
		$this->previsualizacion = $codigo;

		$existe_archivo = file_exists($path);
		$ver_comandos_svn = $svn->hay_cliente_svn();        
		if (! $svn->hay_cliente_svn()) {
			$ver_comandos_svn = false;
		}

		//-- Oculta boton eliminar
		if (! $existe_archivo) {
			$this->pantalla()->eliminar_evento('eliminar');
			$this->pantalla()->eliminar_evento('abrir');
		}
		if (!$ver_comandos_svn || !$svn_blame) {
			$this->pantalla()->eliminar_evento('svn_blame');
		}            
		if (!$ver_comandos_svn || !$svn_diff) {
			$this->pantalla()->eliminar_evento('svn_diff');
			$this->pantalla()->eliminar_evento('svn_revert');
		}
		if (!$ver_comandos_svn || !$svn_add || !$existe_archivo) {
			$this->pantalla()->eliminar_evento('svn_add');
		}
		if (!$ver_comandos_svn || !$existe_archivo || $svn_add) {
			$this->pantalla()->eliminar_evento('trac_ver');
		}
	}
	
	function get_codigo_vista_previa()
	{
		$opciones = $this->get_opciones();
		$metodos = $this->get_metodos_a_generar();
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		
		//-- Se va a modificar algo?
		if ($this->s__es_esclavo && (! empty($metodos) || $archivo_php->esta_vacio())) {
			if (! method_exists($this->controlador(), 'get_metaclase')) {
				throw new toba_error('No se invoco correctamente en el visor de archivos PHP');
			}
			$clase_php = new toba_clase_php($archivo_php, $this->controlador()->get_metaclase());
			$codigo = $clase_php->get_codigo($metodos, $opciones['incluir_comentarios'], $opciones['incluir_separadores']);
			$codigo = "\n".$codigo."\n";
			return $codigo;
		} else {
			//-- Muestra el original
			if (file_exists($this->get_path_archivo())) {
				return file_get_contents($this->get_path_archivo());
			} else {
				return '';
			}
		}
	}

	function get_path_archivo()
	{
		return $this->s__path_archivo;
	}
	
	function get_metodos_a_generar()
	{
		$metodos = array();
		if (isset($this->s__datos_metodos)) {
			foreach ($this->s__datos_metodos as $clave => $valor) {
				if ($valor) {
					$clave = explode('_', $clave);
					$metodos[] = end($clave);
				}
			}
		}        
		return $metodos;
	}
	
	function resetear_metodos()
	{
		$this->s__datos_metodos = array();        
	}

	//-------------------------------------------------------------------------------
	//-- Apertura de archivos por AJAX ----------------------------------------------
	//-------------------------------------------------------------------------------

	function servicio__ejecutar()
	{
		$this->evt__abrir();
	}

	function evt__abrir()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		if (!$archivo_php->existe()) {
			throw new toba_error('Se solicito la apertura de un archivo inexistente (\'' . $archivo_php->nombre() . '\').');
		}
		$archivo_php->abrir();
	}

	
}
?>