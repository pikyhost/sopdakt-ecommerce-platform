<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('user_id')
                ->label(__('User ID')),
            ExportColumn::make('category_id')
                ->label(__('Category ID')),
            ExportColumn::make('name')
                ->label(__('Name')),
            ExportColumn::make('sku')
                ->label(__('SKU')),
            ExportColumn::make('price')
                ->label(__('Price')),
            ExportColumn::make('cost')
                ->label(__('Cost')),
            ExportColumn::make('shipping_estimate_time')
                ->label(__('Shipping Estimate Time')),
            ExportColumn::make('description')
                ->label(__('Description')),
            ExportColumn::make('slug')
                ->label(__('Slug')),
            ExportColumn::make('meta_title')
                ->label(__('Meta Title')),
            ExportColumn::make('meta_description')
                ->label(__('Meta Description')),
            ExportColumn::make('after_discount_price')
                ->label(__('After Discount Price')),
            ExportColumn::make('discount_start')
                ->label(__('Discount Start')),
            ExportColumn::make('discount_end')
                ->label(__('Discount End')),
            ExportColumn::make('views')
                ->label(__('Views')),
            ExportColumn::make('sales')
                ->label(__('Sales')),
            ExportColumn::make('fake_average_rating')
                ->label(__('Average Rating')),
            ExportColumn::make('label_id')
                ->label(__('Label ID')),
            ExportColumn::make('summary')
                ->label(__('Summary')),
            ExportColumn::make('quantity')
                ->label(__('Quantity')),
            ExportColumn::make('custom_attributes')
                ->label(__('Custom Attributes')),
            ExportColumn::make('is_published')
                ->label(__('Is Published')),
            ExportColumn::make('is_featured')
                ->label(__('Is Featured')),
            ExportColumn::make('created_at')
                ->label(__('Created At')),
            ExportColumn::make('updated_at')
                ->label(__('Updated At')),
            ExportColumn::make('is_free_shipping')
                ->label(__('Is Free Shipping')),
            ExportColumn::make('order_items_count')
                ->counts('orderItems')
                ->label(__('Times Ordered')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
