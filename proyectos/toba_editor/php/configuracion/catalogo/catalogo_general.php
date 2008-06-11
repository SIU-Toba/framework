<?php
require_once(toba_dir() . '/php/contrib/lib/toba_nodo_basico.php');

class catalogo_general extends toba_nodo_basico
{
	function __construct()
	{
		parent::__construct('Configuracion General',null);
		$this->iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba('configurar.png', false),
								'ayuda' => 'Administrar usuarios de la instancia' );
		//Construyo los HIJOS
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		//----------------------------------------------------------------------
		$hijos[0] = new toba_nodo_basico('Propiedades', $this);
		$hijos[0]->agregar_icono(  array( 'imagen' => 	toba_recurso::imagen_toba("nucleo/proyecto.gif", false),
							'ayuda' => null ));
		$hijos[0]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar parametros basicos',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), 1000259, $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[1] = new toba_nodo_basico('Previsualización', $this);
		$hijos[1]->agregar_icono(  array( 'imagen' => 	toba_recurso::imagen_proyecto("config_previsualizacion.gif", false),
							'ayuda' => null ));
		$hijos[1]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Configuración de la previsualización del proyecto',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), '3287', $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[3] = new toba_nodo_basico('Tipo de Pagina', $this);
		$hijos[3]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_proyecto("tipo_pagina.gif", false),
										'ayuda' => null ) );
		$hijos[3]->agregar_utileria(  array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar Tipos de página disponibles',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(),1000235, $opciones ),
				'target' => apex_frame_centro
				) );
				
		//----------------------------------------------------------------------
		$hijos[4] = new toba_nodo_basico('Skins', $this);
		$hijos[4]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_proyecto("css.gif", false),
										'ayuda' => null ) );
		$hijos[4]->agregar_utileria(  array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar Skins propios del proyecto',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(),'3419', $opciones ),
				'target' => apex_frame_centro
				) );
								
		//----------------------------------------------------------------------
		$hijos[5] = new toba_nodo_basico('Zona', $this);
		$hijos[5]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_proyecto("zona.gif", false),
							'ayuda' => null ) );
		$hijos[5]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar ZONA',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), 1000236, $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		$hijos[8] = new toba_nodo_basico('Mensajes', $this);
		$hijos[8]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("mensaje.gif", false),
							'ayuda' => null ) );
		$hijos[8]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar Mensajes del sistema',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), 1000233, $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
/*
		$hijos[10] = new toba_nodo_basico('Elementos de Formulario', $this);
		$hijos[10]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("objetos/abms_ef.gif", false),
							'ayuda' => null ) );
		$hijos[10]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar elementos de formulario',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), 1000020, $opciones ),
				'target' => apex_frame_centro
		) );*/
		//----------------------------------------------------------------------
		$hijos[15] = new toba_nodo_basico('Derechos', $this);
		$hijos[15]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/permisos.gif", false),
							'ayuda' => null ) );
		$hijos[15]->agregar_utileria( array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => 'Editar DERECHOS globales',
				'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), '3276', $opciones ),
				'target' => apex_frame_centro
		) );
		//----------------------------------------------------------------------
      	$hijos[20] = new toba_nodo_basico('Puntos de control', $this);
  		$hijos[20]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/punto_control.png", false),
							'ayuda' => null ) );
	  	$hijos[20]->agregar_utileria( array(
        'imagen'  => toba_recurso::imagen_toba("objetos/editar.gif", false),
        'ayuda'   => 'Editar PUNTOS DE CONTROL globales',
        'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), '10000019', $opciones ),
			  'target'  => apex_frame_centro
		) );
		//----------------------------------------------------------------------
		if( toba_editor::acceso_recursivo() ) {
	      	$hijos[27] = new toba_nodo_basico('Tipos de COMPONENTES', $this);
	  		$hijos[27]->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("objetos/objeto.gif", false),
								'ayuda' => null ) );
		  	$hijos[27]->agregar_utileria( array(
	        'imagen'  => toba_recurso::imagen_toba("objetos/editar.gif", false),
	        'ayuda'   => 'Editar PUNTOS DE CONTROL globales',
	        'vinculo' => toba::vinculador()->get_url( toba_editor::get_id(), '3391', $opciones ),
				  'target'  => apex_frame_centro
			) );
		}
		//----------------------------------------------------------------------		
		$this->set_hijos($hijos);
	}
}
?>