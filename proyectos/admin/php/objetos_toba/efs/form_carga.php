<?php 
//--------------------------------------------------------------------
class form_carga extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			var mecanismos_carga = ['php','sql', 'lista'];
			
			{$this->objeto_js}.evt__estatico__procesar = function(inicial) {
				var cheq = this.ef('estatico').chequeado();
				this.ef('include').mostrar(cheq);
				this.ef('clase').mostrar(cheq);
			}
						
			{$this->objeto_js}.evt__mecanismo__procesar = function(inicial) {
				actual = this.ef('mecanismo').valor();
				var mostrar = (actual != apex_ef_no_seteado);
				//---Ocultar/Mostrar todos
				for (var id_ef in this._efs) {
					if (id_ef != 'mecanismo') {
						this.ef(id_ef).mostrar(mostrar);
					}
				}
				if (mostrar) {
					for (var i=0; i < mecanismos_carga.length; i++) {
						var mostrar = (actual == mecanismos_carga[i]);
						this.cambiar_mecanismo(mecanismos_carga[i], mostrar);
					}
				}
			}

			{$this->objeto_js}.cambiar_mecanismo = function(mecanismo, estado) {
				switch (mecanismo) {
					case 'php':
						this.ef('estatico').mostrar(estado);
						if (estado) {
							this.evt__estatico__procesar(false);
						} else {
							this.ef('include').ocultar();
							this.ef('clase').ocultar();						
						}
						this.ef('dao').mostrar(estado);
						break;
					case 'sql':
						this.ef('sql').mostrar(estado);
						this.ef('fuente').mostrar(estado);
						break;
					
					case 'lista':
						this.ef('lista').mostrar(estado);
						this.ef('clave').mostrar(!estado);
						this.ef('valor').mostrar(!estado);
						break;
				}
			}
		";
	}

}

?>