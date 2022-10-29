source cmd/0_internal/main.noexec.sh
if [ $? -ne 0 ]; then echo "error: try tun from parent dir."; exit 1; fi
###

printf "\n$BASH_SOURCE: started...\n\n"
#sudo apt install sshfs


echo "path_internal=$path_internal"
echo "path=$path"
echo "path_parent=$path_parent"

#dir ggg
#fn_stoponerror $BASH_SOURCE $LINENO $?

printf "\n$BASH_SOURCE: done.\n\n"

##template of cmd file
#source cmd/0_internal/main.noexec.sh
#if [ $? -ne 0 ]; then echo "error: try run from parent dir."; exit 1; fi
#printf "\n$BASH_SOURCE: started...\n\n"



#printf "\n$BASH_SOURCE: done.\n\n"
#fn_stoponerror $BASH_SOURCE $LINENO $?


