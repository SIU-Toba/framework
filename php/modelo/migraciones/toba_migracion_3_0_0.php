<?php
class toba_migracion_3_0_0 extends toba_migracion
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
				
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	function proyecto__limpiar_datos_viejos_cuadro_vinculos()
	{
		$db = $this->elemento->get_db();
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		//Blanqueo los dos datos de las columnas, si tienen valores viejos puede explotar la importacion de la  operacion.. aun cuando no se usen.
		$sql_up = 'UPDATE apex_objeto_ei_cuadro_columna '
			. 'SET vinculo_carpeta = NULL , vinculo_item = NULL '
			. "WHERE objeto_cuadro_proyecto = $proyecto AND usar_vinculo = 1 "
			. 'AND (vinculo_carpeta IS NOT NULL OR vinculo_item IS NOT NULL);';
		$db->ejecutar($sql_up);	
	}
	
	function proyecto__rip_milonic_menu()
	{
		$db = $this->elemento->get_db();
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		//Actualizo los menues que se puedan haber creado con Milonic
		$sql_up[] = 'UPDATE apex_menu '
			. "SET tipo_menu = 'css' "
			. "WHERE tipo_menu = 'milonic' "
			. "AND proyecto = $proyecto;";
		//Actualizo el menu por defecto de la aplicacion si tiene milonic	
		$sql_up[] = "UPDATE apex_proyecto SET menu='css' "
			. "WHERE menu='milonic' "
			. "AND proyecto = $proyecto;";	
		$db->ejecutar($sql_up);
	}
}
?>