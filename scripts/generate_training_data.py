import json
from pathlib import Path

TRAINING_FILE = Path(__file__).with_name('training_data_full.json')

# Table DDL definitions inferred from reference code and dashboard queries.
DDL = {
    'PAYMENTS': '''CREATE TABLE PAYMENTS (
    ID_PAYMENTS INT,
    TransactionsID INT,
    TransDateTime DATETIME,
    TransNumber INT,
    CardPAN NVARCHAR(40),
    TransQuantity FLOAT,
    TransSinglePriceInclSold FLOAT,
    TransAmount FLOAT,
    TransAmountNet FLOAT,
    TransAmountTax FLOAT,
    TransTaxRate FLOAT,
    TransArticleID INT,
    TransArticleCode NVARCHAR(50),
    TransArticleDescription NVARCHAR(100),
    TransDeviceAddress INT,
    TransSubDeviceAddress INT,
    TransStatus NVARCHAR(20),
    TransWasExported BIT,
    TransPollDateTime DATETIME,
    TanksID INT,
    CardsID INT,
    CardCustomerNumber NVARCHAR(50),
    CardNumber NVARCHAR(50),
    CardExtNumber NVARCHAR(50),
    CardSystem NVARCHAR(50),
    CardHolder NVARCHAR(100),
    CardTankNumber NVARCHAR(50),
    CardLimit FLOAT,
    CardOnHand FLOAT,
    CardValidFrom DATETIME,
    CardValidTo DATETIME,
    Number INT,
    TenderCode NVARCHAR(20),
    CurrencySymbol NVARCHAR(10),
    NbrOfNotes INT,
    NotesAmount FLOAT,
    IDGroupCode NVARCHAR(50),
    AdditionalEntry NVARCHAR(50),
    Mileage INT,
    TerminalsID INT,
    TerminalStationCode NVARCHAR(50),
    TerminalNumber NVARCHAR(50),
    CostCentersID INT,
    CostCenterNumber NVARCHAR(50),
    ContractsID INT,
    ContractNumber NVARCHAR(50),
    ContractLimit FLOAT,
    ContractOnHand FLOAT,
    CustomersID INT,
    CustomerNumber NVARCHAR(50),
    CustomerName NVARCHAR(100),
    MandatorsID INT,
    MandatorNumber NVARCHAR(50),
    MandatorDescription NVARCHAR(100),
    EmployeesID INT,
    EmployeeNumber NVARCHAR(50),
    EmployeeName NVARCHAR(100),
    VehiclesID INT,
    VehicleLicensePlate NVARCHAR(50),
    DispenserNumber NVARCHAR(50),
    DispenserDescription NVARCHAR(100),
    NozzleNumber NVARCHAR(50),
    NozzleDescription NVARCHAR(100)
);''',
    'VEHICLES': '''CREATE TABLE VEHICLES (
    ID_VEHICLES INT,
    Number NVARCHAR(50),
    Description NVARCHAR(200),
    LicensePlate NVARCHAR(50),
    Consumption FLOAT,
    ConsumptionPercentage FLOAT,
    VehicleGroupsID INT,
    CostCenterID INT
);''',
    'VEHICLEGROUPS': '''CREATE TABLE VEHICLEGROUPS (
    ID_VEHICLEGROUPS INT,
    Description NVARCHAR(100)
);''',
    'TERMINALS': '''CREATE TABLE TERMINALS (
    ID_TERMINALS INT,
    Description NVARCHAR(100),
    city NVARCHAR(100),
    StationCode NVARCHAR(50)
);''',
    'CARDS': '''CREATE TABLE CARDS (
    ID_CARDS INT,
    PAN NVARCHAR(40),
    CardLayoutsID INT,
    VehiclesID INT
);''',
    'COSTCENTERS': '''CREATE TABLE COSTCENTERS (
    ID_COSTCENTERS INT,
    Description NVARCHAR(150)
);''',
    'CUSTOMERS': '''CREATE TABLE CUSTOMERS (
    ID_CUSTOMERS INT,
    CustomerNumber NVARCHAR(50),
    CustomerName NVARCHAR(100)
);''',
    'STOCKS': '''CREATE TABLE STOCKS (
    id INT,
    trans_date DATE,
    Opening FLOAT,
    TotalReceived FLOAT,
    IssuedToVMS FLOAT,
    IssuedFromFT FLOAT,
    Closing FLOAT
);''',
    'FUELRATIO': '''CREATE TABLE FUELRATIO (
    date DATE,
    coal_bism FLOAT,
    ob_bism FLOAT,
    port_bism FLOAT
);''',
    'TERMINAL_CONFIGURATIONS': '''CREATE TABLE TERMINAL_CONFIGURATIONS (
    id INT,
    operation_type NVARCHAR(50),
    terminal_ids NVARCHAR(MAX)
);''',
    'USER_ACCESS': '''CREATE TABLE USER_ACCESS (
    id INT,
    user_id INT,
    terminal_ids NVARCHAR(MAX)
);'''
}

