"""
Convert B2B Hardware Catalog Excel sheets to CSVs
with column names that ImportAdditionalProductListCommand expects.
"""
import csv, os, sys
import openpyxl

sys.stdout.reconfigure(encoding='utf-8', errors='replace')

EXCEL_PATH = os.path.join(os.path.dirname(__file__), "B2B_Hardware_Catalog_2000SKUs_Full (1).xlsx")
OUT_DIR    = os.path.join(os.path.dirname(__file__), "excel-import-csvs")
os.makedirs(OUT_DIR, exist_ok=True)

SHEETS = ['Laptops','Desktops','Monitors','Networking','Servers',
          'Memory','Peripherals','Docking','Power','VideoConf',
          'Printers','Cables','Security','Tablets']

# Which source column holds the "model" text per sheet
MODEL_COL = {
    'Laptops':    'Model (Config)',
    'Desktops':   'Model (Config)',
    'Monitors':   'Model',
    'Networking': 'Model',
    'Servers':    'Model',
    'Memory':     'Model/Part#',
    'Peripherals':'Model/Part#',
    'Docking':    'Model',
    'Power':      'Model',
    'VideoConf':  'Model',
    'Printers':   'Model/Part#',
    'Cables':     'Model',
    'Security':   'Model/Part#',
    'Tablets':    'Model',
}

LINE_COL = {
    'Laptops':    'Product Line',
    'Desktops':   'Product Line',
    'Monitors':   'Series',
    'Networking': 'Type',
    'Servers':    'Line',
    'Memory':     'Type',
    'Peripherals':'Type',
    'Docking':    None,
    'Power':      'Type',
    'VideoConf':  'Type',
    'Printers':   'Line',
    'Cables':     'Type',
    'Security':   'Type',
    'Tablets':    'Line',
}

def clean_price(raw):
    if raw is None:
        return ''
    s = str(raw).replace('$','').replace(',','').strip()
    try:
        return str(float(s))
    except ValueError:
        return ''

wb = openpyxl.load_workbook(EXCEL_PATH, read_only=True)

for sheet_name in SHEETS:
    ws = wb[sheet_name]
    rows = list(ws.iter_rows(values_only=True))

    # Row 0 = title, Row 1 = headers, Row 2+ = data
    headers = [str(h).strip() if h is not None else '' for h in rows[1]]
    col = {h: i for i, h in enumerate(headers)}

    model_key = MODEL_COL[sheet_name]
    line_key  = LINE_COL[sheet_name]

    out_path = os.path.join(OUT_DIR, f"{sheet_name.lower()}.csv")
    written = 0

    with open(out_path, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(['sku','mfg_part_no','product_name','brand','category','base_price'])

        for row in rows[2:]:
            if row is None or all(v is None for v in row):
                continue

            def cell(key):
                idx = col.get(key)
                return str(row[idx]).strip() if idx is not None and row[idx] is not None else ''

            brand    = cell('Brand')
            line_val = cell(line_key) if line_key else ''
            model    = cell(model_key)
            mfg_pn   = cell('Mfr. P/N')
            sku      = cell('SKU#')
            price    = clean_price(cell('Est. Price'))

            parts = [p for p in [brand, line_val, model] if p]
            product_name = ' '.join(parts)

            if not product_name and not mfg_pn:
                continue

            writer.writerow([sku, mfg_pn, product_name, brand, sheet_name, price])
            written += 1

    print(f"  {sheet_name}: {written} rows -> {out_path}")

wb.close()
print("Done.")
