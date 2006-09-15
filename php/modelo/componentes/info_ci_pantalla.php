<?php

class info_ci_pantalla implements recorrible_como_arbol
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
		$eis = explode(',', $this->datos['objetos']);
		$eis = array_map('trim', $eis);
		foreach ($posibles as $posible) {
			if (in_array($posible->rol_en_consumidor(), $eis)) {
				$this->dependencias[] = $posible;
			}
		}
	}
	
	function tiene_dependencia($dep)
	{
		return in_array($dep, $this->dependencias);
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
			'imagen' => toba_recurso::imagen_apl('objetos/pantalla.gif', false),
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
			'imagen' => toba_recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado a la pantalla",
			'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
								array('destino_tipo' => 'ci_pantalla', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id,
										'destino_pantalla' => $this->datos['pantalla']),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		if ($this->datos['subclase'] && $this->datos['subclase_archivo']) {	// Hay PHP asociado
			// Editor de PHP
			$parametros = $param_editores;
			$parametros['subcomponente'] = $this->datos['identificador'];
			$iconos[] = array(
					'imagen' => toba_recurso::imagen_apl("php.gif", false),
					'ayuda' => 'Ver detalles de la [wiki:Referencia/Objetos/Extension extensión PHP]',
					'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(), 
																			'/admin/objetos/php', $parametros,
																			false, false, null, true, 'central'),
					'plegado' => true										
			);
			// Apertura del archivo
			$opciones = array('servicio' => 'ejecutar', 'zona' => true, 'celda_memoria' => 'ajax', 'validar' => false, 'menu' => true);
			$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(),"/admin/objetos/php", $parametros, $opciones);
			$js = "toba.comunicar_vinculo('$vinculo')";
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_apl('reflexion/abrir.gif', false),
				'ayuda' => 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]',
				'vinculo' => "javascript: $js;",
				'target' => '',
				'plegado' => false
			);
		}
		$iconos[] = array(
				'imagen' => toba_recurso::imagen_apl("objetos/editar.gif", false),
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

	function es_evento($metodo){}	
	function es_evento_valido($metodo){}
	function es_evento_predefinido($metodo){}
	function es_evento_sospechoso($metodo){}

}
?>