<?
//Sacar el parametro TABLA
//Falta especificar los valores por defecto del LAPSO

include_once("nucleo/browser/interface/ef.php");
include_once("nucleo/browser/interface/ef_combo.php");
include_once("nucleo/browser/interface/ef_editable.php");
include_once("nucleo/browser/interface/ef_varios.php");
//########################################################################################################
//########################################################################################################
//##########################################  DIMENSIONES  ###############################################
//########################################################################################################
//########################################################################################################

class dimension_combo_db extends ef_combo_db
{
	function dimension_combo_db($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		parent::ef_combo_db($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio,$parametros);
	}

	function obtener_where()
	{
		if($this->activado()){
			return " ({$this->dato} = '{$this->estado}') ";
		}
	}
	function obtener_from(){}

	function obtener_interface()
	{
		return $this->envoltura_filtro($this->obtener_input());
	}
	
	function validar_estado(){
		//Si es INTERACTIVO, REQUERIDO y esta DESACTIVADO, tiene que gritar...
		if(($this->obligatorio) && !($this->activado()) ){
			return array(false,"El parametro {$this->etiqueta} es OBLIGATORIO");
		}else{
			return array(true,""); 
		}
	}	
    	    
}
//########################################################################################################
//########################################################################################################

class dimension_combo_db_proyecto extends ef_combo_db_proyecto
{
	function dimension_combo_db_proyecto($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		parent::ef_combo_db_proyecto($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio,$parametros);
	}

	function obtener_where()
	{
		if($this->activado()){
			foreach($this->dato as $dato){
				$where[] = " ({$dato} = '{$this->estado[$dato]}') ";	
			}
			return " ( ". implode(" AND ",$where) . " )";
		}
	}
	function obtener_from(){}

	function obtener_interface()
	{
		return $this->envoltura_filtro($this->obtener_input());
	}
}
//########################################################################################################
//########################################################################################################

class dimension_mes extends ef
{
	var $mes;
	var $anio;
	var $operador;
	var $operador_texto;
	
	function dimension_mes($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$this->operador = isset($parametros['operador']) ? $parametros['operador'] : "=";
		$this->operador_texto = isset($parametros['operador_texto']) ? $parametros['operador_texto'] : "Mes ";
		$anio_i = isset($parametros['anio_i']) ? $parametros['anio_i'] : date("Y");
		$anio_f = isset($parametros['anio_f']) ? $parametros['anio_f'] : date("Y");
		$this->inicializar_combos( $anio_i, $anio_f);
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio, $parametros);
	}

	function inicializar_combos($anio_i, $anio_f)
	{
		//Inicializo meses
		$this->mes[1] = 	"Enero";
		$this->mes[2] = 	"Febrero";
		$this->mes[3] = 	"Marzo";
		$this->mes[4] = 	"Abril";	
		$this->mes[5] = 	"Mayo";	
		$this->mes[6] = 	"Junio";	
		$this->mes[7] = 	"Julio";	
		$this->mes[8] = 	"Agosto";	
		$this->mes[9] = 	"Septiembre";	
		$this->mes[10] = 	"Octubre";	
		$this->mes[11] = 	"Noviembre";	
		$this->mes[12] = 	"Diciembre";
		//Inicializo anios
		if(($anio_f - $anio_i)>0){
			for($a=0;$a<=(($anio_f - $anio_i));$a++){
				$this->anio[$anio_i + $a] = $anio_i + $a;
			}
		}else{
			$this->anio[$anio_i] = $anio_i;
		}
	}

	function obtener_where()
	{
		if($this->activado()){
			return " ( ({$this->dato[0]} {$this->operador} {$this->estado[0]})
						AND  ({$this->dato[1]} {$this->operador} {$this->estado[1]}) )";
		}
	}

	function obtener_from(){}

	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return " {$this->operador_texto}: {$this->mes[$this->estado[0]]} de {$this->anio[$this->estado[1]]}";
		}
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return (trim($this->estado[0])!="") && (trim($this->estado[1]!=""));
	}

	function obtener_input()
	{
		$html = form::select($this->id_form."[0]",$this->estado[0],$this->mes);
		$html .= form::select($this->id_form."[1]",$this->estado[1],$this->anio);
		return $html;	
	}
	
	function validar_estado(){
		//Si es INTERACTIVO, REQUERIDO y esta DESACTIVADO, tiene que gritar...
		if(($this->obligatorio) && !($this->activado()) ){
			return array(false,"El parametro {$this->etiqueta} es OBLIGATORIO");
		}else{
			return array(true,""); 
		}
	}

	function obtener_interface()
	{
		return $this->envoltura_filtro( $this->obtener_input() );
	}
}
//########################################################################################################
//########################################################################################################

