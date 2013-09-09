<?php
require_once('objetos_toba/efs/ci_efs.php');

/**
 * La idea de este ci es reutilizar la logica de edición de efs en todo lo posible
 */
class ci_cols extends ci_efs
{

	protected $campo_clave = 'nombre';

	function get_tipo_ef()
	{
		$tipo = $this->get_tabla()->get_fila_columna($this->s__seleccion_efs, 'tipo');
		$datos = $this->get_tabla()->get_fila($this->s__seleccion_efs);
		$metodo = 'get_clase_ef';
		if ($tipo !== 'opciones') {
			return call_user_func(array('toba_filtro_columna_'.$datos['tipo'], $metodo));
		} else {
			return $datos['opciones_ef'];
		}
	}	

	function conf__param_carga(toba_ei_formulario $form)
	{
		$form->desactivar_efs(array('cascada_mantiene_estado', 'carga_cascada_relaj'));		
		return parent::conf__param_carga($form);
	}
	
	function evt__param_carga__modificacion($datos)
	{
		$this->modificado = true;
		$actual = $datos['mecanismo'];
		foreach ($this->mecanismos_carga as $valor_mec) {
			//-- Se quitan los valores de los otros mecanismo por si la interface no lo hizo
			if ($valor_mec != $actual && isset($datos[$valor_mec])) {
				//-- Caso particular a la carga php y dt que comparten un parametro
				if ($actual != 'carga_dt' && $valor_mec == 'carga_metodo') {
					unset($datos[$valor_mec]);
				}
			}
		}
		if ($datos['mecanismo'] != null) {
			unset($datos['mecanismo']);
			unset($datos['estatico']);
			$this->verificar_cantidad_maestros($datos);
		} else {
			//--- Limpia los valores
			$datos = array('carga_maestros' => null);
			foreach ($this->mecanismos_carga as $mec) {
				$datos[$mec] = null;
			}
		}
		//-- Si selecciona otro mecanismo o tipo de clase de carga php, blanquear el datos tabla
		if ($actual != 'carga_metodo' || $datos['tipo_clase'] != 'datos_tabla') {
			$datos['carga_dt'] = null;
		}
		$this->set_parametros($datos);
	}

	function verificar_cantidad_maestros($datos)
	{
		if (isset($datos['carga_maestros'])) {
			$maestros = explode(',', $datos['carga_maestros']);
			if (count($maestros) > 1) {
				throw new toba_error_def('Las columnas de un filtro pueden tener a lo sumo una columna como maestro');
			}
		}
	}
}
?>