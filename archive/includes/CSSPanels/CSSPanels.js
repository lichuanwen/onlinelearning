var xtd;
if (!xtd) xtd = {};

xtd.panel_adjust = function (name_, part_, m){
	var elem = document.getElementById(name_+"_"+part_+"_content");
	var elem1 = document.getElementById(name_+"_"+part_);
	var elem2 = document.getElementById(name_+"_"+part_+"_back");
	var elem22 = document.getElementById(name_+"_"+part_+"_front");
	var elem3 = document.getElementById(name_+"_"+part_+"_left");
	var elem4 = document.getElementById(name_+"_"+part_+"_center");
	var elem5 = document.getElementById(name_+"_"+part_+"_right");
	var hei = elem.offsetHeight;
	hei = ((hei - m) > 0 ? hei : m);
	
	//if (isIE6() && hei > 0) hei -= 1;
	
	elem1.style.height = hei + "px";
	elem2.style.height = hei + "px";
	elem22.style.height = hei + "px";
	var hei2 = ((hei - m) > 0 ? (hei-m) : 0) + "px";
		
	elem3.style.height = hei2;
	elem4.style.height = hei2;
	elem5.style.height = hei2;
	
	if (isIE6()) {
		var img1 = elem2.childNodes[elem2.childNodes.length - 1];
		var img2 = elem2.childNodes[elem2.childNodes.length - 3];
		if (img2.tagName.toLowerCase() == "img") {
		    img2.outerHTML = '<img src="' + img2.getAttribute('src') + '" class="' + img2.getAttribute('className') + '" alt="" />';
        }
        if (img1.tagName.toLowerCase() == "img") {
		    img1.outerHTML = '<img src="' + img1.getAttribute('src') + '" class="' + img1.getAttribute('className') + '" alt="" />';
        }
        
	}

	if (isIE6()) applyOddPixelFix(elem2);
}

xtd.addLoadEvent = function(str) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = function() {
		eval(str);
	}
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      eval(str);
    }
  }
}
xtd.addResizeEvent = function(str) {
  var oldonload = window.onresize;
  if (typeof window.onresize != 'function') {
    window.onresize = function() {
		eval(str);
	}
  } else {
    window.onresize = function() {
      if (oldonload) {
        oldonload();
      }
      eval(str);
    }
  }
}

function CSSPanelAdjust(name_, parts_, minvals_, applyFix) {

	var parts = parts_.split(',');
	var mins = minvals_.split(',');
    
    if (isIE6() || isIE7()) {
        var head_back = document.getElementById(name_+"_h_back");
        var content_back = document.getElementById(name_+"_c_back");
        var footer_back = document.getElementById(name_+"_f_back");
		
        if(applyFix){
			if (head_back) applyIEFix(head_back);
			if (content_back) applyIEFix(content_back);
			if (footer_back) applyIEFix(footer_back);
		}

		if(isIE6()){
			if (head_back) applyOddPixelFix(head_back);
			if (content_back) applyOddPixelFix(content_back);
			if (footer_back) applyOddPixelFix(footer_back);
		}
    }

    if (parts_ != "") {
        for (var i=0;i<parts.length;i++) {
            var fStr = "xtd.panel_adjust('" + name_ + "', '" + parts[i] + "', " + parseInt(mins[i]) + ");";
            eval(fStr);
            xtd.addLoadEvent(fStr);
			xtd.addResizeEvent(fStr);
        }
    }

}
function isIE6() {
	var arVersion = navigator.appVersion.split('MSIE');
	var version = parseFloat(arVersion[1]);
	if(version>=6.0 && version<7){
		return true;
	} else {
		return false;
	}
}
function isIE7() {
	var arVersion = navigator.appVersion.split('MSIE');
	var version = parseFloat(arVersion[1]);
	if(version>=7.0 && version<8){
		return true;
	} else {
		return false;
	}
}

function isIE() {
	return (navigator.userAgent.indexOf('MSIE') != -1);
}

function applyIEFix(uite) {
	var arVersion = navigator.appVersion.split('MSIE');
	var version = parseFloat(arVersion[1]);
	if(version >= 5.5 && document.body.filters) {
        var nodes = uite.childNodes;
        var n = nodes.length;

		for(var i=0;i<n;i++) {
			var node = nodes[i];
			if (node && node.tagName.toLowerCase() == "img") {
				var src = node.getAttribute('src');
                var cName = node.className;
                var style = getInlineStyle(node);
                var id = (node.id != null && node.id != "") ? 'id="' + node.id + '"' : "";
                if (style[style.length - 1] != ";") style += ";";
                var newElem = document.createElement('SPAN');
                newElem.className = cName;
                if (node.id != null && node.id != "") newElem.id = node.id;

                var newHTML = '<span ' + id + ' class="' + cName + '" style=\'' + style + 'display:inline-block;line-height:1px;font-size:1px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + src + '", sizingMethod="scale");\'></span>';
                var newStyle = style + 'display:inline-block;line-height:1px;font-size:1px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + src + '", sizingMethod="scale");';
                newElem.style.cssText = newStyle;
                uite.replaceChild(newElem, node);
			}
		}
	}	
}

function applyOddPixelFix(uite) {
	var nodes = uite.childNodes;
	var n = nodes.length;
	var node = nodes[n-3];
	if (node && (node.tagName.toLowerCase() == "img" || node.tagName.toLowerCase() == "span")) {
		if(uite.offsetHeight % 2 == 1){
			node.style.bottom = "-1px";
		} else {
			node.style.bottom = "0px";
		}
	}
	node = nodes[n-1];
	if (node && (node.tagName.toLowerCase() == "img" || node.tagName.toLowerCase() == "span")) {
		if(uite.offsetHeight % 2 == 1){
			node.style.bottom = "-1px";
		} else {
			node.style.bottom = "0px";
		}
	}
}

function getInlineStyle(node) {
    if (!isIE6() && !isIE7()) return node.style;
    
    var html = node.outerHTML;
    var sind = html.indexOf('style=');
    if (sind != -1) {
        var ch = html.charAt(sind + 6);
        var eind = html.indexOf(ch, sind + 8);
        return html.substring(sind + 7, eind);
    } else {
        return ""
    }
}