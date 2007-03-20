<?php 
  
  class ci_inscripcion_examen extends toba_ci
  {
    protected $personas = array (
      0 => array( "nro_inscripcion"	=> "p01", "nombres" => "Bart", "apellido" => "Simpson"),
      1 => array( "nro_inscripcion"	=> "p02", "nombres" => "Peter", "apellido" => "Pan")
    );

    protected $legajos = array (
      0 => array( "nro_inscripcion"	=> "p01", "carrera" => "c01", "legajo" => "l01"),
      1 => array( "nro_inscripcion"	=> "p01", "carrera" => "c02", "legajo" => "l02"),
      2 => array( "nro_inscripcion"	=> "p02", "carrera" => "c02", "legajo" => "l01"),
    );

    protected $materias = array (
      0 => array( "materia"	=> "m01", "carrera" => "c01", "nombre" => "Matematica"),
      1 => array( "materia"	=> "m02", "carrera" => "c01", "nombre" => "Algebra"),
      2 => array( "materia"	=> "m03", "carrera" => "c02", "nombre" => "Estadistica"),
      3 => array( "materia"	=> "m04", "carrera" => "c02", "nombre" => "Calculo diferencial"),
    );

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
  	}

	  //---- Configuracion de Pantallas ---------------------------------------------------
    function conf__pant_sel_persona($pantalla)
  	{
      user_error(print_r(toba::puntos_control()->dump_estado(), true), E_USER_NOTICE);
      ei_arbol(toba::puntos_control()->dump_estado(), "hola");
	  }

  	function conf__pant_sel_carrera($pantalla)
	  {
      user_error(print_r(toba::puntos_control()->dump_estado(), true), E_USER_NOTICE);
      ei_arbol(toba::puntos_control()->dump_estado());
  	}

	  function conf__pant_sel_materia($pantalla)
  	{
      ei_arbol(/*toba::puntos_control()->dump_estado()*/);
	  }

	  function conf__pant_sel_comision($pantalla)
  	{
      ei_arbol(/*toba::puntos_control()->dump_estado()*/);
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

    //El formato del retorno debe ser array( array('columna' => valor, ...), ...)
    function conf__alumnos($componente)
    {
      $filtrado = array();
      
      for ($i=0; $i < count($this->legajos); $i++)
        if ($this->legajos[$i]["nro_inscripcion"] == $this->s__persona )
          $filtrado[] = $this->legajos[$i];
        
      $componente->set_datos($filtrado);
    }

    //---- materias ---------------------------------------------------------------------

    function evt__materias__anterior()
    {
      $this->set_pantalla('pant_sel_carrera');
    }

    function evt__materias__seleccion($seleccion)
    {
      $this->set_pantalla('pant_sel_comision');
    }

    //El formato del retorno debe ser array( array('columna' => valor, ...), ...)
    function conf__materias($componente)
    { 
      $filtrado = array();
      
      for ($i=0; $i < count($this->materias); $i++)
        if ($this->materias[$i]["carrera"] == $this->s__carrera )
          $filtrado[] = $this->materias[$i];
        
      $componente->set_datos($filtrado);
    }
  }


?>