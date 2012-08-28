<?php

class asignador_objetos
{
	protected $destino;
	protected $origen;
	
	/**
	*	@param array Claves: objeto
	*	@param array Claves: tipo,objeto, proyecto,id_dependencia, pantalla, max_filas, min_filas
	*/
	function __construct($origen, $destino)
	{
		$this->origen = $origen;
		$this->destino = $destino;
	}
	
	function asignar()
	{
		toba::db('instancia')->abrir_transaccion();
		try{
			switch ($this->destino['tipo']) {
				case 'toba_item':
					$this->asignar_a_item();
					break;
				case 'toba_ci':
					$this->asignar_a_ci();
					break;
				case 'toba_cn':
					$this->asignar_a_cn();
					break;
				case 'toba_ci_pantalla':
					$this->asignar_a_pantalla_ci();
					break;
				case 'toba_datos_relacion':
					$this->asignar_a_datos_relacion();
					break;
				default:
					throw new toba_error("El destinatario del objeto ('{$this->destino['tipo']}') no es ninguno de los predefinidos");
			}
			toba::db('instancia')->cerrar_transaccion();
		}catch(Exception $e){
			toba::db('instancia')->abortar_transaccion();
			throw $e;
		}
	}
	
	protected function asignar_a_item()
	{
		$sql = 'SELECT COALESCE(MAX(orden),0) as maximo
					FROM apex_item_objeto 
					WHERE item='.quote($this->destino['objeto']).' AND proyecto='.quote($this->destino['proyecto']);
		$res = consultar_fuente($sql);
		$orden = $res[0]['maximo'];
		$sql = "INSERT INTO apex_item_objeto 
					(proyecto, item, objeto, orden) VALUES (
						'{$this->destino['proyecto']}', 
						'{$this->destino['objeto']}', 
						'{$this->origen['objeto']}', 
						$orden
					)
			";
		ejecutar_fuente($sql, 'instancia');
	}
	
	protected function asignar_a_ci()
	{
		$sql = "INSERT INTO apex_objeto_dependencias
		  			(proyecto, objeto_consumidor, objeto_proveedor,  identificador)	VALUES (
		  				'{$this->destino['proyecto']}',
		  				'{$this->destino['objeto']}', 
			  			'{$this->origen['objeto']}', 
			  			'{$this->destino['id_dependencia']}'
		  			) 
		  		";
		ejecutar_fuente($sql, 'instancia');
		//Aca obtengo la secuencia de la dependencia y la retorno.
		$id = toba::db('instancia')->recuperar_secuencia('apex_objeto_dep_seq');
		return $id;
	}

	protected function asignar_a_cn()
	{
		$sql = "INSERT INTO apex_objeto_dependencias
		  			(proyecto, objeto_consumidor, objeto_proveedor,  identificador)	VALUES (
		  				'{$this->destino['proyecto']}',
		  				'{$this->destino['objeto']}', 
			  			'{$this->origen['objeto']}', 
			  			'{$this->destino['id_dependencia']}'
		  			) ;	";
		ejecutar_fuente($sql, 'instancia');

		//Aca obtengo la secuencia de la dependencia y la retorno.
		$id = toba::db('instancia')->recuperar_secuencia('apex_objeto_dep_seq');
		return $id;
	}
		
	protected function asignar_a_pantalla_ci()
	{
		$dep_id = $this->asignar_a_ci();
		$sql = "INSERT INTO apex_objetos_pantalla( proyecto, pantalla, objeto_ci, dep_id, orden)
		VALUES ('{$this->destino['proyecto']}', '{$this->destino['pantalla']}',
						  '{$this->destino['objeto']}', '$dep_id',
							(SELECT  COALESCE(max(orden) + 1, 0)
							FROM apex_objetos_pantalla
							WHERE proyecto = ".quote($this->destino['proyecto']).' AND
								objeto_ci = '.quote($this->destino['objeto']).' AND
								pantalla = '.quote($this->destino['pantalla']).' )
						);';
		ejecutar_fuente($sql, 'instancia');
	}
	
	protected function asignar_a_datos_relacion()
	{
		$sql = "INSERT INTO apex_objeto_dependencias
		  			(proyecto, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b)
		  		VALUES (
		  			'{$this->destino['proyecto']}',
		  			'{$this->destino['objeto']}', 
		  			'{$this->origen['objeto']}', 
		  			'{$this->destino['id_dependencia']}',
		  			'{$this->destino['min_filas']}',
		  			'{$this->destino['max_filas']}'
	  			) 
	  		";
		ejecutar_fuente($sql, 'instancia');		
	}
}
?>