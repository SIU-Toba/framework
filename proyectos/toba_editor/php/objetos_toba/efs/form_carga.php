<?php 
//--------------------------------------------------------------------
class form_carga extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			var mecanismos_carga = ['carga_metodo','carga_sql', 'carga_lista'];

			{$id_js}.evt__punto_montaje__procesar = function(inicial)
			{
				if (!inicial) {
					this.ef('carga_include').cambiar_valor('');
				}
			}
			
			{$id_js}.modificar_vinculo__ef_carga_include = function(id_vinculo)
			{
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
			}
			
			{$id_js}.evt__tipo_clase__procesar = function(inicial) {
				var cheq = this.ef('tipo_clase').get_estado();
				this.ef('carga_include').mostrar((cheq == 'estatica'), true);
				this.ef('carga_clase').mostrar((cheq == 'estatica'), true);
				this.ef('punto_montaje').mostrar((cheq == 'estatica'));
				this.ef('carga_consulta_php').mostrar((cheq == 'consulta_php'), true);
				this.ef('carga_dt').mostrar((cheq == 'datos_tabla'), true);
				this.ef('carga_metodo_lista').mostrar((cheq == 'consulta_php'), true);
				if (! inicial) {
					if (this.ef('carga_col_clave'))
						this.ef('carga_col_clave').mostrar(cheq != apex_ef_no_seteado);
					if (this.ef('carga_col_desc'))
						this.ef('carga_col_desc').mostrar(cheq != apex_ef_no_seteado);
				}
				var div = $$('nodo_carga_metodo');
				if (div) {
					div.innerHTML = '';
				}
				this.ef('carga_metodo').mostrar(cheq != apex_ef_no_seteado);
			}
			
			/**
			 *  Actualiza el edit del metodo a partir del combo
			 */
			{$id_js}.evt__carga_metodo_lista__procesar = function(inicial) {
				var estado = this.ef('carga_metodo_lista').get_estado();
				if (this.ef('tipo_clase').get_estado() == 'consulta_php') {
					if (estado != apex_ef_no_seteado) {
						this.ef('carga_metodo').set_estado(estado);
						this.ef('carga_metodo').ocultar();
					} else {
						this.ef('carga_metodo').mostrar();
					}
				}
			}
						
			{$id_js}.evt__mecanismo__procesar = function(inicial) {
				actual = this.ef('mecanismo').valor();
				var mostrar = (actual != apex_ef_no_seteado);
				//---Ocultar/Mostrar todos
				for (var id_ef in this._efs) {
					if (id_ef != 'mecanismo' && id_ef != 'sep_carga') {
						this.ef(id_ef).mostrar(mostrar, true);
					}
				}
				if (mostrar) {
					for (var i=0; i < mecanismos_carga.length; i++) {
						var mostrar = (actual == mecanismos_carga[i]);
						this.cambiar_mecanismo(mecanismos_carga[i], mostrar, actual);
					}";
		if ($this->existe_ef('carga_permite_no_seteado')) {
			echo "	{$id_js}.evt__carga_permite_no_seteado__procesar(inicial);";
		}

		echo "
				}
			}

			{$id_js}.cambiar_mecanismo = function(mecanismo, estado, actual) {
				switch (mecanismo) {
					case 'carga_metodo':
						this.ef('tipo_clase').mostrar(estado, true);
						this.evt__tipo_clase__procesar(false);
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
				}
			}
			
			{$id_js}.evt__carga_dt__procesar = function(inicial) {
				if (inicial) return;
				var tabla_actual = this.ef('carga_dt').get_estado();
				if (tabla_actual != apex_ef_no_seteado) {
					this.ef('carga_metodo').set_estado('');
					if (this.ef('carga_col_clave'))
						this.ef('carga_col_clave').set_estado('');
					if (this.ef('carga_col_desc'))						
						this.ef('carga_col_desc').set_estado('');					
					this.controlador.ajax('existe_metodo_dt', tabla_actual, this, this.respuesta_existe_dt); 
				} else {
					this.ef('carga_metodo').ocultar(true);
					if (this.ef('carga_col_clave'))
						this.ef('carga_col_clave').ocultar(true);
					if (this.ef('carga_col_desc'))
						this.ef('carga_col_desc').ocultar(true);
				}
			}
			
			{$id_js}.respuesta_existe_dt = function(existe) {
				this.ef('carga_metodo').mostrar();
				if (this.ef('carga_col_clave'))
					this.ef('carga_col_clave').mostrar(true);
				if (this.ef('carga_col_desc'))					
					this.ef('carga_col_desc').mostrar(true);				
				var div = $$('nodo_carga_metodo');
				if (! div) {
					this.ef('carga_metodo').get_contenedor().innerHTML += '<span id=\"nodo_carga_metodo\"></span>';	
				}			
				div = $$('nodo_carga_metodo');				
				if (! existe) {
					this.ef('carga_metodo').set_estado('');
					var link = '<a href=\"javascript: {$id_js}.generar_metodo()\" ';
					link += 'title=\"Crea un método get_descripciones() dentro de la extensión del datos tabla, conteniendo el select requerido para cargar las descripciones de esta tabla\">';
					link += 'Crear método <strong>get_descripciones</strong></a>';
					div.innerHTML = link;
				} else {
					this.respuesta_crear_dt(existe);
				}			
			}			
			
			{$id_js}.generar_metodo = function() {
				var tabla_actual = this.ef('carga_dt').get_estado();
				this.controlador.ajax('crear_metodo_get_descripciones', tabla_actual, this, this.respuesta_crear_dt);
			}			
			
			{$id_js}.respuesta_crear_dt = function(datos) {
				if (datos) {
					var div = $$('nodo_carga_metodo');				
					div.innerHTML = '';				
					this.ef('carga_metodo').set_estado('get_descripciones');
					if (this.ef('carga_col_clave'))
						this.ef('carga_col_clave').set_estado(datos[1]);
					if (this.ef('carga_col_desc'))
						this.ef('carga_col_desc').set_estado(datos[2]);
				}
			}

			{$id_js}.evt__carga_permite_no_seteado__procesar = function(es_inicial) {
				if (this.ef('carga_permite_no_seteado').chequeado()) {
					this.ef('carga_no_seteado').mostrar();
					this.ef('carga_no_seteado_ocultar').mostrar();
				} else {
					this.ef('carga_no_seteado').ocultar();
					this.ef('carga_no_seteado_ocultar').ocultar();
				}
			}
		";
	}

}

?>