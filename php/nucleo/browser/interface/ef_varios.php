<?
require_once("nucleo/browser/interface/ef.php"); // Elementos de interface


/**
 * ef <abstracta>
 * 			|
 * 			+----> ef_checkbox
 * 			|
 * 			+----> ef_fijo
 * 			|
 * 			+----> ef_elemento_ini (FALTA botones: limpiar, inicializar, parametros mejor)
 * 			|
 * 			+----> ef_combo_editable
 * 
 * #########################################################################################################
 * #######################################################################################################
 */

class ef_checkbox extends ef
// PARAMETROS ADICIONALES:
// "valor": Valor BRUTO asume el checkbox cuando esta seteado
{ // "valor_info": Nombre coloquial del valor
    var $valor;
    var $valor_no_seteado;
    var $valor_info;
    
     function ef_checkbox($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
			//VAlor FIJO
			if(isset($parametros["estado"])){
				$this->estado = $parametros["estado"];		
			}

         if (isset($parametros["valor"])){
             $this->valor = $parametros["valor"];
             }
         if (isset($parametros["valor_info"])){
             $this->valor_info = $parametros["valor_info"];
             }
         if (isset($parametros["valor_no_seteado"])){
             $this->valor_no_seteado = $parametros["valor_no_seteado"];
		}
         parent :: ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
    }
    
     function obtener_info()
    {
         // Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
        if($this->activado()){
             return "{$this->etiqueta}: {$this->valor_info}";
             }
         }
    
     function obtener_input()
    {
         if(!isset($this->estado) || $this->estado == "NULL") $this->estado = "";
         
         if ($this->solo_lectura) 
         {
		 	if ($this->estado != "")
	            $html_devuelto = form::hidden($this->id_form, $this->estado);
			else
				$html_devuelto = "";
				
            if ((trim($this->estado) != '') and (trim($this->estado) == '1')) 
            {
                $html_devuelto .= recurso::imagen_apl('checked.gif',true,16,16);
            }
            else
            {
                $html_devuelto .= recurso::imagen_apl('unchecked.gif',true,16,16);            
            }
            return $html_devuelto;   
         }else
         {
            return form :: checkbox($this->id_form, $this->estado, $this->valor,null,$this->javascript);
         }            
    }

	function cargar_estado($estado=null)
	//Carga el estado interno
	{
   		if(isset($estado)){								
    		$this->estado=$estado;
			return true;
	    }elseif(isset($_POST[$this->id_form])){
				if(!is_array($_POST[$this->id_form])){
					if(get_magic_quotes_gpc()){
						$this->estado = stripslashes($_POST[$this->id_form]);
					}else{
	   				$this->estado = $_POST[$this->id_form];
					}
				}else{
	   				$this->estado = $_POST[$this->id_form];
				}
			return true;
    	}else{
			//Si el valor no seteado existe, paso el estado a ese valor.
    		if(isset($this->valor_no_seteado)){
	    		$this->estado = $this->valor_no_seteado;
	    		return true;
    		}else{
    			$this->estado = null;
    		}
    	}
		return false;
	}
}
// ########################################################################################################
// ########################################################################################################
// PARAMETROS ADICIONALES:
// "estado": Valor que tiene que tomar el elemento
class ef_fijo extends ef
{
	var $estilo;
	
     function ef_fijo($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
         parent :: ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);

    	if(isset( $parametros["estado"])){
	    	$this->estado = $parametros["estado"];
    	}else{
	    	$this->estado = "";
    	}
    	if(isset( $parametros["estilo"])){
	    	$this->estilo = $parametros["estilo"];
    	}else{
	    	$this->estilo = "abm-input";
    	}
	}
    
     function cargar_estado($estado = "")
     { // Desabilito la carga via POST
        if(isset($estado)){
             $this->estado = $estado;
             return true;
             }
         return false;
         }
    
     function obtener_input()
    {
		$estado = (isset($this->estado)) ? $this->estado : null;
		$html = "<div class='{$this->estilo}' id='{$this->id_form}'>".$estado."</div>";
		return $html;
	}
}