DOCUMENTATION = {
    'PAYMENTS': '''The PAYMENTS table records every fuel dispensing and refueling transaction.

Important columns:
- TransDateTime: transaction timestamp.
- TransQuantity: fuel quantity dispensed.
- TransAmount: total transaction amount.
- TransAmountNet: amount before tax.
- TransAmountTax: tax amount.
- TransArticleDescription: type of fuel or article.
- CardPAN / CardNumber / CardHolder: fuel card identification.
- CustomerName / CustomerNumber: customer identity.
- TerminalNumber / TerminalStationCode / TerminalsID: pump or station identifier.
- VehiclesID: vehicle reference (join to VEHICLES.ID_VEHICLES to get license plate from VEHICLES.Number).
- Mileage: mileage at refueling time.
- ContractNumber / ContractsID: linked contract.
- CostCenterNumber / CostCentersID: cost center code.
- EmployeeName / EmployeesID: operator.
- AdditionalEntry: manual entry for non-RFID refuels.
''',
    'VEHICLES': '''The VEHICLES table stores vehicle master data used in analytics.

Important columns:
- ID_VEHICLES: primary key, used to join with PAYMENTS.VehiclesID.
- Number: vehicle license plate number (THIS IS THE LICENSE PLATE!).
- Description: vehicle description.
- LicensePlate: alternative license plate field.
- Consumption: target fuel consumption per kilometer or hour.
- ConsumptionPercentage: fuel efficiency target.
- VehicleGroupsID: group classification.
- CostCenterID: cost center assignment.

IMPORTANT: To get vehicle license plate, always JOIN PAYMENTS to VEHICLES and use VEHICLES.Number column.
''',
    'VEHICLEGROUPS': '''The VEHICLEGROUPS table defines vehicle fleet segments.

Important columns:
- Description: group name such as ob, coal, port.
''',
    'TERMINALS': '''The TERMINALS table defines pump and terminal location metadata.

Important columns:
- Description: terminal description.
- city: terminal city or location.
- StationCode: external station code.
''',
    'CARDS': '''The CARDS table stores fuel card metadata.

Important columns:
- PAN: primary account number or card token.
- CardLayoutsID: card type indicator, e.g. RFID or proximity.
- VehiclesID: optional vehicle link.
''',
    'COSTCENTERS': '''The COSTCENTERS table stores business cost center descriptions.

Important columns:
- Description: cost center name.
''',
    'CUSTOMERS': '''The CUSTOMERS table stores customer master data.

Important columns:
- CustomerNumber: unique customer code.
- CustomerName: customer or company name.
''',
    'STOCKS': '''The STOCKS table stores daily fuel stock reconciliation.

Important columns:
- trans_date: reconciliation date.
- Opening: opening stock volume.
- TotalReceived: fuel received that day.
- IssuedToVMS: issued to BAR / VMS.
- IssuedFromFT: issued from fuel terminal.
- Closing: closing stock volume.
''',
    'FUELRATIO': '''The FUELRATIO table stores production volume for fuel ratio calculation.

Important columns:
- date: production date.
- coal_bism: coal production volume.
- ob_bism: overburden or OB production volume.
- port_bism: port-related production volume.
''',
    'TERMINAL_CONFIGURATIONS': '''The TERMINAL_CONFIGURATIONS table holds terminal selection for dashboard categories.

Important columns:
- operation_type: decantation, toploading, or fuel_dispensing.
- terminal_ids: list of terminal IDs assigned to that operation.
''',
    'USER_ACCESS': '''The USER_ACCESS table holds terminal visibility restrictions for a user.

Important columns:
- user_id: reference to the user.
- terminal_ids: allowed terminal IDs for that user.
'''
}

