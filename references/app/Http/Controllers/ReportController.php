<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\Terminal;
use App\Models\Station;
use App\Models\Contract;
use App\Models\Event;
use App\Models\FuelRatio;

class ReportController extends Controller
{
    public function vehicles(Request $request)
    {
        $vehicleName = 'All Vehicles';
        $query = Payment::with(['vehicle', 'terminal', 'card'])
            ->where('TransQuantity', '>', 0)
            ->where('TerminalStationCode', 1)
            ->where('transdatetime', '>=', now()->subDays(7))
            ->orderBy('transdatetime', 'desc');

        if ($request->has('vehicle') && $request->vehicle[0] != '0') {
            $vehicleId = $request->vehicle[0];
            $query->where('VehiclesID', $vehicleId);
            $vehicle = Vehicle::find($vehicleId);
            $vehicleName = $vehicle ? $vehicle->Number : 'Unknown Vehicle';
        }

        $transactions = $query->paginate(15);
        $vehicles = Vehicle::where('ID_VEHICLES', '!=', 291)->orderBy('Number')->get();

        return view('reports.vehicles', compact('transactions', 'vehicles', 'vehicleName'));
    }

    public function sites(Request $request)
    {
        $stationName = '';
        $query = Payment::with(['vehicle', 'terminal', 'card'])
            ->where('transquantity', '!=', 0)
            ->where('transdatetime', '>=', now()->subDays(30))
            ->orderBy('transdatetime', 'desc');

        if ($request->has('site')) {
            $stationCode = $request->site;
            $query->where('TerminalStationCode', $stationCode);
            $station = Station::where('StationCode', $stationCode)->first();
            $stationName = $station ? $station->StationName : '';
        }

        $transactions = $query->paginate(15);
        $stations = Station::all();

        return view('reports.sites', compact('transactions', 'stations', 'stationName'));
    }

    public function terminals(Request $request)
    {
        $terminalName = '';
        $stationName = '';
        $query = Payment::with(['vehicle', 'terminal', 'card'])
            ->where('transquantity', '!=', 0)
            ->where('transdatetime', '>=', now()->subDays(14))
            ->orderBy('transdatetime', 'desc');

        if ($request->has('terminal')) {
            $terminalId = $request->terminal;
            $query->where('terminalsID', $terminalId);
            $terminal = Terminal::with('station')->find($terminalId);
            if ($terminal) {
                $terminalName = $terminal->Description;
                $stationName = $terminal->station ? $terminal->station->StationName : '';
            }
        }

        $transactions = $query->paginate(15);
        $terminals = Terminal::all();

        return view('reports.terminals', compact('transactions', 'terminals', 'terminalName', 'stationName'));
    }

    public function contractors(Request $request)
    {
        $contractorName = '';
        $query = Payment::with(['vehicle', 'terminal', 'card', 'contract'])
            ->where('transquantity', '!=', 0)
            ->where('transdatetime', '>=', now()->subDays(14))
            ->orderBy('transdatetime', 'desc');

        if ($request->has('contractor')) {
            $contractId = $request->contractor;
            $query->where('contractsid', $contractId);
            $contract = Contract::find($contractId);
            $contractorName = $contract ? $contract->Description : '';
        }

        $transactions = $query->paginate(15);
        $contracts = Contract::where('ID_CONTRACTS', '!=', 2)->get();

        return view('reports.contractors', compact('transactions', 'contracts', 'contractorName'));
    }

    public function events()
    {
        $events = Event::orderBy('EventDateTime', 'desc')->limit(100)->get();
        return view('reports.events', compact('events'));
    }

    public function cards()
    {
        $cards = DB::table('VEHICLES as v')
            ->join('VehicleGroups as g', 'v.VehicleGroupsID', '=', 'g.ID_VEHICLEGROUPS')
            ->join('Cards as c', 'c.VehiclesID', '=', 'v.ID_VEHICLES')
            ->join('COSTCENTERS as e', 'v.CostCenterID', '=', 'e.ID_COSTCENTERS')
            ->select(
                'v.Number', 'v.Description', 'v.LicensePlate',
                'g.Description as GroupName', 'c.PAN',
                'e.Description as CostCenter'
            )
            ->whereNotNull('v.Number')
            ->whereNotNull('v.Description')
            ->whereNotNull('v.LicensePlate')
            ->whereNotNull('g.Description')
            ->whereNotNull('c.PAN')
            ->whereNotNull('e.Description')
            ->paginate(15);

        return view('reports.cards', compact('cards'));
    }

    public function reconciliation()
    {
        $reconciliation = DB::table('stocks')
            ->orderBy('trans_date', 'desc')
            ->limit(365)
            ->get();

        return view('reports.reconciliation', compact('reconciliation'));
    }

