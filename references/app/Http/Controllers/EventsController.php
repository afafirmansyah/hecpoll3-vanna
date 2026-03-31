<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    public function index(Request $request)
    {
        // Set default date range to last 3 days if not provided
        $fromDate = $request->get('from_date', now()->subDays(2)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query = DB::table('EVENTS')
            ->select('ID_EVENTS', 'EventDateTime', 'Source', 'Description', 'Details')
            ->whereBetween('EventDateTime', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->orderBy('EventDateTime', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('Source', 'LIKE', $searchTerm)
                  ->orWhere('Description', 'LIKE', $searchTerm)
                  ->orWhere('Details', 'LIKE', $searchTerm);
            });
        }

        $events = $query->paginate(20)->appends($request->query());

        return view('events', compact('events', 'fromDate', 'toDate'))->with('request', $request);
    }

    public function export(Request $request)
    {
        // Set default date range to last 3 days if not provided
        $fromDate = $request->get('from_date', now()->subDays(2)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query = DB::table('EVENTS')
            ->select('ID_EVENTS', 'EventDateTime', 'Source', 'Description', 'Details')
            ->whereBetween('EventDateTime', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->orderBy('EventDateTime', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('Source', 'LIKE', $searchTerm)
                  ->orWhere('Description', 'LIKE', $searchTerm)
                  ->orWhere('Details', 'LIKE', $searchTerm);
            });
        }

        $events = $query->get();

        $filename = 'events_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Event ID', 'Date Time', 'Source', 'Description', 'Details'];
        $sheet->fromArray($headers, null, 'A1');
        
        $row = 2;
        foreach ($events as $event) {
            $sheet->fromArray([
                $event->ID_EVENTS ?? '-',
                $event->EventDateTime ?? '-',
                $event->Source ?? '-',
                $event->Description ?? '-',
                $event->Details ?? '-'
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