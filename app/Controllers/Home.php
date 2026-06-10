<?php

namespace App\Controllers;

use App\Models\Reports\Summary_expenses_categories;
use App\Models\Reports\Summary_payments;
use App\Models\Reports\Summary_sales;
use CodeIgniter\HTTP\RedirectResponse;
use Config\OSPOS;

class Home extends Secure_Controller
{
    public function __construct()
    {
        parent::__construct('home', null, 'home');
    }

    /**
     * @return void
     */
    public function getIndex(): void
    {
        helper(['report', 'locale']);

        $person_id = (int)$this->session->get('person_id');
        $config = config(OSPOS::class)->settings;

        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime('-29 days'));
        $today = date('Y-m-d');

        $sale_inputs = [
            'start_date'  => $start_date,
            'end_date'    => $end_date,
            'sale_type'   => 'complete',
            'location_id' => 'all',
        ];

        $today_inputs = [
            'start_date'  => $today,
            'end_date'    => $today,
            'sale_type'   => 'complete',
            'location_id' => 'all',
        ];

        $kpis = [];
        $charts = [];

        if ($this->employee->has_grant('reports_sales', $person_id)) {
            $summary_sales = model(Summary_sales::class);
            $period_summary = $summary_sales->getSummaryData($sale_inputs);
            $today_summary = $summary_sales->getSummaryData($today_inputs);
            $sales_rows = $summary_sales->getData($sale_inputs);

            $kpis[] = [
                'label' => lang('Reports.revenue'),
                'value' => to_currency($period_summary['total'] ?? 0),
                'hint'  => lang('Common.dashboard_last_30_days'),
            ];
            $kpis[] = [
                'label' => lang('Reports.profit'),
                'value' => to_currency($period_summary['profit'] ?? 0),
                'hint'  => lang('Common.dashboard_last_30_days'),
            ];
            $kpis[] = [
                'label' => lang('Common.dashboard_today_sales'),
                'value' => to_currency($today_summary['total'] ?? 0),
                'hint'  => to_date(strtotime($today)),
            ];

            $sales_labels = [];
            $sales_series = [];
            foreach ($sales_rows as $row) {
                $sale_timestamp = strtotime($row['sale_date']);
                $sales_labels[] = date('M j', $sale_timestamp);
                $sales_series[] = [
                    'meta'  => to_date($sale_timestamp),
                    'value' => $row['total'],
                ];
            }

            $charts[] = [
                'title'         => 'Sales Trend',
                'subtitle'      => 'Daily revenue over the last 30 days',
                'chart_id'      => 'home_sales_chart',
                'chart_var'     => 'homeSalesChart',
                'chart_type'    => 'home/charts/area',
                'labels_1'      => $sales_labels,
                'series_data_1' => $sales_series,
                'show_currency' => true,
                'has_data'      => !empty($sales_series),
                'summary'       => $period_summary,
                'summary_keys'  => ['total', 'profit'],
                'report_url'    => site_url("reports/graphical_summary_sales/$start_date/$end_date/complete/all"),
            ];
        }

        if ($this->employee->has_grant('reports_payments', $person_id)) {
            $summary_payments = model(Summary_payments::class);
            $payment_rows = $summary_payments->getData($sale_inputs);
            $payment_summary = $summary_payments->getSummaryData($sale_inputs);

            $payment_labels = [];
            $payment_series = [];
            foreach ($payment_rows as $row) {
                if ($row['trans_group'] == lang('Reports.trans_payments') && !empty($row['trans_amount'])) {
                    $payment_labels[] = $row['trans_type'];
                    $payment_series[] = [
                        'meta'  => $row['trans_type'] . ' ' . round($row['trans_amount'] / max($payment_summary['total'], 1) * 100, 2) . '%',
                        'value' => $row['trans_amount'],
                    ];
                }
            }

            if (!empty($payment_series)) {
                $charts[] = [
                    'title'         => 'Payment Methods',
                    'subtitle'      => 'How customers paid during this period',
                    'chart_id'      => 'home_payments_chart',
                    'chart_var'     => 'homePaymentsChart',
                    'chart_type'    => 'home/charts/hbar',
                    'labels_1'      => $payment_labels,
                    'series_data_1' => $payment_series,
                    'show_currency' => true,
                    'has_data'      => true,
                    'summary'       => $payment_summary,
                    'summary_keys'  => ['total'],
                    'report_url'    => site_url("reports/graphical_summary_payments/$start_date/$end_date/complete/all"),
                ];
            }
        }

        if ($this->employee->has_grant('reports_expenses_categories', $person_id)) {
            $expense_inputs = [
                'start_date' => $start_date,
                'end_date'   => $end_date,
                'sale_type'  => 'complete',
            ];

            $summary_expenses = model(Summary_expenses_categories::class);
            $expense_summary = $summary_expenses->getSummaryData($expense_inputs);

            $kpis[] = [
                'label' => lang('Common.dashboard_expenses'),
                'value' => to_currency($expense_summary['expenses_total_amount'] ?? 0),
                'hint'  => lang('Common.dashboard_last_30_days'),
            ];
        }

        $data = [
            'config'       => $config,
            'kpis'         => $kpis,
            'charts'       => $charts,
            'period_label' => lang('Common.dashboard_period', [to_date(strtotime($start_date)), to_date(strtotime($end_date))]),
        ];

        echo view('home/home', $data);
    }

    /**
     * Logs the currently logged in employee out of the system.  Used in app/Views/partial/header.php
     *
     * @return RedirectResponse
     * @noinspection PhpUnused
     */
    public function getLogout(): RedirectResponse
    {
        $this->employee->logout();
        return redirect()->to('login');
    }

    /**
     * Load "change employee password" form
     *
     * @noinspection PhpUnused
     */
    public function getChangePassword(int $employee_id = -1): void    // TODO: Replace -1 with a constant
    {
        $person_info = $this->employee->get_info($employee_id);
        foreach (get_object_vars($person_info) as $property => $value) {
            $person_info->$property = $value;
        }
        $data['person_info'] = $person_info;

        echo view('home/form_change_password', $data);
    }

    /**
     * Change employee password
     */
    public function postSave(int $employee_id = -1): void    // TODO: Replace -1 with a constant
    {
        if (!empty($this->request->getPost('current_password')) && $employee_id != -1) {
            if ($this->employee->check_password($this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS), $this->request->getPost('current_password'))) {
                $employee_data = [
                    'username'     => $this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                    'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'hash_version' => 2
                ];

                if ($this->employee->change_password($employee_data, $employee_id) && strlen($employee_data['password']) >= 8) {
                    echo json_encode([
                        'success' => true,
                        'message' => lang('Employees.successful_change_password'),
                        'id'      => $employee_id
                    ]);
                } else { // Failure    // TODO: Replace -1 with constant
                    echo json_encode([
                        'success' => false,
                        'message' => lang('Employees.unsuccessful_change_password'),
                        'id'      => -1
                    ]);
                }
            } else {    // TODO: Replace -1 with constant
                echo json_encode([
                    'success' => false,
                    'message' => lang('Employees.current_password_invalid'),
                    'id'      => -1
                ]);
            }
        } else {    // TODO: Replace -1 with constant
            echo json_encode([
                'success' => false,
                'message' => lang('Employees.current_password_invalid'),
                'id'      => -1
            ]);
        }
    }
}
