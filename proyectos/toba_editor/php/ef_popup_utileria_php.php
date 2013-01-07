<?php

/**
 * Icono de utileria que se le agrega a un ef_popup para poder abrir/ver un codigo php
 */
class ef_popup_utileria_php implements toba_ef_icono_utileria
{
	protected $vinculo;
	protected $es_abrir;
	protected $id_vinculo;
	private $_sin_archivo = false;

	function __construct($es_abrir, $registrar_inmediatamente=true)
	{
		$this->es_abrir = $es_abrir;
		$this->vinculo = new toba_vinculo('toba_editor', 30000014);
		$this->vinculo->set_celda_memoria('php');
		if ($this->es_abrir) {
			$this->vinculo->set_servicio('ejecutar');
			$this->vinculo->set_ajax(true);
		}
		if ($registrar_inmediatamente) {
			$this->id_vinculo = toba::vinculador()->registrar_vinculo($this->vinculo);
		}
	}

	function agregar_parametros($parametros)
	{
		foreach ($parametros as $clave => $valor) {
				$this->vinculo->agregar_parametro($clave, $valor);
		}
	}

	function cambiar_item($item=30000014)
	{
		$this->vinculo->set_item('toba_editor', $item);
	}

	function invocar_sin_archivo($sin_archivo)
	{
		$this->_sin_archivo = $sin_archivo;
	}

	function registrar()
	{
		$this->id_vinculo = toba::vinculador()->registrar_vinculo($this->vinculo);
	}

	function get_html(toba_ef $ef)
	{
		$objeto_js = $ef->objeto_js();
		if ($this->es_abrir) {
			$img = toba_recurso::imagen_proyecto('reflexion/abrir.gif', true);
		} else {
			$img = toba_recurso::imagen_toba('nucleo/php.gif', true);
		}
		if (! $this->_sin_archivo) {
			$salida = "<a href='#' onclick=\"if ($objeto_js.get_estado() == ''){return;}
											vinculador.agregar_parametros({$this->id_vinculo}, {archivo: $objeto_js.get_estado()});
											vinculador.invocar({$this->id_vinculo})\">$img</a>";
		} else {
			if (! $ef->tiene_estado()) {
				$img = toba_recurso::imagen_toba('nucleo/extender.gif', true);								
				$objeto_js = $ef->controlador()->get_id_objeto_js();			//Imita el metodo de modificacion de vinculos usado por los eis
				$nombre = 'modificar_vinculo__ef_'. $ef->get_id();				//de otro modo se hace imposible agregarle parametros en runtime
				$codigo = " if (!existe_funcion($objeto_js, '$nombre')){return;}
							$objeto_js.$nombre({$this->id_vinculo});
							vinculador.invocar({$this->id_vinculo});";
							   
				$salida = "<a href='#' onclick=\"$codigo\">$img</a>";
			} else {				
				$salida = "<a href='#' onclick=\"vinculador.invocar({$this->id_vinculo})\">$img</a>";
			}			
		}
		return $salida;
	}
}

?>
