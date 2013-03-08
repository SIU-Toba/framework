<?php
class toba_contexto_ejecucion_info extends toba_elemento_transversal_info
{
	function ini()
	{
		$proyecto = quote($this->_id['proyecto']);
		$sql = "SELECT
					pm_contexto,
					contexto_ejecucion_subclase,
					contexto_ejecucion_subclase_archivo
				FROM apex_proyecto
				WHERE proyecto = $proyecto;";

		$this->_datos['_info'] = toba::db()->consultar_fila($sql);
		toba::logger()->debug($sql);
	}

	function  set_subclase($nombre, $archivo, $pm)
	{
		$db = toba_contexto_info::get_db();
		$proyecto = quote($this->_id['proyecto']);
		$nombre = quote($nombre);
		$archivo = quote($archivo);
		$pm = quote($pm);
		$sql = "UPDATE  apex_proyecto
					SET  contexto_ejecucion_subclase = $nombre,
					contexto_ejecucion_subclase_archivo = $archivo,
					pm_contexto = $pm
					WHERE	proyecto = $proyecto;";

		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}

	function get_clase_nombre()
	{
		return 'toba_contexto_ejecucion';
	}

	function get_clase_archivo()
	{
		return 'nucleo/lib/toba_contexto_ejecucion.php';
	}

	function get_punto_montaje()
	{
		return $this->_datos['_info']['pm_contexto'];
	}

	function get_subclase_nombre()
	{
		return $this->_datos['_info']['contexto_ejecucion_subclase'];
	}

	function get_subclase_archivo()
	{
		return $this->_datos['_info']['contexto_ejecucion_subclase_archivo'];
	}

	function get_molde_subclase()
	{
		$molde =  $this->get_molde_vacio();
		$molde->agregar_bloque($this->get_bloque_subclase());
		return $molde;
	}

	function get_bloque_subclase()
	{
		$bloque = array();

		$doc = array('Ventana que se ejecuta siempre al ingresar el proyecto a la ejecución del request (pedido de página).');
		$metodo = new toba_codigo_metodo_php('conf__inicial', array(), $doc);
		$bloque[] = $metodo;

		$doc = array('Ventana que se ejecuta siempre a la salida del proyecto adela ejecución del request (pedido de página).');
		$metodo = new toba_codigo_metodo_php('conf__final', array(), $doc);
		$bloque[] = $metodo;

		return $bloque;
	}	
}
?>
