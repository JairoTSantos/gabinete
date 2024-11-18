<?php

namespace Jairosantos\GabineteDigital\Core;

class GetJson {

    function getJson($url) {
        // Inicializa a sessão cURL
        $ch = curl_init();

        // Configura a URL e outras opções
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Define o header para aceitar JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        // Executa a requisição e armazena o resultado
        $response = curl_exec($ch);

        // Verifica se houve erro na requisição
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => $error];
        }

        // Fecha a sessão cURL
        curl_close($ch);

        // Decodifica o JSON
        $data = json_decode($response, true);

        // Verifica se a decodificação foi bem-sucedida
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        } else {
            return ['error' => json_last_error_msg()];
        }
    }
    
}
