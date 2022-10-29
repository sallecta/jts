fn_filexml_element_value_update ()
{
	file=$1
	xmlpath=$2
	value=$3
	
	#sudo apt install xmlstarlet
	xmlstarlet edit --inplace -P --update  "$xmlpath" --value "$value" $file
	
}
