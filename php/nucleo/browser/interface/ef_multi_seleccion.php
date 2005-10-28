<?php
require_once("nucleo/browser/interface/ef.php");// Elementos de interface

define('carga_dao_estatico', '400');
define('carga_dao_cn', '401');
define('carga_sql', '402');

class ef_multi_seleccion extends ef
{
	protected $valores;				//Array con valores de la lista
	protected $tamanio;

	protected $modo_carga; //DAO, DAO_CN, SQL
	protected $dao;
	protected $include;
	protected $clase;
	protected $clave = 0;
	protected $valor = 1;
	protected $sql;
	protected $fuente;
	protected $dependencia_estricta = false;
	protected $serializar = false;	
	
	//parametros validación
	protected $cant_maxima;
	protected $cant_minima;
	
	static function get_parametros()
	{
		$parametros["dao"]["descripcion"]="Metodo a ejecutar para recuperar datos.";
		$parametros["dao"]["opcional"]=0;	
		$parametros["dao"]["etiqueta"]="DAO - Metodo";	
		$parametros["clase"]["descripcion"]="Nombre de la clase";
		$parametros["clase"]["opcional"]=1;	
		$parametros["clase"]["etiqueta"]="DAO - Clase";	
		$parametros["include"]["descripcion"]="Archivo donde se encuentra definida la clase";
		$parametros["include"]["opcional"]=1;	
		$parametros["include"]["etiqueta"]="DAO - Include";	
		$parametros["clave"]["descripcion"]="Indica que INDICES de la matriz recuperada se utilizaran como CLAVE (Si son varios separar con comas)";
		$parametros["clave"]["opcional"]=0;	
		$parametros["clave"]["etiqueta"]="DAO - resultado: CLAVE";	
		$parametros["valor"]["descripcion"]="Indica que INDICE de la matriz recuperada se utilizara como DESCRIPCION";
		$parametros["valor"]["opcional"]=0;	
		$parametros["valor"]["etiqueta"]="DAO - resultado: DESC.";	
		$parametros["no_seteado"]["descripcion"]="Descripcion que representa la NO-SELECCION del combo.";
		$parametros["no_seteado"]["opcional"]=1;	
		$parametros["no_seteado"]["etiqueta"]="Desc. No seleccion";	
		$parametros["predeterminado"]["descripcion"]="Valor predeterminado";
		$parametros["predeterminado"]["opcional"]=1;	
		$parametros["predeterminado"]["etiqueta"]="Valor predeterminado";
		$parametros["dependencias"]["descripcion"]="El estado dependende de otro EF (CASCADA). Lista de EFs separada por comas";
		$parametros["dependencias"]["opcional"]=1;	
		$parametros["dependencias"]["etiqueta"]="Dependencias";		
		$parametros["dependencia_estricta"]["descripcion"]="Indica que las dependencias deben estar completas antes de cargar los datos";
		$parametros["dependencia_estricta"]["opcional"]=1;	
		$parametros["dependencia_estricta"]["etiqueta"]="Dep. estricta";		
		$parametros["cant_minima"]["descripcion"]="Cantidad Minima";
		$parametros["cant_minima"]["opcional"]=1;	
		$parametros["cant_minima"]["etiqueta"]="Cantidad Minima";		
		$parametros["sql"]["etiqueta"]="SQL";	
		$parametros["sql"]["opcional"]=1;
		$parametros["sql"]["descripcion"]="Query de carga";
		$parametros["solo_lectura"]["descripcion"]="Establece el elemento como solo lectura.";
		$parametros["solo_lectura"]["opcional"]=1;	
		$parametros["solo_lectura"]["etiqueta"]="Solo lectura";		
		$parametros["valores"]["descripcion"] = "Valores que se muestran si no se cargan datos";
		$parametros["valores"]["opcional"] = 1;	
		$parametros["valores"]["etiqueta"] = "Valores Estáticos";				
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if((isset($parametros["solo_lectura"]))&&($parametros["solo_lectura"]==1)){
			$this->solo_lectura = true;
		}else{
			$this->solo_lectura = false;
		}		
        $this->valores = array();
		if(isset($parametros["valores"])){
			if(is_array($parametros["valores"])){
				$this->valores = $parametros["valores"];
			}
		}
		if(isset($parametros["tamanio"])) {
			$this->tamanio = $parametros['tamanio'];
			unset($parametros['tamanio']);
		}
		if(isset($parametros["cant_maxima"])) {
			$this->cant_maxima = $parametros['cant_maxima'];
			unset($parametros['cant_maxima']);
		}
		if(isset($parametros["cant_minima"])) {
			$this->cant_minima = $parametros['cant_minima'];
			unset($parametros['cant_minima']);
		}
		
		if (isset($parametros["clave"])) {
			$this->clave = $parametros["clave"];
			unset($parametros["clave"]);
		}
		
		if (isset($parametros["valor"])) {
			$this->valor = $parametros["valor"];
			unset($parametros["valor"]);
		}		
		
		//Se carga a partir de un DAO?
		if(isset($parametros["dao"])){
			$this->dao = $parametros["dao"];
			if(isset($parametros["include"])){
				$this->include = $parametros["include"];
			}			
			if(isset($parametros["clase"])){
				$this->clase = $parametros["clase"];
			}
			if(isset($this->include) && isset($this->clase) )
				$this->modo_carga = carga_dao_estatico;
			else
				$this->modo_carga = carga_dao_cn;
			unset($parametros["dao"]);
			unset($parametros["clase"]);
			unset($parametros["include"]);		
		}

		//Se carga a partir de un SQL?
		if (isset($parametros["sql"])) {
			$this->modo_carga = carga_sql;
			$this->sql = stripslashes($parametros["sql"]);
	        if((isset($parametros["fuente"]))&&(trim($parametros["fuente"])!="")){
	    		$this->fuente = $parametros["fuente"];
	            unset($parametros["fuente"]);
	        }else{
	            $this->fuente = "instancia"; //La instancia por defecto es la CENTRAL
	        }
			unset($parametros["sql"]);
		}
		if(isset($parametros["dependencia_estricta"])) {
			$this->dependencia_estricta = true;
			unset($parametros['dependencia_estricta']);
		}
		if(isset($parametros["serializar"])) {
			$this->serializar = $parametros["serializar"];
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		if( $this->dependencia_estricta ){
			if( $this->control_dependencias_cargadas() ){
				$this->cargar_datos();	
			}
		}else{
			$this->cargar_datos();	
		}
	}

	function cargar_datos_dao($parametros = array())
	{
		include_once($this->include);
		$valores = call_user_func_array(array($this->clase, $this->dao), $parametros);
		$this->valores = $this->preparar_valores($valores);
	}
	
	function cargar_datos_db()
	{
		$this->valores = array();//Limpio la lista de valores
		if(isset($this->no_seteado)){
    		if(trim($this->estado)==""){
    			$this->estado = apex_ef_no_seteado;
	   		}
	    	$this->valores[apex_ef_no_seteado] = $this->no_seteado;
        }
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$rs = $db[$this->fuente][apex_db_con]->Execute($this->sql);
		if(!$rs){
			monitor::evento("bug","COMBO DB: No se genero el recordset. ". $db[$this->fuente][apex_db_con]->ErrorMsg()." -- SQL: {$this->sql} -- ");
		}
		if($rs->EOF){
			//echo ei_mensaje("EF etiquetado '$etiqueta'<br> No se obtuvieron registros: ". $this->sql);
		}
		$temp = $this->preparar_valores($rs->getArray());
		if(is_array($temp)){
			$this->valores = $this->valores + $temp;
		}
		//ei_arbol($this->valores);
	}	

    function preparar_valores($datos_recordset)
    {
		$valores = array();
		foreach ($datos_recordset as $fila){
            $valores[$fila[$this->clave]] = $fila[$this->valor];
		}
        return $valores;
    }
	
	function cargar_datos($datos = null)
	{
		if ($datos !== null) {
			$this->valores = $datos;
		} elseif ($this->modo_carga == carga_dao_estatico) {
			$this->cargar_datos_dao();
		} elseif ($this->modo_carga == carga_sql) {
			$this->cargar_datos_db();		
		}
	}
	
	function get_valores()
	{
		return $this->valores;
	}
	
	function cargar_estado($estado = null)
	{
		if ($estado == '')
			$estado = null;
		elseif ($this->serializar !== false) {
			$estado = explode($this->serializar, $estado);
		}
		if (!parent::cargar_estado($estado))
			$this->resetear_estado();
	}
	
	function obtener_estado()
	{
		if ($this->serializar !== false) {
			return implode($this->estado, $this->serializar);	
		}		
		return parent::obtener_estado();	
	}
	
	function obtener_info()
	{
		if($this->activado()){
			return "{$this->etiqueta}: ".implode($this->estado, ', ');
			}
	}	
	
	function resetear_estado()
	{
		$this->estado = array();
		if (isset($this->predeterminado)) {
			$this->estado = $this->predeterminado;
		}
	}
	
	function validar_limites()
	{
		if (isset($this->cant_minima)){ 
			if (count($this->estado) < $this->cant_minima){
				$this->validacion = false;
				$elemento = ($this->cant_minima == 1) ? "un elemento" : "{$this->cant_minima} elementos";
                return array(false, "Seleccione al menos $elemento.");
			}
		}
		if (isset($this->cant_maxima)){ 
			if (count($this->estado) > $this->cant_maxima){
				$this->validacion = false;
				$elemento = ($this->cant_maxima == 1) ? "un elemento" : "{$this->cant_maxima} elementos";				
                return array(false, "No puede seleccionar más de $elemento.");
			}
		}
		$this->validacion = true;
		return array(true,"");
	}
	
	function validar_seleccionados()
	{
		//SI el conjunto inicial es vacio el control falla siempre.
		if( $this->dependencia_estricta ){
			return array(true, "");
		}
		foreach ($this->estado as $seleccionado) {
			if (! array_key_exists($seleccionado, $this->valores) )	{
				$this->validacion = false;
                return array(false, "El elemento seleccionado no pertenece a los datos de entrada.");
			}
		}
		return array(true, "");
	}

    function validar_estado()
    {
		if( $this->activado() ) {
            $limites =  $this->validar_limites();
			if (!$this->validacion)
				return $limites;
			return $this->validar_seleccionados();
		} else { 
			return parent::validar_estado();
		}
    }
	
	function obtener_javascript()
	{
		$js = "";
		if (isset($this->cant_minima)) { 
			$elemento = ($this->cant_minima == 1) ? "un elemento" : "{$this->cant_minima} elementos";
			$js .= "
				if (cant < {$this->cant_minima}) {
					alert(\"El campo {$this->etiqueta} debe tener al menos $elemento.\");            
					formulario.{$this->id_form}.focus();
					return false;
				}";
		}
		if (isset($this->cant_maxima)) { 
			$elemento = ($this->cant_maxima == 1) ? "un elemento" : "{$this->cant_maxima} elementos";
			$js .= "
				if (cant > {$this->cant_maxima}) {
					alert(\"El campo {$this->etiqueta} no puede tener más de $elemento.\");            
					formulario.{$this->id_form}.focus();
					return false;
				}";
		}	
		return $js;
	}
	
	function cargar_datos_master_ok()
	//Si el master esta cargado, el EF procede a cargar sus registros
	{
		$parametros = array();
		for($a=0;$a<count($this->dependencias);$a++){
			$parametros[] = $this->dependencias_datos[$this->dependencias[$a]];
		}
		$param = implode(",", $parametros);
		if($this->modo_carga == carga_dao_estatico ) {
			$this->cargar_datos_dao(array($param));
		}
		if(isset($this->sql)){
			//1) Reescribo el SQL con los datos de las dependencias	
			foreach($this->dependencias_datos as $dep => $valor){
				$this->sql = ereg_replace(apex_ef_dependenca.$dep.apex_ef_dependenca,$valor,$this->sql);
			}
			//echo $this->id . " - " . $this->sql;
			//2) Regenero la consulta a la base
			$this->cargar_datos_db();
		}		
	}	
	
	function obtener_valores()
	{
		return $this->valores;	
	}	
}

//########################################################################################################
//########################################################################################################

class ef_multi_seleccion_lista extends ef_multi_seleccion
{
	protected $mostrar_utilidades;
	
	static function get_parametros()
	{
		$parametros = ef_multi_seleccion::get_parametros();
		$parametros["mostrar_utilidades"]["descripcion"]="Mostrar utilidades";
		$parametros["mostrar_utilidades"]["opcional"]=1;	
		$parametros["mostrar_utilidades"]["etiqueta"]="Utilidades";
		$parametros["tamanio"]["descripcion"]="Cantidad de elementos que se visualizan simultáneamente";
		$parametros["tamanio"]["opcional"]=1;	
		$parametros["tamanio"]["etiqueta"]="Tamaño";			
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (isset($parametros['mostrar_utilidades'])) {
			$this->mostrar_utilidades = $parametros['mostrar_utilidades'];
		} else { 
			$this->mostrar_utilidades = false;
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);		
	}

	function obtener_input()
	{
		$html = "";
		if (!$this->solo_lectura && $this->mostrar_utilidades)	{
			$html .= "
				<script  type='text/javascript' language='javascript'>
					function multi_seleccion_mostrar(todos)
					{
						var elem = document.{$this->nombre_formulario}.{$this->id_form};
						for (var i=0; i < elem.length; i++) {
							elem.options[i].selected = todos;
						}
					}
				</script>
				<div style='float: right; font-size:9px;'>
					<a href='javascript: multi_seleccion_mostrar(true)'>Todos</a> / 
					<a href='javascript: multi_seleccion_mostrar(false)'>Ninguno</a></div>
			";
		}
		$tamanio = isset($this->tamanio) ? $this->tamanio: count($this->valores);
		$estado = isset($this->estado) ?  $this->estado : array();
		$html .= $this->obtener_javascript_general() . "\n\n";
		$extra = ($this->solo_lectura) ? "disabled" : "";
		$html .= form::multi_select($this->id_form, $estado, $this->valores, $tamanio, 'ef-combo', $extra);
		return $html;
	}
	
    function obtener_javascript()
    {
		$js_cantidad = "
				var cant = 0;
				for (var i=0; i < formulario.{$this->id_form}.length; i++) {
					if (formulario.{$this->id_form}.options[i].selected)
						cant++;
				};";

		return $js_cantidad.parent::obtener_javascript();
	}	
	
	//-----------------------------------------------
	//-------------- DEPENDENCIAS -------------------
	//-----------------------------------------------
	
	function javascript_slave_recargar_datos()
	{
		return "
		function recargar_slave_{$this->id_form}(datos)
		{
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			s_.options.length = 0;//Borro las opciones que existan
			//Creo los OPTIONS recuperados
			var hay_datos = false
			for (id in datos){
				if (id !=  '".apex_ef_no_seteado."')
					hay_datos = true;
				s_.options[s_.options.length] = new Option(datos[id], id);
			}
			if (hay_datos)
			{
				s_.disabled = false;
				s_.focus();
			}
			atender_proxima_consulta();
		}
		";	
	}	
	
	//-----------------------------------------------

	function javascript_slave_reset()
	{		
		$js = "
		function reset_{$this->id_form}()
		{
			s_ = document.{$this->nombre_formulario}.{$this->id_form};
			s_.disabled = true;
			s_.options.length = 0;\n";

		//Reseteo las dependencias	
		if(isset($this->dependientes)){
			foreach($this->dependientes as $dependiente){
				$js .= " reset_{$dependiente}();\n";
			}
		}
		$js .= "}\n";
		//Hay que resetear a los DEPENDIENTES
		return $js;
	}	
}
//########################################################################################################
//########################################################################################################

class ef_multi_seleccion_check extends ef_multi_seleccion
{
	
	protected $mostrar_utilidades;
	
	static function get_parametros()
	{
		$parametros = ef_multi_seleccion::get_parametros();
		$parametros["mostrar_utilidades"]["descripcion"]="Mostrar utilidades";
		$parametros["mostrar_utilidades"]["opcional"]=1;	
		$parametros["mostrar_utilidades"]["etiqueta"]="Utilidades";
		return $parametros;
	}

	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (isset($parametros['mostrar_utilidades'])) {
			$this->mostrar_utilidades = $parametros['mostrar_utilidades'];
		} else { 
			$this->mostrar_utilidades = false;
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);		
	}
	
	function obtener_input()
	{
		$estado = isset($this->estado) ?  $this->estado : array();
		$html = $this->obtener_javascript_general() . "\n\n";
		if ($this->solo_lectura) {
			$html .= "<div id='{$this->id_form}_opciones' style='clear:both'>";
			foreach ($this->valores as $id => $descripcion) {
				$checkeado = in_array($id, $estado) ? "checked" : "";
				$html .= "<div class='ef-multi-check'>";
				if (in_array($id, $estado)) {
					$html .= recurso::imagen_apl('checked.gif',true,16,16);
				} else  {
					$html .= recurso::imagen_apl('unchecked.gif',true,16,16);
				}
				$html .= "$descripcion</div>\n";
			}
			$html .= "</div>";			
		} else {
			if (count($this->valores) > 0 && $this->mostrar_utilidades)	{
				$html .= "
					<script  type='text/javascript' language='javascript'>
						function multi_seleccion_mostrar(todos)
						{
							var elem = document.{$this->nombre_formulario}['{$this->id_form}[]'];
							if (elem.length) {
								//Si son muchos elementos
								for (var i=0; i < elem.length; i++) {
									elem[i].checked = todos;
								}
							} else {
								//Es uno unico
								elem.checked = todos;
							}
						}
					</script>
					<div style='float: right; font-size:9px;white-space:nowrap;'>
						<a href='javascript: multi_seleccion_mostrar(true)'>Todos</a> / 
						<a href='javascript: multi_seleccion_mostrar(false)'>Ninguno</a></div>
				";
			}		
			$html .= "<div id='{$this->id_form}_opciones' style='clear:both'>";
			foreach ($this->valores as $id => $descripcion) {
				$checkeado = in_array($id, $estado) ? "checked" : "";
				$html .= "<div class='ef-multi-check'>";
				$html .= "<input name='{$this->id_form}[]' id='{$this->id_form}[]' type='checkbox' value='$id' $checkeado class='ef-checkbox'>";
				$html .= "$descripcion</div>\n";
			}
			$html .= "</div>";
		}
		return $html;
	}	
	
	//-----------------------------------------------
	//-------------- DEPENDENCIAS -------------------
	//-----------------------------------------------
	
	function javascript_slave_recargar_datos()
	{
		return "
		function recargar_slave_{$this->id_form}(datos)
		{
			var opciones = document.getElementById('{$this->id_form}_opciones');

			//Creo los OPTIONS recuperados
			for (id in datos){
				var nuevo = document.createElement('div');
				nuevo.className = 'ef-multi-check'; 
/*				var check = document.createElement('input');
				check.setAttribute('id', '{$this->id_form}[]',0);
				check.setAttribute('name', '{$this->id_form}[]',0);				
				check.type = 'checkbox';
				check.value = id;
				check.className = 'ef-checkbox';
				nuevo.appendChild(check);*/
				nuevo.innerHTML = \"<input name='{$this->id_form}[]' type='checkbox' value='\" + id + \"' class='ef-checkbox'>\";				
				nuevo.appendChild(document.createTextNode(datos[id]));
				opciones.appendChild(nuevo);
			}
			atender_proxima_consulta();
		}
		";	
	}	
	
	//-----------------------------------------------

	function javascript_slave_reset()
	{		
		$js = "
		function reset_{$this->id_form}()
		{
			var opciones = document.getElementById('{$this->id_form}_opciones');
			while(opciones.childNodes[0]) {
				opciones.removeChild(opciones.childNodes[0])
			}
		";

		//Reseteo las dependencias	
		if(isset($this->dependientes)){
			foreach($this->dependientes as $dependiente){
				$js .= " reset_{$dependiente}();\n";
			}
		}
		$js .= "}\n";
		//Hay que resetear a los DEPENDIENTES
		return $js;
	}		
}



?>