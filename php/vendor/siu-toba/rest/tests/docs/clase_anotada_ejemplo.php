<?php

/** // https://github.com/wordnik/swagger-core/wiki/Datatypes
 * La anotaci�n model permite utilizar el model "Persona" en la definicion de los metodos, sino hay que definir los tipos de datos complejos inline en cada metodo.
 *
 * @description    descripcion clase
 *
 * jk
 * %%
 *
 * @model {
 * "id": "Persona",
 * "required": ["id"],
 *      "properties": {
 *          "id": {"type": "integer"},
 *          "nombre": {"type": "string" },
 *          "apellido" : {"type": "string"}
 *      }
 * }
 */
class clase_anotada_ejemplo
{
    /**
     * Se consume en GET /personas.
     *
     * @param_query $juego string nombre del juego
     * @param_query $nombre string Se define como condicion;valor
     * @param_query $fecha_nac string condicion;valor
     * @notes         En los filtros 'condicion' puede ser contiene|no_contiene|comienza_con|es_igual_a
     *
     * @param_body $limit integer Limitar a esta cantidad de registros
     * @responses        200  array $Persona descripcion
     * @responses        404 No se pudo encontrar a la persona
     * @responses        400 $null
     */
    public function get($id_persona)
    {
    }
}
