<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Models\Inventory;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransactionResource;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            // Create the transaction record
            $transaction = Transaction::create([
                'product_id' => $data['product_id'],
                'type'       => $data['type'], // 'sale' or 'restock'
                'quantity'   => $data['quantity'],
                'notes'      => $data['notes'],
            ]);

            // Adjust stock based on transaction type
            $inventory = Inventory::where('product_id', $data['product_id'])->firstOrFail();

            if ($data['type'] === 'sale') {
                $inventory->decrement('quantity', abs($data['quantity'])); // Reduce stock
            } else {
                $inventory->increment('quantity', abs($data['quantity'])); // Increase stock
            }
        });

        $this->getCreatedNotification()?->send();

        $this->redirect('/admin/transactions', true);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
