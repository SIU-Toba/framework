<?php
class toba_migracion_2_3_0 extends toba_migracion
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
		
		//Se separa el idenfiticador de clase, del identificador interno de toba.
		$sql[] = 'ALTER TABLE apex_consulta_php ADD COLUMN archivo_clase VARCHAR(60) NULL;';
		$sql[] = 'UPDATE apex_consulta_php SET archivo_clase = clase;';

		//Se agrega el punto de montaje a los moldes del asistente
		$sql[] = 'ALTER TABLE apex_molde_operacion ADD COLUMN punto_montaje INT8 NULL;';
		$sql[] = 'ALTER TABLE apex_molde_operacion ADD CONSTRAINT "apex_molde_operacion_fk_puntos_montaje" 
			     FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje" ("proyecto", "id") ON DELETE NO ACTION ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		$sql[] = 'ALTER TABLE apex_molde_opciones_generacion ADD COLUMN punto_montaje INT8 NULL;';
		$sql[] = 'ALTER TABLE apex_molde_opciones_generacion ADD CONSTRAINT "apex_molde_opciones_generacion_fk_puntos_montaje" 
			     FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje" ("proyecto", "id") ON DELETE NO ACTION ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		$sql[] = 'ALTER TABLE apex_molde_operacion_abms ADD COLUMN punto_montaje INT8 NULL;';
		$sql[] = 'ALTER TABLE apex_molde_operacion_abms ADD CONSTRAINT	"apex_molde_operacion_abms_fk_puntos_montaje" 
			     FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje" ("proyecto", "id") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		$sql[] = 'ALTER TABLE apex_molde_operacion_abms_fila ADD COLUMN punto_montaje INT8 NULL;';
		$sql[] = 'ALTER TABLE apex_molde_operacion_abms_fila ADD CONSTRAINT "apex_molde_operacion_abms_fila_fk_puntos_montaje" 
			     FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje" ("proyecto", "id") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
				
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);		
	}
	
	function proyecto__actualizar_asistentes()
	{
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		
		//Aca tengo que meter un script para cambiar los puntos de montaje de los asistentes?
		$sql = "SELECT id FROM apex_puntos_montaje WHERE etiqueta= 'proyecto' AND proyecto = $proyecto;";		
		$id = $this->elemento->get_db()->consultar_fila($sql);	
		if (! empty($id)) {
			$sql_up = array();
			$sql_up[] = "UPDATE apex_molde_operacion SET punto_montaje = '{$id['id']}' WHERE proyecto = $proyecto AND punto_montaje IS NULL; ";
			$sql_up[] = "UPDATE apex_molde_opciones_generacion SET punto_montaje = '{$id['id']}' WHERE proyecto = $proyecto AND carga_php_include IS NOT NULL AND punto_montaje IS NULL; ";
			$sql_up[] = "UPDATE apex_molde_operacion_abms SET punto_montaje = '{$id['id']}' WHERE proyecto = $proyecto AND cuadro_carga_php_include IS NOT NULL AND punto_montaje IS NULL; ";			
			$sql_up[] = "UPDATE apex_molde_operacion_abms_fila SET punto_montaje = '{$id['id']}' WHERE proyecto = $proyecto AND ef_carga_php_include IS NOT NULL AND punto_montaje IS NULL; ";
			$this->elemento->get_db()->ejecutar($sql_up);			
		}
	}
	
	function proyecto__copiar_punto_acceso()
	{
		$dir_destino = $this->elemento->get_dir(). '/www/';
		$destino_final = toba_manejador_archivos::path_a_plataforma($dir_destino.'servicios.php');
		$origen = toba_dir(). '/php/modelo/template_proyecto/www/servicios.php';
		if (! toba_manejador_archivos::existe_archivo_en_path($destino_final)) {
			$template = file_get_contents($origen);				//Leo el template original
			
			$editor = new toba_editor_texto();
			$editor->agregar_sustitucion( '|__proyecto__|', $this->elemento->get_id());	
			$salida = $editor->procesar( $template );
			file_put_contents($destino_final, $salida, FILE_APPEND);						
		}
	}
}
?>