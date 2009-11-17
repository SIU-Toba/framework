<?php
php_referencia::instancia()->agregar(__FILE__);


class icono_informacion implements toba_ef_icono_utileria
{
	function get_html(toba_ef $ef)
	{
		$objeto_js = $ef->objeto_js();
		$javascript = "alert('Estado actual: ' + $objeto_js.get_estado());";
		$salida = "<a class='icono-utileria' href='#' onclick=\"$javascript\">";
		$salida .= toba_recurso::imagen_toba('info_chico.gif', true, null, null, "Ver estado actual del campo");
		$salida .= "</a>";
		return $salida;
	}
}

class icono_limpiar implements toba_ef_icono_utileria
{
	function get_html(toba_ef $ef)
	{
		$objeto_js = $ef->objeto_js();
		$javascript = "$objeto_js.resetear_estado();";
		$salida = "<a class='icono-utileria' href='#' onclick=\"$javascript\">";
		$salida .= toba_recurso::imagen_toba('limpiar.png', true, null, null, "Resetear estado actual del campo");
		$salida .= "</a>";
		return $salida;
	}
}


class ci_iconos_utilerias extends toba_ci
{
	function conf__form(toba_ei_formulario $form)
	{
		//-- Se agrega un icono de información al lado de cada ef
		$icono_informacion = new icono_informacion();
		foreach ($form->get_nombres_ef() as $ef) {
			$form->ef($ef)->agregar_icono_utileria($icono_informacion);
		}

		//-- Para el ef_combo se agrega otra utileria
		$form->ef('combo')->agregar_icono_utileria(new icono_limpiar());
	}

}

?>