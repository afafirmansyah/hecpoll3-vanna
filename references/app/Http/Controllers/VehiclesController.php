<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehiclesController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('VEHICLES as v')
            ->leftJoin('PAYMENTS as p', 'p.vehiclesID', '=', 'v.ID_VEHICLES')
            ->leftJoin('VehicleGroups as g', 'v.VehicleGroupsID', '=', 'g.ID_VEHICLEGROUPS')
            ->leftJoin('Cards as c', 'c.VehiclesID', '=', 'v.ID_VEHICLES')
            ->leftJoin('CUSTOMERS as cus', 'cus.ID_CUSTOMERS', '=', 'c.CustomersID')
            ->select(
                'v.ID_VEHICLES',
                'cus.lastname as CustomerName',
                'v.Description as Make',
                'v.LicensePlate as Type',
                'v.Number as LicensePlate',
                'g.Description as GroupName',
                'v.Consumption as Target',
                DB::raw('MAX(p.Mileage) as Mileage'),
                DB::raw('MAX(p.TransDateTime) as LastTransactionDate')
            )
            ->where('v.Number', '!=', '000001')
            ->groupBy('v.ID_VEHICLES', 'cus.lastname', 'v.Description', 'v.LicensePlate', 'v.Number', 'g.Description', 'v.Consumption');

        if ($request->filled('customers')) {
            $query->whereIn('cus.ID_CUSTOMERS', $request->customers);
        }

        if ($request->filled('makes')) {
            $query->whereIn('v.Description', $request->makes);
        }

        if ($request->filled('types')) {
            $query->whereIn('v.LicensePlate', $request->types);
        }

        if ($request->filled('groups')) {
            $query->whereIn('g.ID_VEHICLEGROUPS', $request->groups);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('v.Description', 'LIKE', $searchTerm)
                  ->orWhere('v.LicensePlate', 'LIKE', $searchTerm)
                  ->orWhere('v.Number', 'LIKE', $searchTerm)
                  ->orWhere('v.Consumption', 'LIKE', $searchTerm)
                  ->orWhere('g.Description', 'LIKE', $searchTerm)
                  ->orWhere('cus.lastname', 'LIKE', $searchTerm);
            });
        }

        $query->orderBy(DB::raw('MAX(p.TransDateTime)'), 'desc');
        $vehicles = $query->paginate(20);

        $customers = DB::table('CUSTOMERS')
            ->select('ID_CUSTOMERS', 'lastname')
            ->whereNotNull('lastname')
            ->where('lastname', '!=', 'default customer')
            ->orderBy('lastname')
            ->get();

        $makes = DB::table('VEHICLES')
            ->select('Description as Make')
            ->whereNotNull('Description')
            ->where('Description', '!=', '')
            ->distinct()
            ->orderBy('Description')
            ->get();
            
        $types = DB::table('VEHICLES')
            ->select('LicensePlate as Type')
            ->whereNotNull('LicensePlate')
            ->where('LicensePlate', '!=', '')
            ->distinct()
            ->orderBy('LicensePlate')
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

        return view('vehicles', compact('vehicles', 'customers', 'makes', 'types', 'vehicleGroups'));
    }

    public function export(Request $request)
    {
        $query = DB::table('VEHICLES as v')
            ->leftJoin('PAYMENTS as p', 'p.vehiclesID', '=', 'v.ID_VEHICLES')
            ->leftJoin('VehicleGroups as g', 'v.VehicleGroupsID', '=', 'g.ID_VEHICLEGROUPS')
            ->leftJoin('Cards as c', 'c.VehiclesID', '=', 'v.ID_VEHICLES')
            ->leftJoin('CUSTOMERS as cus', 'cus.ID_CUSTOMERS', '=', 'c.CustomersID')
            ->select(
                'v.ID_VEHICLES',
                'cus.lastname as CustomerName',
                'v.Description as Make',
                'v.LicensePlate as Type',
                'v.Number as LicensePlate',
                'g.Description as GroupName',
                'v.Consumption as Target',
                DB::raw('MAX(p.Mileage) as Mileage'),
                DB::raw('MAX(p.TransDateTime) as LastTransactionDate')
            )
            ->where('v.Number', '!=', '000001')
            ->groupBy('v.ID_VEHICLES', 'cus.lastname', 'v.Description', 'v.LicensePlate', 'v.Number', 'g.Description', 'v.Consumption');

        if ($request->filled('customers')) {
            $query->whereIn('cus.ID_CUSTOMERS', $request->customers);
        }

        if ($request->filled('makes')) {
            $query->whereIn('v.Description', $request->makes);
        }

        if ($request->filled('types')) {
            $query->whereIn('v.LicensePlate', $request->types);
        }

        if ($request->filled('groups')) {
            $query->whereIn('g.ID_VEHICLEGROUPS', $request->groups);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('v.Description', 'LIKE', $searchTerm)
                  ->orWhere('v.LicensePlate', 'LIKE', $searchTerm)
                  ->orWhere('v.Number', 'LIKE', $searchTerm)
                  ->orWhere('v.Consumption', 'LIKE', $searchTerm)
                  ->orWhere('g.Description', 'LIKE', $searchTerm)
                  ->orWhere('cus.lastname', 'LIKE', $searchTerm);
            });
        }

        $query->orderBy(DB::raw('MAX(p.TransDateTime)'), 'desc');
        $vehicles = $query->get();

        $filename = 'vehicles_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = ['Vehicle ID', 'Customer', 'Make', 'Type', 'License Plate', 'Group', 'Target', 'Mileage', 'Last Transaction Date'];
        $sheet->fromArray($headers, null, 'A1');
        
        $row = 2;
        foreach ($vehicles as $vehicle) {
            $sheet->fromArray([
                $vehicle->ID_VEHICLES ?? '-',
                $vehicle->CustomerName ?? '-',
                $vehicle->Make ?? '-',
                $vehicle->Type ?? '-',
                $vehicle->LicensePlate ?? '-',
                $vehicle->GroupName ?? '-',
                $vehicle->Target ?? '-',
                $vehicle->Mileage ?? '-',
                $vehicle->LastTransactionDate ?? '-'
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