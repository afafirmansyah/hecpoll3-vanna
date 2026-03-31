<?php

namespace App\Traits;

trait HasTerminalAccess
{
    protected function applyTerminalAccessFilter($query, $terminalColumn = 'TerminalsID')
    {
        $userAccess = auth()->user()->userAccess;
        
        if ($userAccess && !empty($userAccess->terminal_ids)) {
            $query->whereIn($terminalColumn, $userAccess->terminal_ids);
        }
        
        return $query;
    }
    
    protected function hasTerminalAccess()
    {
        $userAccess = auth()->user()->userAccess;
        return !$userAccess || !empty($userAccess->terminal_ids);
    }
}