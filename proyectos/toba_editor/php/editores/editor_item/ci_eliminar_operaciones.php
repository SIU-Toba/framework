<?php 
class ci_eliminar_operaciones extends toba_ci
{
	protected $arbol;
	protected $lista_comp;
	
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
			$tip = "<strong>Tipo</strong>: {$arbol['tipo']}<br><strong>Nombre</strong>: {$arbol['nombre']}";
			$datos[$a]['tipo'] = $arbol['tipo'];
			$datos[$a]['clase'] = toba_recurso::imagen_toba($arbol['icono'], true, null, null, $tip);
			$datos[$a]['componente'] = $arbol['componente'];
			$datos[$a]['consumidores_externos'] =  $arbol['consumidores_externos'];
			if(isset($arbol['subclase'])) {
				$datos[$a]['posee_subclase'] = toba_recurso::imagen_toba('aplicar.png', true);
				$img = 'info_chico.gif';
				$txt_archivo = "</hr>Atencion: el componente posee asociada la subclase <strong>{$arbol['subclase']}</strong> en el archivo <strong>{$arbol['subclase_archivo']}</strong>.";
			} else {
				$datos[$a]['posee_subclase'] = '';
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
		$this->lista_comp = $datos;
	}

	function conf__form(toba_ei_formulario_ml $form_ml)
	{
		return $this->get_info_eliminacion();
	}

	function evt__eliminar()
	{
		$proyecto = toba_contexto_info::get_proyecto();
		$item = toba::zona()->get_info();
		$db = toba_contexto_info::get_db();
		$arbol_componentes = array_slice($this->arbol,1);		
		
		//--- Se eliminan metadatos
		$db->abrir_transaccion();
		foreach ($this->lista_comp as $comp) {
			if ($comp['eliminar']) {
				//--- Elimina metadatos
				$id_dr = toba_info_editores::get_dr_de_clase($comp['tipo']);
				$componente = array('proyecto' => $id_dr[0], 'componente' => $id_dr[1]);
				$dr = toba_constructor::get_runtime($componente, 'toba_datos_relacion', false);
				$dr->get_persistidor()->desactivar_transaccion();
				$dr->resetear();
				$dr->cargar(array('proyecto' => $proyecto, 'objeto' => $comp['componente']));
				$dr->eliminar_todo();
			}			
		}
		//--Borro el item
		$id_dr = toba_info_editores::get_dr_de_clase('toba_item');
		$componente = array('proyecto' => $id_dr[0], 'componente' => $id_dr[1]);
		$dr = toba_constructor::get_runtime($componente, 'toba_datos_relacion', false);
		$dr->get_persistidor()->desactivar_transaccion();
		$dr->resetear();
		$dr->cargar(array('proyecto' => $proyecto, 'item' => $item['item']));
		$dr->eliminar_todo();

		$db->cerrar_transaccion();
		//$db->abortar_transaccion();
		
		//--- Se eliminan subclases
		foreach ($this->lista_comp as $id => $comp) {
			if ($comp['eliminar'] && $comp['eliminar_archivo']) {
				$archivo = $arbol_componentes[$id]['subclase_archivo'];
				$archivo = toba_instancia::get_path_proyecto($proyecto).'/php/'.$archivo;
				unlink($archivo);
			}			
		}		
		toba::notificacion()->agregar('La operación y sus componentes seleccionados han sido eliminado');
		toba::zona()->resetear();
	}
}

?>