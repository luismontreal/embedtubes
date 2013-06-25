#!/bin/bash
#feed vars coming from EmbedVideoImporter Module in Galago sites
url=$feed_url
fileName=$feed_file_name
partSize=$feed_part_size
prefix=$feed_mrss_prefix
#dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
path=$feed_path_mrss
pathToParts=$feed_path_parts
rssHeader='<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:bing="http://bing.com/schema/media/" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:phn="http://www.pornhub.com/"><channel>'
rssFooter='</channel></rss>'

#gets line of first item ending
function getItemLine {
	itemLine=`grep -m 1 -n '<\/item>' $1 | cut -f1 -d:`     
}

#arg 1 itemLine 
#arg 2 currentFile 
#arg 3 previousFile
function completePrevious {
	$(head -n $1 $2 >> $3)
	$(echo $rssFooter >> $3)
}

function adjustCurrent {
	$(sed -i "1,${itemLine}d" $1)
	$(sed -i "1i${rssHeader}" $1)
}

#Download the feed
$(wget -N ${url}${fileName} -P ${path})

# Clean from CTRL chars
$(LANG=C sed 's/[\x01-\x1f\x80-\xFF]//g' ${path}${fileName}  > ${path}${fileName}_tmp)

# Delete original
$(rm ${path}${fileName})

#Rename temp to final file
$(mv ${path}${fileName}_tmp ${path}${fileName})

#Delete old parts
$(rm -f ${pathToParts}*)

#Split file in parts
$(split -b ${partSize} -d ${path}${fileName} ${pathToParts}${prefix})

#Find number of parts
numberOfParts=`ls -l ${pathToParts}${prefix}* | grep ^- | wc -l`

for((i=1; i<numberOfParts; i++))
	do
		sufix=$(printf "%02d" $i)
		iPrevious=$((i-1)) 
		sufixPrevious=$(printf "%02d" $iPrevious)
		currentFile=$pathToParts$prefix$sufix
		previousFile=$pathToParts$prefix$sufixPrevious
	
		getItemLine $currentFile
		completePrevious $itemLine $currentFile $previousFile
		adjustCurrent $currentFile
	done

