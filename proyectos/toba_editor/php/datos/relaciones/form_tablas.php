<?php 
class form_tablas extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__tabla_1__procesar = function(es_inicial)
		{
			if( !es_inicial  ) this.disparar_modificacion();
		}
		
		{$this->objeto_js}.evt__tabla_2__procesar = function(es_inicial)
		{
			if( !es_inicial  ) this.disparar_modificacion();
		}

		{$this->objeto_js}.disparar_modificacion = function()
		{
			var t1 = this.ef('tabla_1').valor();
			var t2 = this.ef('tabla_2').valor();
			var seteadas = ( t1 != apex_ef_no_seteado ) && ( t2 != apex_ef_no_seteado );
			if ( seteadas ) {
				if ( t1 == t2 ) {
					notificacion.agregar('Seleccione tablas distintas.');
					notificacion.mostrar();
				} else {
					this.set_evento(new evento_ei('modificacion',true,''));
				}
			}
		}
		";
	}
}

?>