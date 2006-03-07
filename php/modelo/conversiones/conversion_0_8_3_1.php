<?
require_once("modelo/conversiones/conversion.php");
/**
*	-----------------------------------------------------------
*	 MIGRACION DE EVENTOS POR DEFECTO A EXPLICITOS Y POR GRUPOS
*	-----------------------------------------------------------
*/
class conversion_0_8_3_1 extends conversion
{
	function get_version()
	{
		return "0.8.3.1";	
	}

	/**
		Los eventos de los FORMULARIOS se organizan en grupos: cargado, no_cargado
	*/
	function cambio_eventos_grupos_ei_formulario()
	{
		//alta = 'no_cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'no_cargado',
					en_botonera = 1
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'alta';";
		$this->ejecutar_sql($sql);
		//modificacion = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado',
					en_botonera = 1
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'modificacion'
				-- por si se ejecuta dos veces, los implicitos no pertenecen a un grupo
				AND (apex_objeto_eventos.implicito IS NULL
						OR apex_objeto_eventos.implicito <> 1);";
		$this->ejecutar_sql($sql);
		//cancelar = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado',
					en_botonera = 1
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'cancelar';";
		$this->ejecutar_sql($sql);
		//baja = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado',
					en_botonera = 1
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'baja';";
		$this->ejecutar_sql($sql);
	}

	/**
		Los eventos de los FILTROS se organizan en grupos: cargado, no_cargado
	*/
	function cambio_eventos_grupos_ei_filtro()
	{
		//filtrar = 'no_cargado,cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'no_cargado,cargado',
					en_botonera = 1
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_filtro'
				AND apex_objeto_eventos.identificador = 'filtrar';";
		$this->ejecutar_sql($sql);
		//cancelar = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado',
					en_botonera = 1
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_filtro'
				AND apex_objeto_eventos.identificador = 'cancelar';";
		$this->ejecutar_sql($sql);
	}

	/**
		Los eventos por defecto ahora se declaran explicitamente.
		Los objetos que no tenian eventos definidos tenian un evento 'modificacion' por defecto.
		Ahora el evento se declara en forma EXPLICITA y es marcado como implicito.
	*/
	function cambio_eventos_implicitos()
	{
		/*
			Cuando estos EIs no tenian eventos, se les seteaba un en codigo una MODIFICACION IMPLICITA.
		*/
		$sql = "INSERT INTO apex_objeto_eventos(
					objeto,
					proyecto,
					implicito,
					identificador,
					etiqueta,
					maneja_datos,
					en_botonera			
				)
				SELECT 
					o.objeto,
					o.proyecto,
					1,
					'modificacion',
					'Modificacion',
					1,
					0
				FROM apex_objeto o
				LEFT OUTER JOIN apex_objeto_eventos e
				           ON o.objeto = e.objeto
				           AND o.proyecto = e.proyecto
				WHERE 	o.clase IN ('objeto_ei_formulario', 'objeto_ei_filtro')
				AND 	o.proyecto = '$this->proyecto'
				GROUP BY 1, 2
				HAVING (COUNT(e.*) = 0)";
		$this->ejecutar_sql($sql);
		/*
			Modificacion implicita de los multilineas
				- todos los que no tienen eventos
				- todos los que tienen como evento solo seleccion
		*/
		$sql = " INSERT INTO apex_objeto_eventos(
					objeto,
					proyecto,
					implicito,
					identificador,
					etiqueta,
					maneja_datos,
					en_botonera
				)
				SELECT 
					o.objeto,
					o.proyecto,
					1,
					'modificacion',
					'Modificacion',
					1,
					0
				FROM apex_objeto o
				LEFT OUTER JOIN apex_objeto_eventos e
				           ON o.objeto = e.objeto
				           AND o.proyecto = e.proyecto
				           AND e.identificador <> 'seleccion'
				WHERE 	o.clase = 'objeto_ei_formulario_ml'
				AND 	o.proyecto = '$this->proyecto'
				GROUP BY 1, 2
				HAVING (COUNT(e.*) = 0)";
		$this->ejecutar_sql($sql);
	}

	/**
		Establecer el menu milonic en los proyectos.
	*/
	function cambio_definicion_menu()
	{
		$sql = "UPDATE apex_proyecto 
				SET menu = 'milonic' 
				WHERE proyecto = '$this->proyecto'";
		$this->ejecutar_sql($sql);
	}

	/**
		Si un cuadro no tiene clave definida, seleccionar la clave del DBR
	*/
	function cambio_explicitar_clave_cuadro()
	{
		$sql = "UPDATE apex_objeto_cuadro 
				SET clave_dbr = 1 
				WHERE objeto_cuadro_proyecto = '$this->proyecto'
				AND (	(columnas_clave IS NULL)
						OR trim(columnas_clave) = '' )";
		$this->ejecutar_sql($sql);
	}

	//--------------------------------------------------------------------------------
	//------------------ MODIFICACIONES en la extension de clases --------------------
	//--------------------------------------------------------------------------------

	/**
	*	Las clases no extendidas necesitan setear la clase anterior como subclase
	*/
	function cambio_subclases_ci_completar_subclases()
	{
		$sql = "UPDATE apex_objeto
				SET    subclase = apex_clase.clase,
				       subclase_archivo = apex_clase.archivo
				FROM apex_clase
				WHERE apex_objeto.proyecto = '{$this->proyecto}'
				AND (apex_objeto.subclase IS NULL OR trim(apex_objeto.subclase) = '')
				AND apex_objeto.clase = apex_clase.clase AND apex_objeto.clase_proyecto = apex_clase.proyecto
				AND apex_objeto.clase IN ('ci_cn', 'objeto_ci_abm', 'ci_abm_dbr', 'ci_abm_dbt', 'ci_abm_nav')";
		$this->ejecutar_sql($sql);
	}

	/**
	*	Las clases ci_cn, objeto_ci_abm, ci_abm_dbr, ci_abm_dbt y ci_abm_nav pasan a ser ci comunes.
	*/
	function cambio_subclases_ci_cambiar_clase()
	{
		$sql = "UPDATE apex_objeto 
				SET clase = 'objeto_ci' 
				WHERE 
					proyecto = '{$this->proyecto}' AND
					clase IN ('ci_cn', 'objeto_ci_abm', 'ci_abm_dbr', 'ci_abm_dbt', 'ci_abm_nav')";
		$this->ejecutar_sql($sql);
	}
	
	/**
	*	Las se fusionan las clases objeto_cn_t y objeto_cn
	*/
	function cambio_subclases_cn()
	{
		$sql = "UPDATE apex_objeto
				SET clase = 'objeto_cn' 
				WHERE 
					proyecto = '{$this->proyecto}' AND				
					clase IN ('objeto_cn_t')
		";
		$this->ejecutar_sql($sql);		
	}
	
	/**
	*	Todos los items con patrones relacionados con ci pasan a usar el patrón CI, incluso los que tienen popup
	*/
	function cambio_patrones_ci()
	{
		$sql = "
			UPDATE apex_item
			SET
				actividad_patron = 'CI'
			WHERE
				proyecto = '{$this->proyecto}' AND							
				actividad_patron IN ('ci', 'generico_ci_cn', 'ci_cn_popup', 'CI_POPUP')
		";
		$this->ejecutar_sql($sql);
	}
}
?>
