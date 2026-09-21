<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'          => '',
        'hostname'     => 'mysql-3e7d387f-taiscampos2118-41ca.b.aivencloud.com',
        'username'     => 'avnadmin',
        'password'     => '',
        'database'     => 'locadora_veiculos',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',

        'encrypt'      => [
            'ssl_key'    => null,
            'ssl_cert'   => null,
            'ssl_ca'     => '/etc/secrets/aiven-ca.pem',
            'ssl_capath' => null,
            'ssl_cipher' => null,
            'ssl_verify' => true,
        ],

        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 10472,
        'numberNative' => false,
        'foundRows'    => false,

        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => true,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,

        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
            return;
        }

        // A senha permanece protegida na variável de ambiente da Render.
        $password = getenv('database.default.password');

        if ($password !== false && $password !== '') {
            $this->default['password'] = $password;
        }
    }
}
