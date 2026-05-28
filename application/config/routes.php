<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['dashboard'] = 'dashboard/index';
$route['code'] = 'code/index';
$route['item'] = 'item/index';
$route['material'] = 'material/index';
$route['customer'] = 'customer/index';

// RECEIVE LB
$route['receive-lb'] = 'receiveLb/index';
$route['receive-lb/print_pdf'] = 'receiveLb/print_pdf';
$route['receive-lb/edit'] = 'receiveLb/edit';
$route['receive-lb/load_data'] = 'ReceiveLb/load_data';
$route['receive-lb/get_supplier'] = 'ReceiveLb/get_supplier';
$route['receive-lb/get_plant_by_user'] = 'ReceiveLb/get_plant_by_user';
$route['receive-lb/create'] = 'receiveLb/create';
$route['receive-lb/update'] = 'receiveLb/update';
$route['receive-lb/remove'] = 'receiveLb/remove';

// STOCK ACTUAL
$route['stock-actual'] = 'StockActual/index';
$route['stock-actual/print_pdf'] = 'StockActual/print_pdf';
$route['stock-actual/edit'] = 'StockActual/edit';
$route['stock-actual/load_data'] = 'StockActual/load_data';
$route['stock-actual/get-supplier'] = 'StockActual/get_supplier';
$route['stock-actual/get_all_item'] = 'StockActual/get_all_item';
$route['stock-actual/get_stock_for_edit'] = 'StockActual/get_stock_for_edit';
$route['stock-actual/get_plant_by_user'] = 'StockActual/get_plant_by_user';
$route['stock-actual/create'] = 'StockActual/create';
$route['stock-actual/update'] = 'StockActual/update';
$route['stock-actual/remove'] = 'StockActual/remove';

// MATERIAL BALANCE
$route['material-balance'] = 'MaterialBalance/index';
$route['material-balance/edit'] = 'MaterialBalance/edit';
$route['material-balance/load_data'] = 'MaterialBalance/load_data';
$route['material-balance/get-supplier'] = 'MaterialBalance/get_supplier';
$route['material-balance/create'] = 'MaterialBalance/create';
$route['material-balance/update'] = 'MaterialBalance/update';
$route['material-balance/remove'] = 'MaterialBalance/remove';

// ITEM BALANCE
$route['item-balance'] = 'ItemBalance/index';
$route['item-balance/edit'] = 'ItemBalance/edit';
$route['item-balance/load_data'] = 'ItemBalance/load_data';
$route['item-balance/get-supplier'] = 'ItemBalance/get_supplier';
$route['item-balance/create'] = 'ItemBalance/create';
$route['item-balance/update'] = 'ItemBalance/update';
$route['item-balance/remove'] = 'ItemBalance/remove';

// CASH IN
$route['cash-in/get-customer'] = 'CashIn/get_customer';
$route['cash-in/get-rekening'] = 'CashIn/get_rekening';
$route['cash-in'] = 'CashIn/index';
$route['cash-in/print_pdf'] = 'CashIn/print_pdf';
$route['cash-in/edit'] = 'CashIn/edit';
$route['cash-in/load_data'] = 'CashIn/load_data';
$route['cash-in/get-supplier'] = 'CashIn/get_supplier';
$route['cash-in/get_invoice_tempo'] = 'CashIn/get_invoice_tempo';
$route['cash-in/get_user_plant_select2'] = 'CashIn/get_user_plant_select2';
$route['cash-in/get_customer_deposit'] = 'CashIn/get_customer_deposit';
$route['cash-in/get_invoice_fifo_source'] = 'CashIn/get_invoice_fifo_source';
$route['cash-in/validate_invoice_remain'] = 'CashIn/validate_invoice_remain';
$route['cash-in/create'] = 'CashIn/create';
$route['cash-in/update'] = 'CashIn/update';
$route['cash-in/remove'] = 'CashIn/remove';

// REPORT INVENTORY
$route['report-inventory/load_data'] = 'ReportInventory/load_data';
$route['report-inventory/export_excel_po'] = 'ReportInventory/export_excel_po';
$route['report-inventory/export_pdf_po'] = 'ReportInventory/export_pdf_po';

$route['report-inventory'] = 'ReportInventory/index';

