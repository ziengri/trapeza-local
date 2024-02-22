var my_tree_closed;
var nn6 = document.documentElement;
if(document.all){ nn6 = false; }
var ie4 = (document.all && !document.getElementById);
var ie5 = (document.all && document.getElementById);

function my_tree_click(el, f){//f: 1 - open, 2 - close, false - default
	el.className=(f===1?'':(f===2?'close':(el.className?'':'close')));
	if(el.getElementsByTagName('UL')[0])
		el.getElementsByTagName('UL')[0].className=(f===1?'':(f===2?'close':(!el.className?'':'close')));
	if((ie4 || ie5) && window.event && window.event.srcElement.type!=='checkbox'){
		window.event.cancelBubble=true;
		window.event.returnValue=false;
	}
	return false;
}
function my_tree_all(my_tree_id, f){//f: 1 - open, 2 - close
	if(f===2) my_tree_id.className='my_tree my_tree_close';
	for(i=0;i<my_tree_id.getElementsByTagName('LI').length;i++){
		var li=my_tree_id.getElementsByTagName('LI')[i];
		if(li.className!=='leaf') my_tree_click(li, f);
	};
	my_tree_id.className='my_tree';
}
function my_tree_init(my_tree_id){
	my_tree_closed=(my_tree_id.className.indexOf('close')>-1);
	for(i=0;i<my_tree_id.getElementsByTagName('LI').length;i++){
		var li=my_tree_id.getElementsByTagName('LI')[i];
		if(ie4 || ie5) li.onclick=new Function("window.event.cancelBubble=true");
		if(!li.getElementsByTagName('UL').length || li.className==='leaf') li.className='leaf';
		else if((tmp=li.getElementsByTagName('A')[0]) && tmp.parentNode===li){
			li.getElementsByTagName('A')[0].onclick=new Function("my_tree_click(this.parentNode)");
			li.getElementsByTagName('A')[0].title='Открыть';
			if(ie4 || ie5){
				li.style.cursor='hand';
				li.onclick=new Function("my_tree_click(this)");
			};
			if(my_tree_closed) li.getElementsByTagName('A')[0].onclick();
		}else{
			li.onclick=new Function("my_tree_click(this)");
			li.style.cursor='hand';
			if(my_tree_closed) li.onclick();
		}
	}
	my_tree_id.className='my_tree';
}
function f_loc(){
    if(window.location.hash){
        var el='_'+window.location.hash.substring(1);
        if(document.getElementById(el)){
            my_tree_click(document.getElementById(el).parentNode.parentNode.parentNode, 1);
            my_tree_click(document.getElementById(el).parentNode.parentNode.parentNode.parentNode.parentNode,1);
            document.getElementById(el).scrollIntoView(true);
            document.getElementById(el).focus();
        }
    }
}

window.onload = function(){
    my_tree_init(document.getElementById('l1'));
    setTimeout('f_loc()', 100);
};
function fc(){
        if(document.getElementById("_iframe")){
            document.getElementById("_iframe").firstChild.src="about:blank";
            document.getElementById("_iframe").style.visibility="hidden";
        }
        if(document.getElementById("_shadow")) document.getElementById("_shadow").style.visibility="hidden";
        if(document.getElementById("frameOverlay")) document.getElementById("frameOverlay").style.visibility="hidden";
        return true;
    }
    /// Get Data for Frame
    function _f(_m){
        $frameOverlay = document.getElementById("frameOverlay");
        if($frameOverlay){
            $frameOverlay.style.visibility="visible";
        }
        _top  = (window.innerHeight-300)/2 - 100;
        _left = (window.innerWidth-470)/2;
        $backGround = document.getElementById("_shadow");
        if($backGround){
            $backGround.style.visibility="visible";
            $backGround.style.top=_top+7;
            $backGround.style.left=_left+7;
        };
        var $divFrame = document.getElementById("_iframe");
        var $infoFrame = document.getElementById("infoFrame");
        if($divFrame){
            $infoFrame.src="detailInfo.php?"+_m;
            $divFrame.style.visibility="visible";
            $divFrame.style.top=_top;
            $divFrame.style.left=_left;
        }
        return false;
    }
    function tabActive(num){
        $('#A2DSearch').find('.tabkont_active').removeClass().addClass('tabkont');
        $('#A2DSearch').find('.tab_active').removeClass().addClass('tab');
        $('#tab'+num).removeClass().addClass('tab_active');
        $('#tabkont'+num).removeClass().addClass('tabkont_active');
    }

    var my_tree_closed;

var nn6 = document.documentElement;
if(document.all){nn6 = false;}
var ie4 = (document.all && !document.getElementById);
var ie5 = (document.all && document.getElementById);

function my_tree_click(el, f){//f: 1 - open, 2 - close, false - default
    el.className=(f==1?'':(f==2?'close':(el.className?'':'close')));
    if(el.getElementsByTagName('UL')[0])
        el.getElementsByTagName('UL')[0].className=(f==1?'':(f==2?'close':(!el.className?'':'close')));
    if((ie4 || ie5) && window.event && window.event.srcElement.type!='checkbox'){
        window.event.cancelBubble=true;
        window.event.returnValue=false;
    }
    return false;
}

function my_tree_all(my_tree_id, f){//f: 1 - open, 2 - close
    if(f==2) my_tree_id.className='my_tree my_tree_close';
    for(i=0;i<my_tree_id.getElementsByTagName('LI').length;i++){
        var li=my_tree_id.getElementsByTagName('LI')[i];
        if(li.className!='leaf') my_tree_click(li, f)
    };
    my_tree_id.className='my_tree';
}

function my_tree_init(my_tree_id){
    my_tree_closed=(my_tree_id.className.indexOf('close')>-1)
    for(i=0;i<my_tree_id.getElementsByTagName('LI').length;i++){
        var li=my_tree_id.getElementsByTagName('LI')[i];
        if(ie4 || ie5) li.onclick=new Function("window.event.cancelBubble=true");
        if(!li.getElementsByTagName('UL').length || li.className=='leaf') li.className='leaf';
        else if((tmp=li.getElementsByTagName('A')[0]) && tmp.parentNode==li){
            li.getElementsByTagName('A')[0].onclick=new Function("my_tree_click(this.parentNode)");
            li.getElementsByTagName('A')[0].title='Раскрыть/Закрыть ветку';
            if(ie4 || ie5){
                li.style.cursor='hand';
                li.onclick=new Function("my_tree_click(this)");
            };
            if(my_tree_closed) li.getElementsByTagName('A')[0].onclick();
        }else{
            li.onclick=new Function("my_tree_click(this)");
            li.style.cursor='hand';
            if(my_tree_closed) li.onclick();
        }
    }
    my_tree_id.className='my_tree';
}
