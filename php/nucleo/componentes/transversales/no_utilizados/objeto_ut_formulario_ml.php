<?
require_once("objeto_ut_formulario.php");	//Ancestro de todos los	OE
/*
	hay que pensar un metodo alternativo de mostrar las lineas (ejemplo: sumarizaciones)
	paso de la clave padre (memorizar para setear proximos)
*/

class	objeto_ut_formulario_ml	extends objeto_ut_formulario
/*
	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla
*/
{
	var $estado;					//Estado interno del multilinea
	var $lista_ef_clave_padre;		//	interno | array |	Lista	de	elementos que forman parte de	la	CLAVE	(PK)
	var $lista_ef_mostrar;			//	interno | array |	Lista	de	elementos que forman parte del display
	var $submit;		

	function objeto_ut_formulario_ml($id)
/*
	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::objeto_ut_formulario($id);
		//Nombre del boton submit
		$this->submit = "ut_f_ml_" . $this->id[1] . "_sunmit";
	}

	function inicializar($parametros)
/*
	@@acceso: objeto
	@@desc: Dispara la creacion de los elementos	de	formulario (EF)
*/
	{
		parent::inicializar($parametros);
		$posicion_mostrar = 0;
		for($a=0;$a<count($this->info_ut_formulario_ef);$a++){
			//Busco los campos que tienen la clave del padre
			if($this->info_ut_formulario_ef[$a]["clave_primaria_padre"]=="1"){
				 $this->lista_ef_clave_padre[] = $this->info_ut_formulario_ef[$a]["identificador"];
			}
			//y veo que campos hay que mostrar
			if($this->info_ut_formulario_ef[$a]["listar"]=="1"){
				//Posicion del elemento en el array de informacion
				$this->lista_ef_mostrar[$posicion_mostrar]["info"] = $a;
				//Nombre del elemento
				$this->lista_ef_mostrar[$posicion_mostrar]["ef"] = $this->info_ut_formulario_ef[$a]["identificador"];
				$posicion_mostrar++;
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_db($clave)
/*
	@@acceso: interno
	@@desc: Busca los registros que corresponden a la clave del padre
*/
	{
		if(!isset($clave)) return false;
		//Busco las	columnas	que tengo que recuperar
		foreach ($this->lista_ef as $ef){	//Tengo que	recorrer	todos	los EF...
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				for($x=0;$x<count($dato);$x++){
					$sql_col[] = $dato[$x];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$sql_col[] = $dato;
			}
		}
		//Armo la porcion de SQL que corresponde al WHERE (PADRE)
		$clave_ok =	$this->formatear_clave_padre($clave);
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
		$this->estado["datos"] = $rs->getArray();//Siempre va a ser un solo registro
		//Seteo la lista de SINCRO, los registros obtenidos estan en estado UPDATE
		foreach(array_keys($this->estado["datos"]) as $id){
			$this->estado["registros"][$id] = "db";//FLAG de cargador de la DB
		}
		$this->estado["proximo_registro"] = count($this->estado["datos"]);
		//ei_arbol($this->estado);
		$this->estado["clave"] = $clave_ok;
		return true;
	}
	//-------------------------------------------------------------------------------

	function cargar_post()
/*
	@@acceso: interno
	@@desc: Recupera el estado del UT desde el POST
*/
	{
		//echo "Cargar POST: ".$this->info['titulo']."<br>";
		$status = parent::cargar_post();
		// Se cargo un REGISTRO en el FORM?
		foreach( array_keys($this->estado["registros"]) as $registro){
			if(isset($_POST[$this->submit."_".$registro."_x"])){
				$this->establecer_registro_activo($registro);
				return $status;
			}
		}
		//Se Elimino o Modifico un regitro cargado?
		if(isset($this->estado["registro_activo"])){
			if(isset($_POST[$this->submit."_U"])){
				$this->modificar_registro_activo();
			}elseif(isset($_POST[$this->submit."_D"])){
				$this->eliminar_registro_activo();
			}else{
				$this->desactivar_registro_activo();
			}
		}else{
			//Se inserto un REGISTRO
			if(isset($_POST[$this->submit."_I"])){
				$this->insertar_registro();
			}else{
				$this->desactivar_registro_activo();
			}
		}
	}
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
	@@acceso: interno
	@@desc: Valida	el	registro
*/
	{
		//ATENCION: Esto requiere una reimplementacion!!!
		//return parent::validar_estado();
	}
	//-------------------------------------------------------------------------------

	function obtener_datos()
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		return $this->estado["datos"];
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	Manejo de REGISTROS	 ------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function establecer_registro_activo($id)
	//Cargar un REGISTRO de la memoria al FORM
	{
		$this->estado["registro_activo"] = $id;
		//ei_arbol($this->estado["datos"][$id],"DATOS Registro");
		//Seteo los	EF	con el valor memorizado de ese registro
		foreach ($this->lista_ef as $ef){	//Tengo que	recorrer	todos	los EF...
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				$temp	= array();
				for($x=0;$x<count($dato);$x++){
					$temp[$dato[$x]]=	stripslashes($this->estado["datos"][$id][$dato[$x]]);
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$temp = stripslashes($this->estado["datos"][$id][$dato]);
			}
			$this->elemento_formulario[$ef]->cargar_estado($temp);
		}
	}
	//-------------------------------------------------------------------------------

	function eliminar_registro_activo()
	{
		//echo "DELETE: ". $this->estado["registro_activo"];
		if( $this->estado["registros"][$this->estado["registro_activo"]] != "I" ){
			$this->estado["registros"][$this->estado["registro_activo"]] = "D";//FLAG de modificacion
		}else{
			unset($this->estado["datos"][$this->estado["registro_activo"]]);
			unset($this->estado["registros"][$this->estado["registro_activo"]]);
		}
		$this->desactivar_registro_activo();
	}
	//-------------------------------------------------------------------------------

	function modificar_registro_activo()
	{
		//echo "UPDATE: ". $this->estado["registro_activo"];
		$this->estado["datos"][$this->estado["registro_activo"]] = parent::obtener_datos();
		//Control de modificacion???
		//Si no es un INSERT pasa a modificado (sino sigue siendo insert, pero con nuevos valores)
		if( $this->estado["registros"][$this->estado["registro_activo"]] != "I" ){
			$this->estado["registros"][$this->estado["registro_activo"]] = "U";//FLAG de modificacion
		}
		if($this->info_ut_formulario['auto_reset']=="1"){
			$this->desactivar_registro_activo();
		}
	}
	//-------------------------------------------------------------------------------

	function insertar_registro()
	{
		//echo "INSERT: ". $this->estado["registro_activo"];
		$posicion = $this->estado["proximo_registro"];
		$this->estado["datos"][$posicion] = parent::obtener_datos();
		$this->estado["registros"][$posicion] = "I";//FLAG de INSERCION
		$this->estado["registro_activo"] = $posicion;
		if($this->info_ut_formulario['auto_reset']=="1"){
			$this->desactivar_registro_activo();
		}
		$this->estado["proximo_registro"]++;
	}
	//-------------------------------------------------------------------------------

	function desactivar_registro_activo()
	{
		unset($this->estado["registro_activo"]);
		$this->limpiar_interface();
	}
	//-------------------------------------------------------------------------------

	function obtener_clave_registro_activo()
	//Devuelve la clave del registro activo, si es que existe
	{
		if(isset($this->estado["registro_activo"])){
			return 	$this->obtener_clave_registro($this->estado["registro_activo"]);
		}else{
			return null;	
		}
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
		return $this->estado;
	}
	//-------------------------------------------------------------------------------

	function establecer_estado_interno($estado)
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual de los elementos del FORMULARIO
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		$this->estado = $estado;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------------	 Generacion	de	SQL	------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_sql()
/*
	@@acceso: objeto
	@@desc: Devuelve el SQL	de	esta UT
*/
	{
		$sql = array();
		if(isset($this->estado["datos"])){
			foreach(array_keys($this->estado["datos"]) as $registro){
				switch($this->estado["registros"][$registro] ){
					case "I":
						$sql[] = $this->generar_sql_insert($registro);
						break;
					case "D":
						$sql[] = $this->generar_sql_delete($registro);
						break;
					case "U":
						$sql[] = $this->generar_sql_update($registro);
						break;
				}
			}
			return $sql;
		}else{
			//echo ei_mensaje("No se cargo el estado de la dependencia: ". $this->id[1]);
			//No tengo datos cargados, no muestro nada.
		}
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_insert($registro)
/*
	@@acceso: objeto
	@@desc: GENERA	un	SQL de insersion para el registro cargado	en	la	INTERFACE
*/
	{
		$columnas = $this->estado["datos"][$registro];
		$columnas = array_map("addslashes",$columnas);
		$sql_col	= array_keys($columnas);
		$sql_val	= array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");

		//Genero	el	SQL de INSERCION
		$sql = "INSERT	INTO ". $this->info_ut_formulario["tabla"] ." (". implode(",",$sql_col)	.") 
				VALUES ('".	implode("','",$sql_val)	."');";
		//ATENCION!!: esto implica	que nunca se va a	poder	grabar la palabra	"NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_update($registro)
/*
	@@acceso: interno
	@@desc: Realizo un UPDATE del	registro	en	la	base.
*/
	{
		$columnas = $this->estado["datos"][$registro];
		//Si no se pueden	modificar las claves, las elimino de la sentencia
		if($this->info_ut_formulario["ev_mod_clave"]!="1"){
			foreach(	array_keys($this->obtener_clave_registro($registro)) as $columna){
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
		$clave_ok = $this->obtener_clave_registro($registro);
		foreach(	$clave_ok as $columna => $valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		//Armo el SQL completo
		$sql =	" UPDATE	" . $this->info_ut_formulario["tabla"]	. 
				" SET	" . implode(",	",$sql_update)	. 
				" WHERE " .	implode(" AND ",$sql_where) .";";
		//ATENCION!!: esto implica	que nunca se va a	poder	grabar la palabra	"NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_delete($registro)
/*
	@@acceso: interno
	@@desc: ELIMINA el registro en la BASE
*/
	{
		$clave = $this->obtener_clave_registro($registro);
		//ei_arbol($clave);
		foreach(	$clave as $columna => $valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		$sql =	" DELETE	FROM " .	$this->info_ut_formulario["tabla"] . 
				" WHERE " .	implode(" AND ",$sql_where) .";";
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------	  MANEJO	de	la	PK	  ---------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_clave_registro($registro)
/*
	@@acceso: actividad
	@@desc: Devuelve la clave de un registro
*/
	{
		foreach($this->lista_ef_clave as $clave){
			$clave_ok[$clave] = $this->estado["datos"][$registro][$clave];
		}
		return $clave_ok;
	}
	//-------------------------------------------------------------------------------

	function obtener_clave()
/*
	@@acceso: actividad
	@@desc: Devuelve la clave (PADRE) que se esta procesando.
*/
	{
		return $this->estado["clave_padre"];
	}
	//-------------------------------------------------------------------------------

	function establecer_clave_maestro($clave)
/*
	@@acceso: interno
	@@desc: Si este elemento funciona como	detalle en un MT_MDS, recibe la clave del	maestro
	@@desc: a travez de esta funcion
*/
	{
		//ATENCION:
		$this->estado["clave_padre"] = $clave;
	}
	//-------------------------------------------------------------------------------

	function formatear_clave_padre($clave_pos)
/*
	@@acceso: interno
	@@desc: Le da formato a	un	clave	definida	en	forma	posicional,	transformandola en formato	asociativo
	@@param:	
*/
	{
		//ei_arbol($clave_pos,"Clave RECIBIDA");
		//Obtengo los nombres de los indices (datos manejados	por los EF clave)
		foreach($this->lista_ef_clave_padre as $ef){
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
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_lista_registros()
	{
		if(!is_array($this->lista_ef_mostrar)){
			echo ei_mensaje("ATENCION: no se definio que campos mostrar.");
			return;
		}
		if(!is_array($this->estado["registros"])){
			return;
		}
		echo "\n\n<table class='objeto-base-3d' width='".$this->info_ut_formulario["ancho"]."'>\n";
		//--- Titulos columnas ---
		echo "<tr>";
		foreach($this->lista_ef_mostrar as $campo){
			if($this->info_ut_formulario_ef[$campo['info']]["lista_cabecera"]!=""){
				$titulo = $this->info_ut_formulario_ef[$campo['info']]["lista_cabecera"];
			}else{
				$titulo = $this->info_ut_formulario_ef[$campo['info']]["etiqueta"];					
			}
			echo "<td  class='lista-col-titulo'>&nbsp;$titulo&nbsp;</td>\n";
		}
			echo "<td  class='lista-col-titulo'></td>\n";
		echo "</tr>\n";
		//--- Registros ---
		foreach( array_keys($this->estado["registros"]) as $registro){
			//SI no esta marcado para eliminar lo muestro
			if($this->estado["registros"][$registro]!="D")
			{ 
				echo "<tr>";
				foreach($this->lista_ef_mostrar as $campo){
					$valor = $this->estado["datos"][$registro][$campo['ef']];
					echo "<td class='col-tex-p1'>$valor</td>\n";
				}
				//-- ¿Registro ACTIVO? --
				if(isset($this->estado["registro_activo"])){
					if( $this->estado["registro_activo"] == $registro){
						echo "<td class='col-cen-s1'>";
						echo recurso::imagen_apl("doc.gif",true,null,null,"El registro se encuentra cargado en la interface");
					}else{
						echo "<td class='col-tex-p1'>";
						echo form::image($this->submit."_".$registro,recurso::imagen_apl("doc.gif"));
					}
				}else{
					echo "<td class='col-tex-p1'>";
					echo form::image($this->submit."_".$registro,recurso::imagen_apl("doc.gif"));
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		echo "</table>";
	}
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero	la	interface
		//ei_arbol($this->obtener_sql(),"SQL");
		if($this->estado_proceso!="INFRACCION")
		{
			$this->generar_lista_registros();
			echo "\n\n<table class='objeto-base-3d' width='100%'>\n";
			echo "<tr><td>";
			parent::obtener_html();
			echo "</td></tr>\n";
			echo "<tr><td class='abm-fila'>\n";
			$this->obtener_botones();
			echo "</td></tr>\n";
			echo "</table>";
		}
	}
	//-------------------------------------------------------------------------------

	function	obtener_javascript()
/*
	@@acceso: interno
	@@desc: 
*/
	{
		$javascript	= "";
		//Obtengo el javascript	de	validacion de cada EF
		foreach ($this->lista_ef_post	as	$ef){
			$javascript .= "";
			$javascript	.=	$this->elemento_formulario[$ef]->obtener_javascript();
			$javascript .= "";

		}
		$javascript	.=	"\n\n";
		return $javascript;
	}
	//-------------------------------------------------------------------------------


	function obtener_botones()
/*
 	@@acceso: interno
	@@desc: Genera los BOTONES del UT
*/
	{
		echo "<table width='100%' class='tabla-0'>\n";
		echo "<tr><td align='right'>";
		if(isset($this->estado["registro_activo"])){
			echo form::submit($this->submit."_L","Limpiar FORMULARIO","abm-input");
			echo form::submit($this->submit."_U","Modificar","abm-input");
			echo form::submit($this->submit."_D","Eliminar","abm-input-eliminar");
		}else{
			echo form::submit($this->submit."_I","Insertar","abm-input");
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
}
?>