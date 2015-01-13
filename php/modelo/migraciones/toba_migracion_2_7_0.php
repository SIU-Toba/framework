<?php
class toba_migracion_2_7_0 extends toba_migracion
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
		
		$sql[] = 'ALTER TABLE apex_usuario_pwd_usados  DROP CONSTRAINT apex_usuario_pwd_usados_uk;';
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	function proyecto__actualizar_pms()
	{
		$db = $this->elemento->get_db();
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		
		$sql = "SELECT id FROM apex_puntos_montaje WHERE etiqueta= 'proyecto' AND proyecto = $proyecto;";		
		$id = $this->elemento->get_db()->consultar_fila($sql);	
		
		if (! empty($id)) {
			$sql_up = array();
			$sql_up[] = 'UPDATE apex_item SET punto_montaje = '. $id['id'] . " WHERE punto_montaje IS NULL  AND proyecto =  $proyecto AND carpeta = 0;" ;			
			$this->elemento->get_db()->ejecutar($sql_up);	
		}
	
	}
}
?>