FIXED_EXAMPLES = [
    {
        'question': 'Show the latest 10 transactions.',
        'sql': 'SELECT TOP 10 * FROM PAYMENTS ORDER BY TransDateTime DESC;'
    },
    {
        'question': 'Display total fuel quantity per terminal for March 2025.',
        'sql': "SELECT TerminalNumber, SUM(TransQuantity) AS TotalLiters FROM PAYMENTS WHERE TransDateTime BETWEEN '2025-03-01 00:00' AND '2025-03-31 23:59' GROUP BY TerminalNumber ORDER BY TotalLiters DESC;"
    },
    {
        'question': 'Show total transaction amount by customer for January 2025.',
        'sql': "SELECT CustomerName, SUM(TransAmount) AS TotalAmount FROM PAYMENTS WHERE TransDateTime BETWEEN '2025-01-01 00:00' AND '2025-01-31 23:59' GROUP BY CustomerName ORDER BY TotalAmount DESC;"
    },
    {
        'question': 'Get top 6 vehicles by fuel volume for the selected period.',
        'sql': "SELECT TOP 6 v.Number AS VehicleLicensePlate, ROUND(SUM(p.TransQuantity) / 1000, 2) AS TotalVolume FROM PAYMENTS p JOIN VEHICLES v ON p.VehiclesID = v.ID_VEHICLES WHERE p.TransDateTime BETWEEN '2025-03-01 00:00' AND '2025-03-31 23:59' GROUP BY v.Number ORDER BY TotalVolume DESC;"
    },
    {
        'question': 'Calculate top 6 cost centers by refueling volume',
        'sql': "SELECT TOP 6 UPPER(cc.Description) AS costcenter, ROUND(SUM(t.TransQuantity) / 1000, 2) AS total FROM PAYMENTS t INNER JOIN VEHICLES v ON v.ID_VEHICLES = t.VehiclesID INNER JOIN COSTCENTERS cc ON cc.ID_COSTCENTERS = v.CostCenterID WHERE t.TransQuantity != 0 AND cc.Description IS NOT NULL AND t.TransDateTime BETWEEN '2025-03-01 00:00' AND '2025-03-31 23:59' GROUP BY cc.Description ORDER BY ROUND(SUM(t.TransQuantity), 2) DESC;"
    },
    {
        'question': 'Get terminal RFID and proximity quantities for the last month.',
        'sql': "SELECT TOP 6 t.Description AS Terminal_name, t.city, ROUND(SUM(CASE WHEN c.CardLayoutsID IN (5, 39) THEN p.TransQuantity ELSE 0 END) / 1000, 2) AS RFID_Quantity, ROUND(SUM(CASE WHEN c.CardLayoutsID = 14 THEN p.TransQuantity ELSE 0 END) / 1000, 2) AS Proximity_Quantity FROM PAYMENTS p JOIN CARDS c ON p.CardsID = c.ID_CARDS JOIN TERMINALS t ON p.TerminalsID = t.ID_TERMINALS WHERE p.TransDateTime BETWEEN '2025-03-01 00:00' AND '2025-03-31 23:59' GROUP BY t.Description, t.city ORDER BY t.City;"
    },
    {
        'question': 'Show production ratios for coal, ob, and port in March 2025.',
        'sql': "WITH fuelconsumption AS (SELECT vg.Description, ROUND(SUM(p.TransQuantity), 2) AS total_fuel_consumed FROM PAYMENTS p JOIN VEHICLES v ON p.VehiclesID = v.ID_VEHICLES JOIN VEHICLEGROUPS vg ON v.VehicleGroupsID = vg.ID_VEHICLEGROUPS WHERE vg.Description IN ('ob', 'coal', 'port') AND p.TransDateTime BETWEEN '2025-03-01 00:00' AND '2025-03-31 23:59' GROUP BY vg.Description), fuelproduction AS (SELECT 'coal' AS Description, SUM(COALESCE(coal_bism, 0)) AS total_production FROM FUELRATIO WHERE date BETWEEN '2025-03-01' AND '2025-03-31' UNION ALL SELECT 'ob' AS Description, SUM(COALESCE(ob_bism, 0)) AS total_production FROM FUELRATIO WHERE date BETWEEN '2025-03-01' AND '2025-03-31' UNION ALL SELECT 'port' AS Description, SUM(COALESCE(port_bism, 0)) AS total_production FROM FUELRATIO WHERE date BETWEEN '2025-03-01' AND '2025-03-31'), ratios AS (SELECT fc.Description, CASE WHEN fp.total_production = 0 THEN NULL ELSE ROUND(fc.total_fuel_consumed / fp.total_production, 2) END AS fuel_ratio FROM fuelconsumption fc JOIN fuelproduction fp ON fc.Description = fp.Description) SELECT * FROM ratios;"
    },
    {
        'question': 'Get stock reconciliation for the first week of March 2025.',
        'sql': "SELECT trans_date, Opening, TotalReceived, IssuedToVMS, IssuedFromFT, Closing FROM STOCKS WHERE trans_date BETWEEN '2025-03-01' AND '2025-03-07' ORDER BY trans_date;"
    },
    {
        'question': 'Get fuel card details for gear cards and vehicle-linked cards.',
        'sql': "SELECT ID_CARDS, PAN, CardLayoutsID, VehiclesID FROM CARDS ORDER BY ID_CARDS DESC;"
    }
]

