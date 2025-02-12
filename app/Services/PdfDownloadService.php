<?php

namespace App\Services;

use App\Jobs\GeneratePdfExportBulkActionJob;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Blade;
use Mpdf\Mpdf;

class PdfDownloadService
{
    protected static array $pdfConfig = [
        'default_font' => 'Cairo',
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font_size' => 12,
        'orientation' => 'P',
    ];

    /**
     * إنشاء ملف PDF من عرض Blade.
     */
    protected static function generatePdf(string $viewPath, array $data): Mpdf
    {
        $mpdf = new Mpdf(self::$pdfConfig);
        $html = Blade::render($viewPath, $data);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    /**
     * تحميل ملف PDF.
     */
    protected static function streamPdfDownload(string $viewPath, string $fileName, array $data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($viewPath, $data) {
            $mpdf = self::generatePdf($viewPath, $data);
            $mpdf->Output();
        }, "{$fileName}.pdf");
    }

    /**
     * عرض إشعار بخصوص تحميل ملفات PDF.
     */
    protected static function showDownloadNotification(string $type): void
    {
        $messages = [
            'many' => 'تم وضع تحميل ملف PDF في قائمة الانتظار. سيتم إعلامك عند الانتهاء.',
        ];

        Notification::make()
            ->icon('heroicon-o-arrow-down-tray')
            ->success()
            ->title('تحميل ملف PDF')
            ->body($messages[$type] ?? 'تم بدء تحميل ملف PDF الخاص بك.')
            ->send();
    }

    /**
     * إنشاء إجراء تصدير جماعي لعدة سجلات.
     */
    public static function createMultipleRecordsExport(
        string $resourceName,
        string $viewPath,
        string $fileName,
        string $icon = 'heroicon-o-arrow-down-tray',
        string $color = 'gray'
    ): BulkAction {
        return BulkAction::make('pdf_bulk_export')
            ->requiresConfirmation()
            ->modalHeading('تحميل (PDF)')
            ->modalIcon($icon)
            ->modalIconColor('primary')
            ->modalSubmitActionLabel('تأكيد')
            ->color($color)
            ->label('تحميل المحدد (PDF)')
            ->modalDescription("هل أنت متأكد أنك تريد تحميل التقارير المحددة؟")
            ->icon($icon)
            ->action(function ($records) use ($viewPath, $fileName) {
                GeneratePdfExportBulkActionJob::dispatch($records, $viewPath, $fileName, auth()->user());
                self::showDownloadNotification('many');
            });
    }
}
