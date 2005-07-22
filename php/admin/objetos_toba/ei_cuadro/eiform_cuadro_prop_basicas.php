<?
require_once("nucleo/browser/clases/objeto_ei_formulario.php");

class eiform_cuadro_prop_basicas extends objeto_ei_formulario
{
	function extender_objeto_js() 
	{
		//$ef = $this->obtener_nombres_ef();
		echo "
		{$this->objeto_js}.evt__clave_dbr__procesar = function (inicial) {
			var flag;
			flag = this.ef('clave_dbr').valor();
			if( flag == 1 ){
				this.ef('columnas_clave').ocultar();
			}else{
				this.ef('columnas_clave').mostrar();
			}
		}

		{$this->objeto_js}.evt__scroll__procesar = function (inicial) {
			var flag;
			flag = this.ef('scroll').valor();
			if( flag == 1 ){
				this.ef('scroll_alto').mostrar();
			}else{
				this.ef('scroll_alto').ocultar();
			}
		}
		";
	}
}
?>