class dimension_mes_lapso extends ef
{
	var $mes_i;
	var $mes_f;
	
	function dimension_mes_lapso($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$parametros['operador'] = ">=";
		$parametros['operador_texto'] = "Mes mayor o igual a: ";
		$this->mes_i =& new dimension_mes($padre, $nombre_formulario, $id."a", $etiqueta, $descripcion,$columna,$obligatorio,$parametros);
		$parametros['operador'] = "<=";
		$parametros['operador_texto'] = "Mes menor o igual a: ";
		$this->mes_f =& new dimension_mes($padre, $nombre_formulario, $id."b", $etiqueta, $descripcion,$columna,$obligatorio,$parametros);
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio,$parametros);
	}

	function obtener_where()
	{
		if($this->activado()){
			return " ( (".$this->mes_i->obtener_where().") AND (".$this->mes_f->obtener_where().") )";
		}
	}

	function obtener_from(){}

	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return " {$this->operador_texto}: {$this->mes[$this->estado[0]]} de {$this->anio[$this->estado[1]]}";
		}
	}

	function cargar_estado($estado=null)
	{
		$this->mes_i->cargar_estado($estado);
		$this->mes_f->cargar_estado($estado);
	}

	function activado()
	{
		return $this->mes_i->activado() || $this->mes_f->activado();
	}
	
	function obtener_input()
	{
		$html = "<table class='tabla-0'><tr><td>";
		$html .= $this->mes_i->obtener_input();
		$html .= "</td></tr><tr><td>";
		$html .= $this->mes_f->obtener_input();
		$html .= "</td></tr></table>";
		return $html;	
	}

	function obtener_interface()
	{
		return $this->envoltura_filtro( $this->obtener_input() );
	}

    
	function validar_estado(){
		return array_merge($this->mes_i->validar_estado(), $this->mes_f->validar_estado());
	}

}
//########################################################################################################
//########################################################################################################

class dimension_fecha_lapso extends ef_editable_fecha
{
	var $fecha_inicial;
	var $fecha_final;
	var $estado_cargado;
	
	function dimension_fecha_lapso($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$this->fecha_inicial = new ef_editable_fecha($padre,$nombre_formulario, $id."_1",$etiqueta,$descripcion,$columna,$obligatorio, $parametros);
		$this->fecha_final = new ef_editable_fecha($padre,$nombre_formulario, $id."_2",$etiqueta,$descripcion,$columna,$obligatorio, $parametros);
		parent::ef_editable_fecha($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio, $parametros);
	}

	function cargar_estado($estado = null)
	{
		$this->fecha_inicial->cargar_estado($estado);
		$this->fecha_final->cargar_estado($estado);
	}	
	
	function validar_estado()
	{
		$estado_inicial = $this->fecha_inicial->validar_estado();
		$estado_final = $this->fecha_final->validar_estado();

		if ($estado_inicial[0] === true && $estado_final[0]= true)	{
			$this->validacion = true;
			return array(true, "");
		} else {
			$this->validacion = false;
			$mensaje_inicial = ($estado_inicial === true)? "" : "Fecha Inicial: ".$estado_inicial[1];
			$mensaje_final = ($estado_final === true)? "" : "Fecha Final: ".$estado_final[1];			
			$mensaje = array(false, $mensaje_inicial."<br>".$mensaje_final);
			var_dump($mensaje);
			return $mensaje;
		}
	
	}
	
	function obtener_info()
	{
		if($this->activado()){
			$estado = $this->obtener_estado();
			return "{$this->etiqueta}: {$estado[0]} y {$estado[1]}";
		}	
	}
	
	function obtener_estado()
	{
		$fecha_ini = $this->fecha_inicial->obtener_estado();
		$fecha_fin = $this->fecha_final->obtener_estado();
		return array($fecha_ini, $fecha_fin);
	}
	