TERMINAL_QUERIES = [
    ('FT-01', '1'),
    ('FT-02', '2'),
    ('FT-03', '3'),
    ('FT-04', '4'),
    ('FT-05', '5'),
    ('FT-06', '6'),
    ('FT-07', '7'),
    ('FT-08', '8')
]
CUSTOMERS = [
    ('PT BAR', 12),
    ('PT MINERAL', 15),
    ('ABC MINING', 8),
    ('PT ENERGI', 21),
    ('PT COALPLUS', 22),
    ('PT TRANSPORT', 23),
    ('PT LOGISTIK', 24),
    ('PT DISTRIBUSI', 25)
]
VEHICLES = [
    ('B1234CD', 101),
    ('A9876XY', 102),
    ('K1023ZZ', 103),
    ('D5566EF', 104),
    ('F7788GH', 105),
    ('L3344MN', 106),
    ('P1122QR', 107),
    ('S9900TU', 108),
    ('Z6677WX', 109),
    ('J5566KL', 110)
]
CONTRACTS = [
    ('C-2025-001', 201),
    ('C-2025-002', 202),
    ('C-2025-003', 203),
    ('C-2025-004', 204),
    ('C-2025-005', 205),
    ('C-2025-006', 206),
    ('C-2025-007', 207),
    ('C-2025-008', 208)
]
PERIODS = [
    ('2025-01-01 00:00', '2025-01-31 23:59'),
    ('2025-02-01 00:00', '2025-02-28 23:59'),
    ('2025-03-01 00:00', '2025-03-31 23:59'),
    ('2025-04-01 00:00', '2025-04-30 23:59'),
    ('2025-05-01 00:00', '2025-05-31 23:59'),
    ('2025-06-01 00:00', '2025-06-30 23:59')
]

examples = list(FIXED_EXAMPLES)

