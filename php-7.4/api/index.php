<?php
$config = array(
    'url' => 'https://help.ssangyongsports.eu.org/help/api/tickets.json',
    'key' => '90CD0C0AC3B029EACF821F5A3E409D3B'
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = array(
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'subject' => $_POST['subject'],
        'message' => $_POST['message'],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'attachments' => array(),
    );

    function_exists('curl_version') or die('CURL support required');
    function_exists('json_encode') or die('JSON support required');

    set_time_limit(30);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['url']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'X-API-Key: ' . $config['key']));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        die('cURL error: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($code != 201) {
        die('Unable to create ticket: ' . $result);
    }

    $ticket_id = json_decode($result, true)['ticket_id'];
    header("Location: /thanks?id=" . urlencode($ticket_id));
    exit();
}
?>
