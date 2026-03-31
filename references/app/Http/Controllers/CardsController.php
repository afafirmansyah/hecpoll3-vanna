<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardsController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('VEHICLES as v')
            ->join('VehicleGroups as g', 'v.VehicleGroupsID', '=', 'g.ID_VEHICLEGROUPS')
            ->join('Cards as c', 'c.VehiclesID', '=', 'v.ID_VEHICLES')
            ->leftJoin('CUSTOMERS as cus', 'cus.ID_CUSTOMERS', '=', 'c.CustomersID')
            ->leftJoin('CostCenters as cc', 'cc.ID_COSTCENTERS', '=', 'v.CostCenterID')
            ->leftJoin('CardAuthorizationKeys as cak', 'cak.ID_CARDAUTHORIZATIONKEYS', '=', 'c.AuthKeysID')
            ->select(
                'c.ID_CARDS',
                'c.PAN',
                'v.Number as LicensePlate',
                'cus.lastname as CustomerName',
                'g.Description as GroupName',
                'cc.Description as CostCenterName',
                'cak.Description as AuthorizationKeyName',
                'c.Additional1'
            )
            ->whereNotNull('v.Number')
            ->whereNotNull('v.LicensePlate')
            ->whereNotNull('g.Description')
            ->whereNotNull('c.PAN');

        // Apply filters
        if ($request->filled('customers')) {
            $query->whereIn('cus.ID_CUSTOMERS', $request->customers);
        }

        if ($request->filled('groups')) {
            $query->whereIn('g.ID_VEHICLEGROUPS', $request->groups);
        }

        if ($request->filled('cost_centers')) {
            $query->whereIn('cc.ID_COSTCENTERS', $request->cost_centers);
        }

        if ($request->filled('card_statuses')) {
            $query->whereIn(DB::raw('LOWER(cak.Description)'), array_map('strtolower', $request->card_statuses));
        }

        if ($request->filled('additional_entries')) {
            $query->whereIn('c.Additional1', $request->additional_entries);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('c.ID_CARDS', 'LIKE', $searchTerm)
                  ->orWhere('c.PAN', 'LIKE', $searchTerm)
                  ->orWhere('v.Number', 'LIKE', $searchTerm)
                  ->orWhere('cus.lastname', 'LIKE', $searchTerm)
                  ->orWhere('g.Description', 'LIKE', $searchTerm)
                  ->orWhere('cc.Description', 'LIKE', $searchTerm)
                  ->orWhere('cak.Description', 'LIKE', $searchTerm);
            });
        }

        $query->orderBy('cus.lastname', 'asc');

        $cards = $query->paginate(20);

        // Get filter options - exclude 'default customer' and only show linked groups/cost centers
        $customers = DB::table('CUSTOMERS')
            ->select('ID_CUSTOMERS', 'lastname')
            ->whereNotNull('lastname')
            ->where('lastname', '!=', 'default customer')
            ->orderBy('lastname')
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

        return view('cards', compact('cards', 'customers', 'vehicleGroups', 'costCenters'));
    }

    public function export(Request $request)
    {
        $query = DB::table('VEHICLES as v')
            ->join('VehicleGroups as g', 'v.VehicleGroupsID', '=', 'g.ID_VEHICLEGROUPS')
            ->join('Cards as c', 'c.VehiclesID', '=', 'v.ID_VEHICLES')
            ->leftJoin('CUSTOMERS as cus', 'cus.ID_CUSTOMERS', '=', 'c.CustomersID')
            ->leftJoin('CostCenters as cc', 'cc.ID_COSTCENTERS', '=', 'v.CostCenterID')
            ->leftJoin('CardAuthorizationKeys as cak', 'cak.ID_CARDAUTHORIZATIONKEYS', '=', 'c.AuthKeysID')
            ->select(
                'c.ID_CARDS',
                'c.PAN',
                'v.Number as LicensePlate',
                'cus.lastname as CustomerName',
                'g.Description as GroupName',
                'cc.Description as CostCenterName',
                'cak.Description as AuthorizationKeyName',
                'c.Additional1'
            )
            ->whereNotNull('v.Number')
            ->whereNotNull('v.LicensePlate')
            ->whereNotNull('g.Description')
            ->whereNotNull('c.PAN');

        // Apply same filters as index method
        if ($request->filled('customers')) {
            $query->whereIn('cus.ID_CUSTOMERS', $request->customers);
        }

        if ($request->filled('groups')) {
            $query->whereIn('g.ID_VEHICLEGROUPS', $request->groups);
        }

        if ($request->filled('cost_centers')) {
            $query->whereIn('cc.ID_COSTCENTERS', $request->cost_centers);
        }

        if ($request->filled('card_statuses')) {
            $query->whereIn(DB::raw('LOWER(cak.Description)'), array_map('strtolower', $request->card_statuses));
        }

        if ($request->filled('additional_entries')) {
            $query->whereIn('c.Additional1', $request->additional_entries);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('c.ID_CARDS', 'LIKE', $searchTerm)
                  ->orWhere('c.PAN', 'LIKE', $searchTerm)
                  ->orWhere('v.Number', 'LIKE', $searchTerm)
                  ->orWhere('cus.lastname', 'LIKE', $searchTerm)
                  ->orWhere('g.Description', 'LIKE', $searchTerm)
                  ->orWhere('cc.Description', 'LIKE', $searchTerm)
                  ->orWhere('cak.Description', 'LIKE', $searchTerm);
            });
        }

        $query->orderBy('v.Number', 'asc');
        $cards = $query->get();

        $filename = 'cards_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Card ID', 'Card PAN', 'License Plate', 'Customer', 'Group', 'Cost Center', 'Card Status', 'Additional Entry'];
        $sheet->fromArray($headers, null, 'A1');
        
        $row = 2;
        foreach ($cards as $card) {
            $sheet->fromArray([
                $card->ID_CARDS ?? '-',
                $card->PAN ?? '-',
                $card->LicensePlate ?? '-',
                $card->CustomerName ?? '-',
                $card->GroupName ?? '-',
                $card->CostCenterName ?? '-',
                $card->AuthorizationKeyName ?? '-',
                $card->Additional1 == 'Y' ? 'Yes' : ($card->Additional1 == 'N' ? 'No' : ($card->Additional1 ?? 'Active'))
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