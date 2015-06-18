<?php
require_once('comando_toba.php');

class comando_instalacion_silenciosa extends comando_toba
{	
	function recuperar_contenido_archivo($nombre)
	{
		$resultado = '';
		if (file_exists($nombre)) {
			$resultado = file_get_contents($nombre);
		}
		return $resultado;
	}
	
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------
	
	/**
	 * Ejecuta una instalacion completa del framework para desarrollar un nuevo proyecto
	 * @consola_parametros Opcionales: [-d 'iddesarrollo'] [-t 0| 1] [-n 'nombre inst'] [-h 'ubicacion bd'] [-p 'puerto'] [-u 'usuario bd'] [-b nombre bd] [-c 'archivo clave bd'] [-k 'archivo clave toba']
	 * @gtk_icono instalacion.png
	 */
	function opcion__instalar()
	{		
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_release('_');
		$alias = '/'.'toba_'.toba_modelo_instalacion::get_version_actual()->get_release();
		
		//--- Verificar instalacion
		$param = $this->get_parametros();
		
		//--- Borra la instalacion anterior??
		if (toba_modelo_instalacion::existe_info_basica()) {
			toba_modelo_instalacion::borrar_directorio();
		}
		
		//--- Crea la INSTALACION		
		$id_desarrollo = $this->definir_id_grupo_desarrollo($param);
		$tipo_instalacion = $this->definir_tipo_instalacion_produccion($param);
		$nombre = $this->definir_nombre_instalacion($param);
		
		toba_modelo_instalacion::crear($id_desarrollo, $alias, $nombre, $tipo_instalacion);		
		$id_instancia = ($tipo_instalacion == '1') ? 'produccion' : $this->get_entorno_id_instancia(true);		
		
		//--- Crea la definicion de bases
		$base = $nombre_toba;
		if (! $this->get_instalacion()->existe_base_datos_definida($base)) {
			$datos = array(
				'motor' => 'postgres7',
				'profile' => $this->definir_profile_motor($param),
				'usuario' => $this->definir_usuario_motor($param),
				'clave' => $this->definir_clave_motor($param),
				'base' => $this->definir_base_motor($param),
				'puerto' => $this->definir_puerto_motor($param),
				'encoding' => 'LATIN1',
				'schema' => $id_instancia
			);			
			$this->get_instalacion()->agregar_db($base, $datos);
		}			
		//--- Crea la instancia
		$proyectos = array();	
		toba_modelo_instancia::crear_instancia($id_instancia, $base, $proyectos);

		//-- Carga la instancia
		$instancia = $this->get_instancia($id_instancia);
		$instancia->cargar(true);	//Si no es interactivo, crea siempre la BD

		//--- Pido el password para el usuario por defecto
		$pwd = $this->definir_clave_usuario_admin($param);

		//--- Vincula un usuario a todos los proyectos y se instala el proyecto				
		$instancia->agregar_usuario( 'toba', 'Usuario Toba', $pwd);				
		$instancia->exportar_local();
		
		//--- Crea los nuevos alias
		$instancia->crear_alias_proyectos();
		
		$release = toba_modelo_instalacion::get_version_actual()->get_release();
		$instal_dir = toba_modelo_instalacion::dir_base();
		if (toba_manejador_archivos::es_windows()) {
			if (isset($_SERVER['USERPROFILE'])) {
				$path = $_SERVER['USERPROFILE'];
			} else {
				$path = $instal_dir;
			}
			$path .= "\\entorno_toba_$release.bat";
			$bat = "@echo off\n";
			$bat .= "set TOBA_DIR=".toba_dir()."\n";
			$bat .= "set TOBA_INSTANCIA=$id_instancia\n";
			$bat .= "set TOBA_INSTALACION_DIR=$instal_dir\n";		
			$bat .= "set PATH=%PATH%;%TOBA_DIR%/bin\n";
			$bat .= "echo Entorno cargado.\n";
			$bat .= "echo Ejecute 'toba' para ver la lista de comandos disponibles.\n";
			file_put_contents($path, $bat);
		} else {
			$path = $instal_dir;
			$path .= "/entorno_toba.env";
			$bat = "export TOBA_DIR=".toba_dir()."\n";
			$bat .= "export TOBA_INSTANCIA=$id_instancia\n";
			$bat .= "export TOBA_INSTALACION_DIR=$instal_dir\n";				
			$bat .= 'export PATH="$TOBA_DIR/bin:$PATH"'."\n";
			$bat .= "echo \"Entorno cargado.\"\n";
			$bat .= "echo \"Ejecute 'toba' para ver la lista de comandos disponibles.\"\n";
			file_put_contents($path, $bat);
			chmod($path, 0755);
		}
	}
	
