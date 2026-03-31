<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminalConfiguration extends Model
{
    protected $table = 'TERMINAL_CONFIGURATIONS';
    protected $fillable = ['operation_type', 'terminal_ids'];
    protected $casts = ['terminal_ids' => 'array'];

    public static function getTerminalIds($operationType)
    {
        $config = self::where('operation_type', $operationType)->first();
        return $config ? $config->terminal_ids : self::getDefaultIds($operationType);
    }

    private static function getDefaultIds($operationType)
    {
        return match($operationType) {
            'decantation' => [3],
            'toploading' => [1, 2],
            'fuel_dispensing' => [4, 5, 6],
            default => []
        };
    }
}