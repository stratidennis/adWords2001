<?php
require "functions.php";

if (isset($_POST["startCampaign"])) {
    $info = $_POST["inputHistory"]; // Get the history that has been inputed by the user
    if (empty($info)) {
        header("Location: index.php?error=empty");
        exit();
    } elseif (preg_match("/[a-z]/i", $info)) {
        header("Location: index.php?error=letters");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico'>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel='stylesheet' href='styles.css'>
    <title>AdWords - Campaign</title>
</head>

<body>
    <table>
        <tr>
            <th>Date</th>
            <th>Budget</th>
            <th>Costs</th>
        </tr>
        <?php

        if (isset($_POST["startCampaign"])) {

            $format = "d.m.Y";

            $lines = explode(";", $info); // Separating each line
            $dates = array(); // Creating an array for the dates where the budget has been changed
            foreach ($lines as $date) {
                $separateDates = explode("-", $date); // Separating the dates from the budgets and hours
                $dates[] = $separateDates[0]; // Inserting each date into another array
            }

            // Getting the starting date for the campaign and determining the last date of the campaign
            $x = explode("-", $lines[0]);
            $startDate = $x[0];
            $currentBudget = current_budget(end($x), 0); // Getting the first budget of the campaign
            unset($x);
            $endDate = determine_end_date($startDate); // Getting the end date of the campaign

            $currentDate = $startDate;
            $currentDateTimeStamp = strtotime($currentDate);
            $endDateTimeStamp = strtotime($endDate);
            $days = array(); // Initializing array to store all the days of the campaign
            $maxBudgets = array(); // Initializing array to store all the maximum budgets of the campaign
            $costs = array(); // Initializing array to store all the daily costs of the campaign
            $maxBudgetsSum = 0; // Initializing the sum of the maximum budgets per month
            $totalMonthlyCost = 0; // Initializing the sum of the costs per month
            $currentMonth = date("m", strtotime($currentDate)); // Getting the first month in order to be able to know when the month changes
            while ($currentDateTimeStamp <= $endDateTimeStamp) { // Going through each date over a period of three months

                $days[] = $currentDate; // Inserting each date of the campaign in the array

                $maxDailyBudget = $currentBudget; // Initializing the maximum daily budget
                $totalDailyCost = 0; // Initializing the sum of the costs per day

                $numberOfCosts = mt_rand(0, 10); // Deciding the random number of costs per day
                $hours = array();
                for ($i = 0; $i < $numberOfCosts; $i++) { // Picking out $numberOfCosts hours when the costs will be generated
                    $time = new DateTime();
                    $time->setTime(mt_rand(0, 23), mt_rand(0, 59));
                    $hours[] = $time->format('H:i'); // Creating an array with all the hours of the day when a cost will be generated if possible
                }
                usort($hours, $compare_hours); // Sorting the hours array chronologically

                if (count($dates) != 0) {
                    if ($currentDateTimeStamp == strtotime($dates[0])) { // Checking wheter the current date is equal to one of the dates entered in the history log
                        $budgetChangeHours = create_hours_array($lines, $dates[0]); // Getting all the hours in the day when the budget is changed
                        $budgetChanges = create_budgets_array($lines, $dates[0]); // Getting all the budgets from the same day, in the same order
                        if ($maxDailyBudget < intval(max($budgetChanges))) {
                            $maxDailyBudget = intval(max($budgetChanges));
                        }
                        $maxBudgetsSum = $maxBudgetsSum + $maxDailyBudget;
                        $counter = count($budgetChangeHours);
                        for ($j = 0; $j < count($hours); $j++) {
                            // Case when the current budget changes
                            while ($counter != 0 && compare_times($hours[$j], $budgetChangeHours[0])) { // Comparing hours when creating a cost and hours when the budget is changed while there still are hours when the budget may have been changed
                                $currentBudget = intval($budgetChanges[0]);
                                if ($currentBudget != 0) {
                                    $cost = create_cost($maxBudgetsSum, $totalMonthlyCost, $currentBudget, $totalDailyCost); // Creating cost
                                    $totalDailyCost = $totalDailyCost + $cost; // Adding cost to total daily costs
                                }
                                array_shift($budgetChangeHours);
                                array_shift($budgetChanges);
                                $counter--;
                            }
                            if ($currentBudget != 0) { // Case when the current budget does not change
                                $cost = create_cost($maxBudgetsSum, $totalMonthlyCost, $currentBudget, $totalDailyCost); // Creating cost
                                $totalDailyCost = $totalDailyCost + $cost; // Adding cost to total daily costs
                            }
                        }
                        if ($counter != 0) { // Checking if there are any times left when the budget has been changed 
                            $currentBudget = intval(end($budgetChanges)); // If so, the current budget becomes the last changed budget of the day
                        }
                        array_shift($dates);
                    } else {
                        $maxBudgetsSum = $maxBudgetsSum + $maxDailyBudget;
                        if ($currentBudget != 0) {
                            $cost = create_cost($maxBudgetsSum, $totalMonthlyCost, $currentBudget, $totalDailyCost); // Creating cost
                            $totalDailyCost = $totalDailyCost + $cost; // Adding cost to total daily costs
                        }
                    }
                } else {
                    $maxBudgetsSum = $maxBudgetsSum + $maxDailyBudget;
                    if ($currentBudget != 0) {
                        $cost = create_cost($maxBudgetsSum, $totalMonthlyCost, $currentBudget, $totalDailyCost); // Creating cost
                        $totalDailyCost = $totalDailyCost + $cost; // Adding cost to total daily costs
                    }
                }

                $totalMonthlyCost = $totalMonthlyCost + $totalDailyCost;

                $maxBudgets[] = $maxDailyBudget; // Inserting each max daily budget of the campaign in the array
                $costs[] = $totalDailyCost; // Inserting each daily cost of the campaign in the array

                $currentDate = date($format, strtotime("$currentDate +1 day")); // Going to next date
                $currentDateTimeStamp = strtotime($currentDate);

                if ($currentMonth != date("m", strtotime($currentDate))) { // Checking whether the month has changed
                    $dateObj   = DateTime::createFromFormat('!m', $currentMonth);
                    $monthName = $dateObj->format('F');
                    echo "<p>For the month of <b>" . $monthName . "</b> the sum of the maximum budgets is <b>" . $maxBudgetsSum . "</b>, and the total costs are <b>" . $totalMonthlyCost . "</b>.</p><br/>";
                    $maxBudgetsSum = 0;
                    $totalMonthlyCost = 0;
                    $currentMonth = date("m", strtotime($currentDate));
                }
            }

            for ($i = 0; $i < count($days); $i++) {
                echo "<tr><td>" . $days[$i] . "</td><td>" . $maxBudgets[$i] . "</td><td>" . $costs[$i] . "</td></tr>";
            }
        }

        ?>
    </table>
    <div style="text-align:center; margin: -20 auto 100px auto;"><a href='index.php'>Go again</a></div>
</body>

</html>
