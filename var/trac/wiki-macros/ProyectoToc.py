# -*- coding: iso-8859-1 -*-
"""
This macro shows a quick and dirty way to make a table-of-contents for a set
of wiki pages.
"""

TOC = [
       ('Instalacion',                  'Instalación'),
       ('Referencia',                   'Conceptos Básicos'),
       ('Referencia/Objetos',           'Componentes'),
       ('Referencia/Eventos',           'Eventos'),
       ('Referencia/FuenteDatos',       'Fuentes de Datos'),                     
       ('Referencia/Objetos/Persistencia',         'Persistencia'),       
       #('Referencia/MarcoTransaccional','Marco Transaccional'),                     
       #('Referencia/Ejecucion',		'Seguimiento de la ejecución'),                            
       ('Tutorial',       		  'Tutorial'),
       ('Proyectos/Referencia',         'Proyecto Referencia'),
       ]

def execute(hdf, args, env):
    html = '<div class="wiki-toc indice-general">' \
           '<h4>Indice General</h4>' \
           '<ul>'
    curpage = '%s' % hdf.getValue('wiki.page_name', '')
    lang, page = '/' in curpage and curpage.split('/', 1) or ('', curpage)
    for ref, title in TOC:
	#html += curpage;
        if curpage == ref:
            cls =  ' class="active"'
        else:
            cls = ''
        html += '<li%s><a href="%s">%s</a></li>' \
                % (cls, env.href.wiki(ref), title)
    html += '<li><a href="/toba_editor_trunk/doc/api/index.html"><img src="/toba_editor_trunk/doc/api/media/php-small.png" style="vertical-align: middle"/> API PHP</a>';
    html += '<li><a href="/toba_editor_trunk/doc/api_js/index.html"><img src="/toba_editor_trunk/doc/api/media/javascript-small.png" style="vertical-align: middle"/> API Javascript</a>';
    return html + '</ul></div>'
