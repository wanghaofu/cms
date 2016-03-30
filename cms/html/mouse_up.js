var FixPrototypeForGecko=top.FixPrototypeForGecko||function(){
function element_prototype_get_runtimeStyle(){
 //return style instead...
 return this.style;
}
function window_prototype_get_event(){
 return SearchEvent();
}
function event_prototype_get_srcElement(){
 return this.target;
}
function SearchEvent(){
	if(document.all){//IE
		return window.event;
	}
	func=SearchEvent.caller;
	while(func!=null){
		var arg0=func.arguments[0];
		if(arg0){
			if(arg0.constructor==Event || arg0.constructor=="[object MouseEvent]"){
				return arg0;
			}
		}
	func=func.caller;
	}
	return null;
}
function nocontextmenu(){
	event.cancelBubble=true
	event.returnValue=false;
	return false;
}
function rightclick(e){
	if(e.which==2||e.which==3){
		window.removeEventListener("mouseup",rightclick,false);
		e.srcElement.oncontextmenu.call;
	}
}
function norightclick(e){
	if(window.Event){
		if(e.which==2||e.which==3){
			if(e.srcElement.oncontextmenu!=null && typeof(e.srcElement.oncontextmenu)=="function"){
				window.addEventListener("mouseup",rightclick,false);
			}
			return false;
		}
  }else if(event.button==2||event.button==3){
		event.cancelBubble=true;
		event.returnValue=false;
		return false; 
	}
}
function getElementPos(obj){
	var ua = navigator.userAgent.toLowerCase();
	var isOpera = (ua.indexOf('opera') != -1);
	var isIE = (ua.indexOf('msie') != -1 && !isOpera); // not opera spoof
	if(obj.parentNode === null || obj.style.display == 'none'){
		return false;
	}
	var parent = null;
	var pos = [];
	var box;
	if(obj.getBoundingClientRect){//IE
		box = obj.getBoundingClientRect();
		var scrollTop = Math.max(document.documentElement.scrollTop,document.body.scrollTop);
		var scrollLeft = Math.max(document.documentElement.scrollLeft,document.body.scrollLeft);
		return {x:box.left + scrollLeft, y:box.top + scrollTop};
	}
	else if(document.getBoxObjectFor){// gecko
		box = document.getBoxObjectFor(obj);
		var borderLeft = (obj.style.borderLeftWidth)?parseInt(obj.style.borderLeftWidth):0;
		var borderTop = (obj.style.borderTopWidth)?parseInt(obj.style.borderTopWidth):0;
		pos = [box.x - borderLeft, box.y - borderTop];
	}else{// safari & opera
		pos = [obj.offsetLeft, obj.offsetTop];
		parent = obj.offsetParent;
		if(parent != obj){
			while(parent){
				pos[0] += parent.offsetLeft;
				pos[1] += parent.offsetTop;
				parent = parent.offsetParent;
			}
		}
		if(ua.indexOf('opera') != -1 || (ua.indexOf('safari') != -1 && obj.style.position == 'absolute' )){
			pos[0] -= document.body.offsetLeft;
			pos[1] -= document.body.offsetTop;
		}
	}
	if(obj.parentNode){
		parent = obj.parentNode;
	}else{
		parent = null;
	}
	while(parent && parent.tagName != 'BODY' && parent.tagName != 'HTML'){ // account for any scrolled ancestors 
		pos[0] -= parent.scrollLeft; 
		pos[1] -= parent.scrollTop; 
		if(parent.parentNode){
			parent = parent.parentNode;
		}else{
			parent = null;
		}
	}
	return {x:pos[0], y:pos[1]};
}
	function __createPopup(){
		var SetElementStyles = function( element, styleDict ){
			var style = element.style;
			for(var styleName in styleDict ){
				style[ styleName ] = styleDict[ styleName ] ;
			}
		}
		
		var eDiv = document.createElement( 'div' );
		SetElementStyles(eDiv,{
															'position': 'absolute',
															'top': 0 + 'px',
															'left': 0 + 'px',
															'width': 0 + 'px',
															'height': 0 + 'px',
															'zIndex': 1000,
															'display' : 'none',
															'overflow' : 'hidden',
															'background:' : 'red'
														});
		eDiv.body = eDiv;
		var opened = false ;
		var setOpened = function( b ) {
			opened = b;
		}
		var getOpened = function() {
			return opened ;
		}
		return {
			htmlTxt : '',
			document : eDiv,
			isOpen : getOpened(),
			isShow : false,
			hide : function() {
				SetElementStyles( eDiv, {
																	'top': 0 + 'px',
																	'left': 0 + 'px',
																	'width': 0 + 'px',
																	'height': 0 + 'px',
																	'display' : 'none'
																	} );
				eDiv.innerHTML = '' ;
				this.isShow = false ;
			},
			show : function( iX, iY, iWidth, iHeight, oElement ) {
				if (!getOpened()) {
					document.body.appendChild( eDiv ) ;
					setOpened( true ) ; 
				};
				this.htmlTxt = eDiv.innerHTML ;
				if (this.isShow) {
					this.hide();
				};
				eDiv.innerHTML = this.htmlTxt ;
				var pos = getElementPos( oElement ) ;
				eDiv.style.left = ( iX ) + 'px' ;
				eDiv.style.top = ( iY ) + 'px' ;
				eDiv.style.width = iWidth + 'px' ;
				eDiv.style.height = iHeight + 'px' ;
				eDiv.style.display = 'block' ;
				this.isShow = true ;
			}
		}
	}
return function(win,a){
	a=a||{};
	win=win||window;
	not_ie=navigator.userAgent.indexOf("Firefox")>0;
	if(not_ie){
		win.constructor.prototype.__defineGetter__("event",window_prototype_get_event);
		win.Event.prototype.__defineGetter__("srcElement",event_prototype_get_srcElement);
	}
	if(a.runtimeStyle&&not_ie)HTMLElement.prototype.__defineGetter__("runtimeStyle",element_prototype_get_runtimeStyle);
	if(a.oncontextmenu&&not_ie)win.document.oncontextmenu=nocontextmenu;//for IE5+
	if(a.onmousedown&&not_ie)win.document.onmousedown=norightclick;//for all others
	if(a.createPopup)win.createPopup=win.createPopup||function(){return __createPopup();};win.getElementPos=getElementPos;
	return true;
}
}
var FixGecko=new FixPrototypeForGecko(),fix=fix||{};
if (!window.createPopup){
	fix.createPopup=true;
}
FixGecko(window,fix);

