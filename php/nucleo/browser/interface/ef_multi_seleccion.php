<?php
require_once("nucleo/browser/interface/ef.php");// Elementos de interface

abstract class ef_multi_seleccion extends ef
{
	protected $valores;				//Array con valores de la lista
	protected $tamanio;
	
	//parametros validación
	protected $cant_maxima;
	protected $cant_minima;
	
	function __construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
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
		if(isset($parametros["dao"])){
			$this->dao = $parametros["dao"];
		}
		if(isset($parametros["include"])){
			$this->include = $parametros["include"];
		}
		if(isset($parametros["clase"])){
			$this->clase = $parametros["clase"];
		}
		if(isset($this->include) && isset($this->clase) )
		{
			$this->modo = "estatico";
		}else{
			$this->modo = "cn";	
		}
		unset($parametros["dao"]);
		unset($parametros["clase"]);
		unset($parametros["include"]);		
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
		
		if($this->modo == "estatico"){
			$this->cargar_datos(array());
		}		
	}

	function cargar_datos($datos)
	{
		if($this->modo == "estatico" )
		{
			include_once($this->include);
			$sentencia = "\$this->valores = " .  $this->clase . "::" . $this->dao ."();";
			eval($sentencia);//echo $sentencia;
		}else{
			$this->valores = $valores;
		}
	}
	
	function cargar_estado()
	{
		if (!parent::cargar_estado())
			$this->resetear_estado();
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

    function validar_estado()
    {
		if( $this->activado() ) {
            return $this->validar_limites();
		} else { 
			$this->validacion = true;
			return array(true,"");
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
			$parametros[] = "'" . $this->dependencias_datos[$this->dependencias[$a]]."'";
		}
		$param = implode(",", $parametros);
		if($this->modo =="estatico" )
		{
			include_once($this->include);
			$sentencia = "\$valores = " .  $this->clase . "::" . $this->dao ."($param);";
			//Esto es para que quede el no_seteado en los casos en que no se devuelven valores
			eval($sentencia);//echo $sentencia;
			if(isset($valores)){
				$this->valores = $valores;	
				$this->input_extra = "";
			}else{
				//La idea de la linea comentada era lograr el mismo efecto que provoca
				//la carga desde el server de un valor NULL (en blanco con la longitud del no_seteado)
				//$desc = str_repeat(count($this->no_seteado),"&nbsp");
				$this->valores[apex_ef_no_seteado] = "";
			}
		}
	}	
}

//########################################################################################################
//########################################################################################################

class ef_multi_seleccion_lista extends ef_multi_seleccion
{
	protected $mostrar_utilidades;
	
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
		if ($this->mostrar_utilidades)	{
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
		$html .= form::multi_select($this->id_form, $estado, $this->valores, $tamanio, 'ef-combo');
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




?>