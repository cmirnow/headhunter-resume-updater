<?php
$client_id = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$client_secret = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

if (empty($_GET['code'])) {
    echo '<p><a href="https://hh.ru/oauth/authorize?response_type=code&client_id=' . $client_id . '">Обновить резюме</a></p>';
} else {
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'https://hh.ru/oauth/token');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=authorization_code&client_id=" . $client_id . "&client_secret=" . $client_secret . "&code=" . $_GET['code']);
        $out = curl_exec($curl);
        curl_close($curl);
    }
    $token_json = json_decode($out);
    $headers    = array(
        'Authorization: Bearer ' . $token_json->access_token,
        'User-Agent: Masterpro'
    );
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'https://api.hh.ru/resumes/mine');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $out = curl_exec($curl);
        curl_close($curl);
    }
    $resumes = json_decode($out);
    echo '<strong>Статус обновлений резюме:</strong></br>';
    foreach ($resumes->{'items'} as $item) {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, 'https://api.hh.ru/resumes/' . $item->id . '/publish');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $out = curl_exec($curl);
            curl_close($curl);
            if(empty($out)) {
            echo 'Резюме ' . '"' . $item->title . '"' . ' успешно обновлено.</br>';
            } else {
            echo $out . '</br>';
            echo 'Резюме ' . '"' . $item->title . '"' . ' было обновлено ' .($item->updated . '</br>');
        }
    }
}
}
