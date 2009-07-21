"""
    Hace un link a una imagen de toba[[BR]]
    Sintaxis: {{{[[img_toba(path,proyecto)]]}}} [[BR]]
    Proyecto es opcional (por defecto usa el nucleo)
"""    

from trac.wiki.macros import WikiMacroBase

class imagen_toba(WikiMacroBase):
    """Simple HelloWorld macro.

    Note that the name of the class is meaningful:
     - it must end with "Macro"
     - what comes before "Macro" ends up being the macro name

    The documentation of the class (i.e. what you're reading)
    will become the documentation of the macro, as shown by
    the !MacroList macro (usually used in the WikiMacros page).
    """

    revision = "$Rev$"
    url = "$URL$"

    def expand_macro(self, formatter, name, txt):
        """Return some output that will be displayed in the Wiki content.

        `name` is the actual name of the macro (no surprise, here it'll be
        `'HelloWorld'`),
        `args` is the text enclosed in parenthesis at the call of the macro.
          Note that if there are ''no'' parenthesis (like in, e.g.
          [[HelloWorld]]), then `args` is `None`.
        """
        args = txt.split(',', 2)
        url = args[0].strip()   
        if len(args) > 1:
            url = "/%s_trunk/img/%s" % (args[1].strip(), url)
        else:
            url = "/toba_trunk/img/%s" % (url)

        salida = "<img onerror='alert(\"No se encuentra la imagen: \" + this.src)' style='vertical-align:middle' border=0 src='%s' />" % (url)
        return salida
