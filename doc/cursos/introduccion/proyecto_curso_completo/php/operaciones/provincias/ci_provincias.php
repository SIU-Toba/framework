<?php 
class ci_provincias extends toba_ci
{
	//---- cuadro -----------------------------------------------------------------------

	function conf__cuadro($componente)
	{
		$datos = toba::consulta_php('consultas')->get_provincias();
		$componente->set_datos($datos);
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('tabla')->cargar($seleccion);
	}

	//---- form -------------------------------------------------------------------------

	function conf__form($componente)
	{
		if ($this->dep('tabla')->esta_cargada()) {
			$datos = $this->dep('tabla')->get();
			$componente->set_datos($datos);	
			$componente->ef('id_provincia')->set_solo_lectura();
			$componente->evento('baja')->set_msg_confirmacion("¿Eliminar provincia \'\'{$datos['nombre']}\'\'?");
		}
	}

	function evt__form__alta($datos)
	{
		$this->validar_datos($datos);
		try {
			$this->dep('tabla')->set($datos);
			$this->dep('tabla')->sincronizar();
		} catch (toba_error_db $e) {
			$sqlstate = $e->get_sqlstate();
			if ($sqlstate == 'db_23505') {
				toba::notificacion()->agregar('El código se encuentra duplicado.');
			}
		}
	}

	function evt__form__modificacion($datos)
	{
		$this->validar_datos($datos);
		$this->dep('tabla')->set($datos);
		$this->dep('tabla')->sincronizar();
	}

	function evt__form__baja()
	{
		try {
			$this->dep('tabla')->eliminar_todo();
		} catch (toba_error $e) {
			toba::notificacion()->agregar('No es posible eliminar el registro.');
		}
		$this->dep('tabla')->resetear();
	}

	function evt__form__cancelar()
	{
		$this->dep('tabla')->resetear();
	}

	function validar_datos($datos)
	{
		if (strlen($datos['nombre']) < 5) {
			throw new toba_error('El nombre ingresado es demasiado corto.');
		}
	}
}
?>