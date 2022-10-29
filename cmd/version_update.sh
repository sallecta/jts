source cmd/0_internal/main.noexec.sh
if [ $? -ne 0 ]; then echo "error: try run from parent dir."; exit 1; fi
###

printf "\n$BASH_SOURCE: Updating source $cfg_app_name to version $cfg_app_version ...\n\n"

ls "$cfg_path_parent/Sources/$cfg_app_dir_src_name" > /dev/null >&1
fn_stoponerror $BASH_SOURCE $LINENO $?

xml_file_name="templateDetails.xml"
xml_file_path="$cfg_path_parent/Sources/$cfg_app_dir_src_name/$xml_file_name"

cd "$cfg_path_parent"
fn_stoponerror $BASH_SOURCE $LINENO $?

fn_filexml_element_value_update  "$xml_file_path" "/extension/version" "$cfg_app_version"
fn_stoponerror $BASH_SOURCE $LINENO $?

datexmlval=$(LANG=C; date +"%B %Y")
fn_filexml_element_value_update  "$xml_file_path" "/extension/creationDate" "$datexmlval"

echo " file [$xml_file_path] updated."

printf "\n$BASH_SOURCE: done.\n\n"
fn_stoponerror $BASH_SOURCE $LINENO $?


