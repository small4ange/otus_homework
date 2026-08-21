<?php

class VerifyEmail {
    /**
     * Чтение эмейлов из файла
     * @param $filename
     * @return array
     * @throws Exception
     */
    public function readEmailsFromFile($filename) : array
    {
        if(!file_exists($filename)){
            throw new Exception("Файл $filename не найден");
        }
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $emails = array_filter(array_map('trim', $lines));
        return array_values($emails);
    }

    /**
     * Проверка эмейла через регулярное выражение
     * @param string $email
     * @return bool
     */
    public function verifyEmailWithRegexp(string $email) : bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Проверка эмейла на существование домена и его пинг
     * @param string $email
     * @return bool
     */
    public function validateDomain(string $email) : bool
    {
        $parts = explode('@', $email);
        $domain = $parts[1];
        if (!checkdnsrr($domain, 'A')) {
            return false;
        }
        if(!checkdnsrr($domain, 'MX')) {
            return false;
        }
        return true;
    }
}

try {
    $filename = 'emails.txt';
    $verifyEmail = new VerifyEmail();
    $emails = $verifyEmail->readEmailsFromFile(ROOT_FOLDER . "/". $filename);
    foreach ($emails as $email){
        $validDomain = $verifyEmail->validateDomain($email);
        $validEmail = $verifyEmail->verifyEmailWithRegexp($email);
        if (!$validDomain || !$validEmail) {
            echo "Эмейл $email невалиден";
        } else if ($validEmail && $validDomain) {
            echo "Эмейл $email валиден";
        }
    }
} catch(Exception $e) {
    echo "Ошибка: ". $e->getMessage() . "\n";
}



