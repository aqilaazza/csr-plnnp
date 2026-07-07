<?php

namespace App\Services;

use App\Models\Kelayakan;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class KelayakanPdfService
{
    public function generate(Kelayakan $kelayakan)
    {
        // Generate ulang PDF berdasarkan data terbaru
        $pdf = Pdf::loadView('pdf.kelayakan', ['data' => $kelayakan]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->render();
        
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $fontBold = $fontMetrics->getFont('Arial', 'bold');
            $fontNormal = $fontMetrics->getFont('Arial', 'normal');
            $size = 6.5;

            $x1 = 396; // posisi awal "Halaman:"
            $x2 = 426; // posisi "2 dari 3"
            $y = 129;

            $canvas->text($x1, $y, "Halaman:", $fontBold, $size);
            $canvas->text($x1 + 0.2, $y, "Halaman:", $fontBold, $size);
            $canvas->text($x2, $y, "$pageNumber dari $pageCount", $fontNormal, $size);

            $canvas->line(50, 770, 550, 770, [0, 0, 0], 0.5);
        });

        $pdfName = 'kelayakan_' . $kelayakan->id . '.pdf';

        // Simpan PDF baru ke storage
        Storage::put('public/kelayakan/' . $pdfName, $pdf->output());

        // Update path file PDF di database
        $kelayakan->update(['file_pdf' => 'kelayakan/' . $pdfName]);
    }
}