$route['report-inventory/load_receive'] = 'ReportInventory/load_receive';
$route['report-inventory/export_excel_receive'] = 'ReportInventory/export_excel_receive';
$route['report-inventory/export_pdf_receive']   = 'ReportInventory/export_pdf_receive';

$route['report-inventory/load_receive_lb'] = 'ReportInventory/load_receive_lb';
$route['report-inventory/export_excel_receive_lb'] = 'ReportInventory/export_excel_receive_lb';
$route['report-inventory/export_pdf_receive_lb']   = 'ReportInventory/export_pdf_receive_lb';

$route['report-inventory/load_material_balance'] = 'ReportInventory/load_material_balance';
$route['report-inventory/export_excel_material_balance'] = 'ReportInventory/export_excel_material_balance';
$route['report-inventory/export_pdf_material_balance']   = 'ReportInventory/export_pdf_material_balance';

// REPORT PRODUCTION
$route['report-production/load_production'] = 'ReportProduction/load_production';
$route['report-production/export_excel_production'] = 'ReportProduction/export_excel_production';
$route['report-production/export_pdf_production'] = 'ReportProduction/export_pdf_production';

$route['report-production'] = 'ReportProduction/index';

$route['report-production/load_stock_actual'] = 'ReportProduction/load_stock_actual';
$route['report-production/export_excel_stock_actual'] = 'ReportProduction/export_excel_stock_actual';
$route['report-production/export_pdf_stock_actual'] = 'ReportProduction/export_pdf_stock_actual';

$route['report-production/load_item_balance'] = 'ReportProduction/load_item_balance';
$route['report-production/export_excel_item_balance'] = 'ReportProduction/export_excel_item_balance';
$route['report-production/export_pdf_item_balance'] = 'ReportProduction/export_pdf_item_balance';

// REPORT ACCOUNTING
$route['report-accounting/load_cost'] = 'ReportAccounting/load_cost';
$route['report-accounting/export_excel_cost'] = 'ReportAccounting/export_excel_cost';
$route['report-accounting/export_pdf_cost'] = 'ReportAccounting/export_pdf_cost';

$route['report-accounting'] = 'ReportAccounting/index';

$route['report-accounting/load_payment'] = 'ReportAccounting/load_payment';
$route['report-accounting/export_excel_payment'] = 'ReportAccounting/export_excel_payment';
$route['report-accounting/export_pdf_payment'] = 'ReportAccounting/export_pdf_payment';

$route['report-accounting/load_cashin'] = 'ReportAccounting/load_cashin';
$route['report-accounting/export_excel_cashin'] = 'ReportAccounting/export_excel_cashin';
$route['report-accounting/export_pdf_cashin'] = 'ReportAccounting/export_pdf_cashin';

$route['report-accounting/load_ar'] = 'ReportAccounting/load_ar';
$route['report-accounting/load_ar_detail'] = 'ReportAccounting/load_ar_detail';
$route['report-accounting/export_excel_ar'] = 'ReportAccounting/export_excel_ar';
$route['report-accounting/export_pdf_ar'] = 'ReportAccounting/export_pdf_ar';

$route['report-accounting/load_daily_summary'] = 'ReportAccounting/load_daily_summary';
$route['report-accounting/export_daily_excel'] = 'ReportAccounting/export_daily_excel';
$route['report-accounting/export_daily_pdf'] = 'ReportAccounting/export_daily_pdf';

// REPORT SALES
$route['report-sales/load_sales'] = 'ReportSales/load_sales';
$route['report-sales/export_excel_sales'] = 'ReportSales/export_excel_sales';
$route['report-sales/export_pdf_sales'] = 'ReportSales/export_pdf_sales';

$route['report-sales/load_sales_item'] = 'ReportSales/load_sales_item';
$route['report-sales/export_excel_sales_item'] = 'ReportSales/export_excel_sales_item';
$route['report-sales/export_pdf_sales_item'] = 'ReportSales/export_pdf_sales_item';
$route['report-sales/get_items'] = 'ReportSales/get_items';

$route['report-sales'] = 'ReportSales/index';

