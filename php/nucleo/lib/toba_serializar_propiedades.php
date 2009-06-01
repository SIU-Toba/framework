<?php


/**
 * Representa un objeto que es persistible en sesión y desea guardar/recuperar referencias a componentes/recursos toba
 * Para ello utiliza los metodos __wakeup y __sleep de php http://ar.php.net/manual/en/language.oop.magic-functions.php
 */
class toba_serializar_propiedades
{
	protected $_propiedades_toba = array();

	function __sleep()
	{
		$this->_propiedades_toba = array();
		$this->_propiedades_toba['componentes'] = array();
		$props = get_object_vars($this);
		foreach ($props as $nombre => $valor) {
			if ($valor instanceof toba_componente) {
				$excluir[] = $nombre;
				$this->_propiedades_toba['componente'][] = $nombre;
				$this->$nombre = $this->$nombre->get_id();  
			}
		}
		return array_keys($props);
	}
	
	function __wakeup()
	{
		foreach ($this->_propiedades_toba as $tipo => $props) {
			foreach ($props as $prop) {
				if ($tipo == 'componente') {
					$valor = $this->$prop;
					$id = array('componente' => $valor[1], 'proyecto' => $valor[0]);
					$this->$prop = toba_constructor::buscar_runtime($id);			
				}
			}
		}
	}
	
}


?>