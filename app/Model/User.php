<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;
use Src\Auth\IdentityInterface;

class User extends Model implements IdentityInterface
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'login',
        'password',
        'role',
    ];

    public static function createUser(array $data): self
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $data['password'])) {
            $data['password'] = md5($data['password']);
        }

        return self::create($data);
    }

    public function findIdentity(int $id)
    {
        return self::where('id', $id)->first();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function attemptIdentity(array $credentials)
    {
        return self::where([
            'login' => $credentials['login'],
            'password' => md5($credentials['password'])
        ])->first();
    }
}
