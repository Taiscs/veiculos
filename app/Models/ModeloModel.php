<?php

namespace App\Models;

use CodeIgniter\Model;

class ModeloModel extends Model
{
    protected $table            = 'modelos';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nome', 'marca'];
    protected $useTimestamps     = false;
}