<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationsController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subDays(80)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query = DB::table('STOCKS')
            ->select('*')
            ->whereBetween('trans_date', [$fromDate, $toDate])
            ->orderBy('trans_date', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('ID', 'LIKE', $searchTerm);
        }

        $reconciliations = $query->paginate(20)->appends($request->query());

        return view('reconciliations', compact('reconciliations', 'fromDate', 'toDate'))->with('request', $request);
    }

    public function export(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subDays(80)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query = DB::table('STOCKS')
            ->select('*')
            ->whereBetween('trans_date', [$fromDate, $toDate])
            ->orderBy('trans_date', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('ID', 'LIKE', $searchTerm);
        }

        $reconciliations = $query->get();

        $filename = 'reconciliations_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Transactions Date Time', 'Opening Stocks', 'Total Received', 'Issued to BAR - VMS', 'Issued from FT', 'Balance Stocks'];
        $sheet->fromArray($headers, null, 'A1');
        
        $row = 2;
        foreach ($reconciliations as $reconciliation) {
            $sheet->fromArray([
                $reconciliation->trans_date ?? '-',
                $reconciliation->Opening ?? '-',
                $reconciliation->Inflow ?? '-',
                $reconciliation->tpbar ?? '-',
                $reconciliation->Dispensed ?? '-',
                $reconciliation->Closing ?? '-'
            ], null, 'A' . $row);
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}