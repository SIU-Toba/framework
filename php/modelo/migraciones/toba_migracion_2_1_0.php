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

		// Agregar registros por defecto del proyecto que se está migrando
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
	}
}
?>
