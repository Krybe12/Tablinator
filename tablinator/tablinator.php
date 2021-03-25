<?php
require 'conn.php';

if (!isset($_GET["table"]) or !isset($_GET["col"])) return;
$errors = [];
$arrows = ["⬍", "▲", "▼"];
//default values
$orderBy = "";
$ascdesc = "";
$numPerPage = 10; //js variable have prio
$currentPage = 1;
$tableClass = ["table", "is-fullwidth", "has-text-centered"];
$whereLike = "";

//table values
$tableName = $_GET["table"];
$listOfCol = explode(",", $_GET["col"]);

if(isset($_GET["search"])){
    $search = $_GET["search"];
    $searchArr = explode(",", $search);
    if ($searchArr[0] == "true"){
        $autofocus = "focus";
    } else {
        $autofocus = "";
    }
    if(strlen($searchArr[1]) > 0 ){
        $searchVal = $searchArr[1];
        $searchStr = "%" .$searchVal . "%";
        for ($i = 0; $i < count($listOfCol); $i++){
            $whereLike = $whereLike . " " . $listOfCol[$i] . " " . "LIKE " .  "'" . $searchStr . "'" . " OR";
        }
        $whereLike = "WHERE " . $whereLike;
        $whereLike = substr($whereLike, 0, -3);
        if (!validate($whereLike)) $whereLike = "";
    }
}

if(isset($_GET["perPage"]) and isset($_GET["currentPage"])){
    if (is_numeric($_GET["perPage"]) and is_numeric($_GET["currentPage"])){
        $numPerPage = $_GET["perPage"];
        $currentPage = $_GET["currentPage"];
    } else {
        array_push($errors, "wrong paging format");
    }
}

if(isset($_GET["sort"]) and strlen($_GET["sort"]) > 0){
    $sortStr = $_GET["sort"];
    $sortArr = explode(",", $sortStr);
    $orderBy = $sortArr[0];
    $ascdesc = $sortArr[1];
    if (in_array($orderBy, $listOfCol)) {
        if (strtoupper($ascdesc) == "ASC" or strtoupper($ascdesc) == "DESC"){
            $orderBy = "ORDER BY $orderBy $ascdesc";
        }
    } else {
        $orderBy = "";
        array_push($errors, "wrong sorting format");
    }
}

tablinator($tableName, $listOfCol, $orderBy, $numPerPage, $currentPage, $tableClass, $whereLike);

if (count($errors) > 0){
    var_dump($errors);
}

function tablinator($tableName, $listOfCol, $orderBy, $numPerPage, $currentPage, $tableClass, $whereLike){
    global $conn;
    global $searchVal;
    global $autofocus;

    //page count / result count
    $sql = "SELECT COUNT(*) AS COUNT FROM $tableName $whereLike";
    if (!validate($sql)) return;
    $result = $conn->query($sql);
    $resultCount = $result->fetch_assoc()["COUNT"];
    $maxPages = ceil(intval($resultCount) / $numPerPage);

    //select current value
    $selectTen = "";
    $selectFifteen = "";
    $selectTwenty = "";
    $selectTwentyFive = "";
    switch(intval($numPerPage)){
        case 10:
            $selectTen = "selected";
            break;
        case 15:
            $selectFifteen = "selected";
            break;
        case 20:
            $selectTwenty = "selected";
            break;
        case 25:
            $selectTwentyFive = "selected";
            break;
    }

    //limit counting
    $limitStart = $currentPage * $numPerPage - $numPerPage;
    $limit = "LIMIT " . $limitStart . ", ". $numPerPage;

    //entities counting
    $entitiesStart = $limitStart + 1;
    $entitiesEnd = $entitiesStart + $numPerPage - 1;
    if ($entitiesEnd > $resultCount){
        $entitiesEnd = $resultCount;
    }

    $columns = implode(", ", $listOfCol);
    $classes = implode(" ", $tableClass);

    //main sql
    $sql = "SELECT $columns FROM $tableName $whereLike $orderBy $limit;";
    if (!validate($sql)) return;
    $result = $conn->query($sql);
    echo "<div class='wrapper'>";

    echo "<div class='is-flex is-justify-content-space-around'>";
    echo "<div><label for='tablinator-$tableName-val'>Number of entries: </label><select id='tablinator-$tableName-val' class='tablinator-$tableName-val'><option value='10' $selectTen>10</option><option value='15' $selectFifteen>15</option><option value='20' $selectTwenty>20</option><option value='25' $selectTwentyFive>25</option></select></div>";
    echo "<div class='is-flex is-align-items-center'><label for='tablinator-$tableName-input'>Search: </label><input class='input $autofocus tablinator-$tableName-input' value='$searchVal' type='search' id='tablinator-$tableName-input' autocomplete='off'></div>";
    echo "</div>";
   
    if ($result->num_rows > 0) {
        echo "<table class='$classes'>";
        echo "<thead>";
        echo "<tr>";

        for ($i = 0; $i < count($listOfCol); $i++) {
            echo "<th class='is-unselectable is-clickable tablinator-$tableName-column tablinator-$tableName'>";
            echo $listOfCol[$i];
            echo "</th>"; 
        }

        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()){
            echo "<tr>";

            for ($i = 0; $i < count($listOfCol); $i++) {
                echo "<td>";
                echo $row[$listOfCol[$i]];
                echo "</td>"; 
            }
    
            echo "</tr>";
        }
        echo "</tbody>";
        echo '</table>';

        echo "<div class='is-flex is-justify-content-space-around'>";

        echo "<h5>Showing $entitiesStart to $entitiesEnd of $resultCount entries</h5>";

        echo "<div class=''>";
            for ($i = 1; $i <= $maxPages; $i++) {
                $isSelectedBtn = "";
                if ($i == $currentPage){
                    $isSelectedBtn = "is-success is-selected";
                }
                echo "<button class='button is-small m-1 $isSelectedBtn tablinator-$tableName-button tablinator-$tableName'>$i</button>";
            }
        echo "</div>";

        echo "</div>";
    }
    echo "</div>";
}
function validate($str){
    $listOfBadWords = ["DATABASE", "DELETE", "USE", "TRUNCATE", "DROP"];
    for ($i = 0; $i < count($listOfBadWords); $i++){
        if(strripos($str, $listOfBadWords[$i])) return false;
    }
    return true;
}
?>
