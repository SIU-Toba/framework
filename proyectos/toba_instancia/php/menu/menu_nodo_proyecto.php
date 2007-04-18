<?php
require_once(toba_dir() . '/php/contrib/lib/toba_nodo_basico.php');

class menu_nodo_proyecto extends toba_nodo_basico
{
	protected $celda;
	protected $target;
	
	function __construct($proyecto, $padre, $celda, $target)
	{
		parent::__construct($proyecto, $padre);
		$this->proyecto = $proyecto;
		$this->celda = $celda;
		$this->target = $target;
		$this->datos = consultas_instancia::get_datos_proyecto($this->proyecto);
		//----------- OPCIONES --------------------
		$this->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("nucleo/proyecto.gif", false),
									'ayuda' => null ) );
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = $this->celda;
		/*
		$this->agregar_utileria( array(
			'imagen' => toba_recurso::imagen_toba("info_chico.gif", false),
			'ayuda' => 'Informacion del proyecto',
			'vinculo' => toba::vinculador()->crear_vinculo( 'toba_instancia', 3338, null, $opciones ),
			'target' => $this->target
		) );*/
		//----------- HIJOS -----------------------
		$parametros = array('proyecto'=>$this->proyecto);
		$usuarios = new toba_nodo_basico('Usuarios Vinculados ['. consultas_instancia::get_cantidad_usuarios_proyecto($this->proyecto) .']',$this);
		$usuarios->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/usuario.gif", false),
										'ayuda' => 'Usuarios asociados al proyecto' ) );
		$usuarios->agregar_utileria( array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Previsualizar el componente',
			'vinculo' => toba::vinculador()->crear_vinculo( 'toba_instancia', 3331, $parametros, $opciones ),
			'target' => $this->target ) );

		$sesiones = new toba_nodo_basico('Log de sesiones ['. consultas_instancia::get_cantidad_sesiones_proyecto($this->proyecto) .']',$this);
		$sesiones->agregar_icono( array( 'imagen' => 	toba_recurso::imagen_toba("doc.gif", false),
										'ayuda' => 'Sesiones creadas sobre el proyecto' ) );
		$sesiones->agregar_utileria( array(
					'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
					'ayuda' => 'Previsualizar el componente',
					'vinculo' => toba::vinculador()->crear_vinculo( 'toba_instancia', 3336, $parametros, $opciones ),
					'target' => $this->target ));
		$this->set_hijos( array( $usuarios, $sesiones ));
	}
}
?>