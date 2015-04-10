<?php

class toba_ci_pantalla_info implements toba_nodo_arbol, toba_meta_clase
{
	protected $dependencias = array();
	protected $datos;
	protected $proyecto;
	protected $id;
	protected $obj_asociados = array();
		
	function __construct($datos, $dependencias_posibles, $proyecto, $id, $info_dep_asociadas = array())
	{
		$this->datos = $datos;
		$this->set_info_objetos_asociados($info_dep_asociadas);
		$this->asociar_dependencias($dependencias_posibles);
		$this->proyecto = $proyecto;
		$this->id = $id;
		$this->datos['_info']['clase'] = 'toba_ei_pantalla';
	}
	
	function set_info_objetos_asociados($obj)
	{
		$this->obj_asociados = $obj;
	}

	protected function asociar_dependencias($posibles)
	{
		$eis = $this->get_lista_dependencias_asociadas();
		foreach ($posibles as $posible) {
			if (in_array($posible->rol_en_consumidor(), $eis)) {
				$this->dependencias[] = $posible;
			}
		}
	}
	
	protected function get_lista_dependencias_asociadas()
	{
		$lista = array();
		$id_pantalla = $this->datos['pantalla'];
		foreach($this->obj_asociados as $dep){
			if ($dep['pantalla'] == $id_pantalla){
				$lista[] = $dep['identificador_dep'];
			}
		}			
		return $lista;
	}
	
	function tiene_dependencia($dep)
	{
		return in_array($dep, $this->dependencias, true);
	}

	function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		if (isset($this->datos['subclase_archivo'])) {
			$filas = $dr->tabla('pantallas')->get_id_fila_condicion(array('pantalla' => $this->datos['pantalla']), false);
			if (count($filas) != 1) {
				throw new toba_error_modelo("Imposible clonar subclase de pantalla {$this->datos['pantalla']}");
			}
			$fila = current($filas);
			$archivo = $this->datos['subclase_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			
			$id_pm_origen = $this->get_punto_montaje();						
			$id_pm_destino = $dr->tabla('base')->get_fila_columna(0, 'punto_montaje');							
			
			//Busco los directorios de copia utilizando los puntos de montaje
			$path_origen = $this->get_path_clonacion($id_pm_origen,$this->proyecto);
			$path_destino = $this->get_path_clonacion($id_pm_destino, $proyecto_dest, $path_origen);
			
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				toba_manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			if (! copy($path_origen.$archivo, $path_destino.$nuevo_archivo)) {
				throw new toba_error('No es posible copiar el archivo desde '.$path_origen.$archivo.' hacia '.$path_destino.$nuevo_archivo);
			}
			$dr->tabla('pantallas')->set_fila_columna_valor($fila, 'subclase_archivo', $nuevo_archivo);						
		}
	}
	
	protected function get_path_clonacion($id_punto, $proyecto, $path_default='')
	{
		$path_final = $path_default;
		$pm = toba_pms::instancia()->get_instancia_pm_proyecto($proyecto, $id_punto);		//Instancio el pm para el proyecto
		if (! is_null($pm)) {
			$path_final = $pm->get_path_absoluto(). '/';								//Si existe recupero el path al punto, sino uso el generico del proyecto
		} elseif (isset($proyecto)) {
			$path_final = toba::instancia()->get_path_proyecto($proyecto).'/php/';	
		}		
		return $path_final;		
	}
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function get_hijos()
	{
		return $this->dependencias;
	}
	
	function es_hoja()
	{
		return (count($this->dependencias) == 0);
	}
	
	function tiene_propiedades()
	{
		return false;
	}	
	
	function get_nombre_corto()
	{
		if ($this->datos['etiqueta'] != '')
			return str_replace('&', '', $this->datos['etiqueta']);
		else
			return $this->get_id();
	}
	
	function get_nombre_largo()
	{
		if (trim($this->datos['descripcion']) != '')
			return $this->datos['descripcion'];
		else
			return $this->get_nombre_corto();
	}
	
