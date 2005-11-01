<?
require_once("conversion_toba.php");

class conversion_0_8_3_editor extends conversion_toba
{
	function get_version()
	{
		return "0.8.3.editor";	
	}

	/**
		Los eventos de los FORMULARIOS se organizan en grupos: cargado, no_cargado
	*/
	function cambio_eventos_grupos_ei_formulario()
	{
		//alta = 'no_cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'no_cargado'
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'alta';";
		$this->ejecutar_sql($sql,"instancia");
		//modificacion = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado'
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'modificacion'
				-- por si se ejecuta dos veces, los implicitos no pertenecen a un grupo
				AND (apex_objeto_eventos.implicito IS NULL
						OR apex_objeto_eventos.implicito <> 1);";
		$this->ejecutar_sql($sql,"instancia");
		//cancelar = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado'
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'cancelar';";
		$this->ejecutar_sql($sql,"instancia");
		//baja = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado'
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_formulario'
				AND apex_objeto_eventos.identificador = 'baja';";
		$this->ejecutar_sql($sql,"instancia");
	}


	/**
		Los eventos de los FILTROS se organizan en grupos: cargado, no_cargado
	*/
	function cambio_eventos_grupos_ei_filtro()
	{
		//filtrar = 'no_cargado,cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'no_cargado,cargado'
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_filtro'
				AND apex_objeto_eventos.identificador = 'filtrar';";
		$this->ejecutar_sql($sql,"instancia");
		//cancelar = 'cargado'
		$sql = "UPDATE apex_objeto_eventos
				SET grupo = 'cargado'
				FROM apex_objeto 
				WHERE apex_objeto_eventos.objeto = apex_objeto.objeto
				AND apex_objeto_eventos.proyecto = apex_objeto.proyecto
				AND apex_objeto.proyecto = '$this->proyecto'
				AND apex_objeto.clase = 'objeto_ei_filtro'
				AND apex_objeto_eventos.identificador = 'cancelar';";
		$this->ejecutar_sql($sql,"instancia");
	}

	/**
		Los eventos por defecto ahora se declaran explicitamente
	*/
	function cambio_eventos_implicitos()
	{
		//Los objetos que no tenian eventos definidos tenian un evento 'modificacion' por defecto.
		//Ahora el evento se declara en forma EXPLICITA y es marcado como implicito
		$sql = "	
				INSERT INTO apex_objeto_eventos(
					objeto,
					proyecto,
					implicito,
					identificador,
					maneja_datos			
				)
				SELECT 
					o.objeto,
					o.proyecto,
					1,
					'modificacion',
					1
				FROM apex_objeto o
				LEFT OUTER JOIN apex_objeto_eventos e
				           ON o.objeto = e.objeto
				           AND o.proyecto = e.proyecto
				WHERE 	o.clase IN ('objeto_ei_formulario', 'objeto_ei_formulario_ml', 'objeto_ei_filtro')
				AND 	o.proyecto = '$this->proyecto'
				GROUP BY 1, 2
				HAVING (COUNT(e.*) = 0);
		";
		$this->ejecutar_sql($sql,"instancia");
	}

	/**
		Establecer el menu milonic en los proyectos.
	*/
	function cambio_definicion_menu()
	{
		$sql = "UPDATE apex_proyecto 
				SET menu = 'milonic' 
				WHERE proyecto = '$this->proyecto'";
		$this->ejecutar_sql($sql,"instancia");
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
		$this->ejecutar_sql($sql,"instancia");
	}
}