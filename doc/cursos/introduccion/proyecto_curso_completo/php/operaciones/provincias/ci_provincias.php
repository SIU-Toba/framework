<?php 
class ci_provincias extends toba_ci
{
	//---- cuadro -----------------------------------------------------------------------

	function conf__cuadro($componente)
	{
		$datos = toba::consulta_php('soe_consultas')->get_provincias();
		$componente->set_datos($datos);
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		//toba::logger()->notice($seleccion);
		$this->dep('tabla_provincias')->cargar($seleccion);
	}

	//---- form -------------------------------------------------------------------------

	function conf__form($componente)
	{
		if ($this->dep('tabla_provincias')->esta_cargada()) {
			$datos = $this->dep('tabla_provincias')->get();
			$componente->set_datos($datos);	
			$componente->ef('idprovincia')->set_solo_lectura();
			$componente->evento('baja')->set_msg_confirmacion("¿Eliminar provincia \'\'{$datos['nombre']}\'\'?");			
		}
	}

	function evt__form__alta($datos)
	{
		$this->validar_datos($datos);
		try {
			$this->dep('tabla_provincias')->set($datos);
			$this->dep('tabla_provincias')->sincronizar();
			$this->dep('tabla_provincias')->resetear();
		} catch (toba_error_db $e) {
			$sqlstate = $e->get_sqlstate();
			if ($sqlstate == 'db_23505') {
				toba::notificacion()->agregar('El codigo se encuentra duplicado');				
			}
		}
	}
	
	function evt__form__modificacion($datos)
	{
		$this->validar_datos($datos);
		$this->dep('tabla_provincias')->set($datos);
		$this->dep('tabla_provincias')->sincronizar();
		$this->dep('tabla_provincias')->resetear();
	}	

	function evt__form__baja()
	{
		try {
			$this->dep('tabla_provincias')->set(null);
			$this->dep('tabla_provincias')->sincronizar();
			$this->dep('tabla_provincias')->resetear();
		} catch (toba_error $e) {
			toba::notificacion()->agregar('No es posible eliminar el registro.');
			$this->dep('tabla_provincias')->resetear();
		}
	}

	function evt__form__cancelar()
	{
		$this->dep('tabla_provincias')->resetear();
	}
	
	function validar_datos($datos)
	{
		if (strlen($datos['nombre']) < 5) {
			throw new toba_error('El nombre es muy corto');
		}
	}
}
?>