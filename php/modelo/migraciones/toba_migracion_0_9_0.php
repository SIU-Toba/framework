<?php

class toba_migracion_0_9_0 extends toba_migracion
{

	/**
	*	Consulta al usuario el ID del grupo de desarrollo
	*/
	protected function definir_id_grupo_desarrollo()
	{
		$this->manejador_interface->subtitulo('Definir el ID del grupo de desarrollo');
		$this->manejador_interface->mensaje('Este codigo se utiliza para permitir el desarrollo paralelo de equipos '.
								'de trabajo geograficamente distribuidos.');
		$this->manejador_interface->enter();
		$resultado = $this->manejador_interface->dialogo_ingresar_texto( 'ID Grupo', false );
		if ( $resultado == '' ) {
			return null;	
		} else {
			return $resultado;	
		}
	}
	
	//------------------------------------------------------------------------
	//-------------------------- INSTALACION --------------------------
	//------------------------------------------------------------------------
	
	
	/**
	 * La definición del archivo instancias.php se mueve hacia archivos y directorios
	 * en el directorio instalacion dentro de $toba_dir
	 */
	function instalacion__construir_inis()
	{
		// Estos defines se necesitan aca porque no se incluye el archivo de funciones planas db.php
		define("apex_db_motor",0);
		define("apex_db_profile",1);// host-dsn
		define("apex_db_usuario",2);
		define("apex_db_clave",3);
		define("apex_db_base",4);
		define("apex_db_con",5);
		define("apex_db_link",6);
		define("apex_db",7);
		define("apex_db_link_id",8);
		
		if( ! is_file( toba_dir() . '/php/instancias.php' ) ) {
			throw new toba_error("No existe el archivo 'instancias.php'. No es posible realizar la conversion.");
		}		
		require_once('instancias.php');
	
		//*** 0) Creo la carpeta INSTALACION
		toba_modelo_instalacion::crear_directorio();
	
		//*** 1) BASES
	
		$bases_registradas = array();
		$this->manejador_interface->mensaje( "Migrar la definicion de BASES. (php/instancias.php)" );
		if( ! toba_modelo_instalacion::existe_info_bases() ) {
			foreach( $instancia as $i => $datos ) {
			    $base['motor']= $datos[apex_db_motor];
			    $base['profile'] = $datos[apex_db_profile];
			    $base['usuario'] = $datos[apex_db_usuario];
			    $base['clave'] = $datos[apex_db_clave];
			    $base['base'] = $datos[apex_db_base];
				$bases_registradas[] = $i;
				$bases[$i] = $base;
			}
			toba_modelo_instalacion::crear_info_bases( $bases );
			$this->manejador_interface->mensaje("la definicion de BASES se encuentra ahora en '" . 
																	toba_modelo_instalacion::archivo_info_bases() . "'");	
		} else {
			$this->manejador_interface->mensaje( "ya existe una archivo '" . 
																	toba_modelo_instalacion::archivo_info_bases() . "'" );
		}
	
		// *** 2) CLAVES
	
		$this->manejador_interface->mensaje( "Migrar la definicion de CLAVES. (php/instancias.php)" );
		if( ! toba_modelo_instalacion::existe_info_basica() ) {
			$this->manejador_interface->enter();
			$id_grupo_desarrollo = self::definir_id_grupo_desarrollo();
			toba_modelo_instalacion::crear_info_basica( apex_clave_get, apex_clave_db, $id_grupo_desarrollo );
		} else {
			$this->manejador_interface->mensaje( "ya existe una archivo '" . toba_modelo_instalacion::archivo_info_basica() . "'" );
		}
	
		// *** 3) INSTANCIAS
	
		$this->manejador_interface->enter();
		$this->manejador_interface->subtitulo( "Migrar INSTANCIAS toba" );
		$this->manejador_interface->mensaje( "Indique que BASES son INSTANCIAS toba"); 

		//Busco la lista de proyectos de la instalacion
		$proyectos = toba_modelo_proyecto::get_lista();
		if ( ! in_array( 'toba', $proyectos ) ) {
			$proyectos[] = 'toba';
		}		
	
		//Creo las instancias, preguntando en cada caso
		//Existe la opcion de conectarse a la base y preguntar si existe la tabla 'apex_objeto',
		//pero puede ser que por algun motivo la base no este online y sea una instancia
		foreach( $instancia as $i => $datos ) {
			if( $datos[apex_db_motor] == 'postgres7' ) {
				$this->manejador_interface->separador("BASE: $i");
				$this->manejador_interface->lista($datos, 'Parametros CONEXION');
				$this->manejador_interface->enter();
				if ( $this->manejador_interface->dialogo_simple("La base '$i' corresponde a una INSTANCIA TOBA?") ) {
					if( toba_modelo_instancia::existe_carpeta_instancia( $i ) ) {
						$this->manejador_interface->error("No es posible crearla instancia '$i'");
						$this->manejador_interface->mensaje("Ya exite una instancia: $i"); 	
					} else {
						toba_modelo_instancia::crear_instancia( $i, $i, $proyectos );
					}
				}
			}
		}
		$this->manejador_interface->mensaje("Ya es posible borrar el archivo 'toba_dir/php/instancias.php'");
		$this->manejador_interface->separador("");
	}
	
