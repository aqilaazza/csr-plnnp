<?php

namespace App\Exports;

use App\Models\Proposal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Color;

//use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ProposalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithColumnWidths

{
    private $rowNumber = 0;
    private $totalRows = 0;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->totalRows = $data->count();
    }

    // public function collection()
    // {
    //     $data = Proposal::with(['tipologi', 'tipeProses', 'namaPic'])->get();
    //     $this->totalRows = $data->count(); // untuk menentukan jumlah baris nanti
    //     return $data;
    // }

    public function collection()
    {
        return $this->data;
    }

    public function map($item): array
    {
        $this->rowNumber++;

        // Ambil subproses dari tipeProses
        $subprosesList = $item->tipeProses?->subProses ?? collect();

        $berkasOutput = $subprosesList->map(function ($subproses) use ($item) {
            $checked = $subproses->checklistForProposal($item->id)?->is_checked ?? false;
            return $subproses->nama_sub . ' ' . ($checked ? '✓' : '✗');
        })->implode("  "); // Spasi ganda antar item

         // Lokasi seperti di Monitoring
        $lokasi = $item->lokasi
            ?: collect([
                $item->kabupaten_nama,
                $item->kecamatan_nama,
                $item->kelurahan_nama,
            ])->filter()->implode(', ');

        return [
            $this->rowNumber,
            $item->judul,
            $item->instansi_pengajuan,
            $lokasi,
            $item->tanggal_disposisi
                ? Carbon::parse($item->tanggal_disposisi)->format('d-M-y')
                : '',
            (int) $item->nominal_pengajuan,
            $item->barang_pengajuan,
            $item->tipologi->kode ?? '-',
            $item->status,
            (int) $item->nominal_disetujui,
            $item->barang_disetujui,
            $item->namaPic->nama ?? '-',
            $item->tipeProses->nama ?? '-',
            $berkasOutput,
            $item->keterangan,
            $item->overdue
                ? Carbon::parse($item->overdue)->format('d-M-y')
                : '',
            ($item->progress ?? 0) . '%',
        ];
    }


    public function headings(): array
    {
        return [
            'No',
            'Judul',
            'Instansi',
            'Lokasi',
            'Tanggal',
            'Nominal Pengajuan',
            'Barang Pengajuan',
            'Tipologi',
            'Status',
            'Nominal Disetujui',
            'Barang Disetujui',
            'PIC',
            'Proses',
            'Berkas', // heading baru
            'Keterangan',
            'Overdue',
            'Progress (%)',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $totalRows = $this->totalRows + 1; // +1 untuk header
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('C2');

                // Format angka jadi Rupiah (kolom F dan J)
                $sheet->getStyle("F2:F{$totalRows}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');
                $sheet->getStyle("J2:J{$totalRows}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');

                // Loop untuk dropdown
                for ($row = 2; $row <= $totalRows; $row++) {
                    // Dropdown Status (I)
                    $statusValidation = $sheet->getCell("I{$row}")->getDataValidation();
                    $statusValidation->setType(DataValidation::TYPE_LIST);
                    $statusValidation->setAllowBlank(true);
                    $statusValidation->setShowDropDown(true);
                    $statusValidation->setFormula1('"Pending,Disetujui,Ditolak"');

                    // Dropdown Tipologi (H)
                    $tipologiValidation = $sheet->getCell("H{$row}")->getDataValidation();
                    $tipologiValidation->setType(DataValidation::TYPE_LIST);
                    $tipologiValidation->setAllowBlank(true);
                    $tipologiValidation->setShowDropDown(true);
                    $tipologiValidation->setFormula1('"CRTY,EMPW,CABD,INFRST,KLBS"');

                    // Dropdown PIC (L)
                    $picValidation = $sheet->getCell("L{$row}")->getDataValidation();
                    $picValidation->setType(DataValidation::TYPE_LIST);
                    $picValidation->setAllowBlank(true);
                    $picValidation->setShowDropDown(true);
                    $picValidation->setFormula1('"Ibnu,Dita,Wiji,Javas,Alief,Nanda"');

                    // Dropdown Proses (M)
                    $prosesValidation = $sheet->getCell("M{$row}")->getDataValidation();
                    $prosesValidation->setType(DataValidation::TYPE_LIST);
                    $prosesValidation->setAllowBlank(true);
                    $prosesValidation->setShowDropDown(true);
                    $prosesValidation->setFormula1('"Pembayaran Langsung,NPO,PO"');
                }

                // =======================
                // Tambahkan TOTAL di bawah tabel
                // =======================
                $totalRowIndex = $this->totalRows + 2;

                $totalBarangPengajuan = 0;
                $totalBarangDisetujui = 0;

                foreach ($this->data as $proposal) {

                    if (preg_match('/\d+/', $proposal->barang_pengajuan, $match)) {
                        $totalBarangPengajuan += (int) $match[0];
                    }

                    if (preg_match('/\d+/', $proposal->barang_disetujui, $match)) {
                        $totalBarangDisetujui += (int) $match[0];
                    }
                }

                // Label TOTAL di kolom Judul (B)
                $sheet->setCellValue("B{$totalRowIndex}", "TOTAL");

                // Rumus total kolom F (Nominal Pengajuan)
                $sheet->setCellValue(
                    "F{$totalRowIndex}",
                    "=SUM(F2:F" . ($totalRowIndex - 1) . ")"
                );

                // Rumus total kolom J (Nominal Disetujui)
                $sheet->setCellValue(
                    "J{$totalRowIndex}",
                    "=SUM(J2:J" . ($totalRowIndex - 1) . ")"
                );

                $sheet->setCellValue("G{$totalRowIndex}", $totalBarangPengajuan);

                $sheet->setCellValue("K{$totalRowIndex}", $totalBarangDisetujui);       

                // Style baris TOTAL (hijau + teks hitam bold)
                $sheet->getStyle("A{$totalRowIndex}:Q{$totalRowIndex}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '000000'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '78C841'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                // Format rupiah untuk total
                $sheet->getStyle("F{$totalRowIndex}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');

                $sheet->getStyle("J{$totalRowIndex}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');

                // =======================
                // Rekap Status
                // =======================

                $rekapStart = $totalRowIndex + 2;

                // Hitung jumlah status
                $totalDisetujui = $this->data->where('status', 'setuju')->count();
                $totalPending   = $this->data->where('status', 'pending')->count();
                $totalTolak   = $this->data->where('status', 'tolak')->count();

                $sheet->setCellValue("B{$rekapStart}", "REKAP STATUS");
                $sheet->setCellValue("C{$rekapStart}", "JUMLAH");

                // isi data
                $sheet->setCellValue("B".($rekapStart+1), "Disetujui");
                $sheet->setCellValue("C".($rekapStart+1), $totalDisetujui);

                $sheet->setCellValue("B".($rekapStart+2), "Pending");
                $sheet->setCellValue("C".($rekapStart+2), $totalPending);

                $sheet->setCellValue("B".($rekapStart+3), "Tolak");
                $sheet->setCellValue("C".($rekapStart+3), $totalTolak);

                $sheet->setCellValue("B".($rekapStart+4), "Total Proposal");
                $sheet->setCellValue("C".($rekapStart+4), $this->totalRows);

                // Header rekap
                $sheet->getStyle("B{$rekapStart}:C{$rekapStart}")
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '000000'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '78C841'],
                        ],
                    ]);

                // Border
                $sheet->getStyle("B{$rekapStart}:C".($rekapStart+4))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);

                // Bold baris total proposal
                $sheet->getStyle("B".($rekapStart+4).":C".($rekapStart+4))
                    ->getFont()->setBold(true);
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 35,   // Judul
            'C' => 30,   // Instansi
            'D' => 30,   // Lokasi
            'E' => 15,   // Tanggal
            'F' => 20,   // Nominal Pengajuan
            'G' => 20,   // Barang Pengajuan
            'H' => 10,   // Tipologi
            'I' => 15,   // Status
            'J' => 20,   // Nominal Disetujui
            'K' => 20,   // Barang Disetujui
            'L' => 12,   // PIC
            'M' => 15,   // Proses
            'N' => 15,   // Kolom Berkas (misalnya)
            'Q' => 5,   // Kolom Berkas (misalnya)
            // tambahkan sesuai kebutuhan
        ];
    }


    public function styles(Worksheet $sheet)
    {
        $totalDataRows = $this->totalRows + 1; // +1 untuk heading
        $range = 'A1:Q' . $totalDataRows;

        // Style untuk heading saja
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // teks hitam
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '78C841'], // hijau
            ],
        ]);

        // Style border untuk seluruh data termasuk heading
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // border hitam
                ],
            ],
        ]);
    }
}
