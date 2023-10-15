<?php

$csv = array_map("str_getcsv", file("school_fsm.csv",FILE_SKIP_EMPTY_LINES));
$keys = array_shift($csv);
foreach ($csv as $i=>$row) {
    $csv[$i] = array_combine($keys, $row);
}

function array_sort_by_column(&$arr, $col) {
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, SORT_ASC, $arr);
}

function searchForId($search, $array) {
    foreach ($array as $key => $val) {
        if (substr(strtolower($val['school_name']), 0, strlen($search) ) === strtolower($search) || substr(strtolower($val['la_name']), 0, strlen($search) ) === strtolower($search)) {
            if (count($results) < 10) {
                $results[] = $val;
            } else {
                break;
            };
        }
    }
    if (count($results) == 0) {
        foreach ($array as $key => $val) {
            if (strlen($val['school_name']) >= strlen($search)) {
                $lev = levenshtein($search, $val['school_name'], 1, 1, 1);
                $lev_array[] = array("school_name"=>$val['school_name'], "la_name"=>$val['la_name'], "fsm_prop"=>$val['fsm_prop'], "lev"=>$lev);
            };
        }
        array_sort_by_column($lev_array, 'lev');
        foreach ($lev_array as $key => $val) {
                if (count($results) < 10) {
                    $results[] = $val;
                } else {
                    break;
                };
        }
    };
    
    return $results;
    
};

if (strlen($_GET['q']) > 0 ) {
    $results = searchForId($_GET['q'], $csv);
} else {
    $results = array();
};

$json_string = json_encode($results);

header('Content-Type: application/json; charset=utf-8');

echo $json_string;

?>