<?
require_once('comando_toba.php');

class comando_instalacion extends comando_toba
{
	static function get_info()
	{
		return "Administracion de la INSTALACION";
	}

	function mostrar_observaciones()
	{
		$this->consola->mensaje("Directorio BASE: " . toba_dir() );
		$this->consola->enter();
	}
	
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Muestra informacion de la instalacion.
	*/
	function opcion__info()
	{
		if ( instalacion::existe_info_basica() ) {
			$this->consola->enter();
			//VERSION
			$this->consola->lista(array(instalacion::get_version_actual()->__toString()), "VERSION");
			// INSTANCIAS
			$instancias = instancia::get_lista();
			if ( $instancias ) {
				$this->consola->lista( $instancias, 'INSTANCIAS' );
			} else {
				$this->consola->enter();
				$this->consola->mensaje( 'ATENCION: No existen INSTANCIAS definidas.');
			}
			// BASES
			$this->mostrar_bases_definidas();
			// ID de grupo de DESARROLLO
			$grupo = $this->get_instalacion()->get_id_grupo_desarrollo();
			if ( isset ( $grupo ) ) {
				$this->consola->lista( array( $grupo ), 'ID grupo desarrollo' );
			} else {
				$this->consola->enter();
				$this->consola->mensaje( 'ATENCION: No esta definido el ID del GRUPO de DESARROLLO.');
			}
			// PROYECTOS
			$proyectos = proyecto::get_lista();
			if ( $proyectos ) {
				$lista_proyectos = array();
				foreach ($proyectos as $dir => $id) {
					$lista_proyectos[] = "$id ($dir)";
				}
				$this->consola->lista( $lista_proyectos, 'PROYECTOS (sólo en la carpeta por defecto)' );
			} else {
				$this->consola->enter();
				$this->consola->mensaje( 'ATENCION: No existen PROYECTOS definidos.');
			}
		} else {
			$this->consola->enter();
			$this->consola->mensaje( 'La INSTALACION no ha sido inicializada.');
		}
	}

