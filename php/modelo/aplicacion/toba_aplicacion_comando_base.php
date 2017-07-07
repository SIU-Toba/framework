<?php

use SIU\AraiJsonMigrator\AraiMigratorManager;
use SIU\AraiJsonMigrator\AraiMigrator;
use ProgressBar\Manager;
/**
 * Clase de entrada del usuario, un método por interacción
 * @package Centrales
 * @subpackage Modelo
 */
class toba_aplicacion_comando_base implements toba_aplicacion_comando
{
	/**
	 * toba_aplicacion_modelo_base
	 */
	protected $modelo;
	
	/**
	 * @var toba_mock_proceso_gui
	 */
	protected $manejador_interface;
	
	function set_entorno($manejador_interface, toba_aplicacion_modelo $modelo)
	{
		$this->manejador_interface = $manejador_interface;
		$this->modelo = $modelo;
	}
	
	/**
	 * Crea la base de negocios del proyecto
	 */
	function opcion__instalar($parametros)
	{		
		$base = $this->modelo->get_servidor_defecto();
		if (isset($parametros['--base-nombre'])) {
			$base['base'] = $parametros['--base-nombre'];
		}
		if (isset($parametros['--base-schema'])) {
			$base['schema'] = $parametros['--base-schema'];
		}
		$this->modelo->instalar($base);
	}

	/**
	 * Migra una instalacion previa del proyecto
	 */	
	function opcion__migrar($parametros)
	{
		$desde = $this->modelo->get_version_actual();
		$hasta = $this->modelo->get_version_nueva();
		$this->modelo->migrar($desde, $hasta);
	}

	/**
	 * Crea o actualiza el esquema de auditoria sobre las tablas del negocio
	 * @consola_parametros Opcional: [-f] fuente [-s] Lista de schemas incluidos separada por coma [--force 1] Fuerza eliminacion de todos los triggers
	 */
	function opcion__crear_auditoria($parametros)
	{		
		$mantiene_datos =  $this->manejador_interface->dialogo_simple("¿Desea realizar un backup de los datos de auditoria actuales?", true);		
		$mata_triggers = (isset($parametros['--force']) && ($parametros['--force'] == 1));
		$fuente = (isset($parametros['-f'])) ? trim($parametros['-f']) : null;
		$schemas = array();
		if (isset($parametros['-s'])) {
			if (! isset($parametros['-f'])) {
				throw new toba_error_usuario('Se debe especificar la fuente a la que pertenecen los esquemas con el parametro -f');
			} else {
				
				$schemas = explode(',' , $parametros['-s']);
				array_walk($schemas, 'trim');
			}
		}		
		$this->modelo->crear_auditoria(array(),null, true, $fuente, $schemas, $mantiene_datos, $mata_triggers);
	}	
	
	/**
	 * Borra el esquema de auditoria
	 */
	function opcion__borrar_auditoria()
	{
		$this->modelo->borrar_auditoria();
	}		
	
	/**
	 * Elimina datos de auditoria en un rango de tiempo 
	 */
	function opcion__purgar_auditoria()
	{
		$tiempo = $this->manejador_interface->dialogo_ingresar_texto('Ingrese el periodo de datos a mantener (meses)', false);
		$this->modelo->purgar_auditoria($tiempo);
	}
	
	/**
	 * Hace compatible la estructura del esquema con los cambios en la version 2.4.0
 	 * @consola_separador 1
	 */
	function opcion__migrar_auditoria_2_4()
	{
		$this->modelo->migrar_auditoria_2_4();				//Modifico la estructura de las tablas
		$this->modelo->crear_auditoria();					//Regenero los triggers y SPs
	}

