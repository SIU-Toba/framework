<?
require_once("objeto_ei_formulario.php");	//Ancestro de todos los	OE
require_once("nucleo/browser/interface/ef.php");//	Elementos de interface

class	objeto_ei_formulario_ml	extends objeto_ei_formulario
/*
	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla

	- Registro de modificacion en el cliente
	- Array de modificacion para alguien que lo mapee en un buffer
	- Validacion en el cargar_post
	- Flag de agregar filas
	
	Un formulario tiene que saber que si viene del post, para dejar cargase datos o no???
	Cual es la solucion para esta competencia??
	
*/
{
	var $datos = array();		//Datos que tiene el formulario
	var $lista_ef_totales = array();
	
	function __construct($id)
/*
	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::__construct($id);
	}
	//-------------------------------------------------------------------------------

	function inicializar($parametros)
	{
		parent::inicializar($parametros);
	}
	//-------------------------------------------------------------------------------

	function inicializar_especifico()
	{
		for($a=0;$a<count($this->info_formulario_ef);$a++)
		{
			if($this->info_formulario_ef[$a]["total"]){
				$this->lista_ef_totales[] = $this->info_formulario_ef[$a]["identificador"];
			}
		}
		$this->modelo_eventos = "omni";
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//Formulario
		$sql["info_formulario"]["sql"] = "SELECT	auto_reset as	auto_reset,
										scroll as 					scroll,					
										ancho as					ancho,
										alto as						alto,
										filas as					filas,
										filas_agregar as			filas_agregar,
										ev_agregar				as 	ev_agregar,				
										ev_agregar_etiq			as 	ev_agregar_etiq,
										ev_mod_modificar		as 	ev_mod_modificar,		
										ev_mod_modificar_etiq	as 	ev_mod_modificar_etiq,
										ev_mod_eliminar         as 	ev_mod_eliminar,
										ev_mod_eliminar_etiq	as 	ev_mod_eliminar_etiq,
										ev_mod_limpiar	        as 	ev_mod_limpiar,
										ev_mod_limpiar_etiq		as 	ev_mod_limpiar_etiq										
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND		objeto_ut_formulario='".$this->id[1]."';";
		$sql["info_formulario"]["tipo"]="1";
		$sql["info_formulario"]["estricto"]="1";
		//EF
		$sql["info_formulario_ef"]["sql"] = "SELECT	identificador as identificador,
										columnas	as				columnas,
										obligatorio	as				obligatorio,
										elemento_formulario as		elemento_formulario,
										inicializacion	as			inicializacion,
										etiqueta	as				etiqueta,
										descripcion	as				descripcion,
										clave_primaria	as			clave_primaria,
										orden	as					orden,
										total as 					total,
										lista_columna_estilo as		columna_estilo
								FROM	apex_objeto_ut_formulario_ef
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND	objeto_ut_formulario='".$this->id[1]."'
								AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql["info_formulario_ef"]["tipo"]="x";
		$sql["info_formulario_ef"]["estricto"]="1";
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INFORMACION	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	info_estado_ef()
/*
	@@acceso: actividad
	@@desc: Muestra el estado de los	EF
*/
	{
		foreach ($this->lista_ef as $ef){
			$temp1[$ef]	= $this->elemento_formulario[$ef]->obtener_estado();
			$temp2[$ef]	= $this->elemento_formulario[$ef]->obtener_dato();
		}
		$temp["DATOS"]=$temp2;
		$temp["ESTADO"]=$temp1;
		//ei_arbol($temp,"Estado actual	de	los ELEMENTOS de FORMULARIO");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_evento()
	//ATENCION: esto hay que pensarlo. Que eventos se necesitan??
	// Como es la interaccion de un ML con un buffer?
	{
		if($this->controlar_agregar())
		{
			return "alta";
		}
		if($this->controlar_eliminar())
		{
			return "baja";
		}
		if($this->controlar_modificacion())
		{
			return "modificacion";
		}
		return null;
	}
	//-------------------------------------------------------------------------------

	function controlar_modificacion()
	//ATENCION: Esto hay que mejorarlo
	{
		if(acceso_post()){
			return true;
		}else{
			return false;	
		}
		/*//ei_arbol( $datos_actuales, "INTERFACE" );
		//ei_arbol( $this->memoria["datos"], "MEMORIA" );
		if(isset($this->memoria['datos'])){
			if(is_array($this->memoria['datos'])){
				$datos_actuales = $this->obtener_datos();
				foreach($datos_actuales as $clave => $dato){
					$dato = ereg_replace("NULL","",$dato);
					if( $this->memoria['datos'][$clave] != $dato){
						return true;
					}
				}
			}
		}*/
	}
	//-------------------------------------------------------------------------------
	
	function controlar_agregar()
	{
		return false;
	}
	//-------------------------------------------------------------------------------
	
	function controlar_eliminar()
	{
		return false;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function recuperar_interaccion()
	{
		if($this->cargar_post()==true){
			//$this->validar_estado();	
			//Se modificaron los datos?
			//ei_arbol($this->datos, "datos");
		}else{
			//echo ei_mensaje("No se cargo el POST");		
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_post()
	{
/*
	@@acceso: interno
	@@desc: Carga el estado	de	cada EF a partir del	POST!
*/
		if(isset($this->memoria["filas"]))
		{	
			$estado = true;
			//Por cada fila
			for($a=0; $a< $this->memoria["filas"]; $a++)
			{
				$estado = true;
				//1) Cargo los EFs
				foreach ($this->lista_ef as $ef){
					$this->elemento_formulario[$ef]->establecer_id_form($a);
					$x	= $this->elemento_formulario[$ef]->cargar_estado();
					if	(!$x){
						//$estado = false;
						//echo "ERROR en $ef <br>";
					}
				}
				//2) Seteo el registro
				$this->cargar_ef_a_registro($a);
			}
			return $estado;
		}else{
			//echo "Que pasa?";
		}


	}
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
	@@acceso: interno
	@@desc: Valida	el	registro
	@@pendiente: grados:	EJ	un	ef_oculto_proyecto no la deberia	dejar	pasar...
*/
	{
		//Valida	el	estado de los ELEMENTOS	de	FORMULARIO
		  $status =	true;
		foreach ($this->lista_ef as $ef){
			$temp	= $this->elemento_formulario[$ef]->validar_estado();
				if(!$temp[0]){
					 $status	= false;
					$this->registrar_info_proceso("[". $this->elemento_formulario[$ef]->obtener_etiqueta(). 
													"]	- ". $temp[1],"error");
				}
		}
		//Validacion ESPECIFICA
		//Carga los	datos	donde	el	VALIDADOR del HIJO los busca (ahorrar?)
		if(!$temp){
			$status = false;
		}
		  return	$status;
	}
	//-------------------------------------------------------------------------------

	function limpiar_interface()
/*
	@@acceso: actividad
	@@desc: Resetea los elementos	de	formulario
*/
	{
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->resetear_estado();
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_estado_ef($array_ef)
	{
		//ATENCION: En un multilinea esto es distinto. FALTA
	}
	//-------------------------------------------------------------------------------

	function	obtener_nombres_ef()
/*
	@@acceso: actividad
	@@desc: Recupera la lista de nombres de EF
	@@retorno: array | Listado	de	cada elemento de formulario
*/
	{
		foreach ($this->lista_ef_post	as	$ef){
			$nombres_ef[$ef] = $this->elemento_formulario[$ef]->obtener_id_form_orig();		
		}
		return $nombres_ef;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	---------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_datos()
	{
		return $this->datos;
	}
	//-------------------------------------------------------------------------------

	function cargar_datos($datos)
	{
		$this->datos = $datos;
	}
	//-------------------------------------------------------------------------------

	function existen_datos_cargados()
	{
		if(isset($this->datos)){
			if(count($this->datos) > 0){
				//ei_arbol($this->datos,"DATOS");
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------  Multiplexacion de EFs  ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_ef_a_registro($id_registro)
/*
	@@acceso: actividad
	@@desc: Carga el estado del array de EFs en un registro
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			//Aplano el estado del EF en un array
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("obtener_datos: Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$this->datos[$id_registro][$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$this->datos[$id_registro][$dato] = $estado;
			}
		}
		//ei_arbol($this->datos,"CArga de registros");
	}
	//-------------------------------------------------------------------------------

	function cargar_registro_a_ef($id_registro)
/*
	@@acceso: actividad
	@@desc: Carga un REGISTRO en el array de EFs
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		$datos =& $this->datos[$id_registro];
		foreach ($this->lista_ef as $ef)
		{
			//Seteo el ID-formulario del EF para que referencie al registro actual
			$this->elemento_formulario[$ef]->establecer_id_form($id_registro);
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja	 *** DATO COMPUESTO
				$temp = array();
				for($x=0;$x<count($dato);$x++){
					$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
				}
			}else{					//El EF maneja	un	*** DATO SIMPLE
				$temp = stripslashes($datos[$dato]);
			}
			$this->elemento_formulario[$ef]->cargar_estado($temp);
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//----------------------------	  SALIDA	  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_formulario()
	{
		//Tengo que leer los datos, si esque existen
		//SCROLL???
		if($this->info_formulario["scroll"]){
			$ancho = isset($this->info_formulario["ancho"]) ? $this->info_formulario["ancho"] : "500";
			$alto = isset($this->info_formulario["alto"]) ? $this->info_formulario["alto"] : "auto";
			echo "<div style='overflow: scroll; height: $alto; width: $ancho; border: 1px inset; padding: 0px;'>";
			echo "<table class='tabla-0'>\n";
		}else{
			$ancho = isset($this->info_formulario["ancho"]) ? $this->info_formulario["ancho"] : "100";
			echo "<table width='$ancho' class='tabla-0'>\n";
		}
		//------ TITULOS -----
		echo "<tr>";
		foreach ($this->lista_ef_post	as	$ef){
			echo "<td  class='abm-columna'>\n";
			echo $this->elemento_formulario[$ef]->envoltura_ei_ml();
			echo "</td>\n";
		}
		echo "</tr>\n";
		//------ FILAS ------
		if($this->existen_datos_cargados())
		{
			for($a=0;$a<count($this->datos);$a++)
			{
				$this->cargar_registro_a_ef($a);
				echo "\n<!-- FILA $a ------------->\n\n";
				echo "<tr>";
				foreach ($this->lista_ef_post	as	$ef){
					echo "<td class='abm-fila-ml'>\n";
					echo $this->elemento_formulario[$ef]->obtener_input();
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			$this->memoria["filas"] = $a;
			//En este caso tengo que agregar las lineas que falten	
			if(count($this->datos) < $this->info_formulario["filas"]){

			}
		}else
		{
			if(isset($this->info_formulario["filas"])){
				for($a=0;$a< $this->info_formulario["filas"];$a++){
					//Aca va el codigo que modifica el estado de cada EF segun los datos...
					echo "\n<!-- FILA $a ------------->\n\n";
					echo "<tr>";
					foreach ($this->lista_ef_post	as	$ef){
						echo "<td  class='abm-fila-ml'>\n";
						echo $this->elemento_formulario[$ef]->establecer_id_form($a);
						echo $this->elemento_formulario[$ef]->obtener_input();
						echo "</td>\n";
					}
					echo "</tr>\n";
				}
			$this->memoria["filas"] = $a;
			}
		}
		//------ Totales ------
		if(count($this->lista_ef_totales)>0){
			echo "\n<!-- TOTALES ------------->\n\n";
			echo "<tr>";
			foreach ($this->lista_ef_post as $ef){
				echo "<td  class='abm-total'>\n";
				if(in_array($ef, $this->lista_ef_totales)){
					$this->elemento_formulario[$ef]->cargar_estado(0);
					echo $this->elemento_formulario[$ef]->establecer_id_form("s");
					echo $this->elemento_formulario[$ef]->obtener_input();
				}else{
					echo "&nbsp;";
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
		//SCROLL???
		if($this->info_formulario["scroll"]){
			echo "</div>";
		}
	}
	//-------------------------------------------------------------------------------
}
?>