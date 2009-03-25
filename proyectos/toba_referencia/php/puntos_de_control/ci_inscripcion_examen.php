<?php 

class ci_inscripcion_examen extends toba_ci
{
	protected $personas = array (
		0 => array( 'nro_inscripcion'	=> 'p01', 'nombres' => 'Bart', 'apellido' => 'Simpson'),
		1 => array( 'nro_inscripcion'	=> 'p02', 'nombres' => 'Peter', 'apellido' => 'Pan')
	);

	protected $legajos = array (
		0 => array( 'nro_inscripcion'	=> 'p01', 'carrera' => 'c01', 'legajo' => 'l01'),
		1 => array( 'nro_inscripcion'	=> 'p01', 'carrera' => 'c02', 'legajo' => 'l02'),
		2 => array( 'nro_inscripcion'	=> 'p02', 'carrera' => 'c02', 'legajo' => 'l01'),
	);

	protected $materias = array (
		0 => array( 'materia'	=> 'm01', 'carrera' => 'c01', 'nombre' => 'Matematica'),
		1 => array( 'materia'	=> 'm02', 'carrera' => 'c01', 'nombre' => 'Algebra'),
		2 => array( 'materia'	=> 'm03', 'carrera' => 'c02', 'nombre' => 'Estadistica'),
		3 => array( 'materia'	=> 'm04', 'carrera' => 'c02', 'nombre' => 'Calculo diferencial'),
	);

	protected $s__persona;
	protected $s__carrera;

	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function ini()
	{
	}

	function ini__operacion()
	{
		toba::puntos_control()->limpiar_estado();
	}

	//-----------------------------------------------------------------------------------
	//---- Config. ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf()
	{
		ei_arbol(toba::puntos_control()->dump_estado());
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- personas ---------------------------------------------------------------------

	function evt__personas__seleccion($seleccion)
	{
		$this->s__persona = $seleccion['nro_inscripcion'];
		$this->set_pantalla('pant_sel_carrera');
	}

	//El formato del retorno debe ser array( array('columna' => valor, ...), ...)
	function conf__personas($componente)
	{
		$componente->set_datos($this->personas);
	}

	//---- alumnos ----------------------------------------------------------------------

	function evt__alumnos__anterior()
	{
		$this->set_pantalla('pant_sel_persona');
	}

	function evt__alumnos__seleccion($seleccion)
	{
		$this->s__carrera = $seleccion['carrera'];
		$this->set_pantalla('pant_sel_materia');
	}

	function conf__alumnos($componente)
	{
		$filtrado = array();
		$cant_materias = count($this->legajos);
		for ($i = 0; $i < $cant_materias; $i++) {
			if ($this->legajos[$i]['nro_inscripcion'] == $this->s__persona ) {
				$filtrado[] = $this->legajos[$i];
			}
		}
		$componente->set_datos($filtrado);
	}

	//---- materias ---------------------------------------------------------------------

	function evt__materias__anterior()
	{
		$this->set_pantalla('pant_sel_carrera');
	}

	function evt__materias__seleccion()
	{
		$this->set_pantalla('pant_sel_comision');
	}

	function conf__materias($componente)
	{
		$filtrado = array();
		$cant_materias = count($this->materias);
		for ($i = 0; $i < $cant_materias; $i++) {
			if ($this->materias[$i]['carrera'] == $this->s__carrera ) {
				$filtrado[] = $this->materias[$i];
			}
		}
		$componente->set_datos($filtrado);
	}
}

?>