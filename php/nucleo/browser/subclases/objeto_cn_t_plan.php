<?php
require_once("nucleo/negocio/objeto_cn_t.php");	//Ancestro de todos los OE
require_once("nucleo/lib/buffer.php");

class objeto_cn_t_plan extends objeto_cn_t
{
	var $actividad_actual;
	var $hito_actual;
	var $linea_actual;
	var $clave;

	function __construct($id)
	{
		parent::__construct($id);
		$this->inicializar_buffer();
	}

	function mantener_estado_sesion()
	//Propiedades que necesitan persistirse en la sesion
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "clave";
		$propiedades[] = "actividad_actual";
		return $propiedades;
	}
	//-------------------------------------------------------------------------------

	function inicializar_buffer()
	{
		//-- Propiedades --
		$propiedades["tabla"]="apex_objeto_plan";
		$propiedades["clave"][]="objeto_plan_proyecto";
		$propiedades["clave"][]="objeto_plan";
		$propiedades["columna"][]="descripcion";
		$this->buffer['propiedades'] =& new buffer("buffer_".$this->id[1]."_prop",$propiedades, $this->info['fuente']);
		//-- Actividades --
		$actividades["tabla"]="apex_objeto_plan_activ";
		$actividades["clave"][]="objeto_plan_proyecto";
		$actividades["clave"][]="objeto_plan";
		$actividades["clave"][]="posicion";
		$actividades["columna"][]="descripcion_corta";
		$actividades["columna"][]="descripcion";
		$actividades["columna"][]="fecha_inicio";
		$actividades["columna"][]="fecha_fin";
		$actividades["columna"][]="anotacion";
		$actividades["columna"][]="duracion";
		$actividades["columna"][]="altura";
		$actividades["orden"][]="posicion";
		$actividades["no_duplicado"][]="posicion";
		$actividades["no_nulo"][]="posicion";
		$this->buffer['actividades'] =& new buffer("buffer_".$this->id[1]."_act",$actividades, $this->info['fuente']);
		//-- Hitos --
		$hitos["tabla"]="apex_objeto_plan_hito";
		$hitos["clave"][]="objeto_plan_proyecto";
		$hitos["clave"][]="objeto_plan";
		$hitos["clave"][]="posicion";
		$hitos["columna"][]="descripcion_corta";
		$hitos["columna"][]="descripcion";
		$hitos["columna"][]="fecha";
		$hitos["columna"][]="anotacion";
		$hitos["orden"][]="posicion";
		$this->buffer['hitos'] =& new buffer("buffer_".$this->id[1]."_hit",$hitos, $this->info['fuente']);
		//-- Lineas -- 
		$lineas["tabla"]="apex_objeto_plan_linea";
		$lineas["clave"][]="objeto_plan_proyecto";
		$lineas["clave"][]="objeto_plan";
		$lineas["clave"][]="linea";
		$lineas["columna"][]="descripcion_corta";
		$lineas["columna"][]="descripcion";
		$lineas["columna"][]="fecha";
		$lineas["columna"][]="color";
		$lineas["columna"][]="ancho";
		$lineas["columna"][]="estilo";
		$lineas["orden"][]="linea";
		$this->buffer['lineas'] =& new buffer("buffer_".$this->id[1]."_lin",$lineas, $this->info['fuente']);
	}

	function resetear()
	{
		foreach(array_keys($this->buffer) as $buffer){
			$this->buffer[$buffer]->resetear();
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------   INTERFACE EXTERNA   -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_datos($clave)
	//Cargar un plan de la base
	{
		$this->clave = $clave;
		$where[]= "objeto_plan_proyecto = '" . $clave[0] . "'";
		$where[]= "objeto_plan = '" . $clave[1]. "'";
		$this->buffer['propiedades']->cargar_datos($where);
		$this->buffer['actividades']->cargar_datos($where);
		$this->buffer['hitos']->cargar_datos($where);
		$this->buffer['lineas']->cargar_datos($where);
	}

    //-------------------------------------------------------------
	// PROPIEDADES
    //-------------------------------------------------------------

    function set_propiedades($propiedades)
    {
		//agregar y modificar
		//return $this->buffer['propiedades']->establecer_registro($propiedades,0);
    }
    //-------------------------------------------------------------

    function get_propiedades()
    {
		return $this->buffer['propiedades']->get_registro(0);
    }

    //-------------------------------------------------------------
	// ACTIVIDADES
    //-------------------------------------------------------------

    function obtener_lista_actividades()
    {
		return $this->buffer['actividades']->get_registros();
    }
    //-------------------------------------------------------------

	function seleccionar_actividad($clave)
	{
		$this->actividad_actual = $clave;
	}
    //-------------------------------------------------------------

    function obtener_actividad_actual()
    {
		if(isset($this->actividad_actual)){
			return $this->buffer['actividades']->get_registro($this->actividad_actual);
		}
    }
    //-------------------------------------------------------------
    
    function agregar_actividad($actividad)
	{
		//Tengo que asignar el plan al registro
		$plan = $this->clave[1];
		$actividad['objeto_plan'] = $plan;
		$resultado = $this->buffer['actividades']->agregar_registro($actividad);
		if($resultado[0] !== "ok"){
			ei_arbol($resultado,"Agregar");	
		}
	}
    //-------------------------------------------------------------

    function modificar_actividad($actividad)
    {
		if(isset($this->actividad_actual)){
			$estado = $this->buffer['actividades']->modificar_registro($actividad, $this->actividad_actual);
			ei_arbol($estado,"Modificar");
			unset($this->actividad_actual);
			return $estado;
		}else{
			echo "Modificacion, no hay clave";
		}
    }
    //-------------------------------------------------------------

    function eliminar_actividad()
    {
		if(isset($this->actividad_actual)){
			$estado = $this->buffer['actividades']->eliminar_registro($this->actividad_actual);
			ei_arbol($estado,"Eliminar");
			unset($this->actividad_actual);
			return $estado;
		}else{
			echo "Eliminacion, no hay clave";
		}
    }
    //-------------------------------------------------------------
	// HITOS
    //-------------------------------------------------------------

    function obtener_lista_hitos()
    {
		return $this->buffer['hitos']->get_registros();
    }
    //-------------------------------------------------------------

	function seleccionar_hito($clave)
	{
		$this->hito_actual = $clave;
	}
    //-------------------------------------------------------------

    function obtener_hito_actual()
    {
		if(isset($this->hito_actual)){
			return $this->buffer['hitos']->get_registro($this->hito_actual);
		}
    }
    //-------------------------------------------------------------

    function agregar_hito($hito)
	{
		$this->buffer['hitos']->agregar_registro($hito);
	}
    //-------------------------------------------------------------

    function modificar_hito($hito)
    {
		if(isset($this->hito_actual)){
			$estado = $this->buffer['hitos']->modificar_registro($hito, $this->hito_actual);
			unset($this->hito_actual);
			return $estado;
		}
    }
    //-------------------------------------------------------------

    function eliminar_hito()
    {
		if(isset($this->hito_actual)){
			$ok = $this->buffer['hitos']->eliminar_registro($this->hito_actual);
			unset($this->hito_actual);
			return $ok;
		}else{
			return $false;
		}
    }

    //-------------------------------------------------------------
	// LINEAS
    //-------------------------------------------------------------

    function obtener_lista_lineas()
    {
		return $this->buffer['lineas']->get_registros();
	}
    //-------------------------------------------------------------

	function seleccionar_linea($clave)
	{
		$this->linea_actual = $clave;
	}
    //-------------------------------------------------------------

    function obtener_linea_actual()
    {
		if(isset($this->linea_actual)){
			return $this->buffer['lineas']->get_registro($this->linea_actual);
		}
    }
    //-------------------------------------------------------------

    function agregar_linea($linea)
	{
		$this->buffer['lineas']->agregar_registro($linea);
	}
    //-------------------------------------------------------------

    function modificar_linea($linea)
    {
		if(isset($this->linea_actual)){
			$estado = $this->buffer['lineas']->modificar_registro($linea, $this->linea_actual);
			unset($this->linea_actual);
			return $estado;
		}
    }
    //-------------------------------------------------------------

    function eliminar_linea()
    {
		if(isset($this->linea_actual)){
			$ok = $this->buffer['lineas']->eliminar_registro($this->linea_actual);
			unset($this->linea_actual);
			return $ok;
		}else{
			return $false;
		}
    }
    //-------------------------------------------------------------
    //-------------------------------------------------------------

	function procesar()
	{
		ei_arbol($this->buffer['propiedades']->sincronizar_db(),"SINCRO Propiedades");	
		ei_arbol($this->buffer['actividades']->sincronizar_db(),"SINCRO Actividades");	
		ei_arbol($this->buffer['hitos']->sincronizar_db(),"SINCRO Hitos");	
		ei_arbol($this->buffer['lineas']->sincronizar_db(),"SINCRO Lineas");	
	}

	function debug()
	{
		//ei_arbol($this->buffer['propiedades']->info(true),"BUFFER 'PROPIEDADES'");
		ei_arbol($this->buffer['actividades']->info(true),"BUFFER 'ACTIVIDADES'");
		//ei_arbol($this->buffer['hitos']->info(true),"BUFFER 'HITOS'");
		//ei_arbol($this->buffer['lineas']->info(true),"BUFFER 'LINEAS'");
	}
    //-------------------------------------------------------------
    //-------------------------------------------------------------
}
?>