/**
 * Permite la busqueda de los items en el menu
 */
// permite almacenar el estado actual del menú
var estado;

$(document).ready(function() {
    estado = $("li.leaf.active");
});
function buscar_menu(valor){
	if ( valor.length >= 3 ){ // al menos 3 caractares
	  $("li.leaf").each(function(){
		  $text = $(this).children('a:first').text().toLowerCase();
		  $valor = valor.toLowerCase();
		  if( $text.indexOf($valor) >= 0 ){
			  $(this).removeClass('hidden')// Por si antes no coincidia y ahora si
			 $(this).parents("li.treeview").addClass("active");
		  }
		  else{
			  $(this).addClass("hidden");; 
		  }
	    
	  });
	}
	else{
		reestablecer();
	}
		
}


function reestablecer(){
	$("li.leaf").each(function(){
		  $(this).removeClass('hidden');
		  $(this).parents("li.treeview").removeClass("active");
	  });
	estado.parents("li.treeview").addClass("active");
}
