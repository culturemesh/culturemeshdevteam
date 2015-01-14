from lxml import etree
from copy import deepcopy

# get whole thing
print 'Parsing huge file...'
total = etree.parse('tests/fixtures/cmdata-total.xml')
print 'Done'

root = total.getroot()
namespace = root.nsmap

# start new thing
new_root = etree.Element('mysqldump', nsmap=namespace)

new_db = etree.Element('database')
new_db.set('name', 'culturp7_rehearsal')

new_root.append(new_db)

# other things
#print etree.tostring(new_root, pretty_print=True)

print 'Writing tables to individual files'
# now that we have this, we can get all the different tables
for table in root[0]:
	name = table.get('name')
	new_db.append(table)
	print 'On ' + name
	
	# create string
	file_str = etree.tostring(new_root, pretty_print=True)

	# write to file
	new_file = 'tests/fixtures/cmdata-' + name + '.xml'
	f = open(new_file, 'w')
	f.write(file_str)
	f.close()

	# clear new_db
	del new_db[:]

print 'Done'
