<?php
require 'conn.php';
$errors = [];
if (isset($_GET["table"]) and isset($_GET["col"])){
    //default values
    $orderBy = "";
    $ascdesc = "";
    $numPerPage = 10; //js variables are used for pages
    $currentPage = 1;
    $tableClass = ["table", "is-fullwidth", "has-text-centered"];
    //over
    $tableName = $_GET["table"];
    $listOfCol = explode(",", $_GET["col"]);

    if(isset($_GET["perPage"]) and isset($_GET["currentPage"])){
        if (is_numeric($_GET["perPage"]) and is_numeric($_GET["currentPage"])){
            $numPerPage = $_GET["perPage"];
            $currentPage = $_GET["currentPage"];

            $sql = "SELECT COUNT(*) AS COUNT FROM $tableName";
            $result = $conn->query($sql);
            $resultCount = $result->fetch_assoc()["COUNT"];
            $maxPages = ceil(intval($resultCount) / $numPerPage);
        } else {
            array_push($errors, "wrong paging format");
        }
    }
    if(isset($_GET["sort"]) and strlen($_GET["sort"]) > 3){
        $sortStr = $_GET["sort"];
        $sortArr = explode(",", $sortStr);
        $orderBy = $sortArr[0];
        $ascdesc = $sortArr[1];
    }
    tablinator($tableName, $listOfCol, $orderBy, $ascdesc, $numPerPage, $currentPage, $tableClass);
}

function tablinator($tableName, $listOfCol, $orderBy, $ascdesc, $numPerPage, $currentPage, $tableClass){
    global $conn;
    global $maxPages;
    global $resultCount;
    if (strlen($orderBy) > 0) {
        if (in_array($orderBy, $listOfCol)) {
            if (strtoupper($ascdesc) == "ASC" or strtoupper($ascdesc) == "DESC"){
                $orderBy = "ORDER BY $orderBy $ascdesc";
            }
        }
    }

    $limitStart = $currentPage * $numPerPage - $numPerPage;
    $limit = "LIMIT " . $limitStart . ", ". $numPerPage;
    //entities counting
        $entitiesStart = $limitStart + 1;
        $entitiesEnd = $entitiesStart + $numPerPage - 1;
        if ($entitiesEnd > $resultCount){
            $entitiesEnd = $resultCount;
        }
    //over
    $columns = implode(", ", $listOfCol);
    $classes = implode(" ", $tableClass);

    $sql = "SELECT $columns FROM $tableName $orderBy $limit;";
    $result = $conn->query($sql);
    echo "<div class='wrapper'>";

    echo "<div class='is-flex is-justify-content-space-around'>";
    echo "<h5>per page xy</h5>";
    echo "<h5>searchbar.exe</h5>";
    echo "</div>";
   
    if ($result->num_rows > 0) {
        echo "<table class='$classes'>";
        echo "<thead>";
        echo "<tr>";
        //tablinator-$tableName-column tablinator
        for ($i = 0; $i < count($listOfCol); $i++) {
            echo "<th class='tablinator-$tableName-column tablinator'>";
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







<?php
//tablinator("sales", ["product_name", "price", "amount"], "price", "asc", 5, 1, ["table is-striped is-fullwidth has-text-centered"]);
/*
tablinator(
    tableName           -required TYPE STR
    arr of columns      -required TYPE ARR
    sort by column name -optional DEFAULT "" TYPE STR
    asc / desc          -optional DEFAULT "" TYPE STR
    numPerPage          -optional DEFAULT 100 TYPE INT
    currentPage         -optional DEFAULT 1 TYPE INT
    arr of tbl classes  -optional DEFAULT [] TYPE ARR
)
*/
?>
