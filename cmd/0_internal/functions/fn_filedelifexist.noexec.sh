fn_filedelifexist ()
{
	file=$1
	if [ -e $file ]
	then
		rm $file
	fi
}
