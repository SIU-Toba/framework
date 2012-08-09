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
		
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
	
}
?>