<?php 
//--------------------------------------------------------------------
class form_carga extends toba_ei_formulario
{
	

	function extender_objeto_js()
	{
		echo "
			var mecanismos_carga = ['carga_metodo','carga_sql', 'carga_lista', 'carga_dt'];
			
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
						this.cambiar_mecanismo(mecanismos_carga[i], mostrar, actual);
					}

				}
			}

			{$this->objeto_js}.cambiar_mecanismo = function(mecanismo, estado, actual) {
				switch (mecanismo) {
					case 'carga_metodo':
						this.ef('estatico').mostrar(estado, true);
						if (estado) {
							this.evt__estatico__procesar(false);
						} else {
							this.ef('carga_include').ocultar(true);
							this.ef('carga_clase').ocultar(true);						
						}
						if (actual == 'carga_dt') {
							//-- Caso particular porque la forma de esta extension no se banca que dos mecanismos re-utilicen un ef						
							estado = true;
						}
						this.ef('carga_metodo').mostrar(estado, true);
						break;
					case 'carga_sql':
						this.ef('carga_sql').mostrar(estado, true);
						this.ef('carga_fuente').mostrar(estado, true);
						break;
					case 'carga_lista':
						if (this.ef('carga_lista')) 
							this.ef('carga_lista').mostrar(estado, true);
						if (this.ef('carga_col_clave')) 
							this.ef('carga_col_clave').mostrar(!estado, true);
						if (this.ef('carga_col_desc')) 							
							this.ef('carga_col_desc').mostrar(!estado, true);
						break;
					case 'carga_dt':
						this.ef('carga_dt').mostrar(estado, true);
						if (actual == 'carga_metodo') {
							//-- Caso particular porque la forma de esta extension no se banca que dos mecanismos re-utilicen un ef						
							estado = true;
						}						
						this.ef('carga_metodo').mostrar(estado, true);
						break; 
				}
			}
			
			{$this->objeto_js}.evt__carga_dt__procesar = function(inicial) {
				if (this.ef('carga_dt').get_estado() != apex_ef_no_seteado) {
					this.ef('carga_metodo').get_contenedor()
				}
			}
		";
	}

}

?>