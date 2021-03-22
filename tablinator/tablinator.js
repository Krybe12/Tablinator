var pageChange = 0;
var sortChange = [];
var perPageChange = 0;
var searchBar = "";
function throttle(f, delay){ // yoinked from https://stackoverflow.com/questions/4364729/jquery-run-code-2-seconds-after-last-keypress
    var timer = null;
    return function(){
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = window.setTimeout(function(){
            f.apply(context, args);
        },
        delay || 190);
    };
}
class Tablinator {
    constructor(tableName, columnsArr, divID){
        this.tableName = tableName;
        this.columnsArr = columnsArr;
        this.divID = divID;
        this.sortArr = [];
        this.currentPage = 1;
        this.perPage = 10;
        this.search = [];
        this.refresh();
    }
    refresh(){
        try {
            this.search[1] = $(`#tablinator-${this.tableName}-input`).val();
            this.search[0] = $(`#tablinator-${this.tableName}-input`).is(":focus");
        } catch(err) {
            //console.error(err)
        }
        $(`#${this.divID}`).load(`tablinator/tablinator.php?table=${this.tableName}&col=${this.columnsArr}&currentPage=${this.currentPage}&perPage=${this.perPage}&sort=${this.sortArr}&search=${this.search}`, () => {this.createListeners(); if(this.search.length > 0){this.inputSelector()}});
    }
    inputSelector(){
        if ($(`#tablinator-${this.tableName}-input`).hasClass("focus")){
            var searchInput = $(`#tablinator-${this.tableName}-input`);
            var strLength = searchInput.val().length * 2;
            searchInput.focus();
            searchInput[0].setSelectionRange(strLength, strLength);
        }
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
        $(`#tablinator-${this.tableName}-input`).keyup(function() {
            searchBar = $(this).val();
        });
        $(`#tablinator-${this.tableName}-input`).keyup(throttle(() => {
            this.refresh();
        }));
        //$(`#tablinator-${this.tableName}-input`).keyup(() => {
/*             if (searchBar.length > 0){
                this.search[1] = searchBar;
                searchBar = "";
                
            } */
            //this.refresh();
        //});
        $(`#tablinator-${this.tableName}-input`).on('search', () => {
            this.refresh();
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