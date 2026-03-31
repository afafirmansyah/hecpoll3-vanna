<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyRatioController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->get('fromdate', Carbon::now()->subDays(80)->format('Y-m-d'));
        $toDate = $request->get('todate', Carbon::now()->format('Y-m-d'));

        $dailyRatios = DB::table('fuelratio as fr')
            ->leftJoin(DB::raw('(
                SELECT
                    CAST(p.transdatetime as date) as transactiondate,
                    SUM(CASE WHEN vg.description = \'coal\' THEN p.transquantity ELSE 0 END) as coal,
                    SUM(CASE WHEN vg.description = \'ob\' THEN p.transquantity ELSE 0 END) as ob,
                    SUM(CASE WHEN vg.description = \'port\' THEN p.transquantity ELSE 0 END) as port
                FROM
                    payments p
                JOIN 
                    vehicles v on p.vehiclesid = v.id_vehicles
                JOIN 
                    vehiclegroups vg on v.vehiclegroupsid = vg.id_vehiclegroups
                WHERE 
                    vg.description in (\'coal\', \'ob\', \'port\')
                GROUP BY
                    CAST(p.transdatetime as date)
            ) as p'), 'fr.date', '=', 'p.transactiondate')
            ->select(
                'fr.date',
                DB::raw('ROUND(COALESCE(p.coal, 0), 2) as coal_fuel'),
                DB::raw('ROUND(COALESCE(fr.COAL_BISM, 0), 2) as coal_prod'),
                DB::raw('CASE WHEN COALESCE(fr.COAL_BISM, 0) > 0 THEN ROUND(COALESCE(p.coal, 0) / COALESCE(fr.COAL_BISM, 0), 2) ELSE 0 END as coal_ratio'),
                DB::raw('ROUND(COALESCE(p.ob, 0), 2) as ob_fuel'),
                DB::raw('ROUND(COALESCE(fr.OB_BISM, 0), 2) as ob_prod'),
                DB::raw('CASE WHEN COALESCE(fr.OB_BISM, 0) > 0 THEN ROUND(COALESCE(p.ob, 0) / COALESCE(fr.OB_BISM, 0), 2) ELSE 0 END as ob_ratio'),
                DB::raw('ROUND(COALESCE(p.port, 0), 2) as port_fuel'),
                DB::raw('ROUND(COALESCE(fr.PORT_BISM, 0), 2) as port_prod'),
                DB::raw('CASE WHEN COALESCE(fr.PORT_BISM, 0) > 0 THEN ROUND(COALESCE(p.port, 0) / COALESCE(fr.PORT_BISM, 0), 2) ELSE 0 END as port_ratio')
            )
            ->whereBetween('fr.date', [$fromDate, $toDate])
            ->orderBy('fr.date', 'desc')
            ->paginate(20);

        $dailyRatios->appends($request->query());

        return view('daily-ratio', compact('dailyRatios', 'fromDate', 'toDate'));
    }

    private function getDailyRatiosQuery()
    {
        return DB::select("
            SELECT 
                fr.date,
                ROUND(COALESCE(p.coal, 0), 2) as coal_fuel,
                ROUND(COALESCE(fr.COAL_BISM, 0), 2) as coal_prod,
                CASE 
                    WHEN COALESCE(fr.COAL_BISM, 0) > 0 THEN ROUND(COALESCE(p.coal, 0) / COALESCE(fr.COAL_BISM, 0), 2) 
                    ELSE 0 
                END as coal_ratio,
                ROUND(COALESCE(p.ob, 0), 2) as ob_fuel,
                ROUND(COALESCE(fr.OB_BISM, 0), 2) as ob_prod,
                CASE 
                    WHEN COALESCE(fr.OB_BISM, 0) > 0 THEN ROUND(COALESCE(p.ob, 0) / COALESCE(fr.OB_BISM, 0), 2) 
                    ELSE 0 
                END as ob_ratio,
                ROUND(COALESCE(p.port, 0), 2) as port_fuel,
                ROUND(COALESCE(fr.PORT_BISM, 0), 2) as port_prod,
                CASE 
                    WHEN COALESCE(fr.PORT_BISM, 0) > 0 THEN ROUND(COALESCE(p.port, 0) / COALESCE(fr.PORT_BISM, 0), 2) 
                    ELSE 0 
                END as port_ratio
            FROM 
                fuelratio fr
            LEFT JOIN (
                SELECT
                    CAST(p.transdatetime as date) as transactiondate,
                    SUM(CASE WHEN vg.description = 'coal' THEN p.transquantity ELSE 0 END) as coal,
                    SUM(CASE WHEN vg.description = 'ob' THEN p.transquantity ELSE 0 END) as ob,
                    SUM(CASE WHEN vg.description = 'port' THEN p.transquantity ELSE 0 END) as port
                FROM
                    payments p
                JOIN 
                    vehicles v on p.vehiclesid = v.id_vehicles
                JOIN 
                    vehiclegroups vg on v.vehiclegroupsid = vg.id_vehiclegroups
                WHERE 
                    vg.description in ('coal', 'ob', 'port')
                GROUP BY
                    CAST(p.transdatetime as date)
            ) p ON fr.date = p.transactiondate
            WHERE fr.date BETWEEN ? AND ?
            ORDER BY 
                fr.date DESC
        ", [$fromDate, $toDate]);

        return view('daily-ratio', compact('dailyRatios', 'fromDate', 'toDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('fromdate', Carbon::now()->subDays(80)->format('Y-m-d'));
        $endDate = $request->get('todate', Carbon::now()->format('Y-m-d'));

        $dailyRatios = DB::table('fuelratio as fr')
            ->leftJoin(DB::raw('(
                SELECT
                    CAST(p.transdatetime as date) as transactiondate,
                    SUM(CASE WHEN vg.description = \'coal\' THEN p.transquantity ELSE 0 END) as coal,
                    SUM(CASE WHEN vg.description = \'ob\' THEN p.transquantity ELSE 0 END) as ob,
                    SUM(CASE WHEN vg.description = \'port\' THEN p.transquantity ELSE 0 END) as port
                FROM
                    payments p
                JOIN 
                    vehicles v on p.vehiclesid = v.id_vehicles
                JOIN 
                    vehiclegroups vg on v.vehiclegroupsid = vg.id_vehiclegroups
                WHERE 
                    vg.description in (\'coal\', \'ob\', \'port\')
                GROUP BY
                    CAST(p.transdatetime as date)
            ) as p'), 'fr.date', '=', 'p.transactiondate')
            ->select(
                'fr.date',
                DB::raw('ROUND(COALESCE(p.coal, 0), 2) as coal_fuel'),
                DB::raw('ROUND(COALESCE(fr.COAL_BISM, 0), 2) as coal_prod'),
                DB::raw('CASE WHEN COALESCE(fr.COAL_BISM, 0) > 0 THEN ROUND(COALESCE(p.coal, 0) / COALESCE(fr.COAL_BISM, 0), 2) ELSE 0 END as coal_ratio'),
                DB::raw('ROUND(COALESCE(p.ob, 0), 2) as ob_fuel'),
                DB::raw('ROUND(COALESCE(fr.OB_BISM, 0), 2) as ob_prod'),
                DB::raw('CASE WHEN COALESCE(fr.OB_BISM, 0) > 0 THEN ROUND(COALESCE(p.ob, 0) / COALESCE(fr.OB_BISM, 0), 2) ELSE 0 END as ob_ratio'),
                DB::raw('ROUND(COALESCE(p.port, 0), 2) as port_fuel'),
                DB::raw('ROUND(COALESCE(fr.PORT_BISM, 0), 2) as port_prod'),
                DB::raw('CASE WHEN COALESCE(fr.PORT_BISM, 0) > 0 THEN ROUND(COALESCE(p.port, 0) / COALESCE(fr.PORT_BISM, 0), 2) ELSE 0 END as port_ratio')
            )
            ->whereBetween('fr.date', [$startDate, $endDate])
            ->orderBy('fr.date', 'desc')
            ->get();

        $filename = 'daily_ratio_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Create merged headers
        $sheet->setCellValue('A1', 'Transaction Date');
        $sheet->setCellValue('B1', 'Over Burden');
        $sheet->setCellValue('E1', 'COAL');
        $sheet->setCellValue('H1', 'PORT');
        
        // Merge cells for main headers
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:D1');
        $sheet->mergeCells('E1:G1');
        $sheet->mergeCells('H1:J1');
        
        // Sub headers
        $sheet->setCellValue('B2', 'Issued');
        $sheet->setCellValue('C2', 'Input');
        $sheet->setCellValue('D2', 'Ratio');
        $sheet->setCellValue('E2', 'Issued');
        $sheet->setCellValue('F2', 'Input');
        $sheet->setCellValue('G2', 'Ratio');
        $sheet->setCellValue('H2', 'Issued');
        $sheet->setCellValue('I2', 'Input');
        $sheet->setCellValue('J2', 'Ratio');
        
        $row = 3;
        foreach ($dailyRatios as $data) {
            $sheet->fromArray([
                $data->date ? \Carbon\Carbon::parse($data->date)->format('F j, Y') : '-',
                number_format($data->ob_fuel ?? 0, 0),
                number_format($data->ob_prod ?? 0, 0),
                number_format($data->ob_ratio ?? 0, 2),
                number_format($data->coal_fuel ?? 0, 0),
                number_format($data->coal_prod ?? 0, 0),
                number_format($data->coal_ratio ?? 0, 2),
                number_format($data->port_fuel ?? 0, 0),
                number_format($data->port_prod ?? 0, 0),
                number_format($data->port_ratio ?? 0, 2)
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

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'OB_BISM' => 'required|numeric',
            'COAL_BISM' => 'required|numeric',
            'PORT_BISM' => 'required|numeric',
        ]);

        // Check if date already exists
        $existingRecord = DB::table('fuelratio')->where('date', $request->date)->first();
        
        if ($existingRecord) {
            return redirect()->route('daily-ratio')->with('error', 'Data for this date already exists!');
        }

        DB::table('fuelratio')->insert([
            'date' => $request->date,
            'OB_BISM' => $request->OB_BISM,
            'COAL_BISM' => $request->COAL_BISM,
            'PORT_BISM' => $request->PORT_BISM,
        ]);

        return redirect()->route('daily-ratio')->with('success', 'Daily ratio data saved successfully!');
    }

    public function delete(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_daily_ratio')) {
            return redirect()->route('daily-ratio')->with('error', 'You do not have permission to delete daily ratio data.');
        }
        
        $dates = $request->get('dates', []);
        
        if (!empty($dates)) {
            DB::table('fuelratio')->whereIn('date', $dates)->delete();
            return redirect()->route('daily-ratio')->with('success', count($dates) . ' record(s) deleted successfully!');
        }
        
        return redirect()->route('daily-ratio')->with('error', 'No records selected for deletion.');
    }
}