
function MilesManu(num,nam){
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    num=num.replace(".", "");
    var num_parts = num.toString().split(",");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    document.getElementById(nam).value=num_parts.join(".");
}