<?php
class toba_migracion_2_4_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();
		
		$sql[] = "UPDATE apex_columna_formato SET descripcion_corta = 'Decimal 2 posiciones (opcionales)' WHERE columna_formato = '9';" ;
		$sql[] = "INSERT INTO  apex_columna_formato (funcion, descripcion_corta, estilo_defecto) VALUES ('decimal_estricto', 'Decimal 2 posiciones (100,00)', '0');" ;
		$sql[] = "INSERT INTO apex_fuente_datos_motor (fuente_datos_motor, nombre, version) VALUES ('sqlserver', 'SQLServer', '2005');";				
		
		$sql[] = 'CREATE TABLE toba_logs.apex_solicitud_web_service
				(
				   proyecto			VARCHAR(15) NOT NULL, 
				   solicitud			BIGINT	NOT NULL, 
				   metodo			TEXT	NULL, 
				   ip				VARCHAR(20)	NULL, 
				   CONSTRAINT "toba_logs.apex_solicitud_web_service_pk" PRIMARY KEY ("solicitud","proyecto" ), 
				   CONSTRAINT "toba_logs.apex_sol_web_service_solicitud_fk" FOREIGN KEY ("solicitud","proyecto" ) REFERENCES "toba_logs"."apex_solicitud" ( "solicitud","proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE
				); ';
		
		$sql[] = 'ALTER TABLE apex_fuente_datos_schemas  ALTER COLUMN fuente_datos TYPE VARCHAR(20);';
				
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	
	function proyecto__agregar_configuracion_ini()
	{
		$destino_ini = $this->elemento->get_dir().'/proyecto.ini';
		if (file_exists($destino_ini)) {
			$editor = new toba_ini($destino_ini);
			$editor->agregar_entrada('proyecto', array('permite_cambio_perfil_funcional' => '0'));
			$editor->agregar_entrada('proyecto', array('mostrar_resize_fuente' => '0'));
			$editor->guardar();
		}
	}
	
}
?>