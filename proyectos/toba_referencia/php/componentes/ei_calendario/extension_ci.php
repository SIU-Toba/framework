<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
	//--- Propiedades de sesión
	protected $s__dia;
	protected $s__semana;
	protected $s__datos;
	
	function conf()
	{
		if (!isset($this->s__dia) && !isset($this->s__semana)) {
			$this->pantalla()->eliminar_evento('procesar');
			$this->pantalla()->eliminar_dep('formulario');	
		}
	}
	
	// La idea es que en la carga del calendario recupere los contenidos de la base de datos.
	// Para dar un ejemplo concreto de cómo se visulizan los contenidos, se setea un contenido fijo
	// para el día de la fecha.
	function conf__calendario($calendario)
	{
		if (!isset($this->s__datos)) {
			$hoy = date('Y-m-d', time());
			$this->s__datos = array();
			$this->s__datos[$hoy] = array('dia' => $hoy, 'contenido' => 'Testear el sistema');
		}
		$calendario->set_ver_contenidos(true);
		$calendario->set_datos($this->s__datos);
	}

	function evt__calendario__seleccionar_dia($seleccion)
	{
		$this->s__dia = "{$seleccion['anio']}-{$seleccion['mes']}-{$seleccion['dia']}";
		unset($this->s__semana);
	}
	
	function evt__calendario__seleccionar_semana($seleccion)
	{
		$this->s__semana = "{$seleccion['semana']}-{$seleccion['anio']}";
		unset($this->s__dia);
	}

	function conf__formulario(toba_ei_formulario $form)
	{
		if (isset($this->s__dia)) {
			$form->desactivar_efs('semana');
			$datos = array( 'dia' => $this->s__dia );
			if (isset($this->s__datos[$this->s__dia])) {
				$datos['contenido'] = $this->s__datos[$this->s__dia]['contenido'];
			}
		} elseif (isset($this->s__semana)) {
			$form->desactivar_efs('dia');
			$datos = array( 'semana' => $this->s__semana );
			if (isset($this->s__datos[$this->s__semana])) {
				$datos['contenido'] = $this->s__datos[$this->s__semana]['contenido'];
			}			
		}
		$form->set_datos($datos);
	}
	
	// La modificación del formulario debería actualizar los contenidos en la base de datos, 
	// para que luego se reflejen los cambios ante un evento de carga del calendario.
	function evt__formulario__modificacion($datos)
	{
		if (isset($datos['dia'])) {
			$this->s__datos[$datos['dia']] = $datos;
		}
		if (isset($datos['semana'])) {
			$this->s__datos[$datos['semana']] = $datos;
		}
	}
	
	//-- Guardar solo limpia la seleccion actual
	function evt__procesar()
	{
		unset($this->s__dia);
		unset($this->s__semana);
	}
	
}
  
?>