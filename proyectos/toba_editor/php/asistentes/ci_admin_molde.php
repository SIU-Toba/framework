<?php 

/*
	Este tiene que tener una manera de preguntarle al CI hijo si ya se puede generar
	La sincronizacion del editor hijo tiene que ser a travez de un API
	TAmbien el pedido de la clave del molde
	En resumen, todo lo que accede: $this->dependencia('asistente')->dep('datos')->

*/
class ci_admin_molde extends toba_ci
{
	protected $s__tipo;
	protected $datos_tipo_operacion;
	protected $s__opciones_generacion;


	function ini()
	{
		if (! toba::zona()->cargada()) {
			throw new toba_error('La operación se debe invocar desde la zona de un item');
		}
		if (isset($this->s__tipo)) {
			$this->cargar_editor_molde();
		}	
	}
	
	function cargar_editor_molde()
	{
		$info = toba_info_editores::get_lista_tipo_molde($this->s__tipo['tipo']);
		$ci = $info['ci'];
		$this->agregar_dependencia('asistente', 'toba_editor', $ci);	
		$this->dep('asistente')->set_molde_nuevo($this->s__tipo['tipo']);
	}

	//-----------------------------------------------------------------------------------
	//---- API para CIs contenidos ------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function get_datos_basicos()
	{
		$info_item = toba::zona()->get_info();
		$datos['item'] = $info_item['item'];
		$datos['proyecto'] = $info_item['proyecto'];
		$datos['operacion_tipo'] = $this->s__tipo['tipo'];
		return $datos;
	}
	
	//-----------------------------------------------------------------------------------
	//---- Navegacion ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	
	function evt__siguiente_editar()
	{
		$this->set_pantalla('pant_edicion');	
	}
	
	function evt__siguiente_generar()
	{
		$this->dep('asistente')->dep('datos')->sincronizar();
		$this->set_pantalla('pant_generacion');	
	}
	
	
	function evt__volver_editar()
	{
		$this->set_pantalla('pant_tipo_operacion');	
	}	
	
	function evt__volver_generar()
	{
		$this->set_pantalla('pant_edicion');	
	}	
		
	
	//-----------------------------------------------------------------------------------
	//---- Elegir tipo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function conf__form_tipo_operacion()
	{
		if (isset($this->s__tipo)) {
			return $this->s__tipo;
		}
	}
	
	function evt__form_tipo_operacion__modificacion($datos)
	{
		$this->s__tipo = $datos;
		$this->cargar_editor_molde();
	}	

	//-----------------------------------------------------------------------------------
	//---- Editar ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	function conf__pant_edicion()
	{
		$info = toba_info_editores::get_lista_tipo_molde($this->s__tipo['tipo']);
		$this->pantalla()->set_descripcion('Edición de un '.$info['descripcion_corta']);
		$this->pantalla()->agregar_dep('asistente');		
	}

	//-----------------------------------------------------------------------------------
	//---- Generación ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
		
	function conf__pant_generar()
	{
		try {
			//Si hay algun tema bloqueante, no dejo hacer nada
			$bloqueos = $this->asistente(true)->get_bloqueos();
			if(! empty($bloqueos)) {
				$this->pantalla()->eliminar_evento('generar');
				$this->pantalla()->eliminar_dep('form_generaciones');
				toba::notificacion()->agregar('Existen problemas que imposibilitan la ejecución del molde. '
												.' Por favor edite el mismo y vuelva a intentar. '
												.'Los errores se describen a continuacion.');
				foreach($bloqueos as $bloqueo) {
					toba::notificacion()->agregar($bloqueo);	
				}
			}
			// Si no hay opciones de generacion, excluyo el form de opciones
			$opciones = $this->asistente()->get_opciones_generacion();
			if(empty($opciones)) {
				$this->pantalla()->eliminar_dep('form_generaciones');
			}
		} catch ( toba_error_asistentes $e ) {
			toba::notificacion()->agregar("El molde que desea cargar posee errores en su definicion: " . $e->getMessage() );
			$this->pantalla()->eliminar_evento('generar');
			$this->pantalla()->eliminar_dep('form_generaciones');
		}
	}

	//--- Opciones de generacion ----

	function conf__form_generaciones($componente)
	{
		$componente->set_datos( $this->asistente()->get_opciones_generacion() );
	}
	
	function evt__form_generaciones__modificacion($datos)
	{
		$this->s__opciones_generacion = $datos;
	}

	function evt__generar()
	{
		$this->asistente()->crear_operacion( $this->s__opciones_generacion );
	}	

	function asistente($reset=false)
	{
		if($reset || !isset($this->asistente)) {
			$clave = $this->dependencia('asistente')->dep('datos')->tabla('base')->get_clave_valor(0);
			$this->asistente = toba_catalogo_asistentes::cargar_por_molde($clave['proyecto'], $clave['molde']);
			$this->asistente->preparar_molde();
		}
		return $this->asistente;
	}
}
?>