function throttle(f, delay){ // yoinked from https://stackoverflow.com/questions/4364729/jquery-run-code-2-seconds-after-last-keypress
    var timer = null;
    return function(){
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = window.setTimeout(function(){
            f.apply(context, args);
        },
        delay || 250);
    };
}
class Tablinator {
    constructor(tableName, columnsArr, divID){
        this.tableName = tableName;
        this.columnsArr = columnsArr;
        this.divID = divID;
        this.sortArr = [];
        this.sortChange = [];
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
        }
        $(`#${this.divID}`).load(`tablinator/tablinator.php?table=${this.tableName}&col=${this.columnsArr}&currentPage=${this.currentPage}&perPage=${this.perPage}&sort=${this.sortArr}&search=${this.search}`, () => {this.createListeners(); this.inputSelector()});
    }
    inputSelector(){
        if (!$(`#tablinator-${this.tableName}-input`).hasClass("focus")) return;
        var searchInput = $(`#tablinator-${this.tableName}-input`);
        var strLength = searchInput.val().length * 2;
        searchInput.focus();
        searchInput[0].setSelectionRange(strLength, strLength);
    }
    createListeners(){
        $(`.tablinator-${this.tableName}-button`).click((e) => {
            this.currentPage = e.target.innerText;
            this.refresh();
        });

        $(`.tablinator-${this.tableName}-val`).change((e) => {
            this.perPage = e.target.value;
            this.refresh();
        });

        $(`.tablinator-${this.tableName}-column`).click((e) => {
            this.sortChange = [e.target.innerText, "ASC"];
            if (this.sortArr[1] == "DESC"){
                this.sortChange[1] = "ASC";
            } else if (this.sortArr[0] == this.sortChange[0]){
                this.sortChange[1] = "DESC";
            }
            this.sortArr = this.sortChange;
            this.currentPage = 1;
            this.sortChange = [];
            this.refresh();
        });

        $(`#tablinator-${this.tableName}-input`).keyup(throttle(() => {
            this.currentPage = 1;
            this.refresh();
        }));
        $(`#tablinator-${this.tableName}-input`).on('search', () => { //triggers on clearing search bar
            this.currentPage = 1;
            this.refresh();
        });
    }
}