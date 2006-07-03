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
				this.ef('carga_include').mostrar(cheq, true);
				this.ef('carga_clase').mostrar(cheq, true);
			}
						
			{$this->objeto_js}.evt__mecanismo__procesar = function(inicial) {
				actual = this.ef('mecanismo').valor();
				var mostrar = (actual != apex_ef_no_seteado);
				//---Ocultar/Mostrar todos
				for (var id_ef in this._efs) {
					if (id_ef != 'mecanismo') {
						this.ef(id_ef).mostrar(mostrar, true);
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
						this.ef('estatico').mostrar(estado, true);
						if (estado) {
							this.evt__estatico__procesar(false);
						} else {
							this.ef('carga_include').ocultar(true);
							this.ef('carga_clase').ocultar(true);						
						}
						this.ef('carga_metodo').mostrar(estado, true);
						break;
					case 'sql':
						this.ef('carga_sql').mostrar(estado, true);
						this.ef('carga_fuente').mostrar(estado, true);
						break;
					
					case 'lista':
						this.ef('carga_lista').mostrar(estado, true);
						this.ef('carga_col_clave').mostrar(!estado, true);
						this.ef('carga_col_desc').mostrar(!estado, true);
						break;
				}
			}
		";
	}

}

?>