tPopWait=1000;
tPopShow=3000;
showPopStep=20;
popOpacity=100;
cBgColor='#EEEEEE';
cColor='#000000';
cBorderColor='#000000';

sPop=null;
curShow=null;
tFadeOut=null;
tFadeIn=null;
tFadeWaiting=null;

document.write("<style type='text/css' id='defaultPopStyle'>");
document.write(".cPopText {  background-color: "+cBgColor+";color:"+cColor+"; border: 1px "+cBorderColor+" solid;font-size: 12px; padding-right: 4px; padding-left: 4px; height: 20px; padding-top: 2px; padding-bottom: 2px; filter: Alpha(Opacity=0);MozOpacity:0;opacity:0}");
document.write("</style>");
document.write("<div id='dypopLayer' style='position:absolute;z-index:1000;' class='cPopText'></div>");

function showPopupText(){
var o=event.srcElement;
	MouseX=event.x;
	MouseY=event.y;
	if(o.alt!=null && o.alt!=""){o.dypop=o.alt;o.alt=""};
    if(o.title!=null && o.title!=""){o.dypop=o.title;o.title=""};
	if(o.dypop!=sPop) {
			sPop=o.dypop;
			clearTimeout(curShow);
			clearTimeout(tFadeOut);
			clearTimeout(tFadeIn);
			clearTimeout(tFadeWaiting);	
			if(sPop==null || sPop=="") {
				dypopLayer.innerHTML="";
				dypopLayer.style.filter="Alpha()";
				dypopLayer.filters.Alpha.opacity=0;	
				}
			else {
				if(o.dyclass!=null) popStyle=o.dyclass 
					else popStyle="cPopText";
				curShow=setTimeout("showIt()",tPopWait);
			}
	}
}

function showIt(){
		dypopLayer.className=popStyle;
		dypopLayer.innerHTML=sPop;
		popWidth=dypopLayer.clientWidth;
		popHeight=dypopLayer.clientHeight;
		if(MouseX+12+popWidth>document.body.clientWidth) popLeftAdjust=-popWidth-24
			else popLeftAdjust=0;
		if(MouseY+12+popHeight>document.body.clientHeight) popTopAdjust=-popHeight-24
			else popTopAdjust=0;
		dypopLayer.style.left=MouseX+12+document.body.scrollLeft+popLeftAdjust;
		dypopLayer.style.top=MouseY+12+document.body.scrollTop+popTopAdjust;
		dypopLayer.style.filter="Alpha(Opacity=0)";
		fadeOut();
}

function fadeOut(){
	if(dypopLayer.filters.Alpha.opacity<popOpacity) {
		dypopLayer.filters.Alpha.opacity+=showPopStep;
		tFadeOut=setTimeout("fadeOut()",1);
		}
		else {
			dypopLayer.filters.Alpha.opacity=popOpacity;
			tFadeWaiting=setTimeout("fadeIn()",tPopShow);
			}
}

function fadeIn(){
	if(dypopLayer.filters.Alpha.opacity>0) {
		dypopLayer.filters.Alpha.opacity-=1;
		tFadeIn=setTimeout("fadeIn()",1);
		}
}
document.onmouseover=showPopupText;