	/**
	* Crea una instalación básica.
	* @gtk_icono nucleo/agregar.gif
	*/
	function opcion__crear()
	{
		if( ! toba_modelo_instalacion::existe_info_basica()) {
			$param = $this->get_parametros();
			$id_grupo_desarrollo = self::definir_id_grupo_desarrollo($param);
			$alias = self::definir_alias_nucleo($param);
			$tipo_instalacion = $this->definir_tipo_instalacion_produccion($param);
			$nombre = $this->definir_nombre_instalacion($param);
			toba_modelo_instalacion::crear( $id_grupo_desarrollo, $alias, $nombre, $tipo_instalacion);
		}
	}
	
	/**
	 * Cambia los permisos de los archivo para que el usuario Apache cree directorios y pueda crear y leer carpetas navegables 
	 * @consola_parametros [-u usuario apache, se asume www-data] [-g grupo de usuarios, no se asume ninguno]
	 * @gtk_icono  password.png
	 */
	function opcion__cambiar_permisos()
	{
		//Si es produccion dar permisos solo a apache, sino a usuario y grupo
		$subject = $this->get_instalacion()->es_produccion() ? "u" : "ug";
		$param = $this->get_parametros();
		$grupo = isset($param['-g']) ? $param['-g'] : null;
		$usuario = isset($param['-u']) ? $param['-u'] : 'www-data';
		$toba_dir = toba_dir();
		$comandos = array(
			array("chown -R $usuario $toba_dir/www", "Archivos navegables comunes:\n"),
			array("chmod -R $subject+rw $toba_dir/www", ''),			
			array("chown -R $usuario $toba_dir/instalacion", "Archivos de configuración:\n"),
			array("chmod -R $subject+rw $toba_dir/instalacion", ''),			
			array("chown -R $usuario $toba_dir/temp", "Archivos temporales comunes:\n"),
			array("chmod $subject+rw $toba_dir/temp", '')
		);
		foreach (toba_modelo_instalacion::get_lista_proyectos() as $proyecto) {
			$id_proyecto = basename($proyecto);
			$comandos[] = array("chown -R $usuario $proyecto/www", "Archivos navegables de $id_proyecto:\n");
			$comandos[] = array("chmod -R $subject+rw $proyecto/www", '');
			$comandos[] = array("chown -R $usuario $proyecto/temp", "Archivos temporales de $id_proyecto:\n");
			$comandos[] = array("chmod -R $subject+rw $proyecto/temp", '');
		}		
		foreach ($comandos as $comando) {
			exec($comando[0]);
		}		
		if (isset($grupo)) {
			$comando = "chgrp -R $grupo $toba_dir";
			exec($comando);
			$comando = "chmod -R g+rw $toba_dir";
			exec($comando);
		}
	}

	/**
	 * Elimina los logs locales de la instalacion, instancias y proyectos contenidos
	 */
	function opcion__eliminar_logs()
	{
		$this->get_instalacion()->eliminar_logs();
	}
	
	/**
	 * Cambia el número de desarrollador y deja las instancias listas
	 */
	function opcion__cambiar_id_desarrollador()
	{
		$id_grupo_desarrollador = $this->definir_id_grupo_desarrollo();
		$this->get_instalacion()->set_id_desarrollador($id_grupo_desarrollador);		
	}
	

