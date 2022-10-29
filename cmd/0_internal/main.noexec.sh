path_internal="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
path="$(dirname "$path_internal")"
path_parent="$(dirname "$path")"

source "$path_internal/functions/fn_stoponerror.noexec.sh"
if [ $? -ne 0 ]; then exit 1; fi

source "$path_internal/functions/fn_filedelifexist.noexec.sh"
if [ $? -ne 0 ]; then exit 1; fi

source "$path_internal/functions/fn_filexml_element_value_update.noexec.sh"
if [ $? -ne 0 ]; then exit 1; fi

source "$path/cfg/cfg.sh"
fn_stoponerror $BASH_SOURCE $LINENO $?

source "$path/cfg/cfg.ignore.sh"
fn_stoponerror $BASH_SOURCE $LINENO $?

