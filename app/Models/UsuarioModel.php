<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nome', 'email', 'senha', 'tipo', 'perfil_id', 'status'];

    /**
     * Busca o usuário pelo e-mail com suas permissões
     */
    public function getUsuarioComPermissoes(string $email)
    {
        // Caso você use JOIN com tabela de perfis/permissões:
        return $this->select('usuarios.*, perfis.nome as perfil_nome')
                    ->join('perfis', 'perfis.id = usuarios.perfil_id', 'left')
                    ->where('usuarios.email', $email)
                    ->first();

        /* 
        // Caso NÃO use JOINs e queira apenas buscar o usuário simples por e-mail:
        return $this->where('email', $email)->first();
        */
    }
}