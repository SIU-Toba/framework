<?php

/**
 * Centraliza la generación de código e includes javacript:
 *  - Include centralizado de recursos js
 *  - Conversión de estructuras de datos entre php y js
 *
 * @package SalidaGrafica
 */
class toba_acciones_js
{
    private static $instancia;
    protected $acciones = array();

    /**
     * @return toba_acciones_js
     */
    public static function instancia()
    {
        if (! isset(self::$instancia)) {
            self::$instancia = new toba_acciones_js();
        }
        return self::$instancia;
    }

    /**
     * @ignore
     */
    public function generar_js()
    {
        echo implode('', $this->acciones);
    }


    /**
     * Encola la ejecución de un código generico javascript
     * @param $codigo_js Código js (sin tags de apertura)
     */
    public function encolar($codigo_js)
    {
        $this->acciones[] = $codigo_js;
    }

    /**
     * Recarga la página actual
     * @param boolean $post Si es true intenta recargar incluyendo los datos del POST (pide confirmación al usuario)
     */
    public function refrescar_ventana($post = false)
    {
        if ($post) {
            $accion = "window.location.reload(false);\n";
        } else {
            $accion = "window.location.href = window.location.href;\n";
        }
        $this->encolar($accion);
    }

    /**
     * Cierra la ventana actual
     */
    public function cerrar_ventana()
    {
        $accion = "window.close();\n";
        $this->encolar($accion);
    }

    /**
     * Navega hacia la operación destino indicada en el vinculo
     * @param toba_vinculo $vinculo
     */
    public function navegar(toba_vinculo $vinculo)
    {
        $id = toba::vinculador()->registrar_vinculo($vinculo);
        if (isset($id)) {
            $accion = "vinculador.invocar('$id');\n";
            $this->encolar($accion);
        } else {
            toba::logger()->warning("El usuario no puede acceder a la operación ".$vinculo->get_item());
        }
    }

    /**
     * Dado un vinculo, lo abre en una ventana popup
     * Similar a llamar a $vinculo->activar_popup y usar navegar($vinculo)
     * @param toba_vinculo $vinculo
     */
    public function abrir_popup(toba_vinculo $vinculo)
    {
        $vinculo->activar_popup('popup');
        $this->navegar($vinculo);
    }
}
