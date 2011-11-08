<?php
class toba_migracion_2_2_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();
		
		//Cambio el tipo de la columna estilo del ef y quito la FK
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef DROP CONSTRAINT apex_ei_f_ef_fk_estilo;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef DROP COLUMN estilo;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN estilo text;';
		//Agrego una columna estilo al ei_filtro
		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD COLUMN estilo text;';
		
		//Cambio el tipo de la columna estilo del ef y quito la FK
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna DROP CONSTRAINT apex_obj_ei_cuadro_fk_estilo;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna RENAME estilo TO estilo_temp;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN estilo text;';
		
		//Actualizo la columna basado en el valor CSS de la otra tabla
		$sql[] = 'UPDATE apex_objeto_ei_cuadro_columna SET estilo = (SELECT css FROM apex_columna_estilo WHERE columna_estilo = estilo_temp);';
		
		//Elimino la columna temporal
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna DROP COLUMN estilo_temp;';
		
		//Agrego al ei_filtro una columna de tipo hora y fecha_hora
		$sql[] = "INSERT INTO apex_objeto_ei_filtro_tipo_col (tipo_col, descripcion, proyecto) VALUES ('hora', 'Hora', 'toba');";
		$sql[] = "INSERT INTO apex_objeto_ei_filtro_tipo_col (tipo_col, descripcion, proyecto) VALUES ('fecha_hora', ' Fecha y Hora', 'toba');";		
		
		//Agrego tablas para los servicios web
		$sql[] = "CREATE SEQUENCE apex_mapeo_rsa_kp_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;";
		$sql[] = 'CREATE TABLE apex_mapeo_rsa_kp
				(
				cod_mapeo			int8	DEFAULT nextval(\'"apex_mapeo_rsa_kp_seq"\'::text) NOT NULL,
				proyecto				VARCHAR(15) NOT NULL, 
				servicio_web			VARCHAR(50) NOT NULL,
				id					TEXT NOT NULL,		--Hash
				pub_key				TEXT NOT NULL,		--ruta archivo
				anulada				SMALLINT NOT NULL DEFAULT 0,
				CONSTRAINT "apex_mapeo_rsa_kp_pk" PRIMARY KEY("cod_mapeo","proyecto", "servicio_web"),
				CONSTRAINT "apex_mapeo_rsa_kp_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto"("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
				CONSTRAINT "apex_mapeo_rsa_kp_fk_item" FOREIGN KEY ("servicio_web", "proyecto") REFERENCES "apex_item"("item", "proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
				);';
		
		// Agregar registros por defecto del proyecto que se está migrando
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
	}
}
?>