	/**
	 * Arma archivo JSON con las personas y cuentas para importar en arai-usuarios
	 *
	 * @param array $parametros
	 * 		array(
	 * 			'-d' => $this->get_instalacion()->get_dir() . '/usersExportFiles/',
	 *			'-f' => 'usuarios_' . date('YmdHis'),
	 *			'-m' => 'toba',
	 *			'-e' => 'toba@siu.edu.ar',
	 *			'--mascara' => "<apellido>, <nombres>" O una combinacion de patron similar
	 * 		)
	 * @throws Exception
	 */
	function opcion__exportar_usuarios_arai($parametros)
	{
		$parametrosDefault = array(
			'-d' => $this->modelo->get_instalacion()->get_dir() . '/usersExportFiles/',
			'-f' => 'usuarios_' . date('YmdHis'),
			'-m' => 'toba',
			'-e' => 'toba@siu.edu.ar',
		);
		$parametros = array_merge($parametrosDefault, $parametros);

		$this->manejador_interface->mensaje('Creando JSON de personas y cuentas:', false);
		$this->manejador_interface->enter();

		$pathMigration = $parametros['-d'];
		try {
			toba_manejador_archivos::crear_arbol_directorios($pathMigration);
		} catch(toba_error $e) {
			toba_logger::instancia()->error($e->getMessage());
			throw new toba_error("No se pudo crear la carpeta $pathMigration. ¿Problemas de permisos?");
		}
		$tokens = (isset($parametros['--mascara'])) ? $this->recuperar_tokens_indicativos($parametros['--mascara']) : array();
		
		// obtengo los usuarios para generar el JSON
		$datosUsuarios = $this->modelo->getDatosUsuarios($tokens);
		$totalUsuarios = count($datosUsuarios);

		// Inicializo la barra de progreso
		$progressBar = new Manager(0, $totalUsuarios, 120);

		/* @var AraiMigratorManager $araiMigratorManager */
		$araiMigratorManager = new AraiMigratorManager();

		/* @var AraiMigrator $araiMigratorUsuarios */
		$araiMigratorUsuarios = new AraiMigrator('usersExport', utf8_e_seguro('Exportación de usuarios para SIU-Araí.'), $parametros['-m'], $parametros['-e']);

		$cantidadPersonasAgregadas = 0;
		$cantidadCuentasAgregadas = 0;

		// recorro los usuarios
		foreach($datosUsuarios as $datosUsuario) {
			// codifico a UTF-8
			$datosUsuario = array_a_utf8($datosUsuario);

			/* @var Person $person */
			$person = $this->modelo->generatePerson($datosUsuario);

			// Genero la cuenta
			/* @var Account $account */
			$account = $this->modelo->generateAccountApp($datosUsuario, $person);
			if (isset($account)) {
				$araiMigratorUsuarios->addAccount($account);
				$cantidadCuentasAgregadas++;
			} else {
				// Agrego la persona
				$araiMigratorUsuarios->addPerson($person);
				$cantidadPersonasAgregadas++;
			}

			$progressBar->advance();
		}

		// Guardo la informacion en el archivo JSON
		$path = $pathMigration . $parametros['-f'] . '.json';
		$araiMigratorManager->save($path, $araiMigratorUsuarios);

		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("--------------------------------------------------------------------", false);
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("Resumen: ($path)", false);
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("--------", false);
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("Total de usuarios: $totalUsuarios", false);
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("Cantidad de personas exportadas: $cantidadPersonasAgregadas", false);
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("Cantidad de cuentas exportadas: $cantidadCuentasAgregadas", false);
		$this->manejador_interface->enter();
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("--------------------------------------------------------------------", false);
		$this->manejador_interface->enter();
	}
	
	protected function recuperar_tokens_indicativos($values)
	{
		$tokens = array();
		$er = '/^<NOMBRE[S]?>(.*)<APELLIDO[S]?>$/i';
		$er2 =  '/^<APELLIDO[S]?>(.*)<NOMBRE[S]?>$/i';
		
		$coincidencias = preg_match($er, $values, $matches);
		$coincidencias2 = preg_match($er2, $values, $matches2);
		/*echo $coincidencias . var_export($matches, true);
		echo $coincidencias2 . var_export($matches2, true);*/
		switch (true) {
			case ($matches !== false && $coincidencias == 1) :
				$tokens['separador'] = $matches[1];
				$tokens['apellido'] = 1;
				$tokens['nombre'] = 0;				
				break;
			case ($matches2 !== false && $coincidencias2 == 1) :
				$tokens['separador'] = $matches2[1];
				$tokens['apellido'] = 0;
				$tokens['nombre'] = 1;						
				break;
			default: 
				throw new toba_error('No se indico un patron correcto');
		}
		//var_dump($tokens);
		return $tokens;
	}
}

?>