	//---------------------------------------------------------------
	//-------------------------- INSTANCIA --------------------------
	//---------------------------------------------------------------
	
	/**
	 * El nuevo esquema de migración necesita que el proyecto tenga una versión toba asociada
	 */
	function instancia__agregado_version_proyecto()
	{
		$sql = array();
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN toba_version varchar(15);";
		//--- Se asume que los proyectos estan en la primer version de toba con este esquema
		$sql[] = "UPDATE apex_proyecto SET toba_version='0.8.3' WHERE toba_version IS NULL";
		$this->elemento->get_db()->ejecutar($sql);
	}

	function instancia__cambios_estructura()
	{
		$sql = array();
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN salida_impr_html_c varchar(1);";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN salida_impr_html_a varchar(1);";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN version_toba varchar(10);";		
		$sql[] = "ALTER TABLE apex_objeto_dependencias ADD COLUMN orden smallint;";
		$sql[] = "ALTER TABLE apex_usuario ADD COLUMN autentificacion varchar(10);";
		$sql[] = "ALTER TABLE apex_usuario ADD COLUMN clave2 varchar(128);";
		$sql[] = "UPDATE apex_usuario SET clave2 = clave;";
		$sql[] = "ALTER TABLE apex_usuario DROP COLUMN clave;";
		$sql[] = "ALTER TABLE apex_usuario ADD COLUMN clave varchar(128);";
		$sql[] = "UPDATE apex_usuario SET clave = clave2;";
		$sql[] = "ALTER TABLE apex_usuario DROP COLUMN clave2;";
		$sql[] = "INSERT INTO apex_solicitud_tipo (solicitud_tipo, descripcion, descripcion_corta, icono) VALUES ('web', 'Solicitud WEB', 'Solicitud WEB', 'solic_browser.gif');";
		
		//Eventos
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion varchar(1)";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_imphtml_debug smallint";		
		//Datos Relacion
		$sql[] = "ALTER TABLE apex_objeto_datos_rel ADD COLUMN debug smallint;";
		$sql[] = "ALTER TABLE apex_objeto_datos_rel ADD COLUMN sinc_susp_constraints smallint;";
		$sql[] = "ALTER TABLE apex_objeto_datos_rel ADD COLUMN sinc_orden_automatico smallint;";
		//Objeto_ei_esquema
		$sql[] = "ALTER TABLE apex_objeto_esquema ADD COLUMN dirigido smallint;";
		
		$this->elemento->get_db()->ejecutar($sql);
		$this->elemento->get_db()->ejecutar_archivo( toba_dir() . '/php/modelo/ddl/pgsql_a22_permisos.sql' );		
	}
	

	/**
	*	Las claves pasan a encriptarse con md5 (los passwords planos siguen funcionando)
	*/
	function instancia__cambio_claves_encriptadas()
	{
		if ( $this->manejador_interface->dialogo_simple('¿Desea encriptar las passwords de usuarios con MD5? (los ABMs propios de edición de usuarios pueden llegar a no funcionar)') ) {
			$sql = "UPDATE apex_usuario SET clave=md5(clave), autentificacion='md5' 
					WHERE autentificacion IS NULL OR autentificacion='plano'";
			$this->elemento->get_db()->ejecutar($sql);
		}
	}
	
	//--------------------------------------------------------------
	//-------------------------- PROYECTO --------------------------
	//--------------------------------------------------------------
	
	/**
	 * Los items "modernos de toba" (>= 0.8) que utilizan un CI y ocpcionalmente un CN y que
	 * utilizan alguno de los patrones predefinidos para manejarlos se migran a un nuevo
	 * tipo de solicitud (solicitud_web en lugar de la obsoleta solicitud_browser), este cambio
	 * se debe a que el nucelo de toba sufrio una reestructuracion muy grande recayendo
	 * gran parte sobre la solicitud y no se quiere romper la compatilibilidad con los items viejos
	 */
	function proyecto__cambio_solicitud_web()
	{
		$sql = "UPDATE apex_item
				SET	solicitud_tipo='web'
				WHERE
					proyecto='{$this->elemento->get_id()}' AND 
					solicitud_tipo='browser' AND
					actividad_patron IN ('CI', 'CI_POPUP', 'ci', 'ci_cn_popup', 'generico_ci_cn')
		";
		$this->elemento->get_db()->ejecutar($sql);
	}
}
?>
