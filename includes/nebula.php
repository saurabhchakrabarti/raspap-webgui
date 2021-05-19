<?php

require_once 'includes/status_messages.php';
require_once 'includes/config.php';
require_once 'includes/wifi_functions.php';

getWifiInterface();

/**
 * Manage Nebula configuration
 */
function DisplayNebulaConfig()
{
    $status = new StatusMessages();
    if (!RASPI_MONITOR_ENABLED) {
        if (isset($_POST['SaveNebulaSettings'])) {
            $return = SaveNebulaConfig($status, $_FILES['customFile']);
        } elseif (isset($_POST['StartNebula'])) {
            $status->addMessage('Attempting to start Nebula', 'info');
            exec('sudo /bin/systemctl start nebula.service', $return);
            exec('sudo /bin/systemctl enable nebula.service', $return);
            foreach ($return as $line) {
                $status->addMessage($line, 'info');
            }
        } elseif (isset($_POST['StopNebula'])) {
            $status->addMessage('Attempting to stop Nebula', 'info');
            exec('sudo /bin/systemctl stop nebula.service', $return);
            exec('sudo /bin/systemctl disable nebula.service', $return);
            foreach ($return as $line) {
                $status->addMessage($line, 'info');
            }
        }
    }

    exec('pidof nebula | wc -l', $nebulastatus);
    exec('wget https://ipinfo.io/ip -qO -', $return);

    $serviceStatus = $nebulastatus[0] == 0 ? "down" : "up";

    echo renderTemplate(
        "nebula", compact(
            "status",
            "serviceStatus",
            "nebulastatus",
        )
    );
}

/**
 * Validates uploaded zip file
 * @param  object $status
 * @param  object $file
 * @return object $status
 */
function SaveNebulaConfig($status, $file)
{
    $tmp_nebulaclient = '/tmp/nebula.zip';

    try {
        // If undefined or multiple files, treat as invalid
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters');
        }

        // Parse returned errors
        switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('Nebula configuration file not sent');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit');
        default:
            throw new RuntimeException('Unknown errors');
        }

        // Validate extension
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($ext != 'zip') {
            throw new RuntimeException('Invalid file extension');
        }

        // Validate MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($file['tmp_name']),
            array(
                'zip' => 'application/zip'
            ),
            true
        )
        ) {
            throw new RuntimeException('Invalid file format');
        }

        // Validate filesize
        define('KB', 1024);
        if ($file['size'] > 64*KB) {
            throw new RuntimeException('File size limit exceeded');
        }

        // Use safe filename, save to /tmp
        if (!move_uploaded_file(
            $file['tmp_name'],
            sprintf(
                '/tmp/%s.%s',
                'nebula',
                $ext
            )
        )
        ) {
            throw new RuntimeException('Unable to move uploaded file');
        }
        // Copy tmp client config to /etc/openvpn/client
	//$status->addMessage("sudo cp $tmp_nebulaclient " . RASPI_NEBULA_CLIENT_CONFIG,'danger');
        //exec("sudo cp $tmp_nebulaclient " . RASPI_NEBULA_CLIENT_CONFIG, $return);
        //if ($return ==0) {
        $status->addMessage('Nebula configurations uploaded successfully', 'info');
        //} else {
        //    $status->addMessage('Unable to save Nebula Configurations', 'danger');
        //}

        return $status;
    } catch (RuntimeException $e) {
        $status->addMessage($e->getMessage(), 'danger');
        return $status;
    }
}
