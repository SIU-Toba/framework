<?php

class toba_migracion_1_2_0 extends toba_migracion
{
	/*
		Migración pendiente:
			En apex_proyecto hay referencias a item sin FK, cuando se migra los ids a numericos estos quedan sin actualizar
	
		Cambios en el codigo del nucleo:
			uso de los valores de las secuencias.
	
	*/
	
	protected $sql_migracion;
	
	function ini()
	{
		//1- Cambio los constraints para que la migracion de datos se ejecute en cascada		
		$sql = array();
		$sql[] = 'ALTER  TABLE ONLY apex_item_msg DROP CONSTRAINT  "apex_item_msg_fk_item" CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_item_nota DROP CONSTRAINT  "apex_item_nota_fk_item" CASCADE;'; 
		$sql[] = 'ALTER  TABLE ONLY apex_molde_operacion DROP CONSTRAINT	"apex_molde_operacion_fk_item" CASCADE;'; 
		$sql[] = 'ALTER  TABLE ONLY apex_item DROP CONSTRAINT	"apex_item_fk_padre"	 CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_item_info DROP CONSTRAINT	"apex_item_info_fk_item"  CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_item_objeto DROP CONSTRAINT	"apex_item_consumo_obj_fk_item"  CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_usuario_grupo_acc_item DROP CONSTRAINT "apex_usu_item_fk_item" CASCADE;';
		
		$sql[] = 'ALTER  TABLE apex_item_msg 			ADD CONSTRAINT 	"apex_item_msg_fk_item" FOREIGN KEY ("item", "item_proyecto") REFERENCES "apex_item" ("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item_nota 			ADD CONSTRAINT	"apex_item_nota_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_molde_operacion 	ADD CONSTRAINT	"apex_molde_operacion_fk_item" FOREIGN	KEY ("item", "proyecto") REFERENCES	"apex_item"	("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item 				ADD CONSTRAINT	"apex_item_fk_padre"	FOREIGN KEY	("padre_proyecto","padre")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE CASCADE DEFERRABLE INITIALLY	IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item_info 			ADD CONSTRAINT	"apex_item_info_fk_item" FOREIGN	KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item_objeto 		ADD CONSTRAINT	"apex_item_consumo_obj_fk_item" FOREIGN KEY ("proyecto","item") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_usuario_grupo_acc_item ADD CONSTRAINT	"apex_usu_item_fk_item"		FOREIGN KEY	("proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		
		$sql[] = 'ALTER  TABLE apex_proyecto ADD CONSTRAINT	"apex_proyecto_item_is"		FOREIGN KEY	("proyecto","item_inicio_sesion") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_proyecto ADD CONSTRAINT	"apex_proyecto_item_ps"		FOREIGN KEY	("proyecto","item_pre_sesion") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_proyecto ADD CONSTRAINT	"apex_proyecto_item_ss"		FOREIGN KEY	("proyecto","item_set_sesion") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_objeto_eventos ADD CONSTRAINT	"apex_objeto_eventos_fk_accion_vinculo"		FOREIGN KEY	("proyecto","accion_vinculo_item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_objeto_ei_cuadro_columna ADD CONSTRAINT	"apex_obj_ei_cuadro_fk_accion_vinculo"		FOREIGN KEY	("objeto_cuadro_proyecto","vinculo_item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_objeto_ei_formulario_ef ADD CONSTRAINT	"apex_ei_f_ef_fk_accion_vinculo"		FOREIGN KEY	("popup_proyecto","popup_item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';		
		$this->sql_migracion = $sql;		
	}
	
	
	function instancia__cambios_estructura()
	{
		//-- Dimensiones
		$archivo = toba_dir().'/php/modelo/ddl/pgsql_a06_tablas_dimensiones.sql';
		$this->elemento->get_db()->ejecutar_archivo($archivo);
		
		//-- Nuevo ei-filtro
		$archivo = toba_dir().'/php/modelo/ddl/pgsql_a14_componente_ei_filtro.sql';
		$this->elemento->get_db()->ejecutar_archivo($archivo);
		
		//-- Cosas sueltas		
		$sql = array();
		$sql[] = "ALTER TABLE apex_estilo 				ADD COLUMN paleta						varchar";
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN no_imprimir_efs_sin_estado	smallint DEFAULT 0";
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN resaltar_efs_con_estado		smallint DEFAULT 0";
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN filas_agregar_abajo			smallint DEFAULT 0";
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN filas_agregar_texto			varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN filas_borrar_en_linea		smallint DEFAULT 0";	
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN filas_ordenar_en_linea		smallint DEFAULT 0";	
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN edit_expreg				varchar(255)";		
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN check_ml_toggle			smallint DEFAULT 0";		
		$sql[] = "ALTER TABLE apex_elemento_formulario 	ADD COLUMN es_seleccion					smallint DEFAULT 0";
		$sql[] = "ALTER TABLE apex_elemento_formulario 	ADD COLUMN es_seleccion_multiple		smallint DEFAULT 0";
		$sql[] = "ALTER TABLE apex_item					ADD COLUMN exportable					smallint DEFAULT 0";
		$sql[] = "ALTER TABLE apex_objeto_cuadro		ADD COLUMN exportar_paginado			smallint DEFAULT 0";
		
		//-- Perfil de Datos (no se puede ejecutar el archivo completo porque ya existia una tabla en la 1.1.0)
		$sql[] = '
				CREATE SEQUENCE apex_usuario_perfil_datos_dims_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
				CREATE TABLE apex_usuario_perfil_datos_dims
				(	
					proyecto						varchar(15)		NOT NULL,
					usuario_perfil_datos			int4			NOT NULL,
					dimension						int4			NOT NULL,
					elemento						int4			DEFAULT nextval(\'"apex_usuario_perfil_datos_dims_seq"\'::text) NOT NULL,
					clave							varchar			NULL,
					CONSTRAINT	"apex_usuario_perfil_datos_dims_pk" PRIMARY	KEY ("elemento")
				);
		';
		
		//-- Restricciones funcionales (no se puede ejecutar el archivo completo porque ya existia una tabla en la 1.1.0)
		$sql[] ='
				CREATE SEQUENCE apex_restriccion_funcional_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
				CREATE TABLE apex_restriccion_funcional
				(	
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional			int4				DEFAULT nextval(\'"apex_restriccion_funcional_seq"\'::text) NOT NULL,
					descripcion						varchar(255)		NULL,
					CONSTRAINT	"restriccion_funcional_pk" PRIMARY	KEY ("proyecto", "restriccion_funcional")
				);
				
				CREATE TABLE apex_grupo_acc_restriccion_funcional
				(	
					proyecto							varchar(15)		NOT NULL,
					usuario_grupo_acc					varchar(30)		NOT NULL,
					restriccion_funcional				int4			NOT NULL,
					CONSTRAINT	"apex_grupo_acc_restriccion_funcional_pk" 		PRIMARY	KEY ("usuario_grupo_acc","restriccion_funcional","proyecto")
				);
				
				CREATE TABLE apex_restriccion_funcional_ef
				(
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional				int4				NOT NULL,
					item							varchar(60)		NOT NULL,
					objeto_ei_formulario_fila		int4				NOT NULL,
					objeto_ei_formulario			int4				NOT NULL,
					no_visible						smallint			NULL,
					no_editable						smallint			NULL,
					CONSTRAINT	"apex_restriccion_funcional_ef_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","objeto_ei_formulario_fila")
				);
				
				CREATE TABLE apex_restriccion_funcional_pantalla
				(
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional				int4				NOT NULL,
					item							varchar(60)		NOT NULL,
					pantalla						int4				NOT NULL,
					objeto_ci						int4				NOT NULL,
					no_visible						smallint			NULL,
					CONSTRAINT	"apex_restriccion_funcional_pantalla_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","pantalla")
				);
				
				CREATE TABLE apex_restriccion_funcional_evt
				(
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional				int4				NOT NULL,
					item							varchar(60)		NOT NULL,
					evento_id						int4				NOT NULL,
					no_visible						smallint			NULL,
					CONSTRAINT	"apex_restriccion_funcional_evt_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","evento_id")
				);
				