// ########################################################################################################
// ########################################################################################################
class ef_elemento_ini extends ef
// Permite elegir un tipo de elemento e inicializarlo
// Este elemento se inicializa con una consulta sobre la tabla que posee la descripcion de los elementos.
// el SQL tiene que devolver las siguientes columnas: id, texto_a_mostrar, ayuda_elemento, parametros_elemento
// ATENCION: los datos (columnas, etc) que maneja el combo tienen que definirse en este orde: ID del elemento
{ // (sea la cantidad de valores que sea) y despues el campo de inicializacion
    var $sql;
     var $fuente;
     var $datos;
     var $lista_elementos;
     var $opcion_seleccionada;
     var $filas;
     var $columnas;
     var $pos_desc;
     var $pos_ayuda;
     var $pos_parametros;
    
     function ef_elemento_ini($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		 // ei_arbol($parametros);
        global $ADODB_FETCH_MODE, $db;
         $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
         $this->sql = $this->preparar_sql($parametros["sql"]);
         if((isset($parametros["fuente"])) && (trim($parametros["fuente"]) != "")){
             $this->fuente = $parametros["fuente"];
             }else{
             $this->fuente = "instancia"; //La instancia por defecto es la CENTRAL
             }
         // echo $this->sql . "<br>";
        $rs = $db[$this->fuente][apex_db_con]->Execute($this->sql);
         if(!$rs){
             monitor :: evento("bug", "COMBO DB: No se genero el recordset. " . $db[$this->fuente][apex_db_con]->ErrorMsg() . " -- SQL: $this->sql -- ");
             }
         if($rs->EOF){
             echo ei_mensaje("EF etiquetado '$etiqueta'<br> No se obtuvieron registros: " . $this->sql);
             }
         $this->datos = $rs->getArray();
         $this->filas = (isset($parametros["filas"])) ? $parametros["filas"] : 6;
         $this->columnas = (isset($parametros["columnas"])) ? $parametros["columnas"] : 40;
         $this->claves = (isset($parametros["claves"])) ? $parametros["claves"] : 1;
         // Defino la posicion de cada elemento dentro del SQL
         $this->pos_desc = $this->claves;
         $this->pos_ayuda = $this->claves + 1;
         $this->pos_parametros = $this->claves + 2;
         // Tiene que haber una relacion entre los datos que maneja el y la cantidad de claves
        // (Los datos tiene que ser iguales a la cantidad de claves mas 1, por el campo de inicializacion)
        parent :: ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
         //ei_arbol($this->dato,"Dato");
		 if(count($this->dato) != ($this->claves + 1)){
             echo ei_mensaje("Cantidad incorrecta de claves (Verificar cantidad de columnas asociadas al EF)", "error");
             }
         // Prepara la lista que se va a mostrar
        $this->preparar_lista();
         }
    
     function preparar_sql($sql)
     { // Ventana para que los hijos formateen el SQL de acuerdo a su criterio
        return $sql;
     }
    
     function preparar_lista()
     { // Prepara la lista de la interface
        // Primero agrego al array de datos la columna con el identificador
        // Porque tengo que usarla en la llamada la funcion javascript que muestra la ayuda
        for($f = 0;$f < count($this->datos);$f++){
             $id = "";
             for($a = 0; $a < $this->claves ;$a++){
                 $id .= $this->datos[$f][$a] . apex_ef_separador;
                 }
             $id = substr($id, 0, (strlen($id) - strlen(apex_ef_separador)));
             $this->datos[$f]["identificador"] = $id;
             // echo "ID: $id <br>";
        }
         // Armo la lista que voy a mostrar
        for($f = 0;$f < count($this->datos);$f++){
             $this->lista_elementos[ $this->datos[$f]["identificador"] ] = $this->datos[$f][$this->pos_desc];
             }
         // ei_arbol($this->datos);
        // ei_arbol($this->lista_elementos);
    }
    
     function cargar_estado($estado = null)
     { // Carga el estado interno
        if(isset($estado)){
             $this->estado = $estado;
             // Tengo que setear la opccion seleccionada
            // En los datos que maneja el EF, primero tienen que definirse los del combo
            // ei_arbol($this->estado);
            $opcion = "";
             for($a = 0; $a < $this->claves; $a++){
                 $opcion .= $this->estado[$this->dato[$a]] . apex_ef_separador;
                 }
             $this->opcion_seleccionada = substr($opcion, 0, strlen($opcion) - strlen(apex_ef_separador));
             return true;
        }else{
             // Cargo el tipo de elemento del combo SELECCIONADO
            $ok = false;
             if(isset($_POST[$this->id_form . "_elem"])){
                 $this->opcion_seleccionada = $_POST[$this->id_form . "_elem"];
                 // Seteo el estado interno a partir de la concatenacion del combo
                $temp = explode(apex_ef_separador, $this->opcion_seleccionada);
                 for($a = 0; $a < $this->claves; $a++){
                     $this->estado[$this->dato[$a]] = $temp[$a];
                     }
                 $ok = true;
                 }
             // Cargo la inicializacion
            if(isset($_POST[$this->id_form . "_ini"])){             
                 if(get_magic_quotes_gpc()){
                     $this->estado[$this->dato[$this->claves]] = stripslashes($_POST[$this->id_form . "_ini"]);
                 }else{
                     $this->estado[$this->dato[$this->claves]] = $_POST[$this->id_form . "_ini"];
                 }
                 $ok = true;
            }
			return $ok;
        }
     }
    
     function obtener_input()
    {
         // Informacion al usuario sobre el elemento: AYUDA y PARAMETROS
        $html = "
<script language='javascript'>
	function mostrar_ayuda_{$this->id_form}(){
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value;
		switch(ef){\n";
         foreach ($this->datos as $fila){
             $html .= "\t\tcase '{$fila['identificador']}':\n\t\t\talert('" . addslashes($fila[$this->pos_ayuda]) . "');\n\t\t\tbreak;\n";
             }
         $html .= "		}
	}

	function mostrar_parametros_{$this->id_form}(){
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value;
		switch(ef){\n";
         foreach ($this->datos as $fila){
             $html .= "\t\tcase '{$fila['identificador']}':\n\t\t\talert('";
             $html .= preg_replace("/\r\n|\n/", "\\n", addslashes($fila[$this->pos_parametros]));
            // $html .= addslashes($fila[3])
            $html .= "');\n\t\t\tbreak;\n";
             }
         $html .= "		}
	}
</script>\n";
         // Elementos de FORMULARIO
        $html .= "<table class='tabla-0' width='100%'>\n";
         $html .= "<tr><td >\n";
         $html .= form :: select($this->id_form . "_elem", $this->opcion_seleccionada, $this->lista_elementos);
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_ayuda_{$this->id_form}();return false'>" . recurso :: imagen_apl("ayuda.jpg", true, null, null, "Descripcion del ELEMENTO") . "</a>";
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_parametros_{$this->id_form}();return false'>" . recurso :: imagen_apl("parametros.jpg", true, null, null, "Parametros del ELEMENTO") . "</a>";
         $html .= "</td></tr>\n";
         $html .= "<tr><td colspan='3'>\n";
         if(!isset($this->estado[$this->dato[$this->claves]])){
             $txt = "";
             }else{
             $txt = $this->estado[$this->dato[$this->claves]];
             }
         $this->estado[$this->dato[$this->claves]] = "";
         $html .= form :: textarea($this->id_form . "_ini", $txt, $this->filas, $this->columnas);
         $html .= "</td></tr>\n";
         $html .= "</table>\n";
         return $html;
         }
    }

// ########################################################################################################
// ########################################################################################################
class ef_elemento_ini_cheq extends ef_elemento_ini
// Permite elegir un tipo de elemento, inicializarlo y chequear el formato de su inicializacion
// Este elemento se inicializa con una consulta sobre la tabla que posee la descripcion de los elementos.
// el SQL tiene que devolver las siguientes columnas: id, texto_a_mostrar, ayuda_elemento, parametros_elemento
// ATENCION: los datos (columnas, etc) que maneja el combo tienen que definirse en este orde: ID del elemento
{ // (sea la cantidad de valores que sea) y despues el campo de inicializacion
     function ef_elemento_ini_cheq($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
         parent :: ef_elemento_ini($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
         }
    
     function obtener_input()
    {
         // Informacion al usuario sobre el elemento: AYUDA y PARAMETROS
        $html = "
<script language='javascript'>
	//Muestra la ayuda relacionada al EF seleccionado
	function mostrar_ayuda_{$this->id_form}(){
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value;
		switch(ef){\n";
         foreach ($this->datos as $fila){
             $html .= "\t\tcase '{$fila['identificador']}':\n\t\t\talert('" . addslashes($fila[$this->pos_ayuda]) . "');\n\t\t\tbreak;\n";
         }
         $html .= "		}
	}
	
	//Muestra los parametros asociados al EF elegido
	function mostrar_parametros_{$this->id_form}(){
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value;
		switch(ef){\n";
         foreach ($this->datos as $fila){
             $html .= "\t\tcase '{$fila['identificador']}':\n\t\t\talert('";
             $html .= preg_replace("/\r\n|\n/", "\\n", addslashes($fila[$this->pos_parametros]));
             // $html .= addslashes($fila[3])
             $html .= "');\n\t\t\tbreak;\n";
         }
         $html .= "		}
	}
	
	//Devuelve array datos segun EF
	function parametros_{$this->id_form}(){
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value;
		datos = new Object();
		switch(ef){\n";
         foreach ($this->datos as $fila){
             $html .= "\t\tcase '{$fila['identificador']}':\n";
         	if(trim($fila[$this->pos_parametros])!=""){
			     $conjunto_param = explode(";", $fila[$this->pos_parametros]);
	             //ei_arbol($conjunto_param, "parametros");
	             if(is_array($conjunto_param)){
					 foreach($conjunto_param as $parametro){
	                     if(trim($parametro)!=""){
	                         $info = explode(":",trim($parametro));
							 //Verifico que la linea tenga el formato correcto
							 if (count($info)==3){
							 	$html .= "\n\tdatos['$info[0]'] = '".trim($info[2])."';\n";
							 }
	                     }
	                 }
	             } 
			}
			$html .= "\n\tbreak;\n";
          }
         $html .= "}\nreturn datos;
	}
	
	//Chequea formato
	//1 : que la inicializacion tenga el formato correcto
	//2 : que las propiedades inicializadas existan
	//3 : que sean cargadas las propiedades obligatorias
	//4 : verifica carga de propiedades por duplicado
	function chequear_formato_{$this->id_form}(){
		text = document.{$this->nombre_formulario}.{$this->id_form}_ini.value;
		//Creo arreglo que va a guardar los errores, y arreglo asociativo que va a guardar los parametros inicializados
		error = new Array();
		parametros_carg = new Object();
		
		//Obtengo los parametros correspondientes al EF elegido
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value
		parametros_ef = parametros_{$this->id_form}();
		
		//Primero chequeo si esta vacio;
		if (trim(text)==''){
			verifica_obligatorios(error,parametros_ef);
			if (error.length>0){
				return error;
			}else{
				return '';
			}
		}
		linea = text.split(';');
		//Saca el ultimo elemento del array que siempre esta vacio
		linea.pop();
		//Recorro todas las lineas
		for(a=0;a<linea.length;a++){
			nro_linea = a+1;
			//Verifica el formato propiedad:valor
			elemento = 	linea[a].split(':');
			if(elemento.length!=2){
				error.push('Error de formato. Parámetro: ' + nro_linea);
			}else{
				//Guardo el nombre del parametro cargado (antes verifico si ya no se encontraba cargado)
				if (parametros_carg[trim(elemento[0])] == undefined){
					parametros_carg[trim(elemento[0])] = elemento[1];
				}else{
					error.push('El parámetro ' + trim(elemento[0]) + ' ha sido definido mas de una vez');
				}	
			}
		}
				
		//Si hay errores los muestro
		if (error.length>0){
			return error;
		}else{
			//Verifico si los parametros pertenecen al EF elegido;	
			//Recorro los parametros cargados
			for(elem in parametros_carg){
				//Verifica si el parametro existe en ese EF
				if (parametros_ef[elem] == undefined){
					error.push('El parámetro ' + elem + ' no corresponde a ' + ef);
				}else{
					parametros_ef[elem] = 'cargado';
				}
			}

			verifica_obligatorios(error,parametros_ef);
				
			if (error.length>0){
				return error;
			}else{
				return '';
			}
		}
	}
	
	//Verifica que todos los parametros obligatorios hayan sido cargados
	function verifica_obligatorios(error,parametros_ef)
	{
		//Verifica que no haya quedado ningun parametro obligatorio sin inicializar
		for(elem in parametros_ef){
			if (parametros_ef[elem] == 'obligatorio'){
				error.push('El parámetro ' + elem + ' es obligatorio.');
			}
		}
	}
		
	function return_chequear_formato_{$this->id_form}(){
		if (chequear_formato_{$this->id_form}() != ''){
			return true;
		}else{
			return false;
		}
	}
	
	function mostrar_chequear_formato_{$this->id_form}(){
		error = chequear_formato_{$this->id_form}();
		if (error != ''){
			alert(error.join('\\n'));
		}else{
			alert('Formato OK');
		}
	}
	
	//Autocompletan el campo inicializacion con los parametros definidos para ese EF
	function completar_formato_{$this->id_form}(){
		ef = document.{$this->nombre_formulario}.{$this->id_form}_elem.value;
		switch(ef){\n";
         foreach ($this->datos as $fila){
             $html .= "\t\tcase '{$fila['identificador']}':\n\t\t\tvar autocomp = '";
             // Verifica si tiene parametros
            if (trim($fila[$this->pos_parametros]) != ''){
                 $fmascara = explode('\\n', preg_replace("/\r\n|\n/", "\\n", addslashes($fila[$this->pos_parametros])));
                 foreach($fmascara as $linea){
                     $html .= substr($linea, 0, strpos($linea, ":")) . ":  ;\\n";
                     }
                 $html = substr($html, 0, strlen($html)-2);
                 }
             $html .= "';\n\t\t\tbreak;\n";
             }
         $html .= "		}
		document.{$this->nombre_formulario}.{$this->id_form}_ini.value = autocomp;
		document.{$this->nombre_formulario}.{$this->id_form}_ini.focus();
	}
</script>\n";
         // Elementos de FORMULARIO
        $html .= "<table class='tabla-0' width='100%'>\n";
         $html .= "<tr><td colspan=3>\n";
         $html .= form :: select($this->id_form . "_elem", $this->opcion_seleccionada, $this->lista_elementos);
         $html .= "</td></tr>";
         $html .= "<tr><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_ayuda_{$this->id_form}();return false'>" . recurso :: imagen_apl("ayuda.jpg", true, null, null, "Descripcion del ELEMENTO") . "</a>";
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_parametros_{$this->id_form}();return false'>" . recurso :: imagen_apl("parametros.jpg", true, null, null, "Parametros del ELEMENTO") . "</a>\n";
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_chequear_formato_{$this->id_form}();return false'>" . recurso :: imagen_apl("formato.jpg", true, null, null, "Formato de la inicialización") . "</a>\n";
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:completar_formato_{$this->id_form}();return false'>" . recurso :: imagen_apl("autocomp.jpg", true, null, null, "Autocompletar parametros de inicialización") . "</a>\n";
         $html .= "</td></tr>\n";
         $html .= "<tr><td colspan='4'>\n";
         if(!isset($this->estado[$this->dato[$this->claves]])){
             $txt = "";
             }else{
             $txt = $this->estado[$this->dato[$this->claves]];
             }
         $this->estado[$this->dato[$this->claves]] = "";
         $html .= form :: textarea($this->id_form . "_ini", $txt, $this->filas, $this->columnas);
         $html .= "</td></tr>\n";
         $html .= "</table>\n";
         return $html;
         }
    
     function obtener_javascript()
    {
         // Si el campo es obligatorio, en el form hay que llenarlo si o si
        if($this->obligatorio){
            /**
             * return "
             * if(){
             * alert(\"El campo '". $this->etiqueta ."' es obligatorio.\");
             * formulario.". $this->id_form .".focus();
             * return false;
             * }";
             */
             }
         }
    }

// ########################################################################################################
// ########################################################################################################
class ef_elemento_ini_proyecto extends ef_elemento_ini
{ // Inicializador de elementos cuya lista muestra elementos AISLADOS por proyecto
    function ef_elemento_ini_proyecto($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
         parent :: ef_elemento_ini($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
         }
    
     function preparar_sql($sql)
     { // Ventana para que los hijos formateen el SQL de acuerdo a su criterio
        global $solicitud;
         $proyecto = $solicitud->hilo->obtener_proyecto();
         if($proyecto == 'toba'){
             // Tengo que ejecutar la funcion igual para sacar el "%w%"
            $where[] = "( proyecto = 'toba' )";
             $sql = sql_agregar_clausulas_where($sql, $where);
             return $sql;
             }else{
             $where[] = "( proyecto = '$proyecto') OR (( proyecto = 'toba' ) AND (( exclusivo_toba <> 1 ) OR exclusivo_toba IS NULL))";
             $sql = sql_agregar_clausulas_where($sql, $where);
             return $sql;
             }
         }
    }
// ########################################################################################################
// ########################################################################################################
class ef_combo_editable extends ef
{
     var $ef_combo;
     var $ef_editable;
     var $dato;
    
     function ef_combo_editable($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
         if(count($dato) != 2){
             echo ei_mensaje("EF_COMBO_VALOR: El elemento posee 2 columnas asociadas");
             return;
             }
         $this->dato = $dato;
         $parametros_combo['no_seteado'] = isset($parametros['no_seteado'])? $parametros['no_seteado']: null;
         $parametros_combo['sql'] = isset($parametros['sql'])? $parametros['sql']: null;
         $parametros_combo['fuente'] = isset($parametros['fuente'])? $parametros['fuente']: null;
         $this->ef_combo = & new ef_combo_db($padre, $nombre_formulario,
             $id . "_" . $dato[0], $etiqueta,
             $descripcion, $dato[0],
             $obligatorio, $parametros_combo);
         $parametros_editable['estado'] = isset($parametros['estado'])? $parametros['estado']: null;
         $parametros_editable['tamano'] = isset($parametros['tamano'])? $parametros['tamano']: null;
         $parametros_editable['maximo'] = isset($parametros['maximo'])? $parametros['maximo']: null;
         $this->ef_editable = & new ef_editable($padre, $nombre_formulario,
             $id . "_" . $dato[1], $etiqueta,
             $descripcion, $dato[1],
             $obligatorio, $parametros_editable);
         parent :: ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio);
         }
    
     function cargar_estado($estado = null)
     { // Carga el estado interno
        if($estado != null){
             $this->ef_combo->cargar_estado($estado[$this->dato[0]]);
             $this->ef_editable->cargar_estado($estado[$this->dato[1]]);
             }else{
             $this->ef_combo->cargar_estado();
             $this->ef_editable->cargar_estado();
             }
         }
    
     function obtener_estado()
    {
         $temp[$this->dato[0]] = $this->ef_combo->obtener_estado();
         $temp[$this->dato[1]] = $this->ef_editable->obtener_estado();
         return $temp;
         }
    
     function obtener_input()
    {
         // Informacion al usuario sobre el elemento: AYUDA y PARAMETROS
        // Elementos de FORMULARIO
        $html = "";
         $html .= "<table class='tabla-0'>\n";
         $html .= "<tr><td>\n";
         $html .= $this->ef_combo->obtener_input();
         $html .= "</td><td>\n";
         $html .= $this->ef_editable->obtener_input();
         $html .= "</td></tr>\n";
         $html .= "</table>\n";
         return $html;
         }
    }
// ########################################################################################################
// ########################################################################################################

class ef_ini_cheq extends ef
// Permite inicializar y chequear formato
// Este elemento se inicializa con una consulta sobre la tabla que posee la descripcion de las propiedades.
// el SQL tiene que devolver las siguientes columnas: propiedad,descripcion,{opcional/obligatorio}
{
     var $sql;
     var $fuente;
     var $datos;
     var $filas;
     var $columnas;
 	 var $claves;
     var $dato;
	 
     function ef_ini_cheq($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
     {
         // ei_arbol($parametros);
        global $ADODB_FETCH_MODE, $db;
         $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
         $this->sql = $parametros["sql"];
         if((isset($parametros["fuente"])) && (trim($parametros["fuente"]) != "")){
             $this->fuente = $parametros["fuente"];
         }else{
             $this->fuente = "instancia"; //La instancia por defecto es la CENTRAL
         }
         $rs = $db[$this->fuente][apex_db_con]->Execute($this->sql);
         if(!$rs){
             monitor::evento("bug", "No se genero el recordset. " . $db[$this->fuente][apex_db_con]->ErrorMsg() . " -- SQL: $this->sql -- ");
         }
         if($rs->EOF){
             echo ei_mensaje("EF etiquetado '$etiqueta'<br> No se obtuvieron registros: " . $this->sql);
         }
         $this->datos = $rs->getArray();
         $this->filas = (isset($parametros["filas"])) ? $parametros["filas"] : 6;
         $this->columnas = (isset($parametros["columnas"])) ? $parametros["columnas"] : 40;
         $this->claves = (isset($parametros["claves"])) ? $parametros["claves"] : 1;
        // Tiene que haber una relacion entre los datos que maneja el y la cantidad de claves
        // (Los datos tiene que ser iguales a la cantidad de claves mas 1, por el campo de inicializacion)
        parent::ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
		if(count($this->datos[0]) != 3){
             echo ei_mensaje("Cantidad incorrecta de claves (Verificar cantidad de columnas asociadas al EF)", "error");
        }
     }
          
     function obtener_input()
     {
		 // Informacion al usuario sobre el elemento: AYUDA y PARAMETROS
        $html = "
<script language='javascript'>\n
	//Muestra los parametros
	function mostrar_parametros_{$this->id_form}(){\n";
		$param="";
		for($x=0;$x<count($this->datos);$x++){
			$param .= implode($this->datos[$x],":") . '\n';
		}
		$html .= "alert('$param');\n
	}

	//Chequea formato
	//1 : que la inicializacion tenga el formato correcto
	//2 : que las propiedades inicializadas existan
	//3 : que sean cargadas las propiedades obligatorias
	//4 : verifica carga de propiedades por duplicado
	function chequear_formato_{$this->id_form}(){
		text = document.{$this->nombre_formulario}.{$this->id_form}.value;
		//Creo arreglo que va a guardar los errores, y arreglo asociativo que va a guardar los parametros inicializados
		error = new Array();
		parametros_carg = new Object();
		parametros_def = new Object();
		
		//Obtengo los parametros correspondientes al EF elegido
		";
		for($x=0;$x<count($this->datos);$x++){
			$html .= "parametros_def['". $this->datos[$x][0] ."'] ='" . $this->datos[$x][2] . "';\n";
		}
		
		$html .= "//Primero chequeo si esta vacio;
		if (trim(text)==''){
			verifica_obligatorios(error,parametros_def);
			if (error.length>0){
				return error;
			}else{
				return '';
			}
		}
		linea = text.split(';');
		//Saca el ultimo elemento del array que siempre esta vacio
		linea.pop();
		//Recorro todas las lineas
		for(a=0;a<linea.length;a++){
			nro_linea = a+1;
			//Verifica el formato propiedad:valor
			elemento = 	linea[a].split(':');
			if(elemento.length!=2){
				error.push('Error de formato. Parámetro: ' + nro_linea);
			}else{
				//Guardo el nombre del parametro cargado (antes verifico si ya no se encontraba cargado)
				if (parametros_carg[trim(elemento[0])] == undefined){
					parametros_carg[trim(elemento[0])] = elemento[1];
				}else{
					error.push('El parámetro ' + trim(elemento[0]) + ' ha sido definido mas de una vez');
				}	
			}
		}
				
		//Si hay errores los muestro
		if (error.length>0){
			return error;
		}else{
			//Verifico si los parametros pertenecen al EF elegido;	
			//Recorro los parametros cargados
			for(elem in parametros_carg){
				//Verifica si el parametro existe en ese EF
				if (parametros_def[elem] == undefined){
					error.push('El propiedad ' + elem + ' no es válida.');
				}else{
					parametros_def[elem] = 'cargado';
				}
			}

			verifica_obligatorios(error,parametros_def);
				
			if (error.length>0){
				return error;
			}else{
				return '';
			}
		}
	}
	
	//Verifica que todos los parametros obligatorios hayan sido cargados
	function verifica_obligatorios(error,parametros_def)
	{
		//Verifica que no haya quedado ningun parametro obligatorio sin inicializar
		for(elem in parametros_def){
			if (parametros_def[elem] == 'obligatorio'){
				error.push('La propiedad ' + elem + ' es obligatoria.');
			}
		}
	}
	
	function return_chequear_formato_{$this->id_form}(){
		if (chequear_formato_{$this->id_form}() != ''){
			return true;
		}else{
			return false;
		}
	}
	
	function mostrar_chequear_formato_{$this->id_form}(){
		error = chequear_formato_{$this->id_form}();
		if (error != ''){
			alert(error.join('\\n'));
		}else{
			alert('Formato OK');
		}
	}
	
	//Autocompletan el campo inicializacion con las propiedades definidas para ese EF
	function completar_formato_{$this->id_form}(){\n";
         $html .= "var autocomp = '";
		 foreach($this->datos as $fila){     
               $html .= preg_replace("/\r\n|\n/", "\\n", addslashes($fila[0])) . ": ;\\n";    
         }
         $html .= "';\n
		document.{$this->nombre_formulario}.{$this->id_form}.value = autocomp;
		document.{$this->nombre_formulario}.{$this->id_form}.focus();
	}

</script>\n";
         // Elementos de FORMULARIO
         $html .= "<table class='tabla-0' width='100%'>\n";
         $html .= "<tr><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_parametros_{$this->id_form}();return false'>" . recurso::imagen_apl("parametros.jpg", true, null, null, "Parametros del ELEMENTO") . "</a>\n";
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:mostrar_chequear_formato_{$this->id_form}();return false'>" . recurso::imagen_apl("formato.jpg", true, null, null, "Formato de la inicialización") . "</a>\n";
         $html .= "</td><td>\n";
         $html .= "<a href='#' onclick='javascript:completar_formato_{$this->id_form}();return false'>" . recurso::imagen_apl("autocomp.jpg", true, null, null, "Autocompletar parametros de inicialización") . "</a>\n";
         $html .= "</td></tr>\n";
         $html .= "<tr><td colspan='3'>\n";
         if(!isset($this->estado)){
             $txt = "";
         }else{
             $txt = $this->estado;
         }
         $this->estado = "";
         $html .= form::textarea($this->id_form, $txt, $this->filas, $this->columnas);
         $html .= "</td></tr>\n";
         $html .= "</table>\n";
         return $html;
    }	
}
// ########################################################################################################
// ########################################################################################################
//Editor WYSIWYG de HTML

class ef_html extends ef
{
	var $ancho;
	var $alto;
	var $botonera;

	function ef_html($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		$this->ancho = (isset($parametros["ancho"]))? $parametros["ancho"] : "100%";
		$this->alto = (isset($parametros["alto"]))? $parametros["alto"] : "300";
		$this->botonera = (isset($parametros["botonera"]))? $parametros["botonera"] : "Toba";
         parent::ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
	}
	//---------------------------------------------------------

	function obtener_consumo_javascript()
	{
		$consumo = parent::obtener_consumo_javascript();
		//Consumo la expresion regular que machea numeros.
		$consumo[] = "fck_editor";
		return $consumo;
	}
	//---------------------------------------------------------

	function obtener_interface_ut()
	{
		echo $this->obtener_input();
	}
	//---------------------------------------------------------

	function obtener_input()
	{
		if(isset($this->estado)){
			$estado = addslashes($this->estado);
		}else{
			$estado = "";
		}
		$html = "<script type='text/javascript'>
  var oFCKeditor = new FCKeditor('{$this->id_form}','{$this->ancho}','{$this->alto}','{$this->botonera}','{$estado}' ) ;
  oFCKeditor.BasePath = 'js/fckeditor/';
  oFCKeditor.Create() ;
</script>";
		return $html;
	}
}
// ########################################################################################################
// ########################################################################################################
//Editor de PHP con sintaxis coloreada

class ef_php extends ef
{
	var $ancho;
	var $alto;

	function ef_php($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
    {
		$this->ancho = (isset($parametros["ancho"]))? $parametros["ancho"] : "100%";
		$this->alto = (isset($parametros["alto"]))? $parametros["alto"] : "300";
         parent::ef($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
	}
	//---------------------------------------------------------

	function obtener_interface_ut()
	{
		echo $this->obtener_input();
	}
	//---------------------------------------------------------

    function obtener_javascript()
    {
        //Obtengo el CODIGO PHP del iframe
        return "formulario.". $this->id_form .".value = ".$this->id_form."_editor.getContents();";
    }
	//---------------------------------------------------------

	function obtener_input()
	{
	    $estado = str_replace("\r", "", $this->estado);
	    $estado = str_replace("\n", "\\n", $estado);
	    $estado = str_replace('"', '\"', $estado);
	    $estado = str_replace("\t", "\\t", $estado);
	    $estado = "Hola, que tal";
		$html = "
<iframe name='{$this->id_form}_editor' src='".recurso::js('helene/editor.html').
				"' style='width: {$this->ancho}; height: {$this->alto};'></iframe>
<script type='text/javascript'>
function {$this->id_form}_init(){
//alert('$estado');
document.{$this->id_form}_editor.setContents(\"$estado\");
}
$this.onload = {$this->id_form}_init();
</script>";
		$html .= form::hidden($this->id_form,$estado);
		return $html;
	}
}
// ########################################################################################################
// ########################################################################################################
?>