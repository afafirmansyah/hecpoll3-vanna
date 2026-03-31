<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTerminalAccess;

class TransactionsController extends Controller
{
    use HasTerminalAccess;
    public function index(Request $request)
    {
        // Apply user access control
        $userAccess = auth()->user()->userAccess;
        if ($userAccess && empty($userAccess->terminal_ids)) {
            return view('transactions', ['transactions' => collect()->paginate(20), 'terminals' => [], 'customers' => [], 'vehicleGroups' => [], 'costCenters' => [], 'licensePlates' => [], 'fromDate' => '', 'toDate' => ''])->with('request', $request);
        }
        
        // Set default date range to last 7 days if not provided
        $fromDate = $request->get('from_date', now()->subDays(6)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query = DB::table('PAYMENTS as p')
            ->leftJoin('TERMINALS as t', 't.ID_TERMINALS', '=', 'p.TerminalsID')
            ->leftJoin('VEHICLES as v', 'v.ID_VEHICLES', '=', 'p.VehiclesID')
            ->leftJoin('VEHICLEGROUPS as vg', 'vg.ID_VEHICLEGROUPS', '=', 'v.vehiclegroupsID')
            ->leftJoin('COSTCENTERS as cc', 'cc.ID_COSTCENTERS', '=', 'v.CostCenterID')
            ->select(
                'p.TransactionsID',
                'p.TransDateTime',
                't.Description as TerminalName',
                'p.CustomerName',
                'p.CardPAN',
                'v.Number as LicensePlate',
                'vg.Description as Group',
                'cc.Description as CostCenter',
                'p.AdditionalEntry',
                'p.Mileage',
                'p.TransQuantity'
            );

        // Always apply date filter (default to current month)
        $query->whereBetween('p.TransDateTime', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        
        // Apply terminal access control
        if ($userAccess && !empty($userAccess->terminal_ids)) {
            $query->whereIn('p.TerminalsID', $userAccess->terminal_ids);
        }

        if ($request->filled('terminals')) {
            $query->whereIn('t.ID_TERMINALS', $request->terminals);
        }

        if ($request->filled('customers')) {
            $query->whereIn('p.CustomerName', $request->customers);
        }

        if ($request->filled('groups')) {
            $query->whereIn('vg.ID_VEHICLEGROUPS', $request->groups);
        }

        if ($request->filled('cost_centers')) {
            $query->whereIn('cc.ID_COSTCENTERS', $request->cost_centers);
        }

        if ($request->filled('license_plates')) {
            $query->whereIn('v.Number', $request->license_plates);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('p.TransactionsID', 'LIKE', $searchTerm)
                  ->orWhere('t.Description', 'LIKE', $searchTerm)
                  ->orWhere('p.CustomerName', 'LIKE', $searchTerm)
                  ->orWhere('p.CardPAN', 'LIKE', $searchTerm)
                  ->orWhere('v.Number', 'LIKE', $searchTerm)
                  ->orWhere('vg.Description', 'LIKE', $searchTerm)
                  ->orWhere('cc.Description', 'LIKE', $searchTerm);
            });
        }

        $query->orderBy('p.ID_PAYMENTS', 'desc');
        $transactions = $query->paginate(20)->appends($request->query());

        $terminalsQuery = DB::table('TERMINALS')
            ->select('ID_TERMINALS', 'Description')
            ->whereNotNull('Description');
        
        // Apply terminal access control for filter dropdown
        if ($userAccess && !empty($userAccess->terminal_ids)) {
            $terminalsQuery->whereIn('ID_TERMINALS', $userAccess->terminal_ids);
        }
        
        $terminals = $terminalsQuery->orderBy('Description')->get();

        $customers = DB::table('PAYMENTS')
            ->select('CustomerName')
            ->whereNotNull('CustomerName')
            ->where('CustomerName', '!=', 'BISM')
            ->where('CustomerName', '!=', 'Default Customer')
            ->distinct()
            ->orderBy('CustomerName')
            ->get();

        $vehicleGroups = DB::table('VEHICLEGROUPS as vg')
            ->select('vg.ID_VEHICLEGROUPS', 'vg.Description')
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('VEHICLES as v')
                      ->whereColumn('v.VehicleGroupsID', 'vg.ID_VEHICLEGROUPS');
            })
            ->orderBy('vg.Description')
            ->get();

        $costCenters = DB::table('CostCenters as cc')
            ->select('cc.ID_COSTCENTERS', 'cc.Description')
            ->whereNotNull('cc.Description')
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('VEHICLES as v')
                      ->whereColumn('v.CostCenterID', 'cc.ID_COSTCENTERS');
            })
            ->orderBy('cc.Description')
            ->get();

        $licensePlates = DB::table('VEHICLES')
            ->select('Number as LicensePlate')
            ->whereNotNull('Number')
            ->where('Number', '!=', '')
            ->where('Number', '!=', '000001')
            ->distinct()
            ->orderBy('Number')
            ->get();

        return view('transactions', compact('transactions', 'terminals', 'customers', 'vehicleGroups', 'costCenters', 'licensePlates', 'fromDate', 'toDate'))->with('request', $request);
    }

    public function export(Request $request)
    {
        // Apply user access control
        $userAccess = auth()->user()->userAccess;
        if ($userAccess && empty($userAccess->terminal_ids)) {
            return response()->json(['error' => 'No access to any terminals'], 403);
        }
        
        // Set default date range to last 7 days if not provided
        $fromDate = $request->get('from_date', now()->subDays(6)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        $query = DB::table('PAYMENTS as p')
            ->leftJoin('TERMINALS as t', 't.ID_TERMINALS', '=', 'p.TerminalsID')
            ->leftJoin('VEHICLES as v', 'v.ID_VEHICLES', '=', 'p.VehiclesID')
            ->leftJoin('VEHICLEGROUPS as vg', 'vg.ID_VEHICLEGROUPS', '=', 'v.vehiclegroupsID')
            ->leftJoin('COSTCENTERS as cc', 'cc.ID_COSTCENTERS', '=', 'v.CostCenterID')
            ->select(
                'p.TransactionsID',
                'p.TransDateTime',
                't.Description as TerminalName',
                'p.CustomerName',
                'p.CardPAN',
                'v.Number as LicensePlate',
                'vg.Description as Group',
                'cc.Description as CostCenter',
                'p.AdditionalEntry',
                'p.Mileage',
                'p.TransQuantity'
            );

        // Always apply date filter (default to current month)
        $query->whereBetween('p.TransDateTime', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        
        // Apply terminal access control
        if ($userAccess && !empty($userAccess->terminal_ids)) {
            $query->whereIn('p.TerminalsID', $userAccess->terminal_ids);
        }

        if ($request->filled('terminals')) {
            $query->whereIn('t.ID_TERMINALS', $request->terminals);
        }

        if ($request->filled('customers')) {
            $query->whereIn('p.CustomerName', $request->customers);
        }

        if ($request->filled('groups')) {
            $query->whereIn('vg.ID_VEHICLEGROUPS', $request->groups);
        }

        if ($request->filled('cost_centers')) {
            $query->whereIn('cc.ID_COSTCENTERS', $request->cost_centers);
        }

        if ($request->filled('license_plates')) {
            $query->whereIn('v.Number', $request->license_plates);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('p.TransactionsID', 'LIKE', $searchTerm)
                  ->orWhere('t.Description', 'LIKE', $searchTerm)
                  ->orWhere('p.CustomerName', 'LIKE', $searchTerm)
                  ->orWhere('p.CardPAN', 'LIKE', $searchTerm)
                  ->orWhere('v.Number', 'LIKE', $searchTerm)
                  ->orWhere('vg.Description', 'LIKE', $searchTerm)
                  ->orWhere('cc.Description', 'LIKE', $searchTerm);
            });
        }

        $query->orderBy('p.ID_PAYMENTS', 'desc');
        $transactions = $query->get();

        $filename = 'transactions_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Trans ID', 'Date Time', 'Terminal', 'Customer', 'Card PAN', 'License Plate', 'Group', 'Cost Center', 'Additional Entry', 'Mileage', 'Quantity'];
        $sheet->fromArray($headers, null, 'A1');
        
        $row = 2;
        foreach ($transactions as $transaction) {
            $sheet->fromArray([
                $transaction->TransactionsID ?? '-',
                $transaction->TransDateTime ?? '-',
                $transaction->TerminalName ?? '-',
                $transaction->CustomerName ?? '-',
                $transaction->CardPAN ?? '-',
                $transaction->LicensePlate ?? '-',
                $transaction->Group ?? '-',
                $transaction->CostCenter ?? '-',
                $transaction->AdditionalEntry ?? '-',
                $transaction->Mileage ?? '-',
                $transaction->TransQuantity ? number_format($transaction->TransQuantity, 2) : '-'
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

    public function updateMileage(Request $request)
    {
        try {
            $updated = DB::table('PAYMENTS')
                ->where('TransactionsID', $request->id)
                ->update(['Mileage' => $request->mileage]);

            return response()->json(['success' => $updated > 0]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}