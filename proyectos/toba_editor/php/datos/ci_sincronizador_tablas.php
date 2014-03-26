<?php
require_once('configuracion/catalogo/catalogo_tablas.php');
class ci_sincronizador_tablas extends toba_ci
{
	protected $s__fuente;
	protected $s__catalogo;
	protected $s__seleccionadas;

	function ini__operacion()
	{
		list($proyecto, $fuente) = toba::zona()->get_editable();
		$this->s__catalogo = new catalogo_tablas($proyecto, $fuente);
		$this->s__catalogo->cargar();
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		if (!empty($this->s__seleccionadas) && !is_array(current($this->s__seleccionadas))) {
			$this->s__seleccionadas = array($this->s__seleccionadas);	//Por si viene con el formato viejo
		}
		$procesables = array();
		if (! empty($this->s__seleccionadas)) {
			$procesables = aplanar_matriz($this->s__seleccionadas, 'tabla');
		}		
		$this->s__catalogo->desactivar_no_procesadas($procesables);
		foreach ($procesables as $tabla) {
			$this->s__catalogo->confirmar_acciones($tabla);
		}

		$this->s__catalogo->resetear();
		$this->s__catalogo->cargar();
		$this->dep('cuadro')->deseleccionar();
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$resultado = array_merge($this->generar_listado_tablas_nuevas(), $this->generar_listado_tablas_modificadas());
		$cuadro->set_datos($resultado);
	}

	function generar_listado_tablas_nuevas()
	{
		$resultado = array();
		$nuevas = $this->s__catalogo->get_tablas_nuevas();
		foreach ($nuevas as $alta) {
			$resultado[] = array('tabla' => $alta['tabla'], 'cambio' => 'Se creará un Datos Tabla.');
		}
		return $resultado;
	}

	function generar_listado_tablas_modificadas()
	{
		$resultado = array();
		$modificables = $this->s__catalogo->get_tablas_actualizables();
		//ei_arbol($modificables);
		foreach ($modificables as $klave => $mod) {
			$cambios = '';
			if (isset($mod['A'])) {$cambios .= ' Columnas nuevas: <strong>' . implode(', ', array_keys($mod['A'])) . '</strong><br/>';}
			if (isset($mod['B'])) {$cambios .= ' Columnas a quitar: <strong>' . implode(', ', array_keys($mod['B'])) . '</strong><br/>';}
			if (isset($mod['M'])) {
				foreach(array_keys($mod['M']) as $col) {
					$cambios .= " En la columna <strong>$col</strong> se cambiaran las siguientes propiedades: <br/>";
					$cambios .= ' * ' . implode(', ', array_keys($mod['M'][$col])). '<BR>';
				}
			}
			$resultado[] = array('tabla' => $klave, 'cambio' => $cambios);
		}
		return $resultado;
	}

	function conf__cc_inicio_colapsado($claves)
	{
		$colapsa = true;
		if (isset($this->s__seleccionadas)) {
			foreach ($this->s__seleccionadas as $tabla) {
				if (in_array($tabla['tabla'], $claves)) {
					$colapsa = false;
				}
			}
		}
		return $colapsa;
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__seleccionadas = $seleccion;
	}
}
?>