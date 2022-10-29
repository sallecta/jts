source cmd/0_internal/main.noexec.sh
if [ $? -ne 0 ]; then echo "error: try run from parent dir."; exit 1; fi
###

printf "\n$BASH_SOURCE: Making release of $cfg_app_name version $cfg_app_version ...\n\n"

release_file_name="tpl_$cfg_app_name""_""$cfg_app_version"".ignore.zip"
release_file_path="$cfg_path_parent/Releases/$release_file_name"

cd "$cfg_path_parent"
fn_stoponerror $BASH_SOURCE $LINENO $?

$cfg_path_cmd/version_update.sh
fn_stoponerror $BASH_SOURCE $LINENO $?

fn_filedelifexist "$release_file_path"
fn_stoponerror $BASH_SOURCE $LINENO $?

cd "$cfg_path_parent/Sources"
fn_stoponerror $BASH_SOURCE $LINENO $?

zip -r -FS -q "$release_file_path" "$cfg_app_dir_src_name"
fn_stoponerror $BASH_SOURCE $LINENO $?

echo " file [$release_file_path] created."

cd "$cfg_path_parent"
fn_stoponerror $BASH_SOURCE $LINENO $?

printf "\n$BASH_SOURCE: done.\n\n"
fn_stoponerror $BASH_SOURCE $LINENO $?


