<?php

if (function_exists('curl_init')) {
    echo "cURL está ativado!";
} else {
    echo "ERRO: cURL não está ativado!";
}
