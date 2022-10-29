source cmd/0_internal/main.noexec.sh
if [ $? -ne 0 ]; then echo "error: try tun from parent dir."; exit 1; fi
###

printf "\n$BASH_SOURCE: shh to $cfgi_remote_server...\n\n"

#sudo apt install sshpass

#tmp_file=$path0/ssh.sh.tmp
#echo $cfgi_remote_password > $tmp_file

#ssh $cfgi_remote_user@$cfgi_remote_server
#exit

#sshpass -f ssh.sh.tmp ssh $cfgi_remote_user@$cfgi_remote_server
sshpass -p $cfgi_remote_password ssh $cfgi_remote_user@$cfgi_remote_server

printf "\n$BASH_SOURCE: done.\n\n"
fn_stoponerror $BASH_SOURCE $LINENO $?
