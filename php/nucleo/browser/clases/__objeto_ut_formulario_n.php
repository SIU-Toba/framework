<?
require_once("objeto_ut.php");	//Ancestro de todos los	OE
require_once("nucleo/browser/interface/ef.php");//	Elementos de interface

class	objeto_ut_formulario	extends objeto_ut
/*
	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla
*/
{
	var $elemento_formulario;		//	interno | array |	Rererencias	a los	ELEMENTOS de FORMULARIO
	var $nombre_formulario;			//	interno | string | Nombre del	FORMULARIO en el cliente
	var $etapa_actual;				//	interno | string | Etapa ACTUAL.	Puede	ser SA, SM,	PA	o PM.	Se	define en el metodo procesar()
	var $estado_proceso;				//	interno | string | "OK","ERROR","INFRACCION"
	var $lista_ef;						//	interno | array |	Lista	completa	de	a los	EF
	var $lista_ef_clave;				//	interno | array |	Lista	de	elementos que forman	parte	de	la	CLAVE	(PK)
	var $lista_ef_secuencia;		//	interno | array |	Lista	de	elementos que representan secuencias
	var $lista_ef_post;				//	interno | array |	Lista	de	elementos que se reciben por POST
	var $lista_ef_no_sql = array();
	var $flag_no_propagacion;		//	interno | string | Flag	que indica si hay	que dejar de reproducir	el	estado de la MEMORIA
	var $clave;							//	interno | array |	Clave	que esta	procesando el formulario
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INICIALIZACION	 --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
		
	function	objeto_ut_formulario($id)
/*
	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::objeto($id);
		$this->etapa_actual = "";
		$this->estado_proceso =	"";
		$this->lista_ef =	array();
		$this->lista_ef_clave =	array();
		$this->lista_ef_secuencia = array();
		$this->lista_ef_post	= array();
		$this->flag_no_propagacion	= "no_prop";
		//Reproduzco una posible memoria que se haya seteado 
		//(CLAVE que se esta modificando)
		$this->cargar_memoria();
		$this->memorizar();
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//-- Formulario ----------------------
		$sql["info_ut_formulario"]["sql"] = "SELECT	tabla	as	tabla,
										titulo as						titulo,
										ev_mod_eliminar as				ev_mod_eliminar,
										ev_mod_clave as					ev_mod_clave,
										ev_mod_limpiar	as				ev_mod_limpiar,
										auto_reset as					auto_reset,						
										campo_bl	as					campo_bl,
										ancho as						ancho
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND		objeto_ut_formulario='".$this->id[1]."';";
		$sql["info_ut_formulario"]["tipo"]="1";
		$sql["info_ut_formulario"]["estricto"]="1";
		//-- Formulario EF --------------
		$sql["info_ut_formulario_ef"]["sql"] = "SELECT	identificador as identificador,
										columnas	as					columnas,
										obligatorio	as					obligatorio,
										elemento_formulario as			elemento_formulario,
										inicializacion	as				inicializacion,
										etiqueta	as					etiqueta,
										descripcion	as					descripcion,
										clave_primaria	as				clave_primaria,
										orden	as						orden,
										-- Exclusivos del ML
										clave_primaria_padre as			clave_primaria_padre,
										listar as						listar,
										lista_cabecera as				lista_cabecera,
										lista_valor_sql as				lista_valor_sql,
										lista_orden as					lista_orden,
										no_sql as						no_sql
								FROM	apex_objeto_ut_formulario_ef
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND	objeto_ut_formulario='".$this->id[1]."'
								AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql["info_ut_formulario_ef"]["tipo"]="x";
		$sql["info_ut_formulario_ef"]["estricto"]="1";
		return $sql;
	}
//--------------------------------------------------------------------------------------------

	function	inicializar($parametros)
/*
	@@acceso: objeto
	@@desc: Dispara la creacion de los elementos	de	formulario (EF)
*/
	{
		$this->nombre_formulario =	$parametros["nombre_formulario"];
		  //Creo	el	array	de	objetos EF (Elementos de Formulario) que conforman	el	ABM
		$this->crear_elementos_formulario();
		//Cargo el array que	posee	los nombres	que los EF toman en el cliente
		//Esto es consumido por	el	VALIDADOR de REGISTRO del CLIENTE
		//Permite crear una rutina	de	validacion del	JAVASCRIPT usando	el	ID	del EF.
		foreach ($this->lista_ef_post	as	$ef){
			$this->nombre_ef_cli[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();
		}

		//-------- MANEJO de DEPENDENCIAS -----------
		
		
	}
	//-------------------------------------------------------------------------------
	
	function	crear_elementos_formulario()
/*
	@@acceso: interno
	@@desc: Genera	el	array	de	objetos EF que	constituye la columna vertebral del	ABM
*/
	{
		for($a=0;$a<count($this->info_ut_formulario_ef);$a++){

				//-[1]- Armo las listas	que determinan	el	plan de accion	del ABM
				$id = $this->info_ut_formulario_ef[$a]["identificador"];
				$this->lista_ef[]	= $id;
				switch ($this->info_ut_formulario_ef[$a]["elemento_formulario"]) {
					 case	"ef_oculto":
						  break;
					 case	"ef_oculto_secuencia":
						  $this->lista_ef_secuencia[]	= $id;
						  break;
					 case	"ef_oculto_proyecto":
						  break;
					 case	"ef_oculto_usuario":
						  break;
					 default:
						  $this->lista_ef_post[] =	$id;
				}
				//Lista de CLAVES	del ABM
				if($this->info_ut_formulario_ef[$a]["clave_primaria"]==1){
					 $this->lista_ef_clave[] =	$id;
				}
				//Columnas que no hay que utilizar para generar los SQL
				if($this->info_ut_formulario_ef[$a]["no_sql"]==1){
					 $this->lista_ef_no_sql[] =	$id;
				}
				//-[2]- Genero	el	ARRAY	de	ELEMENTOS de FORMULARIO
				//Genero	el	array	de	parametros que	inicializa al EF
				$parametros	= parsear_propiedades($this->info_ut_formulario_ef[$a]["inicializacion"]);
				//ei_arbol($parametros,"PARAMETRO adicional ELEMENTO:	".	$this->info_ut_formulario_ef[$a]["identificador"]);

			//A los elementos	que consulten la base les seteo la fuente	de	datos	que utiliza	el	objeto
			//Lo hago fijandome si tiene una	clave	"sql" y dejando la fuente particular que se haya seleccionado en la definición
			if(isset($parametros["sql"])){
				if(!isset($parametros["fuente"])){
					$parametros["fuente"]=$this->info["fuente"];
				}
			}
				//Preparo el identificador	del dato	que maneja el EF.
				//Esta parametro puede ser	un	ARRAY	o un string: exiten EF complejos	que manejan	mas de una
			//Columna de la tabla a	la	que esta	asociada	el	ABM
				if(ereg(",",$this->info_ut_formulario_ef[$a]["columnas"])){
					 $dato =	explode(",",$this->info_ut_formulario_ef[$a]["columnas"]);
				for($d=0;$d<count($dato);$d++){//Elimino espacios en las	claves
					$dato[$d]=trim($dato[$d]);
				}
				}else{
					 $dato =	$this->info_ut_formulario_ef[$a]["columnas"];
				}
			//Nombre	del formulario.
			$sentencia_creacion_ef = "\$this->elemento_formulario['".$id."']	=&	new ".
						$this->info_ut_formulario_ef[$a]["elemento_formulario"] ."(	\$this->id,	".
																"'" .	$this->nombre_formulario ."',	'". 
																	$id ."', '". 
																	$this->info_ut_formulario_ef[$a]["etiqueta"]	."', '".	
																	$this->info_ut_formulario_ef[$a]["descripcion"]	."', ". 
																	"\$dato,	'". 
																	$this->info_ut_formulario_ef[$a]["obligatorio"]	."', ".
																	"\$parametros);";
			//echo $sentencia_creacion_ef	. "<br>";
			eval($sentencia_creacion_ef);
			//Proceso dependencias
			//ATENCION!!!, solo se pueden consultar dependencias que se definieron ANTES!!
		}	
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
		ei_arbol($temp,"Estado actual	de	los ELEMENTOS de FORMULARIO");
	}
	//-------------------------------------------------------------------------------

	function	info()
/*
	@@acceso: actividad
	@@desc: Muestra es la informacion COMPLETA
*/
	{
		parent::info();
		ei_arbol($this->info_estado_ef());
	}	
	//-------------------------------------------------------------------------------

	function	permitir_eliminar()
/*
	@@acceso: objeto
	@@desc: Responde la interface	de	modificacion permite	eliminar	registros
*/
	{
		if($this->info_ut_formulario["ev_mod_eliminar"]==1){
			return true;
		}else{
			return false;		
		}
	}	

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	DEPENDENCIAS  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function procesar_dependencias()
	{
		foreach ($this->lista_ef as $ef){
			$dependencias = $this->elemento_formulario[$ef]->obtener_dependencias();
			if(is_array($dependencias)){
				//echo "entre $ef<br>";
				foreach( $dependencias as $dep ){
					//echo "entre $dep<br>";
					if(is_object($this->elemento_formulario[$dep])){
						$estado[$dep] = $this->elemento_formulario[$dep]->obtener_estado();
					}else{
						echo ei_mensaje("La dependencia '$dep' es invalida");
					}
				}
				$this->elemento_formulario[$ef]->cargar_datos_dependencias($estado);
			}
		}
	}

	function cargar_dependencias()
	{
		
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_db($clave)
/*
	@@acceso: interno
	@@desc: Busca un registro de la base y	lo	carga	en	los EF.
*/
	{
		if(!isset($clave)) return false;
		//Busco las	columnas	que tengo que recuperar
		foreach ($this->lista_ef as $ef){	//Tengo que	recorrer todos los EF...
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				for($x=0;$x<count($dato);$x++){
					$sql_col[] = $dato[$x];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$sql_col[] = $dato;
			}
		}
		$sql_col = array_diff($sql_col, $this->lista_ef_no_sql);
		//Armo la porcion	de	SQL que corresponde al WHERE
		$clave_ok =	$this->formatear_clave($clave);
		foreach($clave_ok	as	$columna	=>	$valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		$sql =	" SELECT	" . implode(",	",$sql_col)	. 
				" FROM "	. $this->info_ut_formulario["tabla"] .
				" WHERE " .	implode(" AND ",$sql_where) .";";
		//Busco el registro en la base
		global $db,	$ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE	= ADODB_FETCH_ASSOC;
		$rs =	$db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$rs){//SQL	mal formado
			$this->observar("error","[recuperar_registro_db] -	No	se	genero un recordset [SQL] $sql -	[ERROR] " .	
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
		}
		if($rs->EOF){//NO	existe el registro
			return false;
		}
		$datos_db =	current($rs->getArray());//Siempre va a ser un solo registro
		//ei_arbol($datos_db,"DATOS DB");
		//Seteo los	EF	con el valor recuperado
		foreach ($this->lista_ef as $ef){	//Tengo que	recorrer	todos	los EF...
			if(!in_array($ef,$this->lista_ef_no_sql)){
				$dato	= $this->elemento_formulario[$ef]->obtener_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					$temp	= array();
					for($x=0;$x<count($dato);$x++){
						$temp[$dato[$x]]=	stripslashes($datos_db[$dato[$x]]);
					}
				}else{					//El EF maneja	un	DATO SIMPLE
					$temp	= stripslashes($datos_db[$dato]);
				}
				$this->elemento_formulario[$ef]->cargar_estado($temp);
			}
		}
		//Memorizo que clave cargue de la base
		$this->memoria["clave"] = $clave_ok;
		$this->memorizar();
		$this->procesar_dependencias();
		return true;
	}
	//-------------------------------------------------------------------------------

	function	cargar_post()
/*
	@@acceso: interno
	@@desc: Carga el estado	de	cada EF a partir del	POST!
*/
	{
		//Cargo los	EF	que forman parte del	formulario del	cliente
		$ok =	false;
		foreach ($this->lista_ef as $ef){
			$x	= $this->elemento_formulario[$ef]->cargar_estado();
			if	($x) $ok	= $x;
		}
		return $ok;
	}
	//-------------------------------------------------------------------------------

	function	validar_estado()
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

	function cargar_estado_ef($array_ef)
/*
	@@acceso: actividad
	@@desc: Esta funcion	permite establecer el valor de un elemento del FORMULARIO (Visible u	Oculto)
	@@param:	array	| una	entrada por	EF	(id->valor)	que quiera cargar. los EF compuestos tienen que	recibir como valor un ARRAY con la forma que	espera el EF destino
*/
	{
		if(is_array($array_ef)){
			foreach($array_ef	as	$ef => $valor){
				if(isset($this->elemento_formulario[$ef])){
					$this->elemento_formulario[$ef]->cargar_estado($valor);
				}else{
					$this->registrar_info_proceso("[cargar_estado_ef] No existe	un	elemento	de	formulario identificado	'$ef'","error");
				}
			}
		}else{
			$this->registrar_info_proceso("[cargar_estado_ef] Los	EF	se	cargan a	travez de un array asociativo	(\"clave\"=>\"dato a	cargar\")!","error");
		}
	}
	//-------------------------------------------------------------------------------

	function	limpiar_interface()
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

	function	ejecutar_metodo_ef($ef,	$metodo,	$parametro=null)
/*
	@@acceso: actividad
	@@desc: Esto sirve para	comunicarse	con EF que pueden	cambiar en tiempo	de	ejecucion
	@@desc: EJ:	un	combo	que necesita cambiar	una propiedad del	WHERE	segun	la	solicitud
	@@param:	string |	elemento	de	formulario a llamar
	@@param:	string |	metodo a	llamar en el EF
	@@param:	array	| Argumentos de la funcion
*/
	{
		if(isset($this->elemento_formulario[$ef])){
			return $this->elemento_formulario[$ef]->$metodo($parametro);
		}else{
			echo ei_mensaje("El EF identificado	'$ef'	no	existe.");
		}
	}
	//-------------------------------------------------------------------------------

	function	actualizacion_post_insert()
/*
	@@acceso: interno
	@@desc: Recupera el valor de las	secuencias de la base
*/
	{
		if(is_array($this->lista_ef_secuencia)){//Hay secuencias?
			global $db,	$ADODB_FETCH_MODE;
			//Itero las	secuencias y les cargo su estado
			foreach($this->lista_ef_secuencia as $secuencia){
				$columna	= $this->elemento_formulario[$secuencia]->obtener_dato();//Una	secuencia no puede tener un dato	compuesto.
				$sql = "SELECT	MAX($columna) FROM {$this->info_ut_formulario['tabla']};";
				$ADODB_FETCH_MODE	= ADODB_FETCH_NUM;
				$rs =	$db[$this->info["fuente"]][apex_db_con]->Execute($sql);
				if(!$rs){//SQL	mal formado
					$this->observar("error","OBJETO UT - Formulario	[actualizacion_post_insert] -	error	buscando	el	valor	de	la	secuencia: $secuencia 
								[SQL]	$sql - [ERROR]	" . $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),false,true,true);
				}
				if($rs->EOF){//NO	existe el registro
					$this->observar("error","OBJETO UT - Formulario	[actualizacion_post_insert] -	error	buscando	el	valor	de	la	secuencia (NULL) : $secuencia",false,true,true);
				}
				if(trim($rs->fields[0])==""){
					$this->observar("error","OBJETO UT - Formulario	[actualizacion_post_insert] -	error	buscando	el	valor	de	la	secuencia (\"\") : $secuencia",false,true,true);
				}
				//Cargo el valor de la secuencia	en	el	EF
				$this->elemento_formulario[$secuencia]->cargar_estado($rs->fields[0]);	
			}
		}
	}
	//-------------------------------------------------------------------------------

	function	obtener_datos()
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("obtener_datos: Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$registro[$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$registro[$dato] = $estado;
			}
		}
		return $registro;
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
			$nombres_ef[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();
		}
		return $nombres_ef;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------  Control EXTERNO  ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_estado_interno()
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual de los elementos del FORMULARIO
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef){
			$estado[$ef] = $this->elemento_formulario[$ef]->obtener_estado();
		}
		return $estado;
	}
	//-------------------------------------------------------------------------------

	function establecer_estado_interno($estado)
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual de los elementos del FORMULARIO
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($estado as $ef => $contenido){
			$this->elemento_formulario[$ef]->cargar_estado($contenido);
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------------	 Generacion	de	SQL	------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_sql($tipo="insert")
/*
	@@acceso: objeto
	@@desc: Devuelve el SQL	de	esta UT
*/
	{
		switch($tipo){
			case "insert":
				return $this->generar_sql_insert();
				break;
			case "delete":
				return $this->generar_sql_delete();
				break;
			case "update":
				return $this->generar_sql_update();
				break;
		}
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_insert()
/*
	@@acceso: objeto
	@@desc: GENERA	un	SQL de insersion para el registro cargado	en	la	INTERFACE
*/
	{
		global $db;
		foreach ($this->lista_ef as $ef){					//Tengo que	recorrer	todos	los EF...
			if(!(in_array($ef,$this->lista_ef_secuencia)))	//...	Menos	las secuencias
			{
				$dato	= $this->elemento_formulario[$ef]->obtener_dato();
				$estado = $this->elemento_formulario[$ef]->obtener_estado();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
						return array(false,"procesar_insert: Error de consistencia interna en el EF etiquetado: ".
											$this->elemento_formulario[$ef]->obtener_etiqueta() );
					}
					for($x=0;$x<count($dato);$x++){
						$sql_col[] = $dato[$x];
						$sql_val[] = $estado[$dato[$x]];
					}
				}else{					//El EF maneja	un	DATO SIMPLE
					$sql_col[] = $dato;
					$sql_val[] = $estado;
				}
			}
		}
		//Reduzco repeticiones en los	ARRAYS y ESCAPO caracteres
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		for($a=0;$a<count($sql_col);$a++){
			//El campo tiene que manejarse como SQL?
			if(!in_array($sql_col[$a],$this->lista_ef_no_sql) ){
				$columnas[$sql_col[$a]]	= addslashes($sql_val[$a]);
			}
		}
		$sql_col = array_keys($columnas);
		$sql_val = array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");

		//Genero	el	SQL de INSERCION
		$sql = "INSERT	INTO ". $this->info_ut_formulario["tabla"] ." (". implode(",",$sql_col)	.") 
				VALUES ('".	implode("','",$sql_val)	."');";
		//ATENCION!!: esto implica	que nunca se va a	poder	grabar la palabra	"NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		//return	array(false,"SQL:	".$sql);
		return array($sql);
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_update()
/*
	@@acceso: interno
	@@desc: Realizo un UPDATE del	registro	en	la	base.
*/
	{
		global $db;
		//Recupero los	valores de los	EF, para	generar el SQL	de	UPDATE
		foreach ($this->lista_ef_post	as	$ef){		//Recorro SOLO	los EF que vienen	del POST
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					//No tengo que	dejar	una observacion tambien???
					ei_arbol($dato,"DATOS manejados");
					ei_arbol($estado,"ESTADO interno");
					//return	array(false,"procesar_update:	Error	de	consistencia interna	en	el	EF	etiquetado:	".
						//				$this->elemento_formulario[$ef]->obtener_etiqueta() );
					return "";
				}
				for($a=0;$a<count($dato);$a++){
					$sql_col[] = $dato[$a];
					$sql_val[] = $estado[$dato[$a]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$sql_col[] = $dato;
				$sql_val[] = $estado;
			}
		}
		//Reduzco repeticiones en los	ARRAYS y ESCAPO CARACTERES
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		for($a=0;$a<count($sql_col);$a++){
			//El campo tiene que manejarse como SQL?
			if(!in_array($sql_col[$a],$this->lista_ef_no_sql) ){
				$columnas[$sql_col[$a]]	= addslashes($sql_val[$a]);
			}
		}
		//Tengo que sacar las columnas que no van en el SQL
		

		//Si no se pueden	modificar las claves, las elimino de la sentencia
		if($this->info_ut_formulario["ev_mod_clave"]!="1"){
			foreach(	array_keys($this->obtener_clave()) as $columna){
				unset($columnas[$columna]);
			}
		}
		//Sero los nombres de las columnas de los	valores
		$sql_col	= array_keys($columnas);
		$sql_val	= array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		//Armo la porcion	de	SQL que corresponde a las COLUMNAS
		for($a=0;$a<count($sql_col);$a++){
			$sql_update[] = $sql_col[$a] . "	= '" . $sql_val[$a] . "'";
		}
		//Armo la porcion	de	SQL que corresponde al WHERE
		$clave_ok = $this->formatear_clave($this->memoria["clave"]);
		foreach(	$clave_ok as $columna => $valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		//Armo el SQL completo
		$sql =	" UPDATE	" . $this->info_ut_formulario["tabla"]	. 
				" SET	" . implode(",	",$sql_update)	. 
				" WHERE " .	implode(" AND ",$sql_where) .";";
		//ATENCION!!: esto implica	que nunca se va a	poder	grabar la palabra	"NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		return array($sql);
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_delete()
/*
	@@acceso: interno
	@@desc: ELIMINA el registro en la BASE
*/
	{
		global $db;
		//Grabo el contenido	de	la	interface en la base
		//Armo la porcion	de	SQL que corresponde al WHERE
		$clave_ok = $this->formatear_clave($this->memoria["clave"]);
		foreach(	$clave_ok as $columna => $valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		$sql =	" DELETE	FROM " .	$this->info_ut_formulario["tabla"] . 
				" WHERE " .	implode(" AND ",$sql_where) .";";
		return array($sql);
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------	  MANEJO	de	la	PK	  ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	obtener_clave($refrescar_memoria = true)
/*
	@@acceso: actividad
	@@desc: Devuelve la clave que	se	esta procesando como	un	array	asociativo (dato/valor)
	@@desc: (El	dato representa la columna	de	la	tabla).La clave de los EFs
	@@desc: Atencion:	SI	el	ABM esta	configurado	en	AUTO-RESET,	es	probable	que este	metodo devuelva un NULL
*/
	{
		$clave_actual = array();//Preparo un array para	cargar claves
		foreach($this->lista_ef_clave	as	$clave)
		{
			$temp	= $this->elemento_formulario[$clave]->obtener_estado();
			if(is_array($temp)){//Los EF compuestos ya devuelven un array dato/valor
				$clave_actual = array_merge($clave_actual,$temp);
			}else{//Los	EF	simples devuelven	un	string, tengo que	armar	el	par dato/valor	a mano
				$dato	= $this->elemento_formulario[$clave]->obtener_dato();
				$clave_actual[$dato]	= $temp;
			}
		}
		if($refrescar_memoria){
			//Refresco la clave de la memoria, si es que se puede modificar...
			if($this->info_ut_formulario["ev_mod_clave"]=="1"){
				$this->memoria["clave"] = $clave_actual;
				$this->memorizar();
			}
		}
		return $clave_actual;
	}
	//-------------------------------------------------------------------------------

	function	formatear_clave($clave_pos)
/*
	@@acceso: interno
	@@desc: Le da formato a	un	clave	definida	en	forma	posicional,	transformandola en formato	asociativo
	@@param:	
*/
	{
		//ei_arbol($clave_pos,"Clave RECIBIDA");
		//Obtengo los nombres de los indices (datos manejados	por los EF clave)
		foreach($this->lista_ef_clave	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_dato();
			//Si se maneja	un	dato complejo lo DESARMO...				
			if(is_array($temp)){
				//ei_arbol($temp,"Dato recibido de un EF");
				for($a=0;$a<count($temp);$a++){
					$dato[] = $temp[$a];
				}
			}else{
				$dato[] = $temp;
			}
		}
		//Si la cantidad de indices no coincide con la cantidad de valores, algo esta	mal
		if(count($clave_pos)!=count($dato)){
			echo ei_mensaje("UT - FORMULARIO	[ " .	$this->id[1] .	" ] -	La	clave	especificada no corresponde con la definida","error");
			ei_arbol($clave_pos,"CLAVE	recibida");
			ei_arbol($dato,"Estructura	esperada");
			//No puedo seguir	el	procesamiento si las	cosas	estan	asi
			$this->observar("error","[formatear_clave] -	La	clave	especificada esta	mal formada",true,false,true);
		}
		//Armo la definicion	asociativa.	El	criterio	es: el orden de los valores pasados
		//tiene que	corresponder al orden de los EF,	y dentro	de	estos	al	orden	de	las
		//columnas definidas.
		$indice = 0;
		//Itero de esta manera para dar la posibilidad de pasar una	clave	no	numerica	(aunque ordenada)
		foreach($clave_pos as $clave){
			$clave_asoc[$dato[$indice]]=$clave;
			$indice++;
		}
		return $clave_asoc;
	}
	//-------------------------------------------------------------------------------

	function	establecer_clave_maestro($clave)
/*
	@@acceso: interno
	@@desc: Si este elemento funciona como	detalle en un MT_MDS, recibe la clave del	maestro
*/
	{
		//En este caso	solo se necesita setear	los EF implicados
		$this->cargar_estado_ef($clave);
	}

	//-------------------------------------------------------------------------------

	function inhabilitar_modificacion_claves()
/*
	@@acceso: actividad
	@@desc: Inhabilita la modificacion de claves.
*/
	{
		if($this->info_ut_formulario["ev_mod_clave"]!="1"){
			foreach($this->lista_ef_clave	as	$ef){
				if(in_array($ef, $this->lista_ef_post)){
					$this->elemento_formulario[$ef]->establecer_solo_lectura();
				}
			}
		}
	}
	//-------------------------------------------------------------------------------
    function lista_claves()
/*
	@@acceso: mt_mds
	@@desc: Devuelve una lista con las claves del formulario.
*/

    {
    	if (isset($this->lista_ef_clave)) 
    	{
    	    return $this->lista_ef_clave;
    	}
        else
        {
            return array();
        }
    }
    

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	consumo_javascript_global()
	{
		//Busco las	dependencias
		$consumo	= array();
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo	= array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}
	//-------------------------------------------------------------------------------

	function	obtener_javascript()
/*
	@@acceso: interno
	@@desc: devuelve el javascript del formulario
*/
	{
		$javascript	= "";
		//Obtengo el javascript	de	validacion de cada EF
		foreach ($this->lista_ef_post	as	$ef){
			$javascript	.=	$this->elemento_formulario[$ef]->obtener_javascript();
		}
		$javascript	.=	"\n\n";
		return $javascript;
	}
	//-------------------------------------------------------------------------------

	function	obtener_html()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero	la	interface
		if($this->estado_proceso!="INFRACCION")
		{
			echo "\n<!-- ***************** Inicio UT FORMULARIO (	".	$this->id[1] ." )	***********	-->\n\n";
			echo "<table width='100%' class='tabla-0'>";
			foreach ($this->lista_ef_post	as	$ef){
				echo "<tr><td class='abm-fila'>\n";
				$this->elemento_formulario[$ef]->obtener_interface_ut();
				echo "</td></tr>\n";
			}
			echo "</table>\n";
			echo "\n<!-- ----------------	Fin UT FORMULARIO	(". $this->id[1] .")	--------------	-->\n\n";
		}
	}
	//-------------------------------------------------------------------------------

}
?>