	function obtener_where()
	{
		$fecha_ini = $this->fecha_inicial->obtener_estado();
		$fecha_fin = $this->fecha_final->obtener_estado();
		
    if (!is_array($this->dato)) 
    {
        $aux = $this->dato;
        $this->dato = array($aux);
    }

    $where = '';    
		if($this->activado()){	
        foreach($this->dato as $valor_dato)
        {
			    if ($fecha_ini != "NULL")
		    		$where_ini = "{$valor_dato} >= '$fecha_ini'";
	 		else
   				$where_ini = "TRUE";

	 		if ($fecha_fin != "NULL")
	 			$where_fin = "{$valor_dato} <= '$fecha_fin'";
	 		else
	 			$where_fin = "TRUE";
   
   			$where .= "(($where_ini) AND  ($where_fin)) AND ";
        }
        $where = substr($where,0,-4);
			return $where;
		}
	}

	function obtener_input()
	{
		$html = "\n\n<SCRIPT language='javascript' src='".recurso::js("calendario_es.js")."'></SCRIPT>\n";
		$html .= "<SCRIPT language='javascript'>document.write(getCalendarStyles());</SCRIPT>\n";
		$html .= "<SCRIPT language='javascript'>var calendario = new CalendarPopup('div_calendario');calendario.showYearNavigation();calendario.showYearNavigationInput();</SCRIPT>\n";
		$html .= "<DIV id='div_calendario'  style='VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white'></DIV>\n";

		$html .= "<table class='tabla-0' cellpadding='2'>";
		$html .= "<tr><td class='parametro-item'>\n";
		$html .= $this->fecha_inicial->obtener_input();
		$html .= "</td><td class='parametro-item'>y</td><td class='parametro-item'>\n";		
		$html .= $this->fecha_final->obtener_input();
		$html .= "</td></tr></table>\n";	
		return $html;
	}	

	function obtener_from() {}
	
	function obtener_interface()
	{
		return $this->envoltura_filtro( $this->obtener_input() );
	}

	function activado()
	{
		$activado_inicial = $this->fecha_inicial->activado();
		$activado_final = $this->fecha_final->activado();
		if ($activado_inicial || $activado_final)
			return true;
		else
			return false;
	}
	
}
//########################################################################################################
//########################################################################################################

class dimension_texto_operador extends ef_editable
{
    var $operador;
	var $pre_string;
	var $post_string;
	
	function dimension_texto_operador($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$this->operador = $parametros['operador'];
		$this->pre_string = isset($parametros['pre_string']) ? $parametros['pre_string'] : "";
		$this->post_string = isset($parametros['post_string']) ? $parametros['post_string'] : "";
		parent::ef_editable($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio,$parametros);
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return isset($this->estado) && (trim($this->estado!=""));
	}

	function obtener_where()
	{
		if($this->activado()){
			return " ({$this->dato} {$this->operador} '{$this->pre_string}{$this->estado}{$this->post_string}') ";
		}else{
			return null;
		}
	}

	function obtener_from(){}

	function validar_estado(){
		//Si es INTERACTIVO, REQUERIDO y esta DESACTIVADO, tiene que gritar...
		if( ($this->obligatorio) && ($this->activado()===false) ){
			return array(false, $this->etiqueta . ": El parametro es requerido y se encuentra desactivado");
		}else{
			return array(true, ""); 
		}
	}

	function obtener_interface()
	{
		return $this->envoltura_filtro($this->obtener_input());
	}
}
//########################################################################################################
//########################################################################################################

class dimension_numero_conector extends ef
{
	var $conectores;
	var $digitos;
	
