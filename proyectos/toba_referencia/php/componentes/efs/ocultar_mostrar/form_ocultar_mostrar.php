<?php 
php_referencia::instancia()->agregar(__FILE__);

class form_ocultar_mostrar extends toba_ei_formulario
{
	
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__efecto__procesar = function(es_inicial) 
			{
				if (! es_inicial) {
					this.evt__categoria__procesar(es_inicial);
				}
			}		
			
			{$id_js}.evt__categoria__procesar = function(es_inicial) 
			{
				switch (this.ef('categoria').get_estado()) {
					case 'A':
						this.mostrar_bloque_A(true);
						this.mostrar_bloque_B(false);
						break;
					case 'B':
						this.mostrar_bloque_A(false);
						this.mostrar_bloque_B(true);
						break;
					case 'Todas':
						this.mostrar_bloque_A(true);
						this.mostrar_bloque_B(true);
						break;						
					default:
						this.mostrar_bloque_A(false);
						this.mostrar_bloque_B(false);
						break;					
				}
			}
			
			{$id_js}.mostrar_bloque_A = function(visible)
			{
				if (this.ef('efecto').get_estado() == 'ocultar') {
					this.ef('sepa').mostrar(visible);
					this.ef('a1').mostrar(visible);
					this.ef('a2').mostrar(visible);
				} else {
					this.ef('a1').set_solo_lectura(visible);
					this.ef('a2').set_solo_lectura(visible);
				}
			}
			
			{$id_js}.mostrar_bloque_B = function(visible)
			{
				if (this.ef('efecto').get_estado() == 'ocultar') {			
					this.ef('sepb').mostrar(visible);
					this.ef('b1').mostrar(visible);
					this.ef('b2').mostrar(visible);			
				} else {
					this.ef('b1').set_solo_lectura(visible);
					this.ef('b2').set_solo_lectura(visible);
				}
			}			
		";
	}
}

?>