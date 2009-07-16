<?php
class toba_migracion_1_5_0 extends toba_migracion
{
		function instancia__cambios_estructura()
		{
			$sql = array();
			//------------- Nueva tabla para guardar el checksum de los proyectos, relacionado a la sincro_svn ---------
			$sql[] = "CREATE TABLE			apex_checksum_proyectos
							(
								checksum						varchar(200)	NOT NULL,
								proyecto							varchar(15)		 NOT NULL,
								--ultima_modificacion		timestamp(0) without	time zone	DEFAULT current_timestamp NOT NULL,
								CONSTRAINT 'apex_checksum_proyectos_pk' PRIMARY KEY ('proyecto'),
								CONSTRAINT 'apex_checksum_proyectos_fk'	FOREIGN KEY ('proyecto') REFERENCES 'apex_proyecto' ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;
							);";
			$this->elemento->get_db()->ejecutar($sql);
		}
}
?>
