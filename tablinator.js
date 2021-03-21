var pageChange = 0;
var sortChange = [];
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
            sortChange = [$(this).text(), "ASC"];
        });
        $(`.tablinator`).click(() => {
            if (pageChange > 0){
                this.currentPage = pageChange;
                pageChange = 0;
            } else if (sortChange.length > 0){
                if (this.sortArr[1] == "DESC"){
                    sortChange[1] = "ASC";
                } else if (this.sortArr[0] == sortChange[0]){
                    sortChange[1] = "DESC";
                }
                this.sortArr = sortChange;
                sortChange = [];
            }
            this.refresh();
        });
    }
}