<?php

class toba_ci_pantalla_info implements toba_nodo_arbol, toba_meta_clase
{
	protected $dependencias = array();
	protected $datos;
	protected $proyecto;
	protected $id;
		
	function __construct($datos, $dependencias_posibles, $proyecto, $id)
	{
		$this->datos = $datos;
		$this->asociar_dependencias($dependencias_posibles);
		$this->proyecto = $proyecto;
		$this->id = $id;
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
		$eis = explode(',', $this->datos['objetos']);
		$eis = array_map('trim', $eis);
		return $eis;		
	}
	
	function tiene_dependencia($dep)
	{
		return in_array($dep, $this->dependencias, true);
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
			'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
								array('destino_tipo' => 'ci_pantalla', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id,
										'destino_pantalla' => $this->datos['pantalla']),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		if ($this->datos['subclase'] && $this->datos['subclase_archivo']) {
			// Hay PHP asociado
			if ( admin_util::existe_archivo_subclase($this->datos['subclase_archivo']) ) {
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
				'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(), "/admin/objetos_toba/editores/ci", 
																	$param_editores, false, false, null, true, "central"),
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

	function get_molde_subclase()
	{
		$molde = new toba_molde_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		$molde->agregar( new toba_molde_metodo_php('generar_layout') );
		$php = array();
		$existe_previo = 0;
		foreach($this->get_lista_dependencias_asociadas() as $dep) {
			if($existe_previo) $php[] =  '<hr />';
			$php[] = '$this->dependencias[\''.$dep.'\']->generar_html();';
			$existe_previo = 1;
		}
		$molde->ultimo_elemento()->set_contenido($php);
		return $molde;
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
		return 'toba_ei_pantalla';
	}

	function get_clase_archivo()
	{
		return 'nucleo/componentes/interface/toba_ei_pantalla.php';	
	}

	//---------------------------------------------------------------------
	
	function get_descripcion_subcomponente()
	{
		return 'Pantalla ' . $this->datos['identificador'];
	}
}
?>