# Add terminal and period combinations.
for term_name, term_id in TERMINAL_QUERIES:
    for start, end in PERIODS:
        examples.append({
            'question': f"Show total transaction volume at terminal {term_name} between {start[:10]} and {end[:10]}.",
            'sql': f"SELECT SUM(TransQuantity) AS TotalLiters FROM PAYMENTS WHERE TerminalNumber = '{term_name}' AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"Count transactions at terminal {term_name} for the period {start[:10]} to {end[:10]}.",
            'sql': f"SELECT COUNT(*) AS TotalTransactions FROM PAYMENTS WHERE TerminalNumber = '{term_name}' AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"Show total transaction amount and number of transactions at terminal {term_name} between {start[:10]} and {end[:10]}.",
            'sql': f"SELECT SUM(TransAmount) AS TotalAmount, COUNT(*) AS TotalTransactions FROM PAYMENTS WHERE TerminalNumber = '{term_name}' AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })

# Add customer and period combinations.
for customer_name, customer_id in CUSTOMERS:
    for start, end in PERIODS:
        examples.append({
            'question': f"Show total amount and quantity for customer {customer_name} between {start[:10]} and {end[:10]}.",
            'sql': f"SELECT SUM(TransAmount) AS TotalAmount, SUM(TransQuantity) AS TotalLiters FROM PAYMENTS WHERE CustomersID = {customer_id} AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"Show the monthly transaction count for {customer_name} in the period {start[:10]} to {end[:10]}.",
            'sql': f"SELECT COUNT(*) AS TotalTransactions FROM PAYMENTS WHERE CustomersID = {customer_id} AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"List transactions for customer {customer_name} during {start[:10]} to {end[:10]}.",
            'sql': f"SELECT * FROM PAYMENTS WHERE CustomersID = {customer_id} AND TransDateTime BETWEEN '{start}' AND '{end}' ORDER BY TransDateTime DESC;"
        })

# Add vehicle and period combinations.
for vehicle_plate, vehicle_id in VEHICLES:
    for start, end in PERIODS:
        examples.append({
            'question': f"Show total fuel volume for vehicle {vehicle_plate} between {start[:10]} and {end[:10]}.",
            'sql': f"SELECT SUM(p.TransQuantity) AS TotalLiters FROM PAYMENTS p JOIN VEHICLES v ON p.VehiclesID = v.ID_VEHICLES WHERE v.Number = '{vehicle_plate}' AND p.TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"Show average transaction quantity for vehicle {vehicle_plate} in the period {start[:10]} to {end[:10]}.",
            'sql': f"SELECT AVG(p.TransQuantity) AS AvgQuantity FROM PAYMENTS p JOIN VEHICLES v ON p.VehiclesID = v.ID_VEHICLES WHERE v.Number = '{vehicle_plate}' AND p.TransDateTime BETWEEN '{start}' AND '{end}' AND p.TransQuantity > 0;"
        })
        examples.append({
            'question': f"List the last 5 fuel transactions for vehicle {vehicle_plate}.",
            'sql': f"SELECT TOP 5 p.* FROM PAYMENTS p JOIN VEHICLES v ON p.VehiclesID = v.ID_VEHICLES WHERE v.Number = '{vehicle_plate}' ORDER BY p.TransDateTime DESC;"
        })

# Add contract and period combinations.
for contract_num, contract_id in CONTRACTS:
    for start, end in PERIODS:
        examples.append({
            'question': f"Show total fuel usage under contract {contract_num} for {start[:7]}.",
            'sql': f"SELECT SUM(TransQuantity) AS TotalLiters FROM PAYMENTS WHERE ContractsID = {contract_id} AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"List payments linked to contract {contract_num} for the period {start[:10]} to {end[:10]}.",
            'sql': f"SELECT * FROM PAYMENTS WHERE ContractNumber = '{contract_num}' AND TransDateTime BETWEEN '{start}' AND '{end}' ORDER BY TransDateTime DESC;"
        })

# Add more named entity queries for employees, card holders, and customers.
example_templates = [
    ("operator {name}", "EmployeeName"),
    ("card holder {name}", "CardHolder"),
    ("customer {name}", "CustomerName"),
]

names = [
    'John', 'Maria', 'Dimas', 'Siti', 'Rudi', 'Aulia', 'Tono', 'Agus', 'Fitri', 'Budi',
    'Rina', 'Dewi', 'Nina', 'Hendra', 'Irwan', 'Yuni', 'Mira', 'Siska', 'Rafi', 'Wulan'
]
for prefix, field in example_templates:
    for name in names:
        examples.append({
            'question': f"Show all transactions for {prefix.format(name=name)}.",
            'sql': f"SELECT * FROM PAYMENTS WHERE {field} LIKE '%{name}%' ORDER BY TransDateTime DESC;"
        })
        examples.append({
            'question': f"Count transactions for {prefix.format(name=name)}.",
            'sql': f"SELECT COUNT(*) AS CountTransactions FROM PAYMENTS WHERE {field} LIKE '%{name}%';"
        })
        examples.append({
            'question': f"Show total fuel quantity for {prefix.format(name=name)}.",
            'sql': f"SELECT SUM(TransQuantity) AS TotalLiters FROM PAYMENTS WHERE {field} LIKE '%{name}%';"
        })

card_layouts = [
    ('RFID', "CardLayoutsID IN (5, 39)"),
    ('proximity', 'CardLayoutsID = 14')
]
for card_type, condition in card_layouts:
    for start, end in PERIODS:
        examples.append({
            'question': f"Show total fuel quantity dispensed using {card_type} cards between {start[:10]} and {end[:10]}.",
            'sql': f"SELECT SUM(TransQuantity) AS TotalLiters FROM PAYMENTS WHERE {condition} AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"Count {card_type} card transactions in {start[:7]}.",
            'sql': f"SELECT COUNT(*) AS TotalTransactions FROM PAYMENTS WHERE {condition} AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })
        examples.append({
            'question': f"Show transaction amount for {card_type} cards during {start[:10]} to {end[:10]}.",
            'sql': f"SELECT SUM(TransAmount) AS TotalAmount FROM PAYMENTS WHERE {condition} AND TransDateTime BETWEEN '{start}' AND '{end}';"
        })

# Add table metadata and configuration queries.
examples.append({
    'question': 'Show terminal configuration IDs for fuel dispensing.',
    'sql': "SELECT terminal_ids FROM TERMINAL_CONFIGURATIONS WHERE operation_type = 'fuel_dispensing';"
})
examples.append({
    'question': 'Show terminal configuration IDs for toploading.',
    'sql': "SELECT terminal_ids FROM TERMINAL_CONFIGURATIONS WHERE operation_type = 'toploading';"
})
examples.append({
    'question': 'Show terminal configuration IDs for decantation.',
    'sql': "SELECT terminal_ids FROM TERMINAL_CONFIGURATIONS WHERE operation_type = 'decantation';"
})
examples.append({
    'question': 'Show all user terminal access restrictions for a user.',
    'sql': 'SELECT * FROM USER_ACCESS WHERE user_id = 1;'
})
examples.append({
    'question': 'Show all terminals and their cities.',
    'sql': 'SELECT ID_TERMINALS, Description, city, StationCode FROM TERMINALS ORDER BY ID_TERMINALS;'
})
examples.append({
    'question': 'Show all vehicle groups and descriptions.',
    'sql': 'SELECT ID_VEHICLEGROUPS, Description FROM VEHICLEGROUPS ORDER BY ID_VEHICLEGROUPS;'
})
examples.append({
    'question': 'List all customers with ID and name.',
    'sql': 'SELECT ID_CUSTOMERS, CustomerNumber, CustomerName FROM CUSTOMERS ORDER BY CustomerName;'
})
examples.append({
    'question': 'Show average consumption target by vehicle group.',
    'sql': "SELECT vg.Description, AVG(v.Consumption) AS AvgConsumption FROM VEHICLES v JOIN VEHICLEGROUPS vg ON v.VehicleGroupsID = vg.ID_VEHICLEGROUPS GROUP BY vg.Description ORDER BY AvgConsumption DESC;"
})
examples.append({
    'question': 'Show stock opening, received and closing quantities for each day in March 2025.',
    'sql': "SELECT trans_date, Opening, TotalReceived, IssuedToVMS, IssuedFromFT, Closing FROM STOCKS WHERE trans_date BETWEEN '2025-03-01' AND '2025-03-31' ORDER BY trans_date;"
})
examples.append({
    'question': 'Show monthly fuel production summary for coal, ob and port.',
    'sql': "SELECT date, coal_bism, ob_bism, port_bism FROM FUELRATIO WHERE date BETWEEN '2025-03-01' AND '2025-03-31' ORDER BY date;"
})

# Add direct table preview examples.
examples.append({
    'question': 'Show the first 50 rows from the PAYMENTS table.',
    'sql': 'SELECT TOP 50 * FROM PAYMENTS ORDER BY TransDateTime DESC;'
})
examples.append({
    'question': 'Show the first 50 rows from the VEHICLES table.',
    'sql': 'SELECT TOP 50 * FROM VEHICLES ORDER BY ID_VEHICLES DESC;'
})
examples.append({
    'question': 'Show the first 50 rows from the TERMINALS table.',
    'sql': 'SELECT TOP 50 * FROM TERMINALS ORDER BY ID_TERMINALS DESC;'
})
examples.append({
    'question': 'Show the first 50 rows from the CARDS table.',
    'sql': 'SELECT TOP 50 * FROM CARDS ORDER BY ID_CARDS DESC;'
})
examples.append({
    'question': 'Show the first 50 rows from the FUELRATIO table.',
    'sql': 'SELECT TOP 50 * FROM FUELRATIO ORDER BY date DESC;'
})
examples.append({
    'question': 'Show the first 50 rows from the STOCKS table.',
    'sql': 'SELECT TOP 50 * FROM STOCKS ORDER BY trans_date DESC;'
})

# Add more analytics query templates.
for start, end in PERIODS:
    examples.append({
        'question': f"Show terminal fuel volume and transaction count in the period {start[:10]} to {end[:10]}.",
        'sql': f"SELECT TerminalNumber, SUM(TransQuantity) AS TotalLiters, COUNT(*) AS TransactionCount FROM PAYMENTS WHERE TransDateTime BETWEEN '{start}' AND '{end}' GROUP BY TerminalNumber ORDER BY TotalLiters DESC;"
    })
    examples.append({
        'question': f"Show top 10 customers by total amount between {start[:10]} and {end[:10]}.",
        'sql': f"SELECT TOP 10 CustomerName, SUM(TransAmount) AS TotalAmount FROM PAYMENTS WHERE TransDateTime BETWEEN '{start}' AND '{end}' GROUP BY CustomerName ORDER BY TotalAmount DESC;"
    })
    examples.append({
        'question': f"Show top 10 vehicles by liters dispensed between {start[:10]} and {end[:10]}.",
        'sql': f"SELECT TOP 10 v.Number AS VehicleLicensePlate, SUM(p.TransQuantity) AS TotalLiters FROM PAYMENTS p JOIN VEHICLES v ON p.VehiclesID = v.ID_VEHICLES WHERE p.TransDateTime BETWEEN '{start}' AND '{end}' GROUP BY v.Number ORDER BY TotalLiters DESC;"
    })

examples.append({
    'question': 'Show total amount and quantity by contract number.',
    'sql': 'SELECT ContractNumber, SUM(TransAmount) AS TotalAmount, SUM(TransQuantity) AS TotalLiters FROM PAYMENTS GROUP BY ContractNumber ORDER BY TotalAmount DESC;'
})
examples.append({
    'question': 'Show total fuel by customer and terminal.',
    'sql': 'SELECT CustomerName, TerminalNumber, SUM(TransQuantity) AS TotalLiters FROM PAYMENTS GROUP BY CustomerName, TerminalNumber ORDER BY CustomerName, TotalLiters DESC;'
})
examples.append({
    'question': 'Show average transaction amount per terminal.',
    'sql': 'SELECT TerminalNumber, AVG(TransAmount) AS AvgAmount FROM PAYMENTS GROUP BY TerminalNumber ORDER BY AvgAmount DESC;'
})
examples.append({
    'question': 'Show daily stock closing values for April 2025.',
    'sql': "SELECT trans_date, Closing FROM STOCKS WHERE trans_date BETWEEN '2025-04-01' AND '2025-04-30' ORDER BY trans_date;"
})
examples.append({
    'question': 'Show the fuel production values for May 2025.',
    'sql': "SELECT date, coal_bism, ob_bism, port_bism FROM FUELRATIO WHERE date BETWEEN '2025-05-01' AND '2025-05-31' ORDER BY date;"
})

# Add manual summary/metadata examples for documentation training.
examples.append({
    'question': 'Which table contains fuel dispensing and refueling transaction records?',
    'sql': 'SELECT TOP 1 "PAYMENTS" AS TableName;'
})
examples.append({
    'question': 'Which table contains vehicle group descriptions?',
    'sql': 'SELECT TOP 1 "VEHICLEGROUPS" AS TableName;'
})
examples.append({
    'question': 'Which table contains daily stock reconciliation?',
    'sql': 'SELECT TOP 1 "STOCKS" AS TableName;'
})

# Ensure examples are unique enough
unique_examples = []
seen = set()
for item in examples:
    key = (item['question'], item['sql'])
    if key not in seen:
        seen.add(key)
        unique_examples.append(item)

# Remove duplicates and keep order
examples = unique_examples

payload = {
    'ddls': DDL,
    'documentation': DOCUMENTATION,
    'examples': examples
}

with open(TRAINING_FILE, 'w', encoding='utf-8') as f:
    json.dump(payload, f, indent=2, ensure_ascii=False)

print(f'Wrote {len(examples)} training examples to {TRAINING_FILE}')

# Ensure examples are unique enough
unique_examples = []
seen = set()
for item in examples:
    key = (item['question'], item['sql'])
    if key not in seen:
        seen.add(key)
        unique_examples.append(item)

# Remove duplicates and keep order
examples = unique_examples

payload = {
    'ddls': DDL,
    'documentation': DOCUMENTATION,
    'examples': examples
}

with open(TRAINING_FILE, 'w', encoding='utf-8') as f:
    json.dump(payload, f, indent=2, ensure_ascii=False)

print(f'Wrote {len(examples)} training examples to {TRAINING_FILE}')
