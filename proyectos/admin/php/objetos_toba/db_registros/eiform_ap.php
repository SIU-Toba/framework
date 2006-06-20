<?


class eiform_ap extends objeto_ei_formulario
{
	function extender_objeto_js() 
	{
		echo "
		{$this->objeto_js}.evt__ap__procesar = function () {
			var flag;
			flag = this.ef('ap').valor();
			if( flag == 0 ){
				this.ef('ap_clase').mostrar();
				this.ef('ap_archivo').mostrar();
			}else{
				this.ef('ap_clase').ocultar();
				this.ef('ap_archivo').ocultar();
			}
		}
		";
	}
}
?>