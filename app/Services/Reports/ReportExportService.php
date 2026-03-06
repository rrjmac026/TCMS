<?php

namespace App\Services\Reports;

use FPDF;
use Illuminate\Http\Response;

/**
 * ReportExportService
 *
 * Handles CSV and PDF exports with subscription-plan enforcement.
 *
 * Plan limits:
 *   basic    → no export (redirect with error)
 *   standard → CSV only, up to 3,000 records/month
 *   premium  → CSV + Excel-compatible CSV + PDF, unlimited
 */
class ReportExportService
{
    /**
     * Monthly CSV export limit for Standard plan (records, not calls).
     */
    const STANDARD_MONTHLY_LIMIT = 3000;

    // -------------------------------------------------------------------------
    // Main entry point
    // -------------------------------------------------------------------------

    /**
     * @param  array   $data     Rows — array of associative arrays (same keys = columns)
     * @param  string  $filename Base filename without extension
     * @param  string  $format   'csv' | 'pdf'
     * @param  string  $title    Human-readable report title
     * @param  string  $plan     'basic' | 'standard' | 'premium'
     */
    public function export(
        array  $data,
        string $filename,
        string $format,
        string $title,
        string $plan
    ): Response|\Illuminate\Http\RedirectResponse {

        // ── Guard: basic plan cannot export ───────────────────────────────
        if ($plan === 'basic') {
            return redirect()->back()
                ->withErrors(['export' => 'Export is not available on the Basic plan. Please upgrade to Standard or Premium.']);
        }

        // ── Guard: standard plan can only do CSV ──────────────────────────
        if ($plan === 'standard' && $format === 'pdf') {
            return redirect()->back()
                ->withErrors(['export' => 'PDF export is a Premium feature. Please upgrade to access it.']);
        }

        // ── Guard: standard plan — 3,000 record/month soft cap ────────────
        if ($plan === 'standard' && count($data) > self::STANDARD_MONTHLY_LIMIT) {
            $data = array_slice($data, 0, self::STANDARD_MONTHLY_LIMIT);
            // We slice silently; the view should warn the user before exporting.
        }

        return match ($format) {
            'pdf'   => $this->exportPdf($data, $filename, $title),
            default => $this->exportCsv($data, $filename),
        };
    }

    // -------------------------------------------------------------------------
    // CSV
    // -------------------------------------------------------------------------

    protected function exportCsv(array $data, string $filename): Response
    {
        if (empty($data)) {
            $csv = "No data available\n";
        } else {
            $headers = array_keys($data[0]);
            $lines   = [];
            $lines[] = implode(',', array_map([$this, 'csvEscape'], $headers));

            foreach ($data as $row) {
                $lines[] = implode(',', array_map([$this, 'csvEscape'], array_values($row)));
            }

            $csv = implode("\n", $lines);
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }

    protected function csvEscape(mixed $value): string
    {
        $value = (string) $value;
        if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    // -------------------------------------------------------------------------
    // PDF (FPDF)
    // -------------------------------------------------------------------------

    protected function exportPdf(array $data, string $filename, string $title): Response
    {
        $pdf = new FPDF('L', 'mm', 'A4'); // Landscape A4
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $w = 277; // usable width in landscape A4

        // ── Header band ──────────────────────────────────────────────────
        $pdf->SetFillColor(26, 58, 107);
        $pdf->Rect(10, 10, $w, 14, 'F');

        $pdf->SetTextColor(201, 168, 76);
        $pdf->SetFont('Times', 'B', 13);
        $pdf->SetXY(10, 11);
        $pdf->Cell($w, 6, 'TESDA Training Center Management System', 0, 1, 'C');

        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Times', 'I', 8);
        $pdf->SetX(10);
        $pdf->Cell($w, 5, $title . '  —  Generated: ' . now()->format('F d, Y  H:i'), 0, 1, 'C');

        $pdf->Ln(5);

        // ── Table ─────────────────────────────────────────────────────────
        if (empty($data)) {
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetFont('Times', 'I', 11);
            $pdf->Cell($w, 10, 'No data available.', 0, 1, 'C');
        } else {
            $columns     = array_keys($data[0]);
            $colCount    = count($columns);
            $colW        = $w / $colCount;

            // Column header row
            $pdf->SetFillColor(26, 58, 107);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Times', 'B', 8);
            $pdf->SetDrawColor(180, 195, 220);
            $pdf->SetLineWidth(0.3);

            foreach ($columns as $col) {
                $pdf->Cell($colW, 8, $this->truncate($col, 22), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Data rows
            $pdf->SetFont('Times', '', 7.5);
            $altRow = false;
            foreach ($data as $row) {
                if ($altRow) {
                    $pdf->SetFillColor(240, 245, 255);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->SetTextColor(26, 58, 107);

                foreach (array_values($row) as $cell) {
                    $pdf->Cell($colW, 7, $this->truncate((string) $cell, 30), 1, 0, 'L', true);
                }
                $pdf->Ln();
                $altRow = !$altRow;
            }

            // Footer row — totals
            $pdf->SetFillColor(26, 58, 107);
            $pdf->SetTextColor(201, 168, 76);
            $pdf->SetFont('Times', 'B', 8);
            $pdf->Cell($colW * $colCount, 7, 'Total Records: ' . count($data), 1, 1, 'R', true);
        }

        // ── Page number footer ────────────────────────────────────────────
        $pdf->SetY(-12);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->SetFont('Times', 'I', 7);
        $pdf->Cell(0, 6, 'Page ' . $pdf->PageNo() . '  |  TCMS Export  |  Confidential', 0, 0, 'C');

        $output = $pdf->Output('S');

        return response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
        ]);
    }

    protected function truncate(string $value, int $max): string
    {
        return mb_strlen($value) > $max ? mb_substr($value, 0, $max - 1) . '…' : $value;
    }
}