<?php
require_once("contrib/lib/toba_nodo_basico.php");

class catalogo_general extends toba_nodo_basico
{
	function __construct()
	{
		parent::__construct('Configuracion General',null);
		$this->iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba('configurar.gif', false),
								'ayuda' => 'Administrar usuarios de la instancia' );
		//Construyo los HIJOS
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		//----------------------------------------------------------------------
		$hijos[0] = new toba_nodo_basico('Propiedades', $this);
		$hijos[0]->agregar_icono(  array( 'imagen' => 	toba_recurso::imagen_toba("proyecto.gif", false),
							'ayuda' => null ));
		$hijos[0]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar parametros basicos',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/proyectos/propiedades', $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[1] = new toba_nodo_basico('Tipo de Pagina', $this);
		$hijos[1]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("tipo_pagina.gif", false),
										'ayuda' => null ) );
		$hijos[1]->agregar_utileria(  array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar Mensajes del sistema',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(),'/admin/apex/elementos/pagina_tipo', $opciones ),
				'target' => apex_frame_centro
				) );
		//----------------------------------------------------------------------
		$hijos[2] = new toba_nodo_basico('Zona', $this);
		$hijos[2]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("zona.gif", false),
							'ayuda' => null ) );
		$hijos[2]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar ZONA',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/apex/elementos/zona', $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[3] = new toba_nodo_basico('Mensajes', $this);
		$hijos[3]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("mensaje.gif", false),
							'ayuda' => null ) );
		$hijos[3]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar Mensajes del sistema',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/apex/elementos/error', $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[4] = new toba_nodo_basico('Elementos de Formulario', $this);
		$hijos[4]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("objetos/abms_ef.gif", false),
							'ayuda' => null ) );
		$hijos[4]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar elementos de formulario',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), 1000020, $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[5] = new toba_nodo_basico('Derechos', $this);
		$hijos[5]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/permisos.gif", false),
							'ayuda' => null ) );
		$hijos[5]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar DERECHOS globales',
				'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '3276', $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$this->set_hijos($hijos);
	}
}
?>