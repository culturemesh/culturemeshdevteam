#bash script
mkdir $(ls | sed 's/cmdata-\([a-zA-Z_]*\).xml/\1/')
