<?php

class toba_cn_info extends toba_componente_info
{
	protected $cn_asociado = false;	// Indica si el componente esta asociado en el contenedor

	static function get_tipo_abreviado()
	{
		return "CN";		
	}

	function set_asociado($asociado=true)
	{
		$this->cn_asociado = $asociado;
	}
	
	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un componente asociado al controlador",
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(),"1000247",
								array(	'destino_tipo' => 'toba_cn', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id ),
								array(	'menu' => true,
										'celda_memoria' => 'central')
							),
			'plegado' => true										
		);
		return array_merge($iconos, parent::get_utilerias());	
	}		

	function get_hijos()
	{
		// Un CN que esta asociado no muestra recursivamente sus asociaciones (puede haber relaciones circulares)
		// El codigo se pone aca porque tiene que ser ejecutado despues del constructor para que el flag tenga efecto
		//	... y esta es la unica ventana cuya ejecucion esta garantizada
		if(!$this->cn_asociado) {
			if(	isset($this->datos['_info_consumo']) && 
				count($this->datos['_info_consumo']) > 0 )	{
				$cantidad_subelementos = count($this->subelementos);
				for ( $a=0; $a<count($this->datos['_info_consumo']); $a++) {
					$clave['proyecto'] = $this->datos['_info_consumo'][$a]['proyecto'];
					$clave['componente'] = $this->datos['_info_consumo'][$a]['objeto'];
					$tipo = $this->datos['_info_consumo'][$a]['clase'];
					$id = $cantidad_subelementos + $a;
					$this->subelementos[$id]= toba_constructor::get_info( $clave, $tipo, $this->carga_profundidad, null, true, $this->datos_resumidos);
					$this->subelementos[$id]->set_consumidor($this, $this->datos['_info_consumo'][$a] );
					$this->subelementos[$id]->set_asociado();
				}
			}
		}
		return $this->subelementos;
	}

	function es_hoja()
	{
		return !$this->tiene_hijos_cargados();
	}

	function tiene_hijos_cargados()
	{
		$tiene_deps = ($this->datos['_info']['cant_dependencias'] > 0);
		$consume_cns = isset($this->datos['_info_consumo']) && count($this->datos['_info_consumo']) > 0;
		$tiene_hijos = $tiene_deps || ($consume_cns && ! $this->cn_asociado);
		return $tiene_hijos;
	}

	function get_estilo_css_li()
	{
		if($this->cn_asociado) {
			return 	"background-color: #FFD87C; font-style: italic;";
		}
	}

	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function get_molde_subclase($multilinea=false)
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar( new toba_codigo_metodo_php('ini') );
		$molde->agregar( new toba_codigo_metodo_php('evt__validar_datos') );
		$molde->agregar( new toba_codigo_metodo_php('evt__procesar_especifico') );
		return $molde;
	}
}
?>