	/**
	 * Migra la instalación de versión. 
	 * @consola_parametros Opcionales: [-d 'desde']  [-h 'hasta'] [-R 0|1].
	 * @gtk_icono convertir.png
	 */
	function opcion__migrar_toba()
	{
		$instalacion = $this->get_instalacion();
		//--- Parametros
		$param = $this->get_parametros();
		$desde = isset($param['-d']) ? new toba_version($param['-d']) : $instalacion->get_version_anterior();
		$hasta = isset($param['-h']) ? new toba_version($param['-h']) : $instalacion->get_version_actual();
		$recursivo = (!isset($param['-R']) || $param['-R'] == 1);

		$versiones = $desde->get_secuencia_migraciones($hasta);
		if (empty($versiones)) {
			return ;
		} 		
		$instalacion->migrar_rango_versiones($desde, $hasta, $recursivo);
	}		
	
	/**
	* Incluye en el archivo toba.conf las configuraciones de alias definidas en instalacion.ini e instancia.ini
	*/
	function opcion__publicar()
	{
		if (! $this->get_instalacion()->esta_publicado()) {
			$this->get_instalacion()->publicar();
		} 
	}	
	
	/**
	* Quita del archivo toba.conf los alias de la instalacion y de los proyectos
	*/
	function opcion__despublicar()
	{
		if ($this->get_instalacion()->esta_publicado()) {
			$this->get_instalacion()->despublicar();
		} 
	}		
	
	//----------------------------------------------------------------------------------------------------------------------------------//
	protected function get_parametros()
	{
		$parametros = parent::get_parametros();
		if (isset($parametros['--archivo_configuracion'])) {
			$resultado = $this->parsear_yml($parametros['--archivo_configuracion']);				//Queda ver cual de los nodos tomaria	
		} else {
			$resultado = $parametros;
		}
		return $resultado;		
	}	
	
	protected function parsear_yml($archivo) 
	{
		$contenido = sfYAML::load($archivo);												//Esta mas actualizado que el de PHP, que ademas requiere PECL
		//$contenido = yaml_parse_file($archivo);
		return $contenido;
	}
	
	//-------------------------------------------------------------
	// Interface
	//-------------------------------------------------------------
	protected function recuperar_dato_y_validez($param, $nombre_parametro)
	{
		$resultado = null;
		do {			
			$ind = current($nombre_parametro);
			$es_invalido = (! isset($param[$ind]));
			if (! $es_invalido) {
				$resultado = $param[$ind];
			}
		} while ($es_invalido && next($nombre_parametro) !== false);
		
		return array('invalido' => $es_invalido, 'resultado' => $resultado);
	}	
		
	protected function definir_alias_nucleo($param)
	{		
		$resultado = $param['--alias-nucleo'];
		if ( $resultado == '' ) {
			return '/toba';
		} else {
			return '/'.$resultado;	
		}		
	}
	
	/**
	*	Consulta al usuario el ID del grupo de desarrollo
	*/
	protected function definir_id_grupo_desarrollo($param)
	{
		$nombre_parametro = array( '-d', '--id-desarrollador', 'toba-id-desarrollador');		
		do {			
			$ind = current($nombre_parametro);
			$es_invalido = (! isset($param[$ind]));
			if (! $es_invalido) {
				$id_desarrollo = $param[$ind];
				if (! is_numeric($id_desarrollo) || $id_desarrollo < 0) {
					$es_invalido = true;
				}				
			}
		} while ($es_invalido && next($nombre_parametro) !== false);		
		if ($es_invalido) {									//Si aun sigue faltando el parametro, defaulteo.
			toba::logger()->error('Se selecciono 0 cero como id de desarrollador, ya que uno válido no fue provisto');
			return 0;			
		}		
		return (int)$id_desarrollo;	
	}
	
	protected function definir_tipo_instalacion_produccion($param)
	{
		$nombre_parametro = array('-t', '-tipo-instalacion-produccion', 'toba-tipo-instalacion');
		$result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
		if ($result['invalido']) {
			toba::logger()->error('Se selecciono tipo de instalación de producción, ya que el tipo no fue provisto');
			return 1;
		}
		return ($result['resultado']) ? 1: 0;		
	}
	
