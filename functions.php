<?php

function calculate_next_dates($months, $format = "d-m-Y")
{
    $dates = array();
    $startDate = date($format);
    $endDate = date($format, strtotime("+" . $months . "month"));
    $current = strtotime($startDate);
    $endDate = strtotime($endDate);
    $stepVal = "+1 day";
    while ($current <= $endDate) {
        $dates[] = date($format, $current);
        $current = strtotime($stepVal, $current);
    }
    return $dates;
}

function determine_end_date($startDate, $format = "d.m.Y")
{
    $x = strtotime("$startDate +3 months");
    $endDate = date($format, $x);
    return $endDate;
}

function current_budget($budgetsArray, $i)
{
    $x = explode(",", $budgetsArray);
    $budget = explode("(", $x[$i]);
    return floatval($budget[0]); // Returning the budget from the $budget array
}

function create_hours_array($lines, $date, $sw = 1)
{
    $hours = array();
    for ($i = 0; $i < count($lines) && $sw == 1; $i++) {
        $x = explode("-", $lines[$i]);
        if ($x[0] == $date) {
            $sw = 0;
            $y = explode(",", $x[1]);
            foreach ($y as $z) {
                $hours[] = substr($z, -6, -1);
            }
        }
    }
    return $hours;
}

function create_budgets_array($lines, $date, $sw = 1)
{
    $budgets = array();
    for ($i = 0; $i < count($lines) && $sw == 1; $i++) {
        $x = explode("-", $lines[$i]);
        if ($x[0] == $date) {
            $sw = 0;
            $y = explode(",", $x[1]);
            foreach ($y as $z) {
                $u = explode("(", $z);
                $budgets[] = floatval($u[0]);
            }
        }
    }
    return $budgets;
}

function compare_times($hour1, $hour2)
{
    $a = explode(":", $hour1);
    $b = explode(":", $hour2);
    if ($a[0] > $b[0]) {
        return true;
    } elseif ($a[0] == $b[0]) {
        if ($a[1] >= $b[1]) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function create_cost($maxBudgetsSum, $totalMonthlyCost, $budget, $totalDailyCost)
{
    $mul = mt_rand(100, 1000);
    $maxCost = 2 * floatval($budget) - $totalDailyCost;
    if ($maxCost < 0) {
        $maxCost = 0;
    }
    $x = mt_rand(0 * $mul, $maxCost * $mul) / $mul;
    $cost = number_format((float)$x, 2, ".", "");
    $k = 0;
    $sw = 1;
    while ((($cost + $totalDailyCost + $totalMonthlyCost) > $maxBudgetsSum) && ($sw == 1)) {
        if ($k < 7) {
            $x = mt_rand(0 * $mul, $maxCost * $mul) / $mul;
            $cost = number_format((float)$x, 2, ".", "");
        } else {
            $cost = 0;
            $sw = 0;
        }
    }
    return $cost;
}

$compare_hours = function ($x, $y) {
    $x_timestamp = strtotime($x);
    $y_timestamp = strtotime($y);
    return $x_timestamp <=> $y_timestamp;
};
