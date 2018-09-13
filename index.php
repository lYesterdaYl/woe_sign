<?php
/**
 * Created by PhpStorm.
 * User: Zhiyuan Du
 * Date: 9/13/2018
 * Time: 00:24 PM
 */


$mon_date = date('Y-m-d', strtotime('monday this week'));
$sun_date = date('Y-m-d', strtotime('sunday this week'));

$sat_date = date('Y-m-d', strtotime('wednesday this week'));
$wed_date = date('Y-m-d', strtotime('saturday this week'));

include('../../../../system_files/inc.php');
include('include/function.php');

$ip = get_ip();

$check_ip = "select * from woe_info where ip = '".$ip."'";
$result = $mysqli->query($check_ip);
$row = $result->fetch_array();

if($row['ip'] == ''){
    $location = file_get_contents("http://api.ipstack.com/".$ip."?access_key=".$access_key);
    $location = json_decode($location, TRUE);
}
else{
    $location['country_name'] = $row['country'];
    $location['region_name'] = $row['region'];
    $location['city'] = $row['city'];
    $location['zip'] = $row['zip'];

    $d = explode(",", $row['degree']);

    $location['latitude'] = $d[0];
    $location['longitude'] = $d[1];
}

$country = $location['country_name'];
$region = $location['region_name'];
$city = $location['city'];
$zip = $location['zip'];
$degree = $location['latitude'].", ".$location['longitude'];
$browser = get_browser();
$os = get_os();

if($ip != "192.168.1.1"){
    $sql = "insert into woe_info (ip, browser, os, country, region, city, zip, degree) VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssss", $ip, $browser, $os, $country, $region, $city, $zip, $degree);
    $stmt->execute();
}

$name = isset($_REQUEST['name'])?$_REQUEST['name']:'';
$discord_name = isset($_REQUEST['dis_name'])?$_REQUEST['dis_name']:'';
$class = isset($_REQUEST['class'])?$_REQUEST['class']:'';
$wed = isset($_REQUEST['wed'])?$_REQUEST['wed']:'';
$sat = isset($_REQUEST['sat'])?$_REQUEST['sat']:'';

$wed = ($wed == true) ? 1:0;
$sat = ($sat == true) ? 1:0;

if($name != '' || $class != '' || $wed != '' || $sat != ''){
    $find_ip = "select ip from woe_sign_up where ip = '".$ip."' and insert_time >= '".$mon_date."' and insert_time <= '".$sun_date."'";
    $result = $mysqli->query($find_ip);
    $row = $result->fetch_array();

    if($row['ip'] == ''){
        $sql = "insert into woe_sign_up (name, class, wed, sat,ip,discord_name) VALUES (?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssiiss", $name, $class, $wed, $sat,$ip,$discord_name);
        $stmt->execute();
    }
    else{
        $sql = "update woe_sign_up set wed = ?, sat = ?, name = ?, discord_name = ? where ip = ? and insert_time >= ? and insert_time <= ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssss", $wed, $sat, $name, $discord_name, $ip, $mon_date, $sun_date);
        $stmt->execute();


    }

}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ChouShaBi WoE Sign-up</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <link rel="stylesheet" type="text/css" href="css/responsive.css">
    </head>
    <body>
        <header class="header">
            <div class="header_inner">
                <img class="header_logo" src="img/logo.jpg" alt="ChouShaBi Logo">
                <div class="header_info_container">
                    <div class="header_title">
                        ChouShaBi
                    </div>
                    <div class="header_job">
                        WoE Sign-up<br>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <section class="content">
                <form class="sign_up_form" method="post">
                    <section class="char_name">
                        <h4>Character Name:</h4>
                        <input name="name">
                    </section>

                    <section class="discord_name">
                        <h4>Discord Name:</h4>
                        <input name="dis_name">
                    </section>

                    <section class="class">
                        <h4>Class:</h4>
                        <select name="class">
                            <option>Rune Knight</option>
                            <option>Guillotine Cross</option>
                            <option>Arch Bishop</option>
                            <option>Ranger</option>
                            <option>Warlock</option>
                            <option>Mechanic</option>
                            <option>Royal Guard</option>
                            <option>Shadow Chaser</option>
                            <option>Sura</option>
                            <option>Maestro</option>
                            <option>Wanderer</option>
                            <option>Sorcerer</option>
                            <option>Geneticist</option>
                        </select>

                    </section>

                    <section class="woe_time">
                        <h4>WoE Time</h4>
                        <input type="checkbox" name="wed"> <?php echo $wed_date." Wednesday"; ?>
                        <input type="checkbox" name="sat"> <?php echo $sat_date." Saturday"; ?>
                    </section>

                    <button type="submit" class="submit">submit</button>
                </form>
            </section>


        </main>






        <script src="js/app.js"></script>

    </body>
</html>