<?php
namespace App\Enum;

enum RoleEnum: string {
    case ADMIN ='admin';
    case USER = 'user';
    case CHECKER = 'checker';
    case SIGNER = 'signer';

    public function label() : string{
        return match($this){
            self::ADMIN => 'Administrator',
            self::USER => 'User Biasa',
            self::CHECKER => 'Approver Pertama',
            self::SIGNER => 'Approver Kedua',
        };
    }
}