	protected function definir_nombre_instalacion($param)
	{
		$nombre_parametro = array('-n', '--nombre-instalacion', 'toba-nombre-instalacion');
		$result = $this->recuperar_dato_y_validez($param, $nombre_parametro);		
		if ($result['invalido']) {
			toba::logger()->error("Se selecciono el texto 'Sin nombre'  como nombre para la instalación, ya que uno válido no fue provisto");
			return 'Sin nombre';
		}		
		return $result['resultado'];
	}
	
	protected function definir_profile_motor($param)
	{
		$nombre_parametro = array('-h', '--base-profile', 'toba-base-profile');
		$result = $this->recuperar_dato_y_validez($param, $nombre_parametro);		
		if ($result['invalido']) {
			toba::logger()->error("Se selecciono 127.0.0.1 como ubicación por defecto, ya que una válida no fue provista");
			return '127.0.0.1';
		}		
		return $result['resultado'];
	}
	
	protected function definir_usuario_motor($param)
	{
		$nombre_parametro = array('-u', '--base-usuario', 'toba-base-usuario');
		$result = $this->recuperar_dato_y_validez($param, $nombre_parametro);		
		if ($result['invalido']) {
			toba::logger()->error("Se selecciono postgres como nombre de usuario por defecto, ya que uno válido no fue provisto");
			return 'postgres';
		}		
		return $result['resultado'];
	}	
	
	protected function definir_base_motor($param)
	{
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_release('_');
		$nombre_parametro = array('-b', '--base-nombre', 'toba-base-nombre');
		$result = $this->recuperar_dato_y_validez($param, $nombre_parametro);		
		if ($result['invalido']) {
			toba::logger()->error("Se selecciono '$nombre_toba' como nombre de bd, ya que uno válido no fue provisto");
			return $nombre_toba;
		}		
		return $result['resultado'];
	}
	
	protected function definir_puerto_motor($param)
	{
		$nombre_parametro = array('-p', '--base-puerto', 'toba-base-puerto');
		$result = $this->recuperar_dato_y_validez($param, $nombre_parametro);		
		if ($result['invalido']) {
			toba::logger()->error("Se selecciono el puerto 5432 por defecto, ya que uno válido no fue provisto");
			return '5432';
		}		
		return $result['resultado'];
	}	
	
	protected function definir_clave_motor($param)
	{
		$nombre_parametro = array('-c', '--archivo-clave-bd', 'toba-archivo-clave-bd');
		do {
			$ind = current($nombre_parametro);
			$es_invalido = (! isset($param[$ind]));
			if (! $es_invalido) {
				$clave = $this->recuperar_contenido_archivo($param[$ind]);			
			}			
		} while($es_invalido && next($nombre_parametro) !== false);
		
		if ($es_invalido) {
			toba::logger()->error("Se selecciono postgres como clave por defecto, ya que una válida no fue provista");
			return 'postgres';
		}		
		return $clave;
	}	
	
	protected function definir_clave_usuario_admin($param)
	{
		$nombre_parametro = array('-k', '--archivo-clave-admin', 'toba-archivo-clave-admin');
		$esto_es_produccion = $this->get_instalacion()->es_produccion();
		do {
			$ind = current($nombre_parametro);
			$es_invalido = (! isset($param[$ind]));
			if (! $es_invalido) {
				$pwd = $this->recuperar_contenido_archivo($param[$ind]);
				if ($esto_es_produccion) {											//Verifico que la clave recuperada posea ciertos requisitos.
					try {
						toba_usuario::verificar_composicion_clave($pwd, apex_pa_pwd_largo_minimo);			
					} catch (toba_error_pwd_conformacion_invalida $e) {
						$es_invalido = true;
					}
				}				
			}			
		} while($es_invalido && next($nombre_parametro) !== false);
		
		if ($es_invalido) {
			$randompass = toba_usuario::generar_clave_aleatoria(apex_pa_pwd_largo_minimo);
			toba::logger()->error("Se selecciono una clave aleatoria, ya que una válida no fue provista");
			return $randompass;
		} 
		return $pwd;
	}	
}
?>
