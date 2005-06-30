<?php

class objeto_ci_test extends objeto_ci_me
{
	
	protected $nombre_de_la_propiedad_a_persistir;

	function __construct($id)
	{
		parent::__construct($id);
	}

	function destruir()
	{
		parent::destruir();
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "nombre_de_la_propiedad_a_persistir";
		return $estado;
	}

	function get_dependencias_ci()
	//Esto es para explicarle a procesos internos cual es la lista de CI que existen
	//Solo es necesario cuando existe una regla ad-hoc que define que dependencias usar
	{
	}

	function evt__limpieza_memoria()
	//Esto puede redefinirse cuando se quiere modificar el plan de desmemorizacion
	//EJ: que borre todo menos el estado del filtro...
	{
		//Indicar una lista de propiedades que no se tienen que borrar
		parent::evt__limpieza_memoria($propiedades);
	}


	//-----------------------------------------------
	//-------------- EVENTOS GLOBALES ---------------
	//-----------------------------------------------

	function evt__incializar()
	//Antes de realizar cualquier actividad
	{
	}

	function evt__post_recuperar_interaccion()
	//Despues de recuperar la interaccion con el usuario
	//Este es un buen lugar para elevar un evento al contenedor relacionado on la informacion ingresada por el usuario
	{
	/*
		EJ: $this->reportar_evento("id_del_evento", $datos );
				(obviamente tiene que haber un metodo en el contenedor que reciba esto)
		Por defecto este metodo llama a $this->evt__validar();			
	*/
	}

	function evt__pre_cargar_datos_dependencias()
	//Antes de cargar datos en las dependencias
	//Este es un buen lugar para
	{
	}

	function evt__validar_datos()
	//Valida el conjunto de datos
	//	- En los CI se ejecuta automaticamente despues de cada request
	//	- En los CI_ME se ejecuta antes de mandarle los datos al CN
	//Si algo esta mal debe disparar una excepcion de tipo ...
	{
	}

	//-- Relacion CN ------------------

	function evt__obtener_datos_cn()
	//Se disparo la secuencia de obtencion de datos
	//Se llama explicitamente a travez de "disparar_obtencion_datos()"
	{
	}

	function evt__entregar_datos_cn()
	//Se disparo la secuencia de entrega de datos al CN
	//Por defecto se llama desde el eventos evt__procesar
	{
	}

	//-- Agregar EVENTOS AD-HOC (botones) ----------------------

	function get_lista_eventos()
	//Generacion de la lista de botones.
	{
		//Agrego un boton llamado "Proceso especial";
		//Es importante tomar en cuenta
		$evento = parent::get_lista_eventos();
		$evento['procesar_cosas']['etiqueta'] = "Proceso especial";
		$evento['procesar_cosas']['imagen'] = "";
		$evento['procesar_cosas']['confirmacion'] = "";
		$evento['procesar_cosas']['estilo']="";
		$evento['procesar_cosas']['tip']="";
		return $evento;
	}
	
	function evt__procesar_cosas()
	//Metodo que atrapa el uso del boton anterior
	{
	}
	
	//-----------------------------------------------
	//-- Administracion de DEPENDENCIAS ----------------
	//-----------------------------------------------

	/*
		En este ejemplo el ID de la dependencia en el CI es 'doc'
	*/

	function evt__doc__alta(){}
	function evt__doc__modificacion(){}
	function evt__doc__baja(){}
	function evt__doc__limpiar(){}
	function evt__doc__carga(){ 
		$datos_carga = array("Datos que cargan el EI");
		return $datos_carga; 
	}

	//-----------------------------------------------
	//-------------------  ETAPAS -------------------
	//-----------------------------------------------

	function get_etapa_actual()						//ME
	//Sobreescribir la navegacion entre etapas
	{
		return "id_etapa_actual"; //Tiene que ser valido!
	}

	function evt__entrada__10() 						//ME (etapa == 2)
	//Entrada en la etapa 10
	{
		$error = true;
		if($error){
			throw new excepcion_toba("No es valido ENTRAR en la etapa 10");
		}
	}					

	function evt__salida__0()						//ME (etapa == 0)
	//Salida de la etapa 0
	{
		$error = true;
		if($error){
			throw new excepcion_toba("No es valido SALIR en la etapa 0");
		}		
	}
	
	//--------------------------------------------------------------------------
	//-----  Informacion NECESARIA para la  GENERACION de INTERFACE ------------
	//--------------------------------------------------------------------------

	function get_lista_ei()
	//Sobreescribir la lista de EIs a mostrar
	{
		$ei[0] = "id_ei_0";
		$ei[1] = "id_ei_1";
		$ei[2] = "id_ei_2";
		return $ei;
	}

	function get_lista_ei__0()						//ME (etapa == 0)
	//Sobreescribir la lista de EIs a mostrar sobre una etapa puntual de un ME.	
	{
		$ei[0] = "id_ei_0";
		$ei[1] = "id_ei_1";
		$ei[2] = "id_ei_2";
		return $ei;
	}					

	function get_lista_tabs()						//ME-sel
	//Sobreescribir la lista de tabs a mostrar
	{
		$tab['id']['etiqueta'] = "";
		$tab['id']['imagen'] = "";
		$tab['id']['tip']="";
 		return $tab;
	}

	//-----------------------------------------------
	//------- Generacion de interface CRUDA ---------
	//-----------------------------------------------

	function obtener_html_contenido__0()			//ME (etapa == 0)
	//Reescibir ad-hoc la constuccion de la pantalla en una etapa puntual de un ME
	{
	}

	//-----------------------------------------------
	//------------ Alimentacion de DAOS -------------
	//-----------------------------------------------

	function get_datos_combo_x()
	//DAOS dependientes del estado de la interface
	{
	}

	function get_datos_cuadro_y()
	//DAOS dependientes del estado de la interface
	{
	}
	//-----------------------------------------------
}
?>