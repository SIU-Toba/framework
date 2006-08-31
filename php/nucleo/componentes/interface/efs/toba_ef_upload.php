<?php
require_once("ef.php");// Elementos de interface
  
class toba_ef_upload extends ef
{
	protected $archivo_cargado = false;		//Se cargo un archivo en la etapa anterior?
	protected $archivo_subido = false;		//Se subio un archivo en esta etapa
	
	function get_input()
	{
		$tab = $this->padre->get_tab_index();
		$extra = " tabindex='$tab'";		
		$estado = $this->get_estado_input();
		//--- Se puede cargar con el nombre del archivo o el arreglo que php brinda
		//--- al hacer el upload
		if (is_array($estado)) {
			$nombre_archivo = isset($estado['name']) ? $estado['name'] : current($estado);
		} else {
			$nombre_archivo = $estado;
		}
		//-- Si hay un archivo lo deja marcado en sesion para la etapa siguiente
		if (isset($nombre_archivo)) {
			toba::get_hilo()->persistir_dato_sincronizado($this->id_form."_cargado", true);
		}
		$salida = "";
		if (! $this->solo_lectura) {
			if (isset($nombre_archivo)) {
				$salida .= form::archivo($this->id_form, null, "ef-upload", "style='display:none'");
				$salida .= "<br><div id='{$this->id_form}_desicion' class='ef-upload-desc'>". $nombre_archivo . "</div>";
				$salida .= form::checkbox("{$this->id_form}_check", null, 1, 'ef-checkbox', "$extra onclick=\"{$this->objeto_js()}.set_editable()\"");
				$salida .= "<label for='{$this->id_form}_check'>Cambiar el Archivo</label>";
			} else {
				$salida = form::archivo($this->id_form, null, 'ef-upload', $extra);
				$salida .= form::checkbox("{$this->id_form}_check", 1, 1, 'ef-checkbox', "style='display:none'");
			}
		} else { // En modo sólo lectura
			if (isset($nombre_archivo)) {
				$salida = "<div class='ef-upload-desc'>". $nombre_archivo ."</div>";
			} else {
				$salida = form::archivo($this->id_form, null, "ef-upload", "disabled='disabled'");
			}
		}
		return $salida;
	}
	
	function get_estado_input()
	{
        if (isset($this->estado)) {
            return $this->estado;
        }else{
            return null;
        }
	}
	
	function cargar_estado_post()
	{
		$this->archivo_cargado = toba::get_hilo()->recuperar_dato($this->id_form."_cargado");
		if(isset($_FILES[$this->id_form])) {
			if (isset($_POST[$this->id_form."_check"])) {
				$this->archivo_subido = true;
				$this->estado = $_FILES[$this->id_form]; 
			}
		}
	}

	function es_archivo_vacio()
	{
		return $_FILES[$this->id_form]["error"] == UPLOAD_ERR_NO_FILE;
	}
	
	function tiene_estado()
	{
		return $this->archivo_cargado || 
				($this->archivo_subido && !$this->es_archivo_vacio());
	}
	
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		if ($this->archivo_subido) {
			$id = $this->estado['error'];
			switch($id){
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_INI_SIZE:
					return "Se supero el tamaño máximo del archivo.";
				case UPLOAD_ERR_FORM_SIZE:
					return "Se supero el limite expresado en el FORM";
				case UPLOAD_ERR_NO_FILE:
					//Este caso lo maneja el obligatorio
					break;
				case UPLOAD_ERR_CANT_WRITE:
					return "No tiene permisos sobre la carpeta de upload";
				default:
					return "Ha ocurrido un error cargando el archivo ($id)";
			}
		}
		return true;
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('efs/ef','efs/ef_upload');
		return $consumos;
	}
	
	function crear_objeto_js()
	{
		return "new ef_upload({$this->parametros_js()})";
	}
	
}

?>