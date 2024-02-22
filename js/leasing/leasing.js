$('body').ready(function(){
    lising_result($("#lising-form"))
    $('#lising-form input[type="text"]').keyup(function(){
        $(this).val(formatMoney(0, "", " ", ",", $(this).val().replace(/[^-0-9]/gim,'')));
        console.log('input')
        lising_result($(this));
    });
    $('body').on('change', '#lising-form [type="radio"]', function(){
        $(this).val(formatMoney(0, "", " ", ",", $(this).val().replace(/[^-0-9]/gim,'')));
        console.log('radio')
        lising_result($(this));
    });
});
function lising_result(btn){
    console.log('lising_result')
    $(".lising-clear").val("");

    var good, first_sum, time_lising, riseinprice, imprest_per, sum_postavki_per, sum_strah = 0;
    var imprest, monthly_pay, sum_postavki = 0, liz_sum=0, lising_price, ezhem_plat, mailtext;

    form = btn.is('form') ? btn : btn.parents('form:first');
    datanow = form.serializeArray();
    console.log(form, datanow)
    $.each(datanow, function(i, field){
        if(field['name']=='first_sum'){
            console.log('first_sum')
            if(field['value']>0 || field['value'] != ""){first_sum = field['value'].replace(/[^-0-9]/gim,'');}
            else{bad_result(field['name']); good = 1; console.log('first_sum 1') }
        }
        if(field['name']=='time_lising'){
            console.log('time_lising')
            if(field['value']>0 || field['value'] != ""){time_lising = field['value'].replace(/[^-0-9]/gim,'');}
            else{bad_result(field['name']); good = 1; console.log('time_lising 1')}
        }
        if(field['name']=='riseinprice'){
            console.log('riseinprice')
            if(field['value']>0 || field['value'] != ""){riseinprice = field['value'].replace(/[^-0-9]/gim,'');}
            else{bad_result(field['name']); good = 1; console.log('riseinprice 1')}
        }
        if(field['name']=='imprest_per'){
            console.log('imprest_per')
            if(field['value']>0 || field['value'] != ""){imprest_per = field['value'].replace(/[^-0-9]/gim,'');}
            else{bad_result(field['name']); good = 1; console.log('imprest_per 1')}
        }
    });
    if(!good){
        imprest = first_sum*imprest_per/100;
        lising_price = parseFloat(first_sum) + parseFloat(first_sum/100*riseinprice) + parseFloat(sum_postavki);
        monthly_pay = (parseFloat(lising_price)-parseFloat(imprest))/(parseFloat(time_lising)-1);
        $("input[name='imprest']").val(formatMoney(2, "", " ", ",", imprest));
        $("input[name='lising_price']").val(formatMoney(2, "", " ", ",", lising_price));
        $("input[name='monthly_pay']").val(formatMoney(2, "", " ", ",", monthly_pay));
    }
    return false;
}
function bad_result(name){
	$("input[name='"+name+"']").addClass("resultnone");
    setTimeout(function(){ $(".resultnone").removeClass("resultnone"); },800);
	return false;
}
function formatMoney(places, symbol, thousand, decimal, num) {
	places = !isNaN(places = Math.abs(places)) ? places : 2;
	symbol = symbol !== undefined ? symbol : "$";
	thousand = thousand || ",";
	decimal = decimal || ".";
	var number = num,
	    negative = number < 0 ? "-" : "",
	    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
	    j = (j = i.length) > 3 ? j % 3 : 0;
	return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
}
