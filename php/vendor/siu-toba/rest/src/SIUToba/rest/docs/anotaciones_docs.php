<?php

namespace SIUToba\rest\docs;

use ReflectionClass;
use SIUToba\rest\lib\rest_instanciador;

class anotaciones_docs
{
    protected static $metodos_validos = array('get', 'put', 'post', 'delete');

    /**
     * @var \ReflectionClass
     */
    protected $reflexion;

    protected $anotaciones_clase;

    /**
     * La clase no puede tener namespaces (esta pensada para las del modelo).
     *
     * @param $archivo
     */
    public function __construct($archivo)
    {
        $i = new rest_instanciador();
        $i->archivo = $archivo;
        $obj = $i->get_instancia();

        $this->reflexion = new ReflectionClass($obj);
        $this->get_annotations($this->reflexion);
    }

    /**
     * Parsea una reflexion (metodo, clase) y devuelve las anotaciones en un arreglo.
     *
     * @param $reflexion
     *
     * @return array formato ['nombre'][]
     */
    protected function get_annotations($reflexion)
    {
        if ($this->anotaciones_clase) {
            return $this->anotaciones_clase;
        }
        $this->anotaciones_clase = $this->get_annotations_metodo($reflexion);

        return $this->anotaciones_clase;
    }

    protected function get_annotations_metodo($reflexion)
    {
        $doc = $reflexion->getDocComment();
        $doc = $this->limpiar_doc_comment($doc);

        return $this->extraer_anotaciones($doc);
    }

    /**
     * Limpia los asteriscos y espacios de un phpdoc.
     *
     * @param $doc string php doc
     *
     * @return string el documento sin los caracteres
     */
    protected function limpiar_doc_comment($doc)
    {
        //remuevo /* */ y * de principio de linea
        $doc = preg_replace('#/\*+|\*/#', '', $doc);
        $doc = preg_replace('#^\s*\*+#m', '', $doc);
        //remuevo separadores de mas
        $doc = preg_replace('#\s+#', ' ', $doc);

        return $doc;
    }

    /**
     * En base a un string, extrae @ anotaciones.
     *
     * @param $doc string
     *
     * @return array formato ['nombre'][]
     */
    protected function extraer_anotaciones($doc)
    {
        //remuevo lo que esta antes del primer @
        $annotations = explode('@', $doc);
        array_shift($annotations);

        $retorno = array();
        foreach ($annotations as $annotation) {
            $pos = strpos($annotation, ' ');
            $nombre = substr($annotation, 0, $pos);
            $contenido = substr($annotation, $pos + 1);
            $retorno [$nombre][] = trim($contenido);
        }

        return $retorno;
    }

    public function get_descripcion_clase()
    {
        if (isset($this->anotaciones_clase['description'])) {
            $desc = $this->anotaciones_clase['description'][0];
        } else {
            $desc = "[@description de la clase]";
        }

        return $desc;
    }

    public function get_metodos()
    {
        $mis_metodos = array();
        $metodos = $this->reflexion->getMethods();
        foreach ($metodos as $metodo) {
            if (!$this->es_metodo_de_api($metodo)) {
                continue;
            }

            $parametros = array();
            $parameters = $metodo->getParameters();
            foreach ($parameters as $p) {
                $parametros[] = $p->getName();
            }

            $anotaciones = $this->get_annotations_metodo($metodo);

            $nuevo_metodo = array(
                'nombre' => $metodo->getName(),
                'parametros' => $parametros,
                'anotaciones' => $anotaciones,
            );
            $mis_metodos[] = $nuevo_metodo;
        }

        return $mis_metodos;
    }

    /**
     * @param $metodo
     *
     * @return bool
     */
    protected function es_metodo_de_api($metodo)
    {
        $valido = true;
        if (!$metodo->isPublic()) {
            $valido = false;
        }

        $partes_metodo = explode('_', $metodo->getName());
        $prefijo = array_shift($partes_metodo);
        if (!in_array($prefijo, static::$metodos_validos)) {
            $valido = false;
        }

        return $valido;
    }

    public function get_parametros_metodo($metodo, $type)
    {
        $api_parameters = array();
        $key = 'param_'.$type;
        $anotaciones = $metodo['anotaciones'];
        if (isset($anotaciones[$key])) {
            $parametros = $anotaciones[$key];
            foreach ($parametros as $parameter) {
                $param = $this->get_parametro_tipo($parameter, $type);
                if ($param) {
                    $api_parameters[] = $param;
                }
            }
//            unset($anotaciones['param_'. $type]);
        }

        return $api_parameters;
    }

    protected function get_parametro_tipo($parametro, $type)
    {
        $matches = array();
        preg_match('#(\$\w*)\b\s+(\w*)\s*(?:\[(.*?)\]\s+)?(.*)#', $parametro, $matches);

        if (count($matches) <= 3) {
            return array();
        }

        $api_parameter = array();
        $api_parameter['name'] = ltrim($matches[1], '$');
        $api_parameter['in'] = $type;
        $api_parameter['type'] = $matches[2];
        $api_parameter['description'] = $matches[4] ?: '[sin descripcion]';

        if (!empty($matches[3])) {
            $modificadores = $matches[3];
            if (preg_match('/required/', $modificadores)) {
                $api_parameter['required'] = true;
            }
        }

        return $api_parameter;
    }

    public function get_summary_metodo($metodo)
    {
        if (isset($metodo['anotaciones']['summary'])) {
            return $metodo['anotaciones']['summary'][0];
        }

        return '';
    }

    public function get_notes_metodo($metodo)
    {
        if (isset($metodo['anotaciones']['notes'])) {
            return $metodo['anotaciones']['notes'][0];
        }

        return '';
    }

    public function get_respuestas_metodo($metodo)
    {
        $respuestas = array();
        if (isset($metodo['anotaciones']['responses'])) {
            foreach ($metodo['anotaciones']['responses'] as $respuesta) {
                $matches = array();
                //200 [array] $tipo descripcion
                if (0 === preg_match('#(\d\d\d)\s+(array)?\s*(\$\w*)?\s*(.*)#', $respuesta, $matches)) {
                    continue;
                };

                $status = $matches[1];

                if ($matches[2] == 'array') {
                    $items = $this->get_tipo_datos($matches[3]);
                    $schema = array(
                        'type' => 'array',
                        'items' => $items,
                    );
                } else {
                    $schema = $this->get_tipo_datos($matches[3]);
                }
                $mje = $matches[4];

                $resObj = array(
                    'schema' => $schema,
                    'description' => $mje,
                );

                $respuestas[$status] = $resObj;
            }
        }

        return $respuestas;
    }

    protected function termina_con($needle, $haystack)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * @param $tipo
     *
     * @return array
     */
    protected function get_tipo_datos($tipo)
    {
        if (empty($tipo)) {
            return;
        }
        if ($tipo[0] == '$') {
            $tipo = ltrim($tipo, '$');
            $tipoRef = array('$ref' => "#/definitions/".$tipo);

            return $tipoRef;
        } else {
            $tipo = array('type' => $tipo);

            return $tipo;
        }
    }
}
