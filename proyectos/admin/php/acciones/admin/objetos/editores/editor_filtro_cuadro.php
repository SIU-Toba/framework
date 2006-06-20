<?
require_once("nucleo/browser/clases/objeto_cuadro.php");

class objeto_cuadro_filtro extends objeto_cuadro
{
    
     function objeto_cuadro_filtro($id, & $solicitud)
    {
         parent :: objeto_cuadro($id, $solicitud);
    }
    
     function procesar_celda_dimension($fila, $valor)
    {
		$parametro = $this->datos[$fila]['dimension_proyecto'] . 
				apex_qs_separador . $this->datos[$fila]['dimension'];    
		$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto($this->id,"dim",$parametro,true);
		return $vinculo;																
    }
}
?>