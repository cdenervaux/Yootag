// ******************
// Yootag Bookmarklet
// ******************
// Credits: http://kathrynbrisbin.blogspot.com

// Use namespace
var yootagFunctions = function(){
	
	// Set the location of the iframe
	function yootagSetIframe(){
		var iframe;
		
		if (navigator.userAgent.indexOf("Safari") != -1) {
			iframe = frames["yootagframe"];
		}
		else {
			iframe = document.getElementById("yootagframe").contentWindow;
		}
		
		if (!iframe) 
			return;
		
		var url = 'http://dev.yootag.com/en/tagmarklet/frame.php?add_url=' + window.location.href;
		try {
			iframe.location.replace(url);
		} 
		catch (e) {
			iframe.location = url; // safari
		}
	}
	
	// Check for a change in the URL
	function yootagFrameMessage(){
		var gCurScroll = yootagScrollPos();
		var hash = location.href.split('#');
		if (hash.length > 1 && hash[hash.length - 1].match('yootag') != null) {
			location.replace(hash[0] + "#");
			yootagSetScroll(gCurScroll);
			yootagHandleMessage(hash[hash.length - 1]);
		}
	}
	
	// Find the location of the scroll
	function yootagScrollPos(){
		if (self.pageYOffset !== undefined) {
			return {
				x: self.pageXOffset,
				y: self.pageYOffset
			};
		}
	}
	
	// Get the scroll position
	function yootagSetScroll(pos){
		var e = document.documentElement, b = document.body;
		e.scrollLeft = b.scrollLeft = pos.x;
		e.scrollTop = b.scrollTop = pos.y;
	}
	
	// Show the message from the iframe
	function yootagHandleMessage(msg){
		yootagClose();
	}
	
	// Remove the iFrame
	function yootagClose(){
		var yootagbox = document.getElementById('yootagbox');
		yootagbox.parentNode.removeChild(yootagbox);
		window.onscroll = null;
	}
	
	return {	
		addTagmarklet: function(){
			if(!document.getElementById('yootagbox')){
				var container = document.createElement("div");
				container.style.padding = "0";
				container.style.margin = "0";
				container.style.border = "1px solid";
				container.style.borderRadius = "10px";
				container.id = "yootagbox";
				container.style.position = "absolute";
				container.style.top = yootagScrollPos().y+1 + "px";
				container.style.right = "0";
				container.style.zIndex = 999999;
				container.style.width = "390px";
				container.style.height = "290px";
				container.innerHTML = '<iframe style="width:100%;height:100%;border:0px;border-radius:10px;background:#ffffff" id="yootagframe"></iframe>';
				document.body.appendChild(container);
				
				
				//set up message checking to run every so often
				var interval = window.setInterval(function(){
					yootagFrameMessage();
				}, 50);
				
				yootagSetIframe();
				//when the window scrolls, change the location of the box
				window.onscroll = function(){
					document.getElementById('yootagbox').style.top = yootagScrollPos().y+1 + "px";
				};
		    }
	    }
    }
}
yootagFunctions().addTagmarklet();