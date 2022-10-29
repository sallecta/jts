source cmd/0_internal/main.noexec.sh
if [ $? -ne 0 ]; then echo "error: try run from parent dir."; exit 1; fi
###

printf "\n$BASH_SOURCE: mounting [$cfgi_remote_server:$cfgi_remote_dir] to [$cfgi_local_dir_mount]...\n\n"
#sudo apt install sshfs

mkdir -p "$cfgi_local_dir_mount"
fn_stoponerror $BASH_SOURCE $LINENO $?

#sshfs -o password_stdin $cfgi_remote_user@$cfgi_remote_server:$cfgi_remote_dir $cfgi_local_dir_mount <<< $(cat $path0/pathwordfile1)

sshfs -o password_stdin $cfgi_remote_user@$cfgi_remote_server:$cfgi_remote_dir $cfgi_local_dir_mount <<< $(echo $cfgi_remote_password)
fn_stoponerror $BASH_SOURCE $LINENO $?

printf "\n$BASH_SOURCE: done\n\n"
