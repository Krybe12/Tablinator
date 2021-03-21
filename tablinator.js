var pageChange = 0;
var sortChange = [];
var perPageChange = 0;
class Tablinator {
    constructor(tableName, columnsArr, divID){
        this.tableName = tableName;
        this.columnsArr = columnsArr;
        this.divID = divID;
        this.sortArr = [];
        this.currentPage = 1;
        this.perPage = 10;
        this.refresh();
    }
    refresh(){
        $(`#${this.divID}`).load(`tablinator.php?table=${this.tableName}&col=${this.columnsArr}&currentPage=${this.currentPage}&perPage=${this.perPage}&sort=${this.sortArr}`, () => {this.createListeners()});
    }
    createListeners(){
        $(`.tablinator-${this.tableName}-button`).click(function() {
            pageChange = $(this).text();
        });
        $(`.tablinator-${this.tableName}-column`).click(function() {
            sortChange = [$(this).text(), "ASC"];
        });
        $(`#tablinator-${this.tableName}-val`).change(function() {
            perPageChange = $(this).val();
        });
        $(`#tablinator-${this.tableName}-val`).change(() => {
            if (perPageChange > 0){
                this.perPage = perPageChange;
                this.currentPage = 1
                perPageChange = 0;
                this.refresh();
            }
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
                this.currentPage = 1;
                sortChange = [];
            }
            this.refresh();
        });
    }
}