    public function mileage()
    {
        $mileageData = DB::select("
            SELECT ca.CardLayoutsID, v.Consumption as target, v.ConsumptionPercentage, 
                   t.ID_Payments, T.VehiclesID, t.transnumber, t.TransDateTime, v.Description, 
                   t.TransArticleDescription, v.LicensePlate, v.Number, t.Mileage, t.TransQuantity,
                   term.Description as termname, t.CardPAN, t.transactionsID, t.TransAmount,
                   (t.Mileage-(SELECT TOP 1 Mileage FROM PAYMENTS P1 WHERE P1.VehiclesID = T.VehiclesID 
                    and P1.CardPAN = T.CardPAN and P1.TRANSDATETIME < T.TRANSDATETIME 
                    and P1.Mileage <> T.Mileage and P1.TransArticleID = T.TransArticleID 
                    AND P1.TransQuantity > 0 ORDER BY P1.TRANSDATETIME DESC)) as driven
            FROM PAYMENTS t 
            JOIN CUSTOMERS c ON c.ID_CUSTOMERS = t.CustomersID 
            JOIN VEHICLES v ON v.ID_VEHICLES = t.VehiclesID 
            INNER JOIN terminals term on term.ID_TERMINALS = t.terminalsID 
            INNER JOIN cards ca on ca.ID_CARDS = t.CARDSID 
            WHERE v.ActMilageType = 'K' and t.transquantity != 0 and v.Consumption != 0 
              and t.TransDateTime > '2024-09-01 00:00' 
              AND t.transdatetime >= DATEADD(DAY, -14, GETDATE()) 
            ORDER BY t.TRANSDATETIME desc
        ");

        $vehicles = Vehicle::where('ID_VEHICLES', '!=', 291)->orderBy('Number')->get();
        $vehicleGroups = DB::table('VEHICLEGROUPS')
            ->whereIn('ID_VEHICLEGROUPS', [20, 21, 22, 23, 24, 25])
            ->get();

        return view('reports.mileage', compact('mileageData', 'vehicles', 'vehicleGroups'));
    }

    public function fuelRatio()
    {
        $fuelRatios = FuelRatio::orderBy('date', 'desc')->get();
        return view('reports.fuel-ratio', compact('fuelRatios'));
    }

    public function daywiseVehicle()
    {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-7 days'));

        $data = DB::select("EXEC GetPaymentsByVehiclePivot ?, ?", [$startDate, $endDate]);

        return view('reports.daywise-vehicle', compact('data', 'startDate', 'endDate'));
    }

    public function daywiseFuelRatio()
    {
        $fuelRatioData = DB::select("
            SELECT fr.date,
                   round(coalesce(p.coal, 0),2) as coal_fuel,
                   round(coalesce(fr.COAL_BISM, 0),2) as coal_prod,
                   case when coalesce(fr.COAL_BISM, 0) > 0 then round(coalesce(p.coal, 0) / coalesce(fr.COAL_BISM, 0), 2) else 0 end as coal_ratio,
                   round(coalesce(p.ob, 0),2) as ob_fuel,
                   round(coalesce(fr.OB_BISM, 0),2) as ob_prod,
                   case when coalesce(fr.OB_BISM, 0) > 0 then round(coalesce(p.ob, 0) / coalesce(fr.OB_BISM, 0), 2) else 0 end as ob_ratio,
                   round(coalesce(p.port, 0),2) as port_fuel,
                   round(coalesce(fr.PORT_BISM, 0),2) as port_prod,
                   case when coalesce(fr.PORT_BISM, 0) > 0 then round(coalesce(p.port, 0) / coalesce(fr.PORT_BISM, 0), 2) else 0 end as port_ratio
            FROM fuelratio fr
            LEFT JOIN (
                SELECT cast(p.transdatetime as date) as transactiondate,
                       sum(case when vg.description = 'coal' then p.transquantity else 0 end) as coal,
                       sum(case when vg.description = 'ob' then p.transquantity else 0 end) as ob,
                       sum(case when vg.description = 'port' then p.transquantity else 0 end) as port
                FROM payments p
                JOIN vehicles v on p.vehiclesid = v.id_vehicles
                JOIN vehiclegroups vg on v.vehiclegroupsid = vg.id_vehiclegroups
                WHERE vg.description in ('coal', 'ob', 'port')
                GROUP BY cast(p.transdatetime as date)
            ) p ON fr.date = p.transactiondate
            ORDER BY fr.date DESC
        ");

        return view('reports.daywise-fuel-ratio', compact('fuelRatioData'));
    }
}