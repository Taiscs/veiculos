if (password_verify($senha, $usuario['senha'])) {
    $db = \Config\Database::connect();

    // Busca as chaves das telas permitidas para o perfil do usuário
    $permissoesQuery = $db->table('perfil_permissoes pp')
                         ->select('t.chave')
                         ->join('telas t', 't.id = pp.tela_id')
                         ->where('pp.perfil_id', $usuario['perfil_id'])
                         ->get()
                         ->getResultArray();

    $permissoes = array_column($permissoesQuery, 'chave');

    $sessionData = [
        'id'         => $usuario['id'],
        'nome'       => $usuario['nome'],
        'email'      => $usuario['email'],
        'tipo'       => $usuario['tipo'] ?? 'usuario',
        'perfil_id'  => $usuario['perfil_id'] ?? null,
        'permissoes' => $permissoes, // Ex: ['cad_carro', 'cad_modelo', 'alugueis', ...]
        'isLoggedIn' => true,
    ];

    $session->set($sessionData);

    return redirect()->to(site_url('bi'));
}
