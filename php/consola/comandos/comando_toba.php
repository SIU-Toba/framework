<?
require_once('consola/comando.php');
require_once('modelo/catalogo_modelo.php');

/**
	@todo: - Seleccion adecuada de Usuarios y Grupo de acceso
			- Tendria que existir un esquema para extender un comando
				por ejemplo, despues de crear una instancia, un proyecto puede querer
				agregar mas tablas a la misma
*/
class comando_toba extends comando
{
	private $interprete; 

	function __construct( gui $manejador_interface, $interprete = null )
	{
		parent::__construct( $manejador_interface );
		$this->interprete = $interprete;
	}

	//-----------------------------------------------------------
	// Acceso a los SUJETOS sobre los que actuan los comandos
	//-----------------------------------------------------------

	/**
	*	Devuelve una referencia al la INSTALACION
	*/
	protected function get_instalacion()
	{
		return catalogo_modelo::instanciacion()->get_instalacion( $this->consola );
	}
	
	/**
	*	Devuelve una referencia a la INSTANCIA
	*/
	protected function get_instancia()
	{
		return catalogo_modelo::instanciacion()->get_instancia(	$this->get_id_instancia_actual(),
																$this->consola );
	}

	/**
	*	Devuelve una referencia al PROYECTO 
	*/
	protected function get_proyecto()
	{
		return catalogo_modelo::instanciacion()->get_proyecto( 	$this->get_id_instancia_actual(),
																$this->get_id_proyecto_actual(),
																$this->consola );
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	protected function get_nucleo()
	{
		return catalogo_modelo::instanciacion()->get_nucleo( $this->consola );
	}

	/**
	*	Devuelve una referencia al CONVERSOR
	*/
	protected function get_conversor()
	{
		return catalogo_modelo::instanciacion()->get_conversor( $this->get_id_instancia_actual(), $this->consola );
	}

	//-----------------------------------------------------------
	// Acceso a los PARAMETROS comunes
	//-----------------------------------------------------------

	/**
	*	Determina la INSTANCIA sobre la que se va a trabajar
	*/
	protected function get_id_instancia_actual( $obligatorio = true )
	{
		$id = null;
		$param = $this->get_parametros();
		if ( isset($param['-i'] ) && ( trim( $param['-i'] ) != '') ) {
			$id = $param['-i'];
		} else {
			$id = $this->get_entorno_id_instancia();
		}
		if ( $obligatorio && is_null( $id ) ) {
			throw new toba_excepcion("Es necesario definir una INSTANCIA. Utilice el modificador '-i' o defina la variable de entorno 'toba_instancia'");
		}
		return $id;
	}

	/**
	*	Describe el parametro INSTANCIA
	*/
	protected function get_info_parametro_instancia()
	{
		$ei = $this->get_entorno_id_instancia();
		$valor_i = isset( $ei ) ?  $ei : 'No definida';
		$this->consola->mensaje("[-i id_instancia] Asume el valor de la variable de entorno 'toba_instancia': $valor_i");
	}

	/**
	*	Determina el PROYECTO sobre el que se va a trabajar
	*/
	protected function get_id_proyecto_actual( $obligatorio = true )
	{
		$id = null;
		$param = $this->get_parametros();
		if ( isset($param['-p']) &&  (trim($param['-p']) != '') ) {
			$id = $param['-p'];
		} else {
			$id = $this->get_entorno_id_proyecto();
		}
		if ( $obligatorio && is_null( $id ) ) {
			throw new toba_excepcion("Es necesario definir un PROYECTO. Utilice el modificador '-p' o defina la variable de entorno 'toba_proyecto'");
		}
		return $id;
	}
	
	/**
	*	Describe el parametro PROYECTO
	*/
	protected function get_info_parametro_proyecto()
	{
		$ep = $this->get_entorno_id_proyecto();
		$valor_p = isset( $ep ) ?  $ep : 'No definida';
		$this->consola->mensaje("[-p id_proyecto] Asume el valor de la variable de entorno 'toba_proyecto': $valor_p");
	}

	//-----------------------------------------------------------
	// Acceso al entorno
	//-----------------------------------------------------------
	
	/**
	*	Acceso a la variable de entorno 'toba_instancia'
	*/
	protected function get_entorno_id_instancia( $obligatorio = false )
	{
		if ( isset( $this->interprete ) ) {
			
		} else {
			if ( isset( $_SERVER['toba_instancia'] ) ) {
				return $_SERVER['toba_instancia'];
			}
		}
	}

	/**
	*	Acceso a la variable de entorno 'toba_proyecto'
	*/
	protected function get_entorno_id_proyecto( $obligatorio = false )
	{
		if ( isset( $this->interprete ) ) {
			
		} else {
			if ( isset( $_SERVER['toba_proyecto'] ) ) {
				return $_SERVER['toba_proyecto'];
			}
		}
	}

	//-----------------------------------------------------------
	// Primitivas de INTERFACE comunes
	//-----------------------------------------------------------
	
	/**
	*	Interface de seleccion de N usuarios
	*/
	protected function seleccionar_usuarios( instancia $instancia )
	{
		// Decido que usuarios voy a vincular
		/*
		$this->consola->subtitulo( "Asociar USUARIOS" );
		$opcion[0] = "Asociar el usuario 'toba'";
		$opcion[1] = "Asociar TODOS los usuarios de la instancia";
		$opcion[2] = "Mostrar una lista de usuario y SELECCIONAR";
		$ok = $this->consola->dialogo_lista_opciones( $opcion, 'Asociar USUARIOS al proyecto. Seleccione una FORMA de CARGA', false );
		*/		
		$ok = 1;
		switch ( $ok ) {
			case 0:			// Usuario toba (pero..existe?)
				break;	
			case 1:			// Todos
				$datos = $instancia->get_lista_usuarios();
				foreach ( $datos as $dato ) {
					$usuarios[] = $dato['usuario'];
				}
				break;	
			case 2:			// Seleccionar usuarios de una lista
				break;	
		}
		return $usuarios;
	}

	/**
	*	Interface de seleccion de 1 grupo de acceso
	*/
	protected function seleccionar_grupo_acceso( proyecto $proyecto )
	{
		$ga = $proyecto->get_lista_grupos_acceso();
		if ( count( $ga ) == 1 ) {
			return $ga[0]['id'];
		} else {
			// FALTA Seleccion del grupo de ACCESO
			return $ga[0]['id'];
		}
	}

	/**
	*	Permite seleccionar una base de datos
	*/	
	protected function seleccionar_base()
	{
		$titulo = "Seleccionar BASE";
		$bases = array();
		foreach( $this->get_instalacion()->get_lista_bases() as $db ) {
			$param = $this->get_instalacion()->get_parametros_base( $db );
			$bases[ $db ] = implode(',  ',$param);
		}
		if ( count( $bases ) > 0 ) {
			$cabecera_tabla = implode( ', ', array_keys( $param ) );
			$defecto = key($bases);
			return $this->consola->dialogo_lista_opciones( $bases, $titulo, false, $cabecera_tabla, true,
															$defecto, $defecto );
		} else {
			return null;	
		}
	}

	/**
	*	Interface de seleccion de PROYECTOS
	*/
	protected function seleccionar_proyectos( $seleccion_multiple = true, $obligatorio = false )
	{
		$titulo = "Seleccionar PROYECTOS";
		$proyectos = proyecto::get_lista();
		if( count( $proyectos ) > 0 ) {
			$sel = $this->consola->dialogo_lista_opciones( $proyectos, $titulo, true, 'Nombre real del proyecto', 
														$obligatorio, array_keys($proyectos), 'todos');
			//--- Se valida que un proyecto no se incluya dos veces
			//--- Ademas se transpone la matriz, ya que ahora proyecto es una PK
			$seleccion = array();
			foreach ($sel as $path) {
				if (isset($seleccion[$proyectos[$path]])) {
					throw new toba_excepcion('ERROR: Una instancia no soporta contener el mismo proyecto más de una vez');	
				}
				$seleccion[$proyectos[$path]] = $path;
			}
			return $seleccion;
		} else {
			if ( $obligatorio ) {
				throw new toba_excepcion('No hay proyectos definidos');	
			}
			return array();
		}
	}

	/**
	*	Interface de carga de usuarios
	*/
	protected function definir_usuario( $titulo="Crear USUARIO" )
	{
		$form = $this->consola->get_formulario( $titulo );
		$form->agregar_campo( array( 'id' => 'usuario',	'nombre' => 'ID usuario' ) );
		$form->agregar_campo( array( 'id' => 'nombre',	'nombre' => 'Nombre' ) );
		$form->agregar_campo( array( 'id' => 'clave', 	'nombre' => 'Clave' ) );
		return $form->procesar();
	}
	
	protected function mostrar_bases_definidas()
	{
		$a = 0;
		foreach( $this->get_instalacion()->get_lista_bases() as $db ) {
			$base[ $a ]['nombre'] = $db;
			$param = $this->get_instalacion()->get_parametros_base( $db );
			$base[ $a ]['parametros'] = implode(',  ',$param);
			$a++; 
		}
		if ( $a > 0 ) {
			$txt_param = implode( ', ', array_keys( $param ) );
			$this->consola->tabla( $base , array( 'BASE', "Parametros ( $txt_param )" ) );
		} else {
			$this->consola->enter();
			$this->consola->mensaje("ATENCION: No hay BASES definidas.");
		}		
	}
}
?>
