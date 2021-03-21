var pageChange = 0;
var sortChange = ["a", "b"];
class Tablinator {
    constructor(tableName, columnsArr, divID){
        this.tableName = tableName;
        this.columnsArr = columnsArr;
        this.divID = divID;
        this.sortArr = [];
        this.currentPage = 1;
        this.perPage = 6;
        this.refresh();
    }
    refresh(){
        $(`#${this.divID}`).load(`tablinator.php?table=${this.tableName}&col=${this.columnsArr}&currentPage=${this.currentPage}&perPage=${this.perPage}&sort=${this.sortArr}`, () => {this.createListeners()});
    }
    createListeners(){
        $(`.tablinator-${this.tableName}-button`).click(function() {
            this.currentPage = $(this).text();
            pageChange = this.currentPage;
        });
        $(`.tablinator-${this.tableName}-column`).click(function() {
            if (sortChange[0] == $(this).text()){
                if (sortChange[1] == "DESC"){
                    sortChange[1] = "ASC";
                } else {
                    sortChange[1] = "DESC";
                }
            } else {
                sortChange = [$(this).text(), "ASC"];
            }
/*             if ($(this).hasClass("ASC")){
                $(this).removeClass("ASC");
                $(this).addClass("DESC");
                sortChange = [$(this).text(), "DESC"];
            } else if ($(this).hasClass("DESC")){
                $(this).removeClass("DESC");
                $(this).addClass("ASC");
                sortChange = [$(this).text(), "ASC"];
            } else {
                $(`.tablinator-${this.tableName}-column`).removeClass("ASC DESC");
                $(this).addClass("ASC");
                sortChange = [$(this).text(), "ASC"];
            } */
        });
        $(`.tablinator`).click(() => {
            if (pageChange > 0){
                this.currentPage = pageChange;
                pageChange = 0;
            } else {
                this.sortarr = sortChange;
            }
            this.refresh();
        });
    }
}

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