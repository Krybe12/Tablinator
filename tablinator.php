<?php
require 'conn.php';
$errors = [];
if (isset($_GET["table"]) and isset($_GET["col"])){
    //default values
    $orderBy = "";
    $ascdesc = "";
    $numPerPage = 10; //js variables have prio
    $currentPage = 1;
    $tableClass = ["table", "is-fullwidth", "has-text-centered"];
    $whereLike = "";
    //over
    $tableName = $_GET["table"];
    $listOfCol = explode(",", $_GET["col"]);
    if(isset($_GET["search"]) and strlen($_GET["search"]) > 0){
        $search = $_GET["search"];
        $searchArr = explode(",", $search);
        if ($searchArr[0] == "true"){
            $autofocus = "focus";
        } else {
            $autofocus = "";
        } 
        $searchVal = $searchArr[1];
    }
    if (strlen($searchVal) > 0){
        $searchStr = $searchVal . "%";
        $whereLike = "";
        for ($i = 0; $i < count($listOfCol); $i++){
            $whereLike = $whereLike . " " . $listOfCol[$i] . " " . "LIKE " .  "'" . $searchStr . "'" . " OR";
        }
        $whereLike = "WHERE " . $whereLike;
        $whereLike = substr($whereLike, 0, -3);
    }
    if(isset($_GET["perPage"]) and isset($_GET["currentPage"])){
        if (is_numeric($_GET["perPage"]) and is_numeric($_GET["currentPage"])){
            $numPerPage = $_GET["perPage"];
            $currentPage = $_GET["currentPage"];
        } else {
            array_push($errors, "wrong paging format");
        }
    }
    if(isset($_GET["sort"]) and strlen($_GET["sort"]) > 3){
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
}

function tablinator($tableName, $listOfCol, $orderBy, $numPerPage, $currentPage, $tableClass, $whereLike){
    global $conn;
    global $resultCount;
    global $searchVal;
    global $autofocus;

    $sql = "SELECT COUNT(*) AS COUNT FROM $tableName $whereLike";
    $result = $conn->query($sql);
    $resultCount = $result->fetch_assoc()["COUNT"];
    $maxPages = ceil(intval($resultCount) / $numPerPage);

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

    $sql = "SELECT $columns FROM $tableName $whereLike $orderBy $limit;";
    $result = $conn->query($sql);
    echo "<div class='wrapper'>";

    echo "<div class='is-flex is-justify-content-space-around'>";
    echo "<div><label for='tablinator-$tableName-val'>Number of entries: </label><select id='tablinator-$tableName-val'><option value='10' $selectTen>10</option><option value='15' $selectFifteen>15</option><option value='20' $selectTwenty>20</option><option value='25' $selectTwentyFive>25</option></select></div>";
    echo "<div class='is-flex is-align-items-center'><label for='tablinator-$tableName-input'>Search: </label><input class='input $autofocus' value='$searchVal' type='search' id='tablinator-$tableName-input' autocomplete='off'></div>";
    echo "</div>";
   
    if ($result->num_rows > 0) {
        echo "<table class='$classes'>";
        echo "<thead>";
        echo "<tr>";
        //tablinator-$tableName-column tablinator
        for ($i = 0; $i < count($listOfCol); $i++) {
            echo "<th class='tablinator-$tableName-column tablinator' style='cursor: pointer;'>";
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
                echo "<button class='button is-small m-1 tablinator-$tableName-button tablinator'>$i</button>";
            }
        echo "</div>";

        echo "</div>";
    }
    echo "</div>";
}
?>
