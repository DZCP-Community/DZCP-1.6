<?php
function check_securelogin() {
    global $gump;
    $rules = [
        'user'    => 'required|alpha_numeric|max_len,100',
        'pwd'    => 'required|max_len,100'
    ];

    $filters = [
        'user' 	  => 'trim|sanitize_string',
        'pwd'	  => 'trim'
    ];

    if(config('securelogin')) {
        $rules['secure'] = 'required|alpha_numeric|max_len,16|min_len,2';
        $filters['secure'] = 'trim|sanitize_string';
    }

    $input = $gump->filter($_POST, $filters);
    if(!is_array($gump->validate($input, $rules))) {
        //permanent login
        $input['permanent'] = false;
        if(isset($_POST['permanent'])) {
            $input['permanent'] = true;
        }

        if(config('securelogin')) {
            $error = false;
            if(array_key_exists('sec_login_page', $_SESSION)) {
                if(!empty($input['secure']) &&
                    !empty($_SESSION['sec_login_page']) &&
                    strtolower($input['secure']) == strtolower($_SESSION['sec_login_page'])) {
                    unset($input['secure']);
                    return ['login' => true, 'input' => $input, 'msg' => ''];
                } else {
                    $error = true;
                }
            }

            if(array_key_exists('sec_login_menu', $_SESSION)) {
                if(!empty($input['secure']) &&
                    !empty($_SESSION['sec_login_menu']) &&
                    strtolower($input['secure']) == strtolower($_SESSION['sec_login_menu'])) {
                    unset($input['secure']);
                    return ['login' => true, 'input' => $input, 'msg' => ''];
                } else {
                    $error = true;
                }
            }

            if($error) {
                unset($input['secure'],$checked);
                return ['login' => false, 'input' => $input, 'msg' => _error_invalid_regcode];
            }
        }

        return ['login' => true, 'input' => $input, 'msg' => ''];
    }

    return ['login' => false, 'input' => $input, 'msg' => $gump->get_readable_errors(true)];
}