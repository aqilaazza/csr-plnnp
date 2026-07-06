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

        $prioritas = $kelayakan->prioritas;
        $dampak = $kelayakan->dampak;

        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->render();

        // Tambahkan page_script seperti di store() untuk matrik
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($prioritas, $dampak) {
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

            $key = "{$prioritas}-{$dampak}";

            if ($pageNumber == 2) {
                $koordinatMatriks = [
                    '1-1' => [205, 186],
                    '1-2' => [265, 186],
                    '1-3' => [326, 186],
                    '1-4' => [390, 186],
                    '1-5' => [466, 186],

                    '2-1' => [205,206],
                    '2-2' => [268, 206],
                    '2-3' => [328, 206],
                    '2-4' => [390, 206],
                    '2-5' => [466, 206],

                    '3-1' => [205,226],
                    '3-2' => [269, 226],
                    '3-3' => [328, 226],
                    '3-4' => [395, 226],
                    '3-5' => [461, 226],

                    '4-1' => [205, 246],
                    '4-2' => [265, 246],
                    '4-3' => [326, 246],
                    '4-4' => [389, 246],
                    '4-5' => [460, 246],

                    '5-1' => [205, 266],
                    '5-2' => [265, 266],
                    '5-3' => [326, 266],
                    '5-4' => [393, 266],
                    '5-5' => [462, 266],
                ];
            }

            if (isset($koordinatMatriks[$key])) {
                [$cx, $cy] = $koordinatMatriks[$key];
                $radiusX = 20;  // radius horizontal (lebar)
                $radiusY = 7;  // radius vertikal (tinggi)
                $segments = 100; // banyak garis untuk elips

                $angleStep = 2 * pi() / $segments;

                $prevX = $cx + $radiusX * cos(0);
                $prevY = $cy + $radiusY * sin(0);

                for ($i = 1; $i <= $segments; $i++) {
                    $angle = $i * $angleStep;
                    $x = $cx + $radiusX * cos($angle);
                    $y = $cy + $radiusY * sin($angle);

                    // Garis hitam dengan ketebalan 1.5
                    $canvas->line($prevX, $prevY, $x, $y, [0, 0, 0], 1.5);

                    $prevX = $x;
                    $prevY = $y;
                }
            } else {
                logger()->warning("Koordinat tidak ditemukan untuk key {$key}");
            }
        });

        $pdfName = 'kelayakan_' . $kelayakan->id . '.pdf';

        // Simpan PDF baru ke storage
        Storage::put('public/kelayakan/' . $pdfName, $pdf->output());

        // Update path file PDF di database
        $kelayakan->update(['file_pdf' => 'kelayakan/' . $pdfName]);
    }
}