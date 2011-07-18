<?php

class toba_registro_conflicto_constraints extends toba_registro_conflicto
{
	/**
	 * @var toba_error_db
	 */
	protected $db_error;
	
	function  __construct($registro, $db_error)
	{
		parent::__construct($registro);
		$this->numero = 2;
		$this->db_error = $db_error;
		
		$sql_state = $this->db_error->get_sqlstate();
		switch ($sql_state) {
			case 'db_23503':
				$this->tipo = toba_registro_conflicto::warning;
				break;
			default: 
				$this->tipo = toba_registro_conflicto::fatal;
		}
		
	}

	function get_descripcion()
	{
		$tabla = $this->registro->get_tabla();
		$sql_conflictivo = $this->registro->to_sql();
		$sql_state = $this->db_error->get_sqlstate();
		$mensaje = $this->db_error->get_mensaje_motor();

		//Creo un mensaje orientativo sobre el conflicto
		switch ($this->tipo) {
			case toba_registro_conflicto::warning: $mensaje_final = "[W:$this->numero] ";
										     break;
			case toba_registro_conflicto::fatal: $mensaje_final = "[F:$this->numero] "; 
		}
		
		$mensaje_final .= "Error de constraints en la tabla $tabla.\n";
		if ($this->descripcion_componente !== '') {
			$mensaje_final .= "Error en un componente {$this->descripcion_componente}.\n";		
		}		
		switch ($sql_state) {		
			case 'db_23503':
				$mensaje_final .= " Existe un error de foreign keys, si cree que se trata de un problema de temporalidad ejecute el comando en modo transaccional. \n";
				break;
			case 'db_26505':
				$mensaje_final .= " Hay un error de unique keys. \n";
				break;
			default:	
				$mensaje_final .= " El error no fue reconocido por el importador. \n";
		}		
		
		$mensaje_final .= "Postgres dijo: $mensaje.\n El sql conflictivo es: $sql_conflictivo";		
		return $mensaje_final;
	}
}

?>
