<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MakeCostZeroBulkAction
{
    public function execute(Collection $records)
    {
        DB::transaction(function () use ($records) {
            $records->each(fn ($record) => $record->update(['cost' => 0]));
        });
    }
}
