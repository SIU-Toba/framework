<?php 
class ci_eliminar_operaciones extends toba_ci
{
	protected $arbol;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
		if (! toba::zona()->cargada()) {
			throw new toba_error('La operación se debe invocar desde la zona de un item');
		} else {
			$info = toba::zona()->get_info();
			$this->arbol = toba_info_editores::get_arbol_componentes_item($info['proyecto'], $info['item']);
			//ei_arbol($this->arbol);
		}
	}

	function get_info_eliminacion()
	{
		$datos = array();
		$a=0;
		$arbol_componentes = array_slice($this->arbol,1);
		foreach( $arbol_componentes as $arbol ) {
			$img = '';
			$txt_archivo = '';
			$txt_consumo = '';
			$datos[$a]['tipo'] = $arbol['tipo'];
			$datos[$a]['componente'] = $arbol['componente'];
			$datos[$a]['consumidores_externos'] = $arbol['consumidores_externos'];
			if(isset($arbol['subclase'])) {
				$datos[$a]['posee_subclase'] = 'Si';
				$img = 'info_chico.gif';
				$txt_archivo = "</hr>Atencion: el componente posee asociada la subclase <strong>{$arbol['subclase']}</strong> en el archivo <strong>{$arbol['subclase_archivo']}</strong>.";
			} else {
				$datos[$a]['posee_subclase'] = 'No';
			}
			if($arbol['consumidores_externos']>0) {
				$img = 'warning.gif';
				$txt_archivo = "</hr>Atencion: el componente esta siendo consumido por otros componentes [<strong>referencias: {$arbol['consumidores_externos']}</strong>].";
				$datos[$a]['eliminar'] = 0;
				$datos[$a]['eliminar_archivo'] = 0;
			} else {
				$datos[$a]['eliminar'] = 1;
				$datos[$a]['eliminar_archivo'] = 1;
			}
			if($img) {
				$datos[$a]['info'] = toba_recurso::imagen_toba($img, true, null, null, $txt_archivo . $txt_consumo);
			}
			$a++;
		}
		return $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__form__modificacion($datos)
	{
	}

	function conf__form(toba_ei_formulario_ml $form_ml)
	{
		return $this->get_info_eliminacion();
	}

	function eliminar()
	{
		
	}
}

?>