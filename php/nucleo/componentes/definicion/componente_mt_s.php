<?php
require_once("objeto_mt.php");
include_once("nucleo/browser/interface/form.php");// HTML FROM

//Que relacion hay entre hacer todo por defecto y el ABMS?
//JAVASCRIPT especifico = javascript

class objeto_mt_s extends objeto_mt
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
	function objeto_mt_s($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto_mt($id);
		$this->cargar_dependencias();
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias()
/*
 	@@acceso: interno
	@@desc: Carga TODOS los UT de los que depende este MT y los INICIALIZA
*/
	{
		//Parametros a los formularios
		$parametro["nombre_formulario"] = $this->nombre_formulario;
		//--- Creo las dependencias ---
		if(is_array($this->indice_dependencias)){
			foreach(array_keys($this->indice_dependencias) as $dep){
				$this->cargar_dependencia($dep);		
				$this->dependencias[$dep]->inicializar($parametro);
			}
		}
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------  CONTROL de las UT  ---------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
 	@@acceso: interno
	@@desc: Valida el estado de cada UT
	@@param: string | identificador de la UT a setear
	@@param: array | Array asociativo con el identificador del EF y el valor que se desea cargar en el mismo
*/
	{
		$status = true;
		foreach(array_keys($this->dependencias) as $dep){
			$temp = $this->dependencias[$dep]->validar_estado();
			if(!$temp){
				$status = false;
				$this->dependencias[$dep]->mostrar_info_proceso();
			}
		}
		return $status;
	}
	//-------------------------------------------------------------------------------

	function cargar_post()
/*
 	@@acceso: interno
	@@desc: Carga el estado de las UT dependendientes desde el POST
*/
	{
		foreach(array_keys($this->dependencias) as $dependencia){
			$this->dependencias[$dependencia]->cargar_post();
		}
	}
	//-------------------------------------------------------------------------------

	function ut_asignar_ef($ut, $datos_ef)
/*
 	@@acceso: actividad
	@@desc: Carga el estado de de un EF de una UT
	@@param: string | identificador de la UT a setear
	@@param: array | Array asociativo con el identificador del EF y el valor que se desea cargar en el mismo
*/
	{
		if(is_object($this->dependencias[$ut])){
			$this->dependencias[$ut]->cargar_estado_ef($datos_ef);
		}else{
			echo ei_mensaje("La Unidad Transaccional especificada no existe");
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  SALIDA HTML ---------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del Marco Transaccional
*/
	{
		//-[1]- Muestro el resultado del procesamiento
		$this->mostrar_info_proceso();
		if(is_array($this->dependencias)){
			foreach(array_keys($this->dependencias) as $dependencia){
				$this->dependencias[$dependencia]->mostrar_info_proceso();//DETALLES
			}
		}

		//-[2]- Genero la SALIDA
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "\n\n<!-- #################################### Inicio MT ( ".$this->id[1]." ) ######################## -->\n\n\n\n";
		$this->obtener_javascript_global_consumido();
		$this->obtener_javascript_validador_form();
		echo "<br>\n";
		echo form::abrir($this->nombre_formulario, $vinculo, " onSubmit='return validar_form_".$this->nombre_formulario."(this)' ");
		echo "<div align='center'>\n";
		echo "<table class='objeto-base'>\n";
		echo "<tr><td>";
		$this->barra_superior();
		echo "</td></tr>\n";
		echo "<tr><td>";
		$this->obtener_interface();		//El hijo genera la INTERFACE
		echo "</td></tr>\n";
		echo "<tr><td>";
		$this->obtener_botones();
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo form::cerrar();
		echo "<br>\n";
		echo "<!-- ------------------ Fin MT -------------- -->\n\n\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_interface()
/*
 	@@acceso: interno
	@@desc: Genera la INTERFACE de la transaccion
*/
	{
		if(is_array($this->dependencias)){
			foreach(array_keys($this->dependencias) as $dependencia){
				$this->dependencias[$dependencia]->obtener_html();
			}
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript_global_consumido()
/*
 	@@acceso: interno
	@@desc: Genera el javascript GLOBAL que se consumen los EF. El javascript GLOBAL esta compuesto
	@@desc: por porciones de codigo reutilizadas entre distintos subelementos.
*/
	{
		$consumo_js = $this->consumo_javascript_global();
		if(is_array($this->dependencias)){
			//Que necesita el Marco?
			//Que necesita cada UT?
			foreach(array_keys($this->dependencias) as $ut){
				$temp = $this->dependencias[$ut]->consumo_javascript_global();
				if(isset($temp)) $consumo_js = array_merge($consumo_js, $temp);
			}
		}
		js::cargar_consumos_globales($consumo_js);
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript_validador_form()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del formulario
*/
	{
		//-[2]- Incluyo el JAVASCRIPT de CONTROLA el FORM
		echo "\n<script language='javascript'>
eliminar_{$this->nombre_formulario} = 0;";//FLAG que indica si el evento corresponde a la eliminacion del registro
		//Funcion que valida al formulario en el cliente
		echo "
		
//----------- Funcion VALIDADORA del FORM ----------

function validar_form_{$this->nombre_formulario}(formulario){\n";
//        echo "alert(\"estoy aca!!\");return false;\n";

		//----------------> Confirmacion en de la ELIMINACION del REGISTRO
		echo "if( eliminar_{$this->nombre_formulario} == 1 ){
	if(!(confirm('Desea ELIMINAR el registro?'))){
		eliminar_{$this->nombre_formulario}=0;
		return false;
	}else{
		return true;
	}
}\n";

		//Control especifico del MT
		$this->obtener_javascript();
		
		//ATENCION: Esto no es generico (-ME)
		//Cargo el JAVASCRIPT de las dependencias
		if(is_array($this->dependencias)){
			foreach(array_keys($this->dependencias) as $ut){
				echo $this->dependencias[$ut]->obtener_javascript();
			}
		}
        echo    "return true;\n";//Todo OK, salgo de la validacion del formulario
        echo    "}
//----------- FIN Funcion VALIDADORA del FORM ----------
</script>
";
	}
	//-------------------------------------------------------------------------------
	
	function obtener_javascript()
/*
 	@@acceso: interno
	@@desc: Validacion especifica del MT
*/
	{
		echo "";
	}
	//-------------------------------------------------------------------------------

	function consumo_javascript_global()
/*
 	@@acceso: interno
	@@desc: Javascript global requerido para la validacion especifica del MT
*/
	{
		return array();
	}
	//-------------------------------------------------------------------------------

	function obtener_botones()
/*
 	@@acceso: interno
	@@desc: Genera los BOTONES del Marco Transaccional
*/
	{
		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td class='abm-zona-botones'>";
		echo form::submit($this->submit,"Procesar","abm-input");
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
}
?>
