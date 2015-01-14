#bin/bash
array=(*/)

for dir in "${array[@]}";
	do 
	mv "cmdata-${dir%?}.xml" "$dir"
done
