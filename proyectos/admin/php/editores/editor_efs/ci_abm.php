<?php 
//--------------------------------------------------------------------
class ci_abm extends objeto_ci
{
	protected $es_nuevo = false;
		
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'es_nuevo';
		return $propiedades;
	}

	function get_etapa_actual()
	{
		if ($this->dependencia('relacion')->esta_cargado() || $this->es_nuevo) {
			return 'edicion';
		} else {
			return 'seleccion';	
		}
	}
	
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if ($this->es_nuevo) {
			unset($eventos['eliminar']);
		}
		return $eventos;
	}
	
	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
		if ($this->es_nuevo) {
			//Seteo los datos asociados al uso de este editor
			$this->dependencia('relacion')->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		}		
		$this->dependencia('relacion')->sincronizar();
		$this->dependencia('relacion')->resetear();
		$this->es_nuevo = false;
	}

	function evt__cancelar()
	{
		$this->dependencia('relacion')->resetear();
		$this->es_nuevo = false;
	}

	function evt__nuevo()
	{
		$this->es_nuevo = true;	
	}
	
	function evt__eliminar()
	{
		$this->dependencia('relacion')->eliminar();
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- listado -------------------------------------------------------

	function evt__listado__seleccion($seleccion)
	{
		$this->dependencia('relacion')->cargar($seleccion);
	}

	function evt__listado__carga()
	{
		$sql = "
			SELECT 
				ef.elemento_formulario,
				ef.descripcion,
				ef.obsoleto,
				CASE 
					WHEN obsoleto = 1 THEN 'Obsoletos'
					ELSE 'Activos'
				END as obsoleto_desc
			FROM
				apex_elemento_formulario ef
			WHERE
				ef.proyecto = '".toba::get_hilo()->obtener_proyecto()."'
			ORDER BY obsoleto,ef.elemento_formulario
		";
		return consultar_fuente($sql, 'instancia');
	}

	
	function evt__form_base__carga()
	{
		return 	$this->dependencia('relacion')->tabla('base')->get();
	}
	
	function evt__form_base__modificacion($datos)
	{
		$this->dependencia('relacion')->tabla('base')->set($datos);
	}

}

?>