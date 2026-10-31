<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_report_model extends CI_Model
{
    public function get_cashflow_report($user_id, $from = null, $to = null)
    {
        if ((int) $user_id <= 0) {
            return false;
        }

        // دریافت مجموع درآمد و هزینه هر روز
        $transactions = $this->db->query(
            "SELECT
                t.transaction_date AS report_date,
                SUM(CASE WHEN c.type = 'income' THEN t.amount ELSE 0 END) AS income,
                SUM(CASE WHEN c.type = 'expense' THEN t.amount ELSE 0 END) AS expense
             FROM transactions AS t
             INNER JOIN categories AS c ON c.id = t.category_id
             WHERE t.user_id = ? AND t.deleted_at IS NULL
             GROUP BY t.transaction_date
             ORDER BY t.transaction_date ASC",
            array($user_id)
        );

        if ($transactions === false) {
            return false;
        }

        // دریافت مجموع تغییر موجودی هر روز
        $history = $this->db->query(
            "SELECT
                effective_date AS report_date,
                SUM(amount_delta) AS amount_delta
             FROM balance_history
             WHERE user_id = ?
             GROUP BY effective_date
             ORDER BY effective_date ASC",
            array($user_id)
        );

        if ($history === false) {
            return false;
        }

        $daily = array();

        // ذخیره درآمد و هزینه روزانه
        foreach ($transactions->result() as $row) {
            $daily[$row->report_date] = array(
                'income' => (float) $row->income,
                'expense' => (float) $row->expense,
                'delta' => 0.0
            );
        }

        // اضافه کردن تغییر موجودی به همان تاریخ
        foreach ($history->result() as $row) {
            if (!isset($daily[$row->report_date])) {
                $daily[$row->report_date] = array(
                    'income' => 0.0,
                    'expense' => 0.0,
                    'delta' => 0.0
                );
            }

            $daily[$row->report_date]['delta'] = (float) $row->amount_delta;
        }

        // مرتب کردن تاریخ‌ها از قدیمی به جدید
        ksort($daily, SORT_STRING);

        $labels = array();
        $income = array();
        $expense = array();
        $balance = array();

        $total_income = 0.0;
        $total_expense = 0.0;
        $current_balance = 0.0;

        foreach ($daily as $date => $values) {
            // موجودی از ابتدای تاریخچه محاسبه می‌شود
            $current_balance += $values['delta'];

            if (($from !== null && $date < $from) || ($to !== null && $date > $to)) {
                continue;
            }

            $total_income += $values['income'];
            $total_expense += $values['expense'];

            $labels[] = $date;
            $income[] = $values['income'];
            $expense[] = $values['expense'];
            $balance[] = $current_balance;
        }

        return array(
            'labels' => $labels,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'totals' => array(
                'income' => $total_income,
                'expense' => $total_expense
            )
        );
    }
}