	/**
	*	Agrega una BASE en la instalacion. [-d 'id_base']
	*/
	function opcion__agregar_db()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos_definida( $def ) ) {
			throw new toba_excepcion( "Ya existe una base definida con el ID '$def'");
		}
		$form = $this->consola->get_formulario("Definir una nueva BASE de DATOS");
		$form->agregar_campo( array( 'id' => 'motor', 	'nombre' => 'MOTOR' ) );
		$form->agregar_campo( array( 'id' => 'profile',	'nombre' => 'HOST/PROFILE' ) );
		$form->agregar_campo( array( 'id' => 'usuario', 'nombre' => 'USUARIO' ) );
		$form->agregar_campo( array( 'id' => 'clave', 	'nombre' => 'CLAVE' ) );
		$form->agregar_campo( array( 'id' => 'base', 	'nombre' => 'BASE' ) );
		$datos = $form->procesar();
		$this->get_instalacion()->agregar_db( $def, $datos );
	}

	/**
	*	Elimina una BASE en la instalacion. [-d 'id_base']
	*/
	function opcion__eliminar_db()
	{
		$i = $this->get_instalacion();
		$def = $this->get_id_base_actual();
		if ( $i->existe_base_datos_definida( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("DEFINICION: $def");
			$this->consola->lista_asociativa( $i->get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la definicion?") ) {
				$i->eliminar_db( $def );
			}
		} else {
			throw new toba_excepcion( "NO EXISTE una base definida con el ID '$def'");
		}
	}
	
	/**
	*	Crea una BASE DEFINIDA en el motor. [-d 'id_base']
	*/
	function opcion__crear_base()
	{
		$def = $this->get_id_base_actual();
		if( $this->get_instalacion()->existe_base_datos( $def ) !== true ) {
			$this->get_instalacion()->crear_base_datos( $def );
		} else {
			throw new toba_excepcion( "Ya EXISTE una base '$def' en el MOTOR");
		}
	}

	/**
	*	Elimina una BASE DEFINIDA existente dentro del motor. [-d 'id_base']
	*/
	function opcion__eliminar_base()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("BASE de DATOS: $def");
			$this->consola->lista_asociativa( $this->get_instalacion()->get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la BASE de DATOS?") ) {
				$this->get_instalacion()->borrar_base_datos( $def );
			}
		} else {
			throw new toba_excepcion( "NO EXISTE una base '$def' en el MOTOR");
		}
	}

	/**
	*	Chequea la conexion con una base. [-d 'id_base']
	*/
	function opcion__test_conexion()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos( $def ) ) {
			$this->consola->mensaje('Conexion OK!');
		} else {
			$this->consola->error("No es posible conectarse a '$def'");
		}
	}

	/**
	*	Crea una instalacion.
	*/
	function opcion__crear()
	{
		if( ! instalacion::existe_info_basica() ) {
			$this->consola->titulo( "Configurando INSTALACION en: " . instalacion::dir_base() );
			$id_grupo_desarrollo = self::definir_id_grupo_desarrollo();
			$alias = self::definir_alias_nucleo();
			instalacion::crear( $id_grupo_desarrollo, $alias );
			$this->consola->enter();
			$this->consola->mensaje("La instalacion ha sido inicializada");
			$this->consola->mensaje("Para definir bases de datos, utilize el comando 'toba instalacion agregar_db -d [nombre_base]'");
		} else {
			$this->consola->enter();
			$this->consola->mensaje( 'Ya existe una INSTALACION.' );
			$this->consola->enter();
		}
	}

	/**
	 * Migra la instalación actual. [-d 'desde']  [-h 'hasta'] [-R 0|1].
	 * -d se asume la versión de toba de la última instancia cargada.
	 * -h se asume la versión de toba del código actual.
	 * -R asume 1, esto quiere decir que migra también todas las instancias y proyectos que encuentre, sino solo lo propio de la instalación
	 */
	function opcion__migrar()
	{
		$instalacion = $this->get_instalacion();
		//--- Parametros
		$param = $this->get_parametros();
		$desde = isset($param['-d']) ? new version_toba($param['-d']) : $instalacion->get_version_anterior();
		$hasta = isset($param['-h']) ? new version_toba($param['-h']) : $instalacion->get_version_actual();
		$recursivo = (!isset($param['-R']) || $param['-R'] == 1);
		//$verbose = (isset($param['-V']));
		
		if ($recursivo) {
			$texto_recursivo = ", sus instancias y proyectos";
		}
		$desde_texto = $desde->__toString();
		$hasta_texto = $hasta->__toString();
		$this->consola->titulo("Migración de la instalación actual".$texto_recursivo." desde la versión $desde_texto hacia la $hasta_texto.");

		$versiones = $desde->get_secuencia_migraciones($hasta);
		if (empty($versiones)) {
			$this->consola->mensaje("No es necesario ejecutar una migración entre estas versiones");
			return ;
		} 
		
		//$this->consola->lista($versiones, "Migraciones disponibles");
		//$this->consola->dialogo_simple("");		
		$instalacion->migrar_rango_versiones($desde, $hasta, $recursivo);
	}	
	
	//-------------------------------------------------------------
	// Interface
	//-------------------------------------------------------------

	/**
	*	Consulta al usuario el ID del grupo de desarrollo
	*/
	protected function definir_id_grupo_desarrollo()
	{
		$this->consola->subtitulo('Definir el ID del grupo de desarrollo');
		$this->consola->mensaje('Este codigo se utiliza para permitir el desarrollo paralelo de equipos '.
								'de trabajo geograficamente distribuidos.');
		$this->consola->enter();
		$resultado = $this->consola->dialogo_ingresar_texto( 'ID Grupo', false );
		if ( $resultado == '' ) {
			return null;	
		} else {
			return $resultado;	
		}
	}

	protected function definir_alias_nucleo()
	{
		$this->consola->enter();		
		$this->consola->subtitulo('Definir el nombre del ALIAS del núcleo Toba');
		$this->consola->mensaje('Este alias se utiliza para consumir todo el contenido navegable de Toba');
		$this->consola->enter();
		$resultado = $this->consola->dialogo_ingresar_texto( 'Nombre del Alias (por defecto "toba")', false );
		if ( $resultado == '' ) {
			return 'toba';
		} else {
			return $resultado;	
		}
		
	}
	
	/**
	*	Determina sobre que base definida en 'info_bases' se va a trabajar
	*/
	private function get_id_base_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
			return $param['-d'];
		} else {
			throw new toba_excepcion("Es necesario indicar el ID de la BASE a utilizar. Utilice el modificador '-d'");
		}
	}
	

}
?>
