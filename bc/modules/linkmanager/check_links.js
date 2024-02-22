/*
 * XML & data handling functions
 * mostly (c) Kirill Hryapin, 2005
 */


// Create XMLHttpRequest object
function xml_request()
{
    this.req = null;

    try {
        this.req = new XMLHttpRequest();
    } catch(e) { // Mozilla, IE7
        try {
            this.req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try {
                this.req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch(e) {}
        }
}

if (!this.req) {
    alert("FATAL ERROR:\n\nBrowser doesn't support XMLHttp requests!");
    return false;
}
}

// ---------------------------------------------------------------------------
// load data
xml_request.prototype.get = function(url)
{
    this.req.open("GET", url, false);
    this.req.send(null);
    if (this.req.status != 200) {
        alert("Request to "+url+"\n\nStatus: "+this.req.status);
        return false;
    }
    else {
        return true;
    }
}

// ---------------------------------------------------------------------------
function check_links()
{
    var req = new xml_request();
    var link_to_check = 0,
    dst = document.getElementById('results');

    do
    {
        req.get('process.php?link_to_check='+(link_to_check++));
        var response = req.req.responseText;
        if (response) {
            dst.innerHTML += response;
        }
    }
    while (response && !response.match("<!--EOF-->"));

    document.body.innerHTML += "<b>"+NETCAT_MODULE_LINKS_CHECKUP_DONE+"</b>";

}
// ---------------------------------------------------------------------------