	function get_id()
	{
		return $this->datos['identificador'];		
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba('objetos/pantalla.gif', false),
			'ayuda' => 'Pantalla dentro del [wiki:Referencia/Objetos/ci ci]'
			);	
		return $iconos;
	}
	
	function get_utilerias()
	{
		$param_editores = array(apex_hilo_qs_zona=> $this->proyecto. apex_qs_separador.
													$this->id,
								"pantalla" => $this->datos['identificador']);	
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado a la pantalla",
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(),"1000247",
								array('destino_tipo' => 'toba_ci_pantalla', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id,
										'destino_pantalla' => $this->datos['pantalla']),
								array(	'menu' => true,
										'celda_memoria' => 'central')
							),
			'plegado' => true										
		);
		if ($this->datos['subclase'] && $this->datos['subclase_archivo']) {
			// Hay PHP asociado
			if ( admin_util::existe_archivo_subclase($this->datos['subclase_archivo'], $this->datos['punto_montaje']) ) {
				$iconos[] = toba_componente_info::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			$this->datos['identificador'] );
				$iconos[] = toba_componente_info::get_utileria_editor_abrir_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			$this->datos['identificador'] );
			} else {
				$iconos[] = toba_componente_info::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			$this->datos['identificador'],
																			'nucleo/php_inexistente.gif',
																			false );
			}
		}
		$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => "Editar esta pantalla",
				'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(), "1000249", 
																	$param_editores,
																array(	'menu' => true,
																		'celda_memoria' => 'central')
							),																	
				'plegado' => false
		);
		return $iconos;	
	}
	
	function get_info_extra()
	{
		return "";	
	}
	
	function tiene_hijos_cargados()
	{
		return true;	
	}
	
	function get_padre()
	{
		return null;
	}

	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_nombre_instancia_abreviado()
	{
		return 'pant';
	}
	
	function get_molde_subclase()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		$ayuda = "Permite modificar la forma en que se grafica la pantalla, por defecto un componente sobre el otro";
		$comentarios = array(
			$ayuda
		);
		$metodo = new toba_codigo_metodo_php('generar_layout', array(), $comentarios);
		$metodo->set_doc($ayuda);
		$molde->agregar($metodo);
		$php = array();
		$existe_previo = 0;
		foreach($this->get_lista_dependencias_asociadas() as $dep) {
			if($existe_previo) $php[] =  "echo '<hr />';";
			$php[] = '$this->dep(\''.$dep.'\')->generar_html();';
			$existe_previo = 1;
		}
		$molde->ultimo_elemento()->set_contenido($php);
		return $molde;
	}

	function get_punto_montaje()
	{
		return $this->datos['punto_montaje'];
	}
	
	function get_subclase_nombre()
	{
		return $this->datos['subclase'];
	}
	
	function get_subclase_archivo()
	{
		return $this->datos['subclase_archivo'];
	}

	function get_clase_nombre()
	{
		return  str_replace('objeto_', 'toba_', $this->datos['_info']['clase']);	// Se deja esta línea para que conserve el mismo comportamiento
	}
	
	function get_clase_archivo()
	{
		return 'nucleo/componentes/interface/toba_ei_pantalla.php';	
	}
	
	function cambiar_clase_origen($nombre_clase)
	{
		$this->datos['_info']['clase'] = $nombre_clase;
	}

	function set_subclase($nombre, $archivo, $pm)
	{
		$db = toba_contexto_info::get_db();
		$nombre = $db->quote($nombre);
		$archivo = $db->quote($archivo);
		$pm = $db->quote($pm);
		$id = $db->quote($this->id);
		$sql = "
			UPDATE apex_objeto_ci_pantalla
			SET 
				subclase = $nombre,
				subclase_archivo = $archivo,
				punto_montaje = $pm
			WHERE
					objeto_ci_proyecto = '{$this->proyecto}'
				AND	objeto_ci = $id
				AND pantalla = '{$this->datos['pantalla']}'
		";
		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}	
	//---------------------------------------------------------------------
	
	function get_descripcion_subcomponente()
	{
		return 'Pantalla ' . $this->datos['identificador'];
	}
}
?>