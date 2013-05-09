<?php
ini_set('max_execution_time', 0);

require_once('consola/comando.php');

/**
	@todo: - Seleccion adecuada de Usuarios y Grupo de acceso
			- Tendria que existir un esquema para extender un comando
				por ejemplo, despues de crear una instancia, un proyecto puede querer
				agregar mas tablas a la misma
*/
class comando_toba extends comando
{
	private $interprete; 
	protected $id_proyecto_actual;
	protected $id_instancia_actual;

	function __construct( $manejador_interface, $interprete = null )
	{
		parent::__construct( $manejador_interface );
		$this->interprete = $interprete;
	}

	function get_info_extra()
	{
		return '';
	}
	
	//-----------------------------------------------------------
	// Acceso a los SUJETOS sobre los que actuan los comandos
	//-----------------------------------------------------------

	/**
	*	Devuelve una referencia al la INSTALACION
	* @return toba_modelo_instalacion
	*/
	protected function get_instalacion()
	{
		return toba_modelo_catalogo::instanciacion()->get_instalacion( $this->consola );
	}
	
	/**
	*	Devuelve una referencia a la INSTANCIA
	* @return toba_modelo_instancia
	*/
	protected function get_instancia($id=null)
	{
		if (!isset($id)) {
			$id = $this->get_id_instancia_actual();
		}
		return toba_modelo_catalogo::instanciacion()->get_instancia(	$id,
																$this->consola );
	}

	/**
	*	Devuelve una referencia al PROYECTO 
	* @return toba_modelo_proyecto
	*/
	protected function get_proyecto($id_proy = null)
	{
		if (!isset($id_proy)) {
			$id_proy = $this->get_id_proyecto_actual();
		}
		return toba_modelo_catalogo::instanciacion()->get_proyecto( 	$this->get_id_instancia_actual(),
																		$id_proy,
																		$this->consola );
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	protected function get_nucleo()
	{
		return toba_modelo_catalogo::instanciacion()->get_nucleo( $this->consola );
	}

	/**
	*	Devuelve una referencia al CONVERSOR
	*/
	protected function get_conversor()
	{
		return toba_modelo_catalogo::instanciacion()->get_conversor( $this->get_id_instancia_actual(), $this->consola );
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
		} elseif (isset($this->id_instancia_actual)) {
			$id = $this->id_instancia_actual;
		} else {
			$id = $this->get_entorno_id_instancia();
		}
		
		if ( $obligatorio && is_null( $id ) ) {
			throw new toba_error("Es necesario definir una INSTANCIA. Utilice el modificador '-i' o defina la variable de entorno 'toba_instancia'");
		}
		return $id;
	}
	
	function set_id_instancia_actual($id)
	{
		$this->id_instancia_actual = $id;
	}

	/**
	*	Describe el parametro INSTANCIA
	*/
	protected function get_info_parametro_instancia()
	{
		$ei = $this->get_entorno_id_instancia();
		$valor_i = isset( $ei ) ?  $ei : 'No definida';
		$this->consola->mensaje("[-i id_instancia] Asume el valor de la variable de entorno 'TOBA_INSTANCIA': $valor_i");
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
			//--- Lo pregunta explicitamente y recuerda el seteo
			if (!isset($this->id_proyecto_actual)) {
				 $this->id_proyecto_actual = $this->consola->dialogo_ingresar_texto('Id. del Proyecto', true);				 
			}
			return $this->id_proyecto_actual;
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
		$this->consola->mensaje("[-p id_proyecto] Asume el valor de la variable de entorno 'TOBA_PROYECTO': $valor_p");
	}

	//-----------------------------------------------------------
	// Acceso al entorno
	//-----------------------------------------------------------
	
	/**
	*	Acceso a la variable de entorno 'toba_instancia'
	*/
	protected function get_entorno_id_instancia($obligatorio = false)
	{
		if ( isset( $this->interprete ) ) {
			
		} else {
			if ( isset( $_SERVER['TOBA_INSTANCIA'] ) ) {
				return $_SERVER['TOBA_INSTANCIA'];
			}
		}
	}

	/**
	*	Acceso a la variable de entorno 'toba_proyecto'
	*/
	protected function get_entorno_id_proyecto($obligatorio = false)
	{
		if ( isset( $this->interprete ) ) {
			
		} else {
			if ( isset( $_SERVER['TOBA_PROYECTO'] ) ) {
				return $_SERVER['TOBA_PROYECTO'];
			}
		}
	}

	//-----------------------------------------------------------
	// Primitivas de INTERFACE comunes
	//-----------------------------------------------------------
	
	/**
	*	Interface de seleccion de N usuarios
	*/
	protected function seleccionar_usuarios( toba_modelo_instancia $instancia )
	{
		// Decido que usuarios voy a vincular
		/*
		$this->consola->subtitulo( "Asociar USUARIOS" );
		$opcion[0] = "Asociar el usuario 'toba'";
		$opcion[1] = "Asociar TODOS los usuarios de la instancia";
		$opcion[2] = "Mostrar una lista de usuario y SELECCIONAR";
		$ok = $this->consola->dialogo_lista_opciones( $opcion, 'Asociar USUARIOS al proyecto. Seleccione una FORMA de CARGA', false );
		*/		
		$usuarios = array();
		$ok = 1;
		if ($instancia->get_instalacion()->es_produccion()) {
			$datos = $instancia->get_usuarios_administradores($this->id_proyecto_actual);
		} else {
			$datos = $instancia->get_lista_usuarios();
		}
		switch ( $ok ) {
			case 0:			// Usuario toba (pero..existe?)
				break;	
			case 1:			// Todos				
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
	protected function seleccionar_grupo_acceso( toba_modelo_proyecto $proyecto )
	{
		//TODO: Seleccion del grupo de ACCESO, por ahora prefiere el grupo ADMIN
		return $proyecto->get_grupo_acceso_admin();
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
		$titulo = $seleccion_multiple ? "Seleccionar PROYECTOS" : "Seleccionar PROYECTO";
		$proyectos = toba_modelo_proyecto::get_lista();
		if( count( $proyectos ) > 0 ) {
			$sel = $this->consola->dialogo_lista_opciones( $proyectos, $titulo, $seleccion_multiple, 'Nombre real del proyecto', 
														$obligatorio, array_keys($proyectos), 'todos');
			//--- Se valida que un proyecto no se incluya dos veces
			//--- Ademas se transpone la matriz, ya que ahora proyecto es una PK
			$seleccion = array();
			if ($seleccion_multiple) {
				foreach ($sel as $path) {
					if (isset($seleccion[$proyectos[$path]])) {
						throw new toba_error('ERROR: Una instancia no soporta contener el mismo proyecto más de una vez');	
					}
					$seleccion[$proyectos[$path]] = $path;
				}
			} else {
				//--Arreglo id,path
				return array($proyectos[$sel], $sel);
			}
			return $seleccion;			
		} else {
			if ( $obligatorio ) {
				throw new toba_error('No hay proyectos definidos');	
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
