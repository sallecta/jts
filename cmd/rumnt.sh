source cmd/0_internal/main.noexec.sh
if [ $? -ne 0 ]; then echo "error: try tun from parent dir."; exit 1; fi
###

printf "\n$BASH_SOURCE: unmounting [$cfgi_remote_server:$cfgi_remote_dir] from [$cfgi_local_dir_mount]...\n\n"

umount $cfgi_local_dir_mount
fn_stoponerror $BASH_SOURCE $LINENO $?

rm -r $cfgi_local_dir_mount
fn_stoponerror $BASH_SOURCE $LINENO $?

printf "\n$BASH_SOURCE: done\n\n"
