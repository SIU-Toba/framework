<?php 
//--------------------------------------------------------------------
class ci_abm extends toba_ci
{
	protected $es_nuevo = false;
		
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'es_nuevo';
		return $propiedades;
	}
	
	function conf()
	{
		if ($this->es_nuevo) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}
	
	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
		if ($this->es_nuevo) {
			//Seteo los datos asociados al uso de este editor
			$this->dependencia('relacion')->tabla('base')->set_fila_columna_valor(0, "proyecto", toba_editor::get_proyecto_cargado());
		}		
		$this->dependencia('relacion')->sincronizar();
		$this->dependencia('relacion')->resetear();
		$this->es_nuevo = false;
	}

	function evt__cancelar()
	{
		$this->dependencia('relacion')->resetear();
		$this->set_pantalla('seleccion');
		$this->es_nuevo = false;
	}

	function evt__nuevo()
	{
		$this->es_nuevo = true;	
		$this->set_pantalla('edicion');
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
		$this->set_pantalla('edicion');
	}

	function conf__listado()
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
				ef.proyecto = ".quote(toba_editor::get_proyecto_cargado()).'
			ORDER BY obsoleto,ef.elemento_formulario
		';
		return consultar_fuente($sql);
	}

	
	function conf__form_base()
	{
		return 	$this->dependencia('relacion')->tabla('base')->get();
	}
	
	function evt__form_base__modificacion($datos)
	{
		$this->dependencia('relacion')->tabla('base')->set($datos);
	}

}

?>