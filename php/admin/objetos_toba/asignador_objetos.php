<?php

class asignador_objetos
{
	protected $destino;
	protected $origen;
	
	function __construct($origen, $destino)
	{
		$this->origen = $origen;
		$this->destino = $destino;
	}
	
	function asignar()
	{
		switch ($this->destino['tipo']) {
			case 'item':
				$this->asignar_a_item();
				break;
			case 'ci':
				$this->asignar_a_ci();
				break;
			case 'ci_pantalla':
				$this->asignar_a_pantalla_ci();
				break;
			default:
				throw new excepcion_toba("El destinatario del objeto ('{$this->destino['tipo']}') no es ninguno de los predefinidos");
		}
	}
	
	protected function asignar_a_item()
	{
		$sql = "SELECT COALESCE(MAX(orden),0) as maximo
					FROM apex_item_objeto 
					WHERE item='{$this->destino['id']}' AND proyecto='{$this->destino['proyecto']}'
			";
		$res = consultar_fuente($sql,'instancia');
		$orden = $res[0]['maximo'];
		$sql = "INSERT INTO apex_item_objeto 
					(proyecto, item, objeto, orden) VALUES (
						'{$this->destino['proyecto']}', 
						'{$this->destino['id']}', 
						'{$this->origen['id']}', 
						$orden
					)
			";
		ejecutar_sql($sql,'instancia');
	}
	
	protected function asignar_a_ci()
	{
		$sql = "INSERT INTO apex_objeto_dependencias
		  			(proyecto, objeto_consumidor, objeto_proveedor,  identificador)	VALUES (
		  				'{$this->destino['proyecto']}',
		  				'{$this->destino['id']}', 
			  			'{$this->origen['id']}', 
			  			'{$this->destino['id_dependencia']}'
		  			) 
		  		";
		ejecutar_sql($sql,'instancia');
	}
	
	protected function asignar_a_pantalla_ci()
	{
		$this->asignar_a_ci();
		$sql = "UPDATE apex_objeto_ci_pantalla
				SET 
					objetos = COALESCE(objetos || ',','') || '{$this->destino['id_dependencia']}'
				WHERE
					objeto_ci_proyecto = '{$this->destino['proyecto']}' AND
					objeto_ci = '{$this->destino['id']}' AND
					pantalla = {$this->destino['pantalla']}
			";
		ejecutar_sql($sql,'instancia');
	}
	
}



?>