from StringIO import StringIO
import re

def execute(hdf, args, env):
    db = env.get_db_cnx()
    cursor = db.cursor()

    thispage = None

    if args:
        thispage = args.replace('\'', '\'\'')
    else :
    	thispage = hdf.getValue('wiki.page_name', '')


    sql = 'SELECT w1.name FROM wiki w1, ' + \
          '(SELECT name, MAX(version) AS VERSION FROM WIKI GROUP BY NAME) w2 ' + \
          'WHERE w1.version = w2.version AND w1.name = w2.name '

    if thispage:
        sql += 'AND ( w1.text LIKE \'%%[wiki:%s %%\' ' % thispage

        CamelCase = re.compile('^[A-Z][^A-Z].*[A-Z][^A-Z]')
        if CamelCase.search( thispage ):
            sql += 'OR w1.text LIKE \'%%%s%%\' ' % thispage
        sql += ')'

    cursor.execute(sql)

    buf = StringIO()

    buf.write('Pages linking to %s:\n' % thispage)
    buf.write('<ul>')



    while 1:
        row = cursor.fetchone()
        if row == None:
            break
        if row[0] != thispage:
            buf.write('<li><a href="%s">' % env.href.wiki(row[0]))
            buf.write(row[0])
            buf.write('</a></li>\n')

    buf.write('</ul>')

    return buf.getvalue()
