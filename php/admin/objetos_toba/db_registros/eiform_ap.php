<?
require_once("nucleo/browser/clases/objeto_ei_formulario.php");

class eiform_ap extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
/*
		echo "	{$this->objeto_js}.evt__validar_datos = function (){
				var salida = true;
				for (elementos in this._efs) {
					if(this.ef(elementos).activo())
					{
						if(this.ef(elementos).valor() == 'nopar')
						{
							cola_mensajes.agregar('Debe seleccionar el elemento '+elementos);
							salida = false;
						}
					}
				}
				return salida;
			}
			";
*/
	}
}
?>