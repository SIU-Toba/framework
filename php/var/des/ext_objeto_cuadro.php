<?php
require_once("nucleo/browser/clases/objeto_cuadro.php");
class objeto_cuadro_x extends objeto_cuadro
/*
	 	@acceso: nucleo
		@desc: Extension de la clase ABMS para realizar validaciones especificas
*/
{
    
     function objeto_cuadro_x($id, & $solicitud)
/*
      @@acceso: nucleo
      @@desc: Muestra la definicion del OBJETO
*/
    {	parent :: objeto_cuadro($id, $solicitud);   }
    
    
     function procesar_celda_x($fila, $valor)
/*
	@@acceso: nucleo
	@@desc: Regenera el valor del dato a mostrar en una celda
	@@param: int | Fila que se esta procesando
	@@param: string | Valor a procesar
*/
    {
		//CONSIGNAS: 
		//1) Utilizar $this->datos[$fila]['x'] para acceder a un dato de la fila
		// $this->datos[$fila]['cronometro'] > 1)
		//2) Utilizar $this->solicitud->vinculador->obtener_vinculo_de_objeto($this->id, $id_vinculo, valor a transmitir)
		// para acceder a un vinculo del OBJETO
		//3) Retornar el valor procesadoreturn $vinculo;																
    }
}
?>