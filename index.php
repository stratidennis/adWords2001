<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico'>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel='stylesheet' href='styles.css'>
    <title>AdWords</title>
</head>

<body>
    <div class='description'>
        <p class='requirement'>A user can select a daily budget for his Adwords campaign. The user can change the budget anytime he wants, including changing the
            budget in the same day. He can also pause the campaign at any time (the budget will be 0).</p>
        <p class='requirement'>The AdWords campaign will generate costs in a random way trough-out the entire day (at most 10 times per day) based on the daily budget
            set by the end-user in the moment the cost generation is initiated, considering the following rules:</p>
        <p class='requirement'>1.The cumulated daily cost can not be greater than two times of what the budget is set in the given moment</p>
        <p class='requirement'>2.The cumulated cost per month can not not be greater than the sum of the maximum budget for each days within the month</p>
        <p class='instructions'>To start the campaign you must input the history of the budget changes in the campaign. The first date that you will enter, will be the
            date when the campaign starts.</p>
        <p class='instructions'>Following the given format, insert the history of your campaign.</p>
    </div>
    <form action='campaign.php' method='post'>
        <div class='history'>
            <textarea class='inputHistory' name='inputHistory'></textarea>
            <div class='inputHistory inputExample'>
                <h3>Format: dd.mm.yyyy- budget(hh:mm);</h3>
                <br>
                <h4>Example:</h4>
                <p>01.01.2019- 7(10:00), 0(11:00), 1(12:00), 6(23:00);</p>
                <p>05.01.2019- 2(10:00);</p>
                <p>06.01.2019- 0(24:00);</p>
                <p>09.02.2019- 1(13:13);</p>
                <p>01.03.2019- 0(12:00), 1(14:00)</p>
            </div>
        </div>

        <?php
        /*Displaying errors if necessary*/
        $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if (strpos($fullUrl, 'error=empty') == true) {
            echo "<p class='error'>You did not fill in any info!</p>";
        } elseif (strpos($fullUrl, 'error=letters') == true) {
            echo "<p class='error'>You can not enter letters!</p>";
        }
        ?>

        <div class='submit'>
            <button type='submit' name='startCampaign'>Submit</button>
        </div>
        <div class='push'></div>
    </form>
</body>

</html>
