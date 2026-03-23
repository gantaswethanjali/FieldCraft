<?php
session_start();

// Must be POST
if($_SERVER['REQUEST_METHOD']!=='POST'){
    $_SESSION['error']='Please submit the form to continue.';
    header('Location: field_plan.php');
    exit;
}

// Valid services
$valid_services=[
    "Lawn Mowing (Regular Cutting)","Edging & Hedge Trimming","Weed & Pest Control","Aeration","Fertilising",
    "Overseeding","Top Dressing","Scarification","Watering / Irrigation",
    "Line Marking","Seasonal Renovation","Match Preparation",
    "Seasonal Grass Management","Pest, Disease & Weed Monitoring"
];

// Selected services
$selected_services=$_POST['services']??[];
$selected_services=array_values(array_intersect(array_map('trim',$selected_services),$valid_services));

// Selected dates (valid future only)
$dates_raw=$_POST['service_dates']??[];
$today=date('Y-m-d');
$selected_dates=[];
foreach($dates_raw as $d){
    $d=trim($d);
    if($d!=='' && $d>=$today) $selected_dates[]=$d;
}

// Validate at least one service + one date
if(empty($selected_services) || empty($selected_dates)){
    $_SESSION['error']="⚠️ Please select at least one service and one valid date.";
    header('Location: field_plan.php');
    exit;
}

// Save to session
$_SESSION['one_time_selected_services']=$selected_services;
$_SESSION['one_time_selected_dates']=$selected_dates;

// Redirect to confirmation
header('Location: confirmation.php');
exit;
