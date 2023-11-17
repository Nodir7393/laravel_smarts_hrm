<?php

namespace Modules\HHParse\Services;

use Modules\HHParse\Models\HhUser;
use phpseclib3\Crypt\AES;

class HHUserService
{
    const KEY = 'qwertyuiopasdfgh';
    const IV = '1234567812345678';
    const MODE = 'cbc';
    public function saveUser($name, $email, $password)
    {
        $user = new HhUser();
        if ($name) {$user->name = $name;}
        if ($email) {$user->email = $email;}
        if ($password) {$user->password = $this->encrypt($password);}
        $user->save();
    }

    public function encrypt($password)
    {
        $cipher = new AES(self::MODE);
        $cipher->setKey(self::KEY);
        $cipher->setIV(self::IV);
        $cipherText = $cipher->encrypt($password);
        return utf8_encode($cipherText);
    }

    public function decrypt($password)
    {
        $password = utf8_decode($password);
        $cipher = new AES(self::MODE);
        $cipher->setKey(self::KEY);
        $cipher->setIV(self::IV);
        $password = $cipher->decrypt($password);
        return $password;
    }
}