				CREATE TABLE apex_restriccion_funcional_ei
				(
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional				int4				NOT NULL,
					item							varchar(60)		NOT NULL,
					objeto							int4				NOT NULL,
					no_visible						smallint			NULL,
					CONSTRAINT	"apex_restriccion_funcional_ei_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","objeto")
				);
				
				CREATE TABLE apex_restriccion_funcional_cols
				(
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional				int4				NOT NULL,
					item							varchar(60)		NOT NULL,
					objeto_cuadro					int4				NOT NULL,
					objeto_cuadro_col				int4				NOT NULL,
					no_visible						smallint			NULL,
					CONSTRAINT	"apex_restriccion_funcional_cols_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","objeto_cuadro_col")
				);
				
				CREATE TABLE apex_restriccion_funcional_filtro_cols
				(
					proyecto						varchar(15)			NOT NULL,
					restriccion_funcional			int4				NOT NULL,
					item							varchar(60)			NOT NULL,
					objeto_ei_filtro_col			int4				NOT NULL,
					objeto_ei_filtro				int4				NOT NULL,
					no_visible						smallint			NULL,
					CONSTRAINT	"apex_restriccion_funcional_filtro_col_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","objeto_ei_filtro_col")
				);
		';

		// Nuevo asistente para importar una operación
		$sql[] = '
			CREATE TABLE apex_molde_operacion_importacion
			(
				proyecto  							varchar(255)	NOT NULL,
				molde								int4			NOT NULL, 
				origen_item							varchar(60)		NOT NULL,
				origen_proyecto						varchar(30)		NULL,
				CONSTRAINT  "apex_molde_operacion_imp_pk" 		PRIMARY KEY ("proyecto","molde")
			);
		';
		
		$this->elemento->get_db()->ejecutar($sql);			
	}	
	
	function instancia__preparar_modelo_para_mysql()
	{
		try {
			$this->elemento->get_db()->ejecutar($this->sql_migracion);		
		} catch (toba_error_db $e) {
			if ($e->get_sqlstate() == 'db_23503') {
				$mensaje_motor = $e->get_mensaje_motor();
				$mensaje = "ERROR al intentar migrar el proyecto. Existe entre los metadatos una referencia a un item inexistente. Por favor corrija esta referencia usando la versión de toba 1.1.0 y reintente la migración.\n";
				$mensaje .= "\n A continuación el mensaje de error de la base:\n".$e->get_mensaje_motor();
				throw new toba_error_def($mensaje);
			} else {
				throw $e;
			}
		}
	}

	function proyecto__migrar_datos_para_mysql()
	{
		/*
			Hacer que el ID de los items sea numerico
		*/
		$sql = "SELECT item FROM apex_item WHERE proyecto = '{$this->elemento->get_id()}'";
		$datos = $this->elemento->get_db()->consultar($sql);
		$renombrar = array();
		$sqls_items = array();
		foreach($datos as $dato) {
			if( (string)(int)$dato['item'] != $dato['item'] ) {
				//-- Cambio en ID del item en la base --
				$id_viejo = $dato['item'];
				$id_nuevo = $this->elemento->get_db()->recuperar_nuevo_valor_secuencia('apex_item_seq');
				$id_nuevo++;
				$sql = "UPDATE apex_item SET item = '$id_nuevo' WHERE item = '$id_viejo' AND proyecto='{$this->elemento->get_id()}';";
				$sqls_items[] = $sql;
				$this->elemento->get_db()->ejecutar($sql);
				
				//-- Cambio el ID del item en el codigo
				$renombrar[$id_viejo] = $id_nuevo;
			}
		}
		
		//Renombrar los .php que consumieron estos ids
		$editor = new toba_editor_archivos();
		foreach ($renombrar as $viejo => $nuevo) {
			$msg = "El id del item '$viejo' pasa a ser '$nuevo'.";
			toba_logger::instancia()->warning($msg);
			$this->manejador_interface->mensaje($msg);				
			
			$viejo = str_replace('/', '\/', $viejo);
			$editor->agregar_sustitucion("/\\'$viejo\\'/", $nuevo);
			$editor->agregar_sustitucion("/\\\"$viejo\\\"/", $nuevo);
		}
		
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
		
		
		//-- Generar un .sql para poder migrar las instalaciones
		$migracion = "BEGIN\n";
		$migracion .= "SET CONSTRAINTS ALL DEFERRED\n";
		$migracion .= "\n\n--------- Se crean las constraints con UPDATE CASCADE\n";
		$migracion .= implode("\n", $this->sql_migracion);
		$migracion .= "\n\n--------- Se migran los ids de los items\n";		
		$migracion .= implode("\n", $sqls_items);
		$migracion .= "COMMIT\n";
		file_put_contents($this->elemento->get_dir().'/migracion_instalaciones_1.2.0.sql', $migracion);
		
		/*
			Renumerar las que poseen 0
			* completar las que no tienen registros con un registro fantasma
				o hacer que las secuencias comiencen si o si desde 1...
		*/
		/*foreach( toba_db_secuencias::get_lista() as $secuencia => $tabla) {
			$sql = "SELECT '$secuencia' as seq, min({$tabla['campo']}) as minimo FROM {$tabla['tabla']}\n";
			//echo $sql;
			$datos = $this->elemento->get_db()->consultar_fila($sql);
			if($datos['minimo'] === 0){
				echo "TABLA: {$tabla['tabla']} CAMPO {$tabla['campo']}\n";
			}
		}*/
		//throw new toba_error('no');
	}
	
	
	/**
	 * En los toba anteriores existia un registro vacio en apex_usuario_perfil_datos por proyecto. Ahora esa tabla se va a usar realmente, los datos viejos no sirven
	 */
	function proyecto__borrar_perfiles_datos_actuales()
	{
		$sql = "DELETE FROM apex_usuario_perfil_datos WHERE proyecto = '{$this->elemento->get_id()}'";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
	/**
	 * El ei_filtro pasa a ser un formulario con un par de campos seteados
	 */
	function proyecto__migrar_filtro_viejo()
	{
		//-- Todos los actuales filtros, se les pone un par de flags en el form.
		$sql = "
			UPDATE apex_objeto_ut_formulario SET no_imprimir_efs_sin_estado = 1, resaltar_efs_con_estado = 1
			FROM  apex_objeto
			WHERE 
					apex_objeto_ut_formulario.objeto_ut_formulario			= apex_objeto.objeto
				AND apex_objeto_ut_formulario.objeto_ut_formulario_proyecto = apex_objeto.proyecto
				AND apex_objeto.clase='toba_ei_filtro' 
				AND apex_objeto_ut_formulario.objeto_ut_formulario_proyecto = '{$this->elemento->get_id()}'
		";
		$cant = $this->elemento->get_db()->ejecutar($sql);
		
		//-- Se cambia la clase
		$sql = "UPDATE apex_objeto SET clase='toba_ei_formulario' WHERE clase='toba_ei_filtro' AND proyecto = '{$this->elemento->get_id()}'";
		$cant += $this->elemento->get_db()->ejecutar($sql);

		//-- Se cambian las extensiones de código y los hint de tipos 
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion("|toba_ei_filtro|" ,"toba_ei_formulario");          
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);		
		return $cant;
	}
	
	/**
	 * Los campos sinc_orden_automatico y sinc_susp_constraints no se estaban leyendo desde los metadatos
	 * Por lo tanto por si algun proyecto destildo/tildo alguno de estos campos, se va a cambiar el estado actual
	 * al comportamiento que tenian las versiones anteriores (el primero en '1' y el segundo en '0')
	 */
	function proyecto__datos_relacion_bug_campos()
	{
		$sql = "UPDATE apex_objeto_datos_rel 
				SET sinc_orden_automatico=1,
					sinc_susp_constraints=0
				WHERE
					proyecto = '{$this->elemento->get_id()}'
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
}

?>
