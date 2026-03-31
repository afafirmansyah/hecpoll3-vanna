<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function vehicle(Request $request)
    {
        $fromDate = $request->fromdate;
        $toDate = $request->todate;
        $vehicleId = $request->vehicle ?? '0';

        $query = "
            SELECT ca.CardLayoutsID, t.transactionsID, t.transdatetime, t.TransQuantity,
                   t.CardPAN, t.AdditionalEntry, t.Mileage, te.Description AS termname,
                   v.Number AS VehicleNumber
            FROM PAYMENTS t 
            INNER JOIN cards ca ON ca.ID_CARDS = t.CARDSID 
            INNER JOIN terminals te ON te.ID_TERMINALS = t.terminalsID 
            INNER JOIN vehicles v ON v.ID_vehicles = t.vehiclesid
            WHERE t.transdatetime BETWEEN ? AND ? AND t.transquantity != 0
        ";

        $params = ["$fromDate 00:00:00", "$toDate 23:59:59"];

        if ($vehicleId != '0') {
            $query .= " AND t.vehiclesID = ?";
            $params[] = $vehicleId;
        }

        $query .= " ORDER BY t.transdatetime DESC";

        $data = DB::select($query, $params);

        return $this->generateExcel($data, 'Vehicle_Report', $fromDate, $toDate, [
            'NO', 'DATE TIME', 'TERMINAL', 'CARD PAN', 'VEHICLE NUMBER', 'MILEAGE', 'ADDITIONAL ENTRY', 'QUANTITY'
        ]);
    }

    public function contractor(Request $request)
    {
        $fromDate = $request->fromdate;
        $toDate = $request->todate;
        $contractId = $request->contract;

        $data = DB::select("
            SELECT ca.CardLayoutsID, t.transactionsID, t.transdatetime, t.TransQuantity,
                   t.CardPAN, t.vehiclelicenseplate AS licenseplate, t.AdditionalEntry, 
                   c.limit, t.TransArticleDescription, t.Mileage, t.CardPAN2, 
                   te.Description AS termname, c.Description AS contractor, 
                   v.Description AS VehicleDescription, v.Number AS VehicleNumber 
            FROM PAYMENTS t 
            LEFT JOIN contracts c ON c.ID_contracts = t.contractsid 
            LEFT JOIN terminals te ON te.ID_TERMINALS = t.terminalsID 
            LEFT JOIN STATIONS st ON t.TerminalStationCode = st.StationCode 
            LEFT JOIN cards ca ON ca.ID_CARDS = t.CARDSID 
            LEFT JOIN vehicles v ON v.ID_vehicles = t.vehiclesid 
            WHERE t.transdatetime BETWEEN ? AND ? AND c.ID_CONTRACTS = ? AND t.transquantity != 0 
            ORDER BY t.transdatetime DESC
        ", ["$fromDate 00:00:00", "$toDate 23:59:59", $contractId]);

        return $this->generateExcel($data, 'Contractor_Report', $fromDate, $toDate, [
            'NO', 'DATE TIME', 'TERMINAL', 'CONTRACTOR', 'CARD PAN', 'VEHICLE NUMBER', 'MILEAGE', 'QUANTITY'
        ]);
    }

    public function events(Request $request)
    {
        $fromDate = $request->fromdate;
        $toDate = $request->todate;

        $data = DB::select("
            SELECT ID_EVENTS, EventDateTime, Description, Details, confirmation
            FROM EVENTS
            WHERE EventDateTime BETWEEN ? AND ?
            ORDER BY EventDateTime DESC
        ", ["$fromDate 00:00:00", "$toDate 23:59:59"]);

        return $this->generateExcel($data, 'Events_Report', $fromDate, $toDate, [
            'NO', 'DATE TIME', 'DESCRIPTION', 'DETAILS'
        ]);
    }

    public function fuelRatio(Request $request)
    {
        $fromDate = $request->fromdate;
        $toDate = $request->todate;

        $data = DB::select("
            SELECT date, OB_BISM, COAL_BISM, PORT_BISM 
            FROM fuelratio 
            WHERE date BETWEEN ? AND ? 
            ORDER BY date ASC
        ", [$fromDate, $toDate]);

        return $this->generateExcel($data, 'Fuel_Ratio_Report', $fromDate, $toDate, [
            'TRANSACTION DATE', 'OB RATIO', 'COAL RATIO', 'PORT RATIO'
        ]);
    }

    private function generateExcel($data, $filename, $fromDate, $toDate, $headers)
    {
        $output = '<table border="1"><thead><tr>';
        
        foreach ($headers as $header) {
            $output .= '<th>' . $header . '</th>';
        }
        
        $output .= '</tr></thead><tbody>';

        foreach ($data as $row) {
            $output .= '<tr>';
            foreach ($row as $key => $value) {
                if ($key === 'CardLayoutsID') continue;
                
                if ($key === 'CardPAN' && isset($row->CardLayoutsID)) {
                    if ($row->CardLayoutsID == 1) {
                        $value = str_replace("=", "P", $value);
                    } elseif ($row->CardLayoutsID == 6) {
                        $value = $value . 'R';
                    } else {
                        $value = $value . 'G';
                    }
                }
                
                if (is_object($value) && method_exists($value, 'format')) {
                    $value = $value->format('d-m-Y h:i:s A');
                }
                
                if (is_numeric($value) && strpos($key, 'Quantity') !== false) {
                    $value = number_format($value, 2);
                }
                
                $output .= '<td>' . htmlspecialchars($value ?? '') . '</td>';
            }
            $output .= '</tr>';
        }

        $output .= '</tbody></table>';

        return response($output)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename={$filename}_{$fromDate}_to_{$toDate}.xls")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}