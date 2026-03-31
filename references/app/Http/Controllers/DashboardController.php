<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\Terminal;
use App\Models\VehicleGroup;
use App\Models\TerminalConfiguration;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = date("Y");
        $month = date("m");
        $day = date("d");

        if ($request->has('fromdate') && $request->has('todate')) {
            $fromDate = $request->fromdate;
            $toDate = $request->todate;
            $whereClause = "TransDateTime BETWEEN '$fromDate 00:00' AND '$toDate 23:59'";
        } else {
            $whereClause = "TransDateTime >= '$year-$month-01'";
            $fromDate = "$year-$month-01";
            $toDate = date('Y-m-d');
        }

        // Get dynamic terminal configurations
        $decantationTerminals = TerminalConfiguration::getTerminalIds('decantation');
        $toloadingTerminals = TerminalConfiguration::getTerminalIds('toploading');
        $fuelDispensingTerminals = TerminalConfiguration::getTerminalIds('fuel_dispensing');

        // Apply user access control
        $userAccess = auth()->user()->userAccess;
        if ($userAccess && !empty($userAccess->terminal_ids)) {
            $allowedTerminals = $userAccess->terminal_ids;
            $decantationTerminals = array_intersect($decantationTerminals, $allowedTerminals);
            $toloadingTerminals = array_intersect($toloadingTerminals, $allowedTerminals);
            $fuelDispensingTerminals = array_intersect($fuelDispensingTerminals, $allowedTerminals);
        } elseif ($userAccess && empty($userAccess->terminal_ids)) {
            // User has access restrictions but no terminals allowed - show no data
            $decantationTerminals = [];
            $toloadingTerminals = [];
            $fuelDispensingTerminals = [];
        }

        // Get summary data with filtered terminals
        $totalReceived = empty($decantationTerminals) ? '0.00' : $this->getQuantitySum($decantationTerminals, $fromDate, $toDate);
        $issuedToVms = empty($toloadingTerminals) ? '0.00' : $this->getQuantitySum($toloadingTerminals, $fromDate, $toDate, 12);
        $fuelIssued = empty($fuelDispensingTerminals) ? '0.00' : $this->getQuantitySum($fuelDispensingTerminals, $fromDate, $toDate);
        $stockTransfer = empty($toloadingTerminals) ? '0.00' : $this->getStockTransferAmount($toloadingTerminals, $fromDate, $toDate);
        $totalStock = $this->getStockSum($toDate);
        
        // Get terminal info for display
        $terminalInfo = $this->getTerminalInfo($decantationTerminals, $toloadingTerminals, $fuelDispensingTerminals);

        // Get chart data (return empty if user has access restrictions but no terminals allowed)
        if ($userAccess && empty($userAccess->terminal_ids)) {
            $vehicleData = [];
            $terminalData = [];
            $vehicleGroupData = [];
            $costCenterData = [];
            $dailyData = [];
            $ratioData = [];
            $mileageRatio = [];
        } else {
            $vehicleData = $this->getTopVehicles($whereClause);
            $terminalData = $this->getTerminalData($whereClause);
            $vehicleGroupData = $this->getVehicleGroupData($whereClause);
            $costCenterData = $this->getCostCenterData($whereClause);
            $dailyData = $this->getDailyData($fromDate, $toDate);
            $ratioData = $this->getRatioData($fromDate, $toDate);
            $mileageRatio = $this->getMileageRatio($whereClause);
        }

        return view('dashboard', compact(
            'totalReceived',
            'issuedToVms',
            'fuelIssued',
            'stockTransfer',
            'totalStock',
            'vehicleData',
            'terminalData',
            'vehicleGroupData',
            'costCenterData',
            'dailyData',
            'ratioData',
            'mileageRatio',
            'terminalInfo',
            'fromDate',
            'toDate'
        ));
    }

    private function getQuantitySum($terminals, $fromDate, $toDate, $customerId = null, $exclude = false)
    {
        $query = DB::table('payments')
            ->whereRaw("TransDateTime between '$fromDate 00:00' and '$toDate 23:59'");

        if (is_array($terminals)) {
            $query->whereIn('TerminalsID', $terminals);
        } else {
            $query->where('TerminalsID', $terminals);
        }

        if ($customerId) {
            if ($exclude) {
                $query->where('CustomersID', '!=', $customerId);
            } else {
                $query->where('CustomersID', $customerId);
            }
        }

        $result = $query->sum('TransQuantity') / 1000;
        return number_format($result, 2);
    }

    private function getStockSum($date)
    {
        $result = DB::table('stocks')
            ->where('trans_date', $date)
            ->sum('Closing') / 1000;
        return number_format($result, 2);
    }

    private function getStockTransferAmount($terminals, $fromDate, $toDate)
    {
        $totalToploading = $this->getQuantitySum($terminals, $fromDate, $toDate, null, true);
        $issuedToVms = $this->getQuantitySum($terminals, $fromDate, $toDate, 12);
        
        $result = (float)str_replace(',', '', $totalToploading) - (float)str_replace(',', '', $issuedToVms);
        return number_format($result, 2);
    }

    private function getTopVehicles($whereClause)
    {
        $userAccess = auth()->user()->userAccess;
        $terminalFilter = '';
        
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = implode(',', $userAccess->terminal_ids);
            $terminalFilter = " AND p.TerminalsID IN ($allowedTerminals)";
        }
        
        return DB::select("
            SELECT TOP 6 v.Number AS VehicleLicensePlate, 
                   ROUND(SUM(p.transquantity) / 1000, 2) AS Total 
            FROM payments p 
            JOIN vehicles v ON p.VehiclesID = v.ID_VEHICLES 
            WHERE $whereClause $terminalFilter
            GROUP BY v.Number 
            ORDER BY ROUND(SUM(p.transquantity), 2) DESC
        ");
    }

    private function getTerminalData($whereClause)
    {
        $userAccess = auth()->user()->userAccess;
        $terminalFilter = '';
        
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = implode(',', $userAccess->terminal_ids);
            $terminalFilter = " AND p.TerminalsID IN ($allowedTerminals)";
        }
        
        return DB::select("
            SELECT TOP 6 (t.description) AS Terminal_name, t.city,
                   ROUND(SUM(CASE WHEN c.CardLayoutsID IN (5, 39) THEN p.transquantity ELSE 0 END) / 1000, 2) AS RFID_Quantity,
                   ROUND(SUM(CASE WHEN c.CardLayoutsID = 14 THEN p.transquantity ELSE 0 END) / 1000, 2) AS Proximity_Quantity
            FROM payments p 
            JOIN cards c ON p.CardsID = c.ID_CARDS 
            JOIN terminals t ON p.TerminalsID = t.ID_TERMINALS 
            WHERE $whereClause $terminalFilter
            GROUP BY t.Description, t.city 
            ORDER BY t.City
        ");
    }

    private function getVehicleGroupData($whereClause)
    {
        $userAccess = auth()->user()->userAccess;
        $terminalFilter = '';
        
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = implode(',', $userAccess->terminal_ids);
            $terminalFilter = " AND p.TerminalsID IN ($allowedTerminals)";
        }
        
        return DB::select("
            SELECT TOP 6 vg.description AS vgp, 
                   ROUND(SUM(p.transquantity) / 1000, 2) AS vgptotal 
            FROM payments p 
            JOIN vehicles v ON p.VehiclesID = v.ID_VEHICLES 
            JOIN VEHICLEGROUPS vg ON v.VehicleGroupsID = vg.ID_VEHICLEGROUPS 
            JOIN cards c ON p.CardsID = c.ID_CARDS 
            WHERE c.CardLayoutsID NOT IN (14) AND vg.ID_VEHICLEGROUPS > 19 AND $whereClause $terminalFilter
            GROUP BY vg.description 
            ORDER BY ROUND(SUM(p.transquantity), 2) DESC
        ");
    }

    private function getDailyData($fromDate, $toDate)
    {
        $userAccess = auth()->user()->userAccess;
        
        // Get configured terminals
        $decantationTerminals = TerminalConfiguration::getTerminalIds('decantation');
        $toloadingTerminals = TerminalConfiguration::getTerminalIds('toploading');
        $fuelDispensingTerminals = TerminalConfiguration::getTerminalIds('fuel_dispensing');
        
        // Apply user access filtering
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = $userAccess->terminal_ids;
            $decantationTerminals = array_intersect($decantationTerminals, $allowedTerminals);
            $toloadingTerminals = array_intersect($toloadingTerminals, $allowedTerminals);
            $fuelDispensingTerminals = array_intersect($fuelDispensingTerminals, $allowedTerminals);
        }
        
        // Convert to comma-separated strings for SQL
        $decantationStr = empty($decantationTerminals) ? '0' : implode(',', $decantationTerminals);
        $toloadingStr = empty($toloadingTerminals) ? '0' : implode(',', $toloadingTerminals);
        $fuelDispensingStr = empty($fuelDispensingTerminals) ? '0' : implode(',', $fuelDispensingTerminals);
        
        return DB::select("
            WITH DateRange AS (
                SELECT CAST('$fromDate' AS DATE) AS Date 
                UNION ALL 
                SELECT DATEADD(day, 1, Date) FROM DateRange WHERE Date < '$toDate'
            ),
            ToploadingData AS (
                SELECT ROUND(SUM(CASE WHEN CustomersID != 12 THEN TransQuantity ELSE 0 END) / 1000, 2) AS fiQty, 
                       CAST(TransDateTime AS DATE) AS TransDate 
                FROM payments WHERE TerminalsID IN ($toloadingStr) AND TransDateTime BETWEEN '$fromDate 00:00' AND '$toDate 23:59' 
                GROUP BY CAST(TransDateTime AS DATE)
            ),
            DecantationData AS (
                SELECT ROUND(SUM(TransQuantity) / 1000, 2) AS fiQty, CAST(TransDateTime AS DATE) AS TransDate 
                FROM payments WHERE TerminalsID IN ($decantationStr) AND TransDateTime BETWEEN '$fromDate 00:00' AND '$toDate 23:59' 
                GROUP BY CAST(TransDateTime AS DATE)
            ),
            FuelIssuedData AS (
                SELECT ROUND(SUM(TransQuantity) / 1000, 2) AS fiQty, CAST(TransDateTime AS DATE) AS TransDate 
                FROM payments WHERE TerminalsID IN ($fuelDispensingStr) AND TransDateTime BETWEEN '$fromDate 00:00' AND '$toDate 23:59' 
                GROUP BY CAST(TransDateTime AS DATE)
            ),
            IssuedToVmsData AS (
                SELECT ROUND(SUM(TransQuantity) / 1000, 2) AS fiQty, CAST(TransDateTime AS DATE) AS TransDate 
                FROM payments WHERE TerminalsID IN ($toloadingStr) AND CustomersID = 12 AND TransDateTime BETWEEN '$fromDate 00:00' AND '$toDate 23:59' 
                GROUP BY CAST(TransDateTime AS DATE)
            ),
            StockData AS (
                SELECT ROUND(SUM(Closing) / 1000, 2) AS fiQty, trans_date AS TransDate 
                FROM stocks WHERE trans_date BETWEEN '$fromDate' AND '$toDate' 
                GROUP BY trans_date
            )
            SELECT FORMAT(d.Date, 'dd/MM') AS [Date],
                   COALESCE(t.fiQty, 0) AS Toploading,
                   COALESCE(dc.fiQty, 0) AS Decantation,
                   COALESCE(f.fiQty, 0) + COALESCE(vms.fiQty, 0) AS FuelIssued,
                   COALESCE(s.fiQty, 0) AS TotalStock
            FROM DateRange d 
            LEFT JOIN ToploadingData t ON d.Date = t.TransDate 
            LEFT JOIN DecantationData dc ON d.Date = dc.TransDate 
            LEFT JOIN FuelIssuedData f ON d.Date = f.TransDate 
            LEFT JOIN IssuedToVmsData vms ON d.Date = vms.TransDate 
            LEFT JOIN StockData s ON d.Date = s.TransDate 
            ORDER BY d.Date
        ");
    }

    private function getRatioData($fromDate, $toDate)
    {
        $userAccess = auth()->user()->userAccess;
        $terminalFilter = '';
        
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = implode(',', $userAccess->terminal_ids);
            $terminalFilter = " AND p.TerminalsID IN ($allowedTerminals)";
        }
        
        return DB::select("
            WITH fuelconsumption AS (
                SELECT vg.description, ROUND(SUM(p.transquantity), 2) AS total_fuel_consumed 
                FROM payments p 
                JOIN vehicles v ON p.vehiclesid = v.id_vehicles 
                JOIN vehiclegroups vg ON v.vehiclegroupsid = vg.id_vehiclegroups 
                WHERE vg.description IN ('ob', 'coal', 'port') AND TransDateTime BETWEEN '$fromDate 00:00' AND '$toDate 23:59' $terminalFilter
                GROUP BY vg.description
            ),
            fuelproduction AS (
                SELECT 'coal' AS description, SUM(COALESCE(coal_bism, 0)) AS total_production 
                FROM fuelratio WHERE date BETWEEN '$fromDate' AND '$toDate'
                UNION ALL
                SELECT 'ob' AS description, SUM(COALESCE(ob_bism, 0)) AS total_production 
                FROM fuelratio WHERE date BETWEEN '$fromDate' AND '$toDate'
                UNION ALL
                SELECT 'port' AS description, SUM(COALESCE(port_bism, 0)) AS total_production 
                FROM fuelratio WHERE date BETWEEN '$fromDate' AND '$toDate'
            )
            SELECT fc.description, 
                   CASE WHEN fp.total_production = 0 THEN NULL 
                        ELSE ROUND(fc.total_fuel_consumed / fp.total_production, 2) 
                   END AS fuel_ratio 
            FROM fuelconsumption fc 
            JOIN fuelproduction fp ON fc.description = fp.description
        ");
    }

    private function getCostCenterData($whereClause)
    {
        $userAccess = auth()->user()->userAccess;
        $terminalFilter = '';
        
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = implode(',', $userAccess->terminal_ids);
            $terminalFilter = " AND t.TerminalsID IN ($allowedTerminals)";
        }
        
        return DB::select("
            SELECT TOP 6 UPPER(cc.Description) AS costcenter, 
                   ROUND(SUM(t.transquantity) / 1000, 2) AS total 
            FROM PAYMENTS t
            INNER JOIN vehicles v ON v.ID_VEHICLES = t.VehiclesID
            INNER JOIN COSTCENTERS cc ON cc.ID_COSTCENTERS = v.CostCenterID
            WHERE t.transquantity != 0 AND cc.Description IS NOT NULL AND $whereClause $terminalFilter
            GROUP BY cc.Description 
            ORDER BY ROUND(SUM(t.transquantity), 2) DESC
        ");
    }

    private function getMileageRatio($whereClause)
    {
        $userAccess = auth()->user()->userAccess;
        $terminalFilter = '';
        
        if ($userAccess && $userAccess->terminal_ids) {
            $allowedTerminals = implode(',', $userAccess->terminal_ids);
            $terminalFilter = " AND t.TerminalsID IN ($allowedTerminals)";
        }
        
        return DB::select("
            SELECT TOP 15 v.Number AS VehicleNumber, 
                   AVG(v.Consumption) AS Target, 
                   AVG(t.TransQuantity / NULLIF(prev.PrevMileageDiff, 0)) AS Actual, 
                   (AVG(t.TransQuantity / NULLIF(prev.PrevMileageDiff, 0)) / AVG(v.Consumption)) * 100 AS TargetPercent 
            FROM PAYMENTS t 
            JOIN VEHICLES v ON v.ID_VEHICLES = t.VehiclesID 
            JOIN CUSTOMERS c ON c.ID_CUSTOMERS = t.CustomersID 
            INNER JOIN terminals term ON term.ID_TERMINALS = t.terminalsID 
            INNER JOIN cards ca ON ca.ID_CARDS = t.CARDSID 
            OUTER APPLY (
                SELECT TOP 1 (t.Mileage - P1.Mileage) AS PrevMileageDiff 
                FROM PAYMENTS P1 
                WHERE P1.VehiclesID = t.VehiclesID AND P1.CardPAN = t.CardPAN 
                  AND P1.TRANSDATETIME < t.TRANSDATETIME AND P1.Mileage <> t.Mileage 
                  AND P1.TransArticleID = t.TransArticleID AND P1.TransQuantity > 0 
                ORDER BY P1.TRANSDATETIME DESC
            ) prev 
            WHERE v.ActMilageType = 'K' AND t.TransQuantity != 0 AND v.Consumption != 0 AND $whereClause $terminalFilter
            GROUP BY v.Number, v.Description 
            ORDER BY TargetPercent DESC
        ");
    }

    private function getTerminalInfo($decantationTerminals, $toloadingTerminals, $fuelDispensingTerminals)
    {
        $terminals = DB::table('TERMINALS')->select('ID_TERMINALS', 'Description')->get()->keyBy('ID_TERMINALS');
        
        return [
            'decantation' => [
                'ids' => $decantationTerminals,
                'names' => collect($decantationTerminals)->map(fn($id) => $terminals[$id]->Description ?? "Terminal $id")->implode(', ')
            ],
            'toploading' => [
                'ids' => $toloadingTerminals,
                'names' => collect($toloadingTerminals)->map(fn($id) => $terminals[$id]->Description ?? "Terminal $id")->implode(', ')
            ],
            'fuel_dispensing' => [
                'ids' => $fuelDispensingTerminals,
                'names' => collect($fuelDispensingTerminals)->map(fn($id) => $terminals[$id]->Description ?? "Terminal $id")->implode(', ')
            ]
        ];
    }

    public function getTerminalSettings()
    {
        $terminals = Terminal::select('ID_TERMINALS', 'Description')->get();
        $configurations = [
            'decantation' => TerminalConfiguration::getTerminalIds('decantation'),
            'toploading' => TerminalConfiguration::getTerminalIds('toploading'),
            'fuel_dispensing' => TerminalConfiguration::getTerminalIds('fuel_dispensing')
        ];
        
        return response()->json(compact('terminals', 'configurations'));
    }

    public function updateTerminalSettings(Request $request)
    {
        $request->validate([
            'decantation' => 'required|array',
            'toploading' => 'required|array', 
            'fuel_dispensing' => 'required|array'
        ]);

        foreach (['decantation', 'toploading', 'fuel_dispensing'] as $type) {
            TerminalConfiguration::updateOrCreate(
                ['operation_type' => $type],
                ['terminal_ids' => $request->$type]
            );
        }

        return response()->json(['success' => true]);
    }
}
