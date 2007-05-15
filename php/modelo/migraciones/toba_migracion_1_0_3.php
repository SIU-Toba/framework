<?php

class toba_migracion_1_0_3 extends toba_migracion
{
	function instancia__creacion_skins()
	{
		$sql[] = "INSERT INTO apex_estilo (estilo,descripcion) VALUES ('cubos','cubos');";
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	
		function proyecto__estilo_filtro()
		{
			$cant = 0;
			$sql = "
				UPDATE apex_objeto_eventos SET 
					estilo = 'ei-boton-filtrar',
					imagen_recurso_origen = 'apex',
					imagen = 'filtrar.png'
				WHERE
					proyecto = '{$this->elemento->get_id()}' AND
					identificador = 'filtrar'
			";
			$cant += $this->elemento->get_db()->ejecutar($sql);
			
			//--- Actualiza el Cancelar
			$sql = "
				UPDATE apex_objeto_eventos
				SET 
					estilo = 'ei-boton-limpiar',
					imagen_recurso_origen = 'apex',
					imagen = 'limpiar.png',
					etiqueta = '&Limpiar'
				FROM
					apex_objeto as obj
				WHERE
					obj.proyecto = '{$this->elemento->get_id()}' AND
					obj.clase = 'objeto_ei_filtro' AND
					obj.proyecto = apex_objeto_eventos.proyecto AND
					obj.objeto = apex_objeto_eventos.objeto AND
					apex_objeto_eventos.identificador = 'cancelar'
			";
			$cant += $this->elemento->get_db()->ejecutar($sql);
			return $cant;
		}
		
		
		function proyecto__skins()
		{
			$sql = "
				UPDATE apex_proyecto
					SET estilo = 'cubos'
					WHERE 
						proyecto='{$this->elemento->get_id()}' AND
						estilo = 'toba'
			";
			return $this->elemento->get_db()->ejecutar($sql);
		}
}	


?>