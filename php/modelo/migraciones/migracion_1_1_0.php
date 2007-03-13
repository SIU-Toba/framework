<?php
require_once('migracion_toba.php');

class migracion_1_1_0 extends migracion_toba
{

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