<?php

/**
 * Clase base de los efs que no mantienen estado
 * @package Componentes
 * @subpackage Efs
 */
abstract class toba_ef_sin_estado extends toba_ef
{
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function set_estado($estado)
	{
		return false;
	}

	function get_estado()
	{
		return null;
	}

	function tiene_estado()
	{
		return false;
	}

	function resetear_estado()
	{
		return false;
	}
	
	function validar_estado()
	{
		return true;
	}

	function get_javascript()
	//Devuelve el javascript del elemento
	{
		return "";
	}
	
	function tiene_etiqueta()
	{
		return false;	
	}		
}
#####################################################################################
#####################################################################################

/**
 * Incluye una barra separadora con la etiqueta como texto, utiliza la clase css ef-barra-divisora
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef ef
 */
class toba_ef_barra_divisora extends toba_ef_sin_estado
{
	protected $clase_css = 'ef-barra-divisora';
	
	function get_input()
	{
		echo "<div class='{$this->clase_css}' id='{$this->id_form}'>{$this->etiqueta}</div>\n";
	}
}

#####################################################################################
#####################################################################################

/**
 * Incluye un fieldset que permite juntar varios efs con una etiqueta común
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef ef
 */
class toba_ef_fieldset extends toba_ef_sin_estado
{
	protected $fin;
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->fin = (isset($parametros['fieldset_fin'])) ? ($parametros['fieldset_fin'] == 1) : false;
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	static function get_lista_parametros()
	{
		$parametros[] = 'fieldset_fin';
		return $parametros;
	}	
	
	function get_input()
	{
		if(! $this->fin){
			echo "<fieldset title='{$this->etiqueta}'>";
			if (trim($this->etiqueta) != ''){
				echo "<legend>{$this->etiqueta}</legend>";
			}//if
		} else {
			echo "</fieldset>";
		}//if externo
	}
	
}	
#####################################################################################
#####################################################################################
?>