<?php

class toba_rf implements toba_nodo_arbol_form
{
    protected $padre;
    protected $id_padre;
    protected $nombre_corto;
    protected $nombre_largo = null;
    protected $id = null;
    protected $iconos = array();
    protected $utilerias = array();
    protected $info_extra = null;
    protected $tiene_hijos_cargados = false;
    protected $es_hoja = true;
    protected $hijos = array();
    protected $propiedades = null;
    protected $imagen;
    protected $imagen_origen;
    protected $nivel;
    protected $carpeta = false;
    protected $camino;
    protected $proyecto;

    protected $img_oculto;
    protected $img_visible;
    protected $no_visible_original;
    protected $no_visible_actual;

    protected $abierto = false;

    protected $restriccion = '';
    protected $item;

    protected $comunicacion_elemento_input = true;
    protected $id_js_arbol;

    public function __construct($nombre, $padre = null, $id = null)
    {
        $this->nombre_corto = $nombre;
        $this->padre = $padre;
        $this->id = $id;
        $this->inicializar();
        $this->get_imagenes_estado();
    }

    public function es_raiz()
    {
        return isset($this->id_padre) && $this->id_padre == $this->id;
    }

    public function inicializar()
    {
    }

    public function get_imagenes_estado()
    {
        $this->img_oculto = toba_recurso::imagen_toba('no-visible.png', false);
        $this->img_visible = toba_recurso::imagen_toba('visible.png', false);
    }

    //-- Setters -------------------------------------------------------

    public function set_restriccion($restriccion)
    {
        $this->restriccion = $restriccion;
        foreach ($this->hijos as $hijo) {
            $hijo->set_restriccion($this->restriccion);
        }
    }

    public function agregar_utileria($utileria)
    {
        $this->utilerias[] = $utileria;
    }

    public function agregar_icono($icono)
    {
        $this->iconos[] = $icono;
    }

    public function agregar_hijo($hijo)
    {
        $this->hijos[] = $hijo;
        $this->tiene_hijos_cargados = true;
        $this->es_hoja = false;
    }

    public function set_hijos($hijos)
    {
        $this->hijos = $hijos;
        $this->tiene_hijos_cargados = true;
        $this->es_hoja = false;
    }

    public function set_utilerias($utilerias)
    {
        $this->utilerias = $utilerias;
    }

    public function set_iconos($iconos)
    {
        $this->iconos = $iconos;
    }

    public function set_padre($padre)
    {
        $this->padre = $padre;
    }

    public function set_nivel($nivel)
    {
        $this->nivel = $nivel;
    }

    public function set_camino($camino)
    {
        $this->camino = $camino;
    }

    //-- Interface -----------------------------------------------------

    public function marcar_abiertos()
    {
        $nodo = $this->get_padre();
        while (! is_null($nodo) && !$nodo->es_raiz() && !$nodo->get_apertura()) {
            $nodo->set_apertura(true);
            $nodo = $nodo->get_padre();
        }
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_nombre_corto()
    {
        return $this->nombre_corto;
    }

    public function get_nombre_largo()
    {
        return $this->nombre_largo;
    }

    public function get_info_extra()
    {
        return $this->info_extra;
    }

    public function get_iconos()
    {
        return $this->iconos;
    }

    public function get_imagen()
    {
        if (isset($this->imagen) && ($this->imagen != '') && ($this->imagen_origen != '')) {
            if ($this->imagen_origen == 'apex') {
                $imagen = toba_recurso::imagen_toba($this->imagen, false);
            } else {
                $imagen = toba_recurso::url_proyecto($this->proyecto).'/img/'.$this->imagen;
            }
        }
        if (!isset($imagen)) {
            $imagen = toba_recurso::imagen_toba($this->icono, false);
        }
        $icono = array('imagen' => $imagen, 'ayuda' => $this->nombre_corto);
        $this->agregar_icono($icono);
    }

    public function get_utilerias()
    {
        return $this->utilerias;
    }

    public function get_padre()
    {
        return $this->padre;
    }

    public function get_id_padre()
    {
        return $this->id_padre;
    }

    public function tiene_hijos_cargados()
    {
        return $this->tiene_hijos_cargados;
    }

    public function es_hoja()
    {
        return $this->es_hoja;
    }

    public function get_hijos()
    {
        return $this->hijos;
    }

    public function tiene_propiedades()
    {
        return $this->propiedades;
    }

    public function get_input($id)
    {
    }

    public function cargar_estado_post($id)
    {
    }

    public function set_apertura($abierto)
    {
        $this->abierto = $abierto;
    }

    public function get_apertura()
    {
        return $this->abierto;
    }

    public function es_carpeta()
    {
        return $this->carpeta;
    }

    public function desactivar_envio_inputs()
    {
        $this->comunicacion_elemento_input = false;
    }

    public function set_estado_visibilidad($acceso)
    {
        $this->no_visible_actual = $acceso;
    }

    public function set_js_ei_arbol($arbol_padre)
    {
        $this->id_js_arbol = $arbol_padre;
    }

    //-------------------------------------------------------------------------------------------//
    //				ESTADO DEL POST
    //-------------------------------------------------------------------------------------------//
    public function propagar_estado_hijos($activos, $inactivos)
    {
        //Primero miro el valor actual del elemento
        $esta_invisible = (in_array($this->id, $inactivos) && ! in_array($this->id, $activos));
        $this->set_estado_visibilidad($esta_invisible);

        if ($this->tiene_hijos_cargados()) {
            foreach ($this->get_hijos() as $hijo) {
                $hijo->propagar_estado_hijos($activos, $inactivos);
            }
        }
    }

    //-------------------------------------------------------------------------------------------//
    //				ENVIADO AL CLIENTE
    //-------------------------------------------------------------------------------------------//
    public function recuperar_estado_recursivo()
    {
        $estado = array('activos' => array(), 'inactivos' => array());
        if ($this->tiene_hijos_cargados()) {
            foreach ($this->get_hijos() as $hijo) {
                $aux = $hijo->recuperar_estado_recursivo();
                $estado['activos'] = array_merge($estado['activos'], $aux['activos']);
                $estado['inactivos'] = array_merge($estado['inactivos'], $aux['inactivos']);
            }
        }

        if (isset($this->no_visible_actual) && $this->no_visible_actual) {
            $estado['inactivos'][] = $this->get_id();
        } else {
            $estado['activos'][] = $this->get_id();
        }
        return $estado;
    }
}