// REPORT CLOSING COST
$route['report-closing-cost/load_daily_closing_cost'] = 'ReportClosingCost/load_daily_closing_cost';
$route['report-closing-cost/export_excel_daily_closing_cost'] = 'ReportClosingCost/export_excel_daily_closing_cost';
$route['report-closing-cost/export_pdf_daily_closing_cost'] = 'ReportClosingCost/export_pdf_daily_closing_cost';

$route['report-closing-cost'] = 'ReportClosingCost/index';

$route['report-closing-cost/load_monthly_closing_cost'] = 'ReportClosingCost/load_monthly_closing_cost';
$route['report-closing-cost/export_excel_monthly_closing_cost'] = 'ReportClosingCost/export_excel_monthly_closing_cost';
$route['report-closing-cost/export_pdf_monthly_closing_cost'] = 'ReportClosingCost/export_pdf_monthly_closing_cost';

// REPORT CLOSING INVENTORY PRICE
$route['report-closing-inventory-price/load_daily_inventory_price'] = 'ReportClosingInventoryPrice/load_daily_inventory_price';
$route['report-closing-inventory-price/export_excel_daily_inventory_price'] = 'ReportClosingInventoryPrice/export_excel_daily_inventory_price';
$route['report-closing-inventory-price/export_pdf_daily_inventory_price'] = 'ReportClosingInventoryPrice/export_pdf_daily_inventory_price';

$route['report-closing-inventory-price'] = 'ReportClosingInventoryPrice/index';

$route['report-closing-inventory-price/load_monthly_closing_inventory_price'] = 'ReportClosingInventoryPrice/load_monthly_closing_inventory_price';
$route['report-closing-inventory-price/export_excel_monthly_inventory_price'] = 'ReportClosingInventoryPrice/export_excel_monthly_inventory_price';
$route['report-closing-inventory-price/export_pdf_monthly_inventory_price'] = 'ReportClosingInventoryPrice/export_pdf_monthly_inventory_price';

// REPORT CLOSING PL
$route['report-closing-pl/load_daily_closing_pl'] = 'ReportClosingPl/load_daily_closing_pl';
$route['report-closing-pl/export_excel_daily_closing_pl'] = 'ReportClosingPl/export_excel_daily_closing_pl';
$route['report-closing-pl/export_pdf_daily_closing_pl'] = 'ReportClosingPl/export_pdf_daily_closing_pl';

$route['report-closing-pl'] = 'ReportClosingPl/index';

$route['report-closing-pl/load_monthly_closing_pl'] = 'ReportClosingPl/load_monthly_closing_pl';
$route['report-closing-pl/export_excel_monthly_closing_pl'] = 'ReportClosingPl/export_excel_monthly_closing_pl';
$route['report-closing-pl/export_pdf_monthly_closing_pl'] = 'ReportClosingPl/export_pdf_monthly_closing_pl';

// REPORT CLOSING SALES PL
$route['report-closing-sales-pl/load_daily_sales_pl'] = 'ReportClosingSalesPl/load_daily_sales_pl';
$route['report-closing-sales-pl/export_excel_daily_sales_pl'] = 'ReportClosingSalesPl/export_excel_daily_sales_pl';
$route['report-closing-sales-pl/export_pdf_daily_sales_pl'] = 'ReportClosingSalesPl/export_pdf_daily_sales_pl';

$route['report-closing-sales-pl'] = 'ReportClosingSalesPl/index';

$route['report-closing-sales-pl/load_monthly_sales_pl'] = 'ReportClosingSalesPl/load_monthly_sales_pl';
$route['report-closing-sales-pl/export_excel_monthly_sales_pl'] = 'ReportClosingSalesPl/export_excel_monthly_sales_pl';
$route['report-closing-sales-pl/export_pdf_monthly_sales_pl'] = 'ReportClosingSalesPl/export_pdf_monthly_sales_pl';

$route['report-closing-sales-pl/load_summary'] = 'ReportClosingSalesPl/load_summary';
$route['report-closing-sales-pl/export_excel_summary'] = 'ReportClosingSalesPl/export_excel_summary';
$route['report-closing-sales-pl/export_pdf_summary'] = 'ReportClosingSalesPl/export_pdf_summary';

// REPORT CLOSING PROCESS 
$route['closing-process'] = 'ReportClosingProcess/index';

$route['closing-process/run'] = 'ReportClosingProcess/run_process';


