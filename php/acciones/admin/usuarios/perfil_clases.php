<?
require_once("nucleo/browser/clases/objeto_cuadro.php");
class objeto_cuadro_perfil extends objeto_cuadro
{
	
	function objeto_cuadro_perfil($id, & $solicitud)
	{	parent :: objeto_cuadro($id, $solicitud);	}
	
	
	function presentar_editor($fila, $valor)
	{
		$param_html['texto'] = "Editar RESTRICCION";
		$param_html['tipo'] = "popup";
		$param_html['imagen_recurso_origen'] = "apex";
		$param_html['imagen'] = "usuarios/editar_perfil.gif";
		$x = isset($this->datos[$fila]['x'])? $this->datos[$fila]['x'] : "600";
		$y = isset($this->datos[$fila]['y'])? $this->datos[$fila]['y'] : "400";
		$param_html['inicializacion'] = "$x,$y,yes";
		$clave = $this->obtener_clave_fila($fila);
		return $this->solicitud->vinculador->generar_solicitud($this->datos[$fila]['editor_proyecto'],
																$this->datos[$fila]['editor'],
																array('dim'=>$clave),
																false, false, $param_html);
	}

	function navegar_dimension($fila, $valor)
	{
		$parametro = $this->datos[$fila]['dim_proy'] . apex_qs_separador . $this->datos[$fila]['dim'];    
		$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto($this->id,"dim",$parametro,true,"Editar DIMENSION",false);
		return $vinculo;
	}

}
//###########################################################
?>