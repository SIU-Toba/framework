<?php 
require_once("contrib/lib/toba_nodo_basico.php");
require_once("menu/menu_nodo_proyecto.php");

class ci_menu extends toba_ci
{
	function conf()
	{
		$this->pantalla()->set_descripcion('Administración de la instancia <strong>'.toba::sesion()->get_id_instancia().'</strong>');	
	}

	function conf__arbol($componente)
	{
		$componente->set_datos( $this->construir_arbol() );
		$componente->set_nivel_apertura(2);
	}
	
	function construir_arbol()
	{
		$target = 'central';
		$celda = 'centro';
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = $celda;
		// nodo USUARIOS
		$nodo_usuarios = new toba_nodo_basico('Usuarios ['. consultas_instancia::get_cantidad_usuarios() .']');
		$nodo_usuarios->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("nucleo/preferencias.gif", false),
											'ayuda' => 'Administrar USUARIOS' ) );
		$nodo_usuarios->agregar_utileria( array(
							'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
							'ayuda' => 'Administrar USUARIOS',
							'vinculo' => toba::vinculador()->crear_vinculo( 'toba_instancia', 3344, null, $opciones ),
							'target' => $target	) );
		// nodo PROYECTOS
		$nodo_proyectos	= new toba_nodo_basico('<b>PROYECTOS</b>');
		$nodo_proyectos->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("seleccionar.gif", false),
												'ayuda' => null ) );
		foreach( admin_instancia::ref()->get_lista_proyectos() as $proyecto ) {
			$proyectos[] = new menu_nodo_proyecto( $proyecto, $nodo_proyectos, $celda, $target );
		}
		$nodo_proyectos->set_hijos( $proyectos );
		// nodo ADMINISTRACION
		$nodo_admin = new toba_nodo_basico('Administracion General');
		$nodo_admin->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("configurar.gif", false),
											'ayuda' => 'Administrar la instancia' ) );
		$nodo_admin->agregar_utileria( array(
			'imagen' => toba_recurso::imagen_toba("info_chico.gif", false),
			'ayuda' => 'Administrar la instancia',
			'vinculo' => toba::vinculador()->generar_solicitud( 'toba_instancia', 3340, null, $opciones ),
			'target' => $target
		));

		$nodo_admin_bips = new toba_nodo_basico('Bloqueo de IPs ['. consultas_instancia::get_cantidad_ips_rechazadas() .']',$nodo_admin);
		$nodo_admin_bips->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("error.gif", false),
												'ayuda' => 'Administrar usuarios de la instancia' ) );
		$nodo_admin_bips->agregar_utileria( array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Previsualizar el componente',
			'vinculo' => toba::vinculador()->generar_solicitud( 'toba_instancia', 3332, null, $opciones ),
			'target' => $target
		) );

		$nodo_admin->set_hijos(array($nodo_usuarios, $nodo_admin_bips));
		//---------------------------
		return array( $nodo_admin, $nodo_proyectos );
	}
}
?>