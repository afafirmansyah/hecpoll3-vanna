<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\HasTerminalAccess;

class DailyReportsController extends Controller
{
    use HasTerminalAccess;
    public function index(Request $request)
    {
        $fromDate = $request->get('fromDate', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('toDate', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get all vehicles for filter dropdown
        $allVehicles = DB::table('VEHICLES')
            ->where('Number', '!=', '000001')
            ->whereNotNull('Number')
            ->orderBy('Number')
            ->pluck('Number')
            ->toArray();

        // Get vehicles with pagination and filters
        $vehiclesQuery = DB::table('VEHICLES')
            ->where('Number', '!=', '000001')
            ->whereNotNull('Number');

        if ($request->filled('vehicles')) {
            $vehiclesQuery->whereIn('Number', $request->vehicles);
        }

        if ($request->filled('search')) {
            $vehiclesQuery->where('Number', 'LIKE', '%' . $request->search . '%');
        }
        
        $vehicles = $vehiclesQuery->orderBy('Number')->paginate(20)->appends($request->query());
        $vehicleNumbers = $vehicles->pluck('Number')->toArray();

        // Get date range - limit to 31 days max
        $dates = [];
        $current = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);
        $dayCount = 0;
        
        while ($current <= $end && $dayCount < 31) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
            $dayCount++;
        }

        // Get transaction data only for current page vehicles
        $transactions = [];
        if (!empty($vehicleNumbers) && $this->hasTerminalAccess()) {
            $query = DB::table('PAYMENTS as p')
                ->join('VEHICLES as v', 'v.ID_VEHICLES', '=', 'p.VehiclesID')
                ->select(
                    'v.Number as LicensePlate',
                    DB::raw('CAST(p.TransDateTime AS DATE) as transaction_date'),
                    DB::raw('SUM(p.TransQuantity) as total_quantity')
                )
                ->whereIn('v.Number', $vehicleNumbers)
                ->whereBetween(DB::raw('CAST(p.TransDateTime AS DATE)'), [$fromDate, $toDate])
                ->groupBy('v.Number', DB::raw('CAST(p.TransDateTime AS DATE)'));
            
            $this->applyTerminalAccessFilter($query, 'p.TerminalsID');
            $transactions = $query->get()
                ->keyBy(function($item) {
                    return $item->LicensePlate . '_' . $item->transaction_date;
                });
        }

        // Build heatmap data
        $heatmapData = [];
        foreach ($vehicleNumbers as $vehicle) {
            $heatmapData[$vehicle] = [];
            foreach ($dates as $date) {
                $key = $vehicle . '_' . $date;
                $heatmapData[$vehicle][$date] = isset($transactions[$key]) ? $transactions[$key]->total_quantity : 0;
            }
        }

        return view('daily-reports', compact('heatmapData', 'dates', 'fromDate', 'toDate', 'vehicles', 'allVehicles'));
    }

    public function export(Request $request)
    {
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $vehicles = $request->get('vehicles', []);
        $search = $request->get('search');

        // Get vehicles with filters
        $vehiclesQuery = DB::table('VEHICLES')
            ->where('Number', '!=', '000001')
            ->whereNotNull('Number');

        if (!empty($vehicles)) {
            $vehiclesQuery->whereIn('Number', $vehicles);
        }

        if ($search) {
            $vehiclesQuery->where('Number', 'LIKE', '%' . $search . '%');
        }
        
        $vehicleNumbers = $vehiclesQuery->orderBy('Number')->pluck('Number')->toArray();

        // Get date range
        $dates = [];
        $current = Carbon::parse($fromDate);
        $end = Carbon::parse($toDate);
        
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Get transaction data
        $transactions = [];
        if (!empty($vehicleNumbers) && $this->hasTerminalAccess()) {
            $query = DB::table('PAYMENTS as p')
                ->join('VEHICLES as v', 'v.ID_VEHICLES', '=', 'p.VehiclesID')
                ->select(
                    'v.Number as LicensePlate',
                    DB::raw('CAST(p.TransDateTime AS DATE) as transaction_date'),
                    DB::raw('SUM(p.TransQuantity) as total_quantity')
                )
                ->whereIn('v.Number', $vehicleNumbers)
                ->whereBetween(DB::raw('CAST(p.TransDateTime AS DATE)'), [$fromDate, $toDate])
                ->groupBy('v.Number', DB::raw('CAST(p.TransDateTime AS DATE)'));
            
            $this->applyTerminalAccessFilter($query, 'p.TerminalsID');
            $transactions = $query->get()
                ->keyBy(function($item) {
                    return $item->LicensePlate . '_' . $item->transaction_date;
                });
        }

        $filename = 'daily_reports_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header row
        $header = ['Vehicle'];
        foreach ($dates as $date) {
            $header[] = Carbon::parse($date)->format('d');
        }
        $sheet->fromArray($header, null, 'A1');
        
        // Data rows
        $row = 2;
        foreach ($vehicleNumbers as $vehicle) {
            $rowData = [$vehicle];
            foreach ($dates as $date) {
                $key = $vehicle . '_' . $date;
                $quantity = isset($transactions[$key]) ? $transactions[$key]->total_quantity : 0;
                $rowData[] = $quantity > 0 ? number_format($quantity, 0) : '';
            }
            $sheet->fromArray($rowData, null, 'A' . $row);
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