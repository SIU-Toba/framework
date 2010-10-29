<?php 
class arbol_perfiles_funcionales extends toba_ei_arbol
{
	function extender_objeto_js()
	{
		$img_acceso = toba_recurso::imagen_toba('aplicar.png', false);
		$img_sin_acceso = toba_recurso::imagen_toba('borrar.gif', false);
		echo "
			function cambiar_acceso(id_input) {
				var valor_actual = $$(id_input).value;
				if (valor_actual == 1) {
					//Esta oculto, hay que mostrarlo
					$$(id_input + '_img').src = '$img_sin_acceso';
					$$(id_input).value = 0;
				} else {
					//Esta visible, hay que ocultarlo
					$$(id_input + '_img').src = '$img_acceso';
					$$(id_input).value = 1;
				}
			}
			
			function marcar(id_input){
				if ($$(id_input).value == 1){
					$$(id_input).value = 0;
				}else{
					$$(id_input).value = 1;
				}
				var valor = $$(id_input).value;
				var padre = $$(id_input).parentNode.parentNode;
				var nodo = this.buscar_primer_marca(padre, 'UL');
				if (nodo) {		
					for (var i=0; i < nodo.childNodes.length; i++) {
						var hijo = nodo.childNodes[i];
						if (hijo.tagName && (hijo.tagName == 'LI')) {
							if (!this.buscar_primer_marca(hijo, 'UL')){
								this.cambiar_estado_acceso(hijo, valor);
							}else{
								this.marcar_recursivo(hijo, valor);
							}
						}
					}
				}			
			}
			
			function marcar_recursivo(carpeta, valor){
				var marca_carpeta = this.buscar_primer_marca(carpeta, 'SPAN');
				if (marca_carpeta){
					for (var i=0; i < marca_carpeta.childNodes.length; i++) {
						var hc = marca_carpeta.childNodes[i];
						if (hc.tagName && (hc.tagName == 'INPUT')) {
							$$(hc.id).value = valor;
							$$(hc.id).checked = (valor == 0) ? true : false;
						}
					}
				}
				var nodo = this.buscar_primer_marca(carpeta, 'UL');		
				for (var i=0; i < nodo.childNodes.length; i++) {
					var hijo = nodo.childNodes[i];
					if (hijo.tagName && (hijo.tagName == 'LI')) {
						if (!this.buscar_primer_marca(hijo, 'UL')){
							this.cambiar_estado_acceso(hijo, valor);
						}else{
							this.marcar_recursivo(hijo, valor);
						}
					}
				}
			}
			
			function cambiar_estado_acceso(nodo, valor){
				for (var i=0; i < nodo.childNodes.length; i++) {
					if (nodo.childNodes[i].tagName == 'SPAN') {
						var hijo = nodo.childNodes[i];
						for (var j=0; j < hijo.childNodes.length; j++) {
							if (hijo.childNodes[j].tagName == 'INPUT') {	
								var imagen = $$(hijo.childNodes[j].id + '_img')
								if (isset(imagen)) {				
									if (valor == 1){
										$$(hijo.childNodes[j].id).value = 0;
										$$(hijo.childNodes[j].id + '_img').src = '$img_sin_acceso';
									}else{
										$$(hijo.childNodes[j].id).value = 1;
										$$(hijo.childNodes[j].id + '_img').src = '$img_acceso';
									}
								}
							}
						}
					}
				}
			}
			
			function buscar_primer_marca(nodo, marca){
				for (var i=0; i < nodo.childNodes.length; i++) {
					if (nodo.childNodes[i].tagName == marca) {
						return nodo.childNodes[i];
					}
				}
				return false;
			}
		";
	}
}

?>