	function dimension_numero_conector($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
//		$this->no_interactivo = $no_interactivo;
		$this->digitos = $parametros['digitos'];
		$this->conectores[">="]="Mayor o igual a";
		$this->conectores["<="]="Menor o igual a";
		$this->conectores[">"]="Mayor a";
		$this->conectores["<"]="Menor a";
		$this->conectores["="]="Igual a";
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio);
	}

	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return "{$this->etiqueta}: {$this->conectores[$this->estado[0]]} {$this->estado[1]}";
		}
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return isset($this->estado[1]) && (trim($this->estado[1]!=""));
	}
	
	function obtener_interface()
	{

		$html = form::select($this->id_form."[0]",$this->estado[0],$this->conectores);
		$html .= form::text($this->id_form."[1]",$this->estado[1],false,$this->digitos,$this->digitos);
		return $this->envoltura_filtro($html);

	}

	function obtener_where()
	{
		if($this->activado()){
			return " ({$this->dato} {$this->estado[0]} {$this->estado[1]}) ";
		}
	}

	function obtener_from(){}

	function validar_estado(){
		//Si es INTERACTIVO, REQUERIDO y esta DESACTIVADO, tiene que gritar...
		if(($this->obligatorio) && ($this->activado()===false) ){
			return array(false, $this->etiqueta . ": El parametro es requerido y se encuentra desactivado");
		}else{
			return array(true, ""); 
		}
	}
}
//########################################################################################################
//########################################################################################################

class dimension_checkbox extends ef_checkbox
{
    var $operador;
	
	function dimension_checkbox($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$this->operador = $parametros['operador'];
		parent::ef_checkbox($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio,$parametros);
	}
	
	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return isset($this->estado) && (trim($this->estado!=""));
	}


	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return "{$this->etiqueta}: {$this->valor_info}";
		}
	}

	function obtener_where()
	{
		if($this->activado()){
			return " ({$this->dato} {$this->operador} '{$this->estado}') ";
		}
	}

	function obtener_from(){}

	function validar_estado(){
		//Si es INTERACTIVO, REQUERIDO y esta DESACTIVADO, tiene que gritar...
		if( ($this->obligatorio) && ($this->activado()===false) ){
			return array(false, $this->etiqueta . ": El parametro es requerido y se encuentra desactivado");
		}else{
			return array(true, ""); 
		}
	}

	function obtener_interface()
	{
		return $this->envoltura_filtro($this->obtener_input());
	}

}
//########################################################################################################
//########################################################################################################

class dimension_numero_rango extends ef
{
	var $digitos;
	var $tamanio;
		
	function dimension_numero_rango($padre, $nombre_formulario, $id, $etiqueta, $descripcion,$columna,$obligatorio,$parametros)
	{
		$this->tamanio = $parametros['tamanio'];
		$this->digitos = $parametros['digitos'];
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$columna,$obligatorio, $parametros);
	}

	function obtener_info()
	{
		//Seguridad: Si NO existe un elemento con este indice en el ARRAY toquetearon el FORM???
		if($this->activado()){
			return "{$this->etiqueta}: Entre  {$this->estado[0]} y {$this->estado[1]}";
		}
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return trim($this->estado[0]) != '' || trim($this->estado[1]) != '';
	}
	
	function obtener_interface()
	{
		$html = form::text($this->id_form."[0]",$this->estado[0],false, $this->digitos,$this->tamanio, 'ef-input-numero');
		$html .= " a ";
		$html .= form::text($this->id_form."[1]",$this->estado[1],false,$this->digitos,$this->tamanio, 'ef-input-numero');
		return $this->envoltura_filtro($html);
	}

	function obtener_where()
	{
		$numero_ini = trim($this->estado[0]);
		$numero_fin = trim($this->estado[1]);
		if($this->activado()){	
			if ($numero_ini != "")
				$where_ini = "{$this->dato} >= '$numero_ini'";
			else
				$where_ini = "TRUE";

			if ($numero_fin != "")
				$where_fin = "{$this->dato} <= '$numero_fin'";
			else
				$where_fin = "TRUE";

			$where = "(($where_ini) AND  ($where_fin))";
			return $where;
		}	
	}

	function obtener_from(){}

	function validar_estado(){
		if(($this->obligatorio) && ($this->activado()===false) )
			return array(false, $this->etiqueta . ": El parametro es requerido y se encuentra desactivado");

		if ($this->activado())
		{
			if (trim($this->estado[0]) != '' && !is_numeric($this->estado[0]))
				return array(false, $this->etiqueta . ": El primer parámetro debe ser numéricos");
				
			if (trim($this->estado[1]) != '' && !is_numeric($this->estado[1]))
				return array(false, $this->etiqueta . ": El segundo parámetro debe ser numéricos");
		}
		return array(true, "");
	}
}

?>