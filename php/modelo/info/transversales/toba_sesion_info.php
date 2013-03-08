<?php
class toba_sesion_info extends toba_elemento_transversal_info
{
	
	function ini()
	{
		$proyecto = quote($this->_id['proyecto']);
		$sql = "SELECT
					pm_sesion,
					sesion_subclase,
					sesion_subclase_archivo
				FROM apex_proyecto
				WHERE proyecto = $proyecto;";

		$this->_datos['_info'] = toba::db()->consultar_fila($sql);
		toba::logger()->debug($sql);
	}

	function  set_subclase($nombre, $archivo, $pm)
	{
		$proyecto = quote($this->_id['proyecto']);
		$archivo = quote($archivo);
		$nombre = quote($nombre);
		$pm = quote($pm);
		
		$sql = "UPDATE apex_proyecto
					SET		sesion_subclase = $nombre,
						sesion_subclase_archivo = $archivo,
						pm_sesion = $pm
					WHERE	proyecto = $proyecto;";

		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}

	function get_clase_nombre()
	{
		return 'toba_sesion'; 
	}

	function get_clase_archivo()
	{
		return 'nucleo/lib/toba_sesion.php';
	}

	function get_punto_montaje()
	{
		return $this->_datos['_info']['pm_sesion'];
	}


	function get_subclase_nombre()
	{
		return $this->_datos['_info']['sesion_subclase'];
	}

	function get_subclase_archivo()
	{
		return $this->_datos['_info']['sesion_subclase_archivo'];
	}

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar_bloque($this->get_bloque_extension());		
		return $molde;
	}

	function get_bloque_extension()
	{
		$bloque = array();
		
		$doc = array('Atrapa el inicio de la sesión del usuario en la instancia (unica vez en toda la sesión)');
		$metodo = new toba_codigo_metodo_php('conf__inicial', array('$datos = null'), $doc);
		$bloque[] = $metodo;

		$doc = array('Atrapa el fin de la sesión del usuario en la instancia (el usuario presiono salir)');
		$metodo = new toba_codigo_metodo_php('conf__final', array(), $doc);
		$bloque[] = $metodo;

		$doc = array('Atrapa la activación de la sesión en cada pedido de página (similar a toba::contexto_ejecucion()->conf__inicial pero se ejecuta sólo con el usuario logueado)');
		$metodo = new toba_codigo_metodo_php('conf__activacion', array(), $doc);
		$bloque[] = $metodo;
		
		return $bloque;
	}
}
?>
