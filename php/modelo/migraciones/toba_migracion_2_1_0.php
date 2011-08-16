<?php
class toba_migracion_2_1_0 extends toba_migracion
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
		$sql[] = "INSERT INTO apex_elemento_formulario (elemento_formulario, padre, descripcion, proyecto, obsoleto) VALUES('ef_editable_hora', 'ef_editable', 'hora', 'toba', '0');";

		$sql[] = 'CREATE SEQUENCE apex_usuario_pregunta_secreta_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;';
		$sql[] = 'CREATE TABLE apex_usuario_pregunta_secreta
				(
				   cod_pregunta_secreta	int8	DEFAULT nextval(\'"apex_usuario_pregunta_secreta_seq"\'::text) NOT NULL,
				   usuario				varchar(60) NOT NULL, 
				   pregunta			text NOT NULL, 
				   respuesta			text NOT NULL, 
				   activa				smallint NOT NULL DEFAULT 1, 
				   CONSTRAINT apex_usuario_pregunta_secreta_pk PRIMARY KEY (cod_pregunta_secreta), 
				   CONSTRAINT apex_usuario_pregunta_secreta_fk_usuario FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE
				) ;';
		
		$sql[] = 'ALTER TABLE apex_item ALTER actividad_accion TYPE text;';
		$sql[] = 'ALTER TABLE apex_item ADD COLUMN punto_montaje int8 NULL;';
		$sql[] = 'ALTER TABLE apex_item ADD FOREIGN KEY (proyecto, punto_montaje) REFERENCES apex_puntos_montaje (proyecto, id) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE;';
			
		// Agregar registros por defecto del proyecto que se está migrando
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
	}
}
?>
