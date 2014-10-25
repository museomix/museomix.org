$(document).ready(function(){
	/*$('.bs-docs-sidenav').affix({
		offset: {
			top: function () { return $(window).width() <= 980 ? 290 : 210 },
			bottom: 270,
		}
	})*/
	$('.bloc-flux').first().each(function(){
		Flux($(this),0);
	})	
	$('body').on('submit','.formulaire-google',function(){
		EnvoyerFormPost($(this));
		return false;
	});
		/* Header on home animation */
	if ($('body.home').length>0) {
		$(window).scroll(function() {
		   if($(window).scrollTop() > $('#museomix_banner').height()) {
				$('#museomix_banner').css( 'opacity' , 0);
				$('.bouton-nav-accueil').show();
				$('div.navbar-fixed-top').css('position','fixed');
		   }
		   if($(window).scrollTop() < ($('#museomix_banner').height()-10)) {
				$('.bouton-nav-accueil').hide();
				$('#museomix_banner').css( 'opacity' , 1);
				$('div.navbar-fixed-top').css('position','relative');
		   }
		});
	}
});
/*
$(function(){
  $(window).scroll(function(){

    if($(this).scrollTop()>= 0){
        $('.navbar .container').animate({
		paddingTop:'0px'
	},500,function(){
		$('.navbar .container').css('background-image','none');
		$('.navbar').addClass('navbar-fixed-top');
	});
    }else{
		$('.navbar .container').animate({
		paddingTop:'270px'
	},500,function(){
		$('.navbar .container').css('background-image','url("../img/header.png")');
		$('.navbar').removeClass('navbar-fixed-top');
	});
	}
  });
});
*/
function Flux(bloc,num){
	var requete = bloc.dataset('requete');
	requete = requete.replace("$","#");
	var action = (bloc.is('.flux-twitter'))? 'fluxtwitter' : 'fluxagenda';
	action = (bloc.is('.bloc-googleform'))? 'googleform' : action;
	action = (bloc.is('.bloc-tableurgoogle'))? 'tableurgoogle' : action;
	var anim = bloc.find('.anim-charg');
	var idAnim = anim.attr('id');
	var anim;
	if(action=='googleform'){
//		alert(requete);
//		return;
	}
	$.ajax({
		type: 'GET',
		url: $ServUrl,
		data: 'action='+action+'&requete='+encodeURIComponent(requete),
		datatype: 'html',
		beforeSend: function(){
			anim = AnimAjax(idAnim);
		},
		success: function(data){
			var r = $(data).find('.resultat-import');
			if(!r.size()){
				alert(data);
			}else{
				bloc.append(r);
			}
		},
		complete:function(){
			anim.kill();
			num += 1;
			$('#ss-submit').addClass('btn').after($('<div class="avertissement-form">Merci de remplir tous les champs / Please enter information for all fields</div>'));
			var suiv = $('.bloc-flux').eq(num);
			if(suiv.size()){ 
				Flux(suiv,num);
			}
		}
	})
}


function EnvoyerFormPost(form){
	if(!VerifierChamps(form)){
		return false;
	}
	var champs = form.serialize();
	var anim = $('.modal').find('.anim-charg');
	var idAnim = anim.attr('id');
	var anim;	
	$.ajax({
		type: 'POST',
		url: form.attr('action'),
		data: champs,
		dataType: 'HTML',
		beforeSend: function(){
			$('.modal-body').html('enregistrement...');
			$('.modal').modal({backdrop: true});		
			anim = AnimAjax(idAnim);
		},
		success: function(data){
			$('.modal-body').html($(data));
			form.each(function(){
  				this.reset();
			});
		},
		complete:function(){
			anim.kill();
		}
	})
}

function VerifierChamps(form){
	var invalid = [];
	var groupe = false;
	var radio = [];
	form.find('.ss-form-entry').tooltip('destroy');
	$.each(form.find('input[type="radio"]'),function(){
		var champ = $(this);
		var nom = champ.attr('name');
		if(groupe == nom){ return true; }
		groupe = nom;
		radio.push(nom);
	});
	$.each(radio,function(i,val){
		if(!form.find('input[name="'+val+'"]').filter(':checked').size()){
			invalid.push(val);
		}
	})
	form.find('input[type="text"]').each(function(){
		var champ = $(this);
		var val = $.trim(champ.val());
		if(val.length==0){
			invalid.push(champ.attr('name'));
		}
	})
	form.find('textarea').each(function(){
		var champ = $(this);
		var val = $.trim(champ.val());
		if(val.length==0){
			invalid.push(champ.attr('name'));
		}
	})
	if(invalid.length>0){
		$.each(invalid,function(i,val){
			var parent = $('input[name="'+val+'"],textarea[name="'+val+'"]').closest('.ss-form-entry');
			parent.tooltip({
				'title':'Ce champ est obligatoire / Mandatory field',
				'placement':'right',
				'trigger':'focus'
			}).tooltip('show');
		});
		$('.modal-body').html('Merci de renseigner tous les champs / Please enter a valid information for all fields');
		$('.modal').modal({backdrop: true});
		return false;
	}
	return true;
}


/* animation chargement
   ==================== */
Anims = [];
function AnimAjax(idBloc){
	anim = new CanvasLoader(idBloc); 
	anim.setColor('#00B7E8');
	anim.setDiameter(35); 
	anim.setDensity(10);
	anim.setRange(1.3);
	anim.setSpeed(2);
	anim.setFPS(14); 			
	anim.show();
	return anim;
}
function StopAnimAjax(){
	if(Anims.idBloc){
		var anim = Anims.idBloc;
		anim.kill();
	} 
}



/*
  ===================
*/
/* CanvasLoader */

(function(w){var k=function(b,c){typeof c=="undefined"&&(c={});this.init(b,c)},a=k.prototype,o,p=["canvas","vml"],f=["oval","spiral","square","rect","roundRect"],x=/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/,v=navigator.appVersion.indexOf("MSIE")!==-1&&parseFloat(navigator.appVersion.split("MSIE")[1])===8?true:false,y=!!document.createElement("canvas").getContext,q=true,n=function(b,c,a){var b=document.createElement(b),d;for(d in a)b[d]=a[d];typeof c!=="undefined"&&c.appendChild(b);return b},m=function(b,
c){for(var a in c)b.style[a]=c[a];return b},t=function(b,c){for(var a in c)b.setAttribute(a,c[a]);return b},u=function(b,c,a,d){b.save();b.translate(c,a);b.rotate(d);b.translate(-c,-a);b.beginPath()};a.init=function(b,c){if(typeof c.safeVML==="boolean")q=c.safeVML;try{this.mum=document.getElementById(b)!==void 0?document.getElementById(b):document.body}catch(a){this.mum=document.body}c.id=typeof c.id!=="undefined"?c.id:"canvasLoader";this.cont=n("div",this.mum,{id:c.id});if(y)o=p[0],this.can=n("canvas",
this.cont),this.con=this.can.getContext("2d"),this.cCan=m(n("canvas",this.cont),{display:"none"}),this.cCon=this.cCan.getContext("2d");else{o=p[1];if(typeof k.vmlSheet==="undefined"){document.getElementsByTagName("head")[0].appendChild(n("style"));k.vmlSheet=document.styleSheets[document.styleSheets.length-1];var d=["group","oval","roundrect","fill"],e;for(e in d)k.vmlSheet.addRule(d[e],"behavior:url(#default#VML); position:absolute;")}this.vml=n("group",this.cont)}this.setColor(this.color);this.draw();
m(this.cont,{display:"none"})};a.cont={};a.can={};a.con={};a.cCan={};a.cCon={};a.timer={};a.activeId=0;a.diameter=40;a.setDiameter=function(b){this.diameter=Math.round(Math.abs(b));this.redraw()};a.getDiameter=function(){return this.diameter};a.cRGB={};a.color="#000000";a.setColor=function(b){this.color=x.test(b)?b:"#000000";this.cRGB=this.getRGB(this.color);this.redraw()};a.getColor=function(){return this.color};a.shape=f[0];a.setShape=function(b){for(var c in f)if(b===f[c]){this.shape=b;this.redraw();
break}};a.getShape=function(){return this.shape};a.density=40;a.setDensity=function(b){this.density=q&&o===p[1]?Math.round(Math.abs(b))<=40?Math.round(Math.abs(b)):40:Math.round(Math.abs(b));if(this.density>360)this.density=360;this.activeId=0;this.redraw()};a.getDensity=function(){return this.density};a.range=1.3;a.setRange=function(b){this.range=Math.abs(b);this.redraw()};a.getRange=function(){return this.range};a.speed=2;a.setSpeed=function(b){this.speed=Math.round(Math.abs(b))};a.getSpeed=function(){return this.speed};
a.fps=24;a.setFPS=function(b){this.fps=Math.round(Math.abs(b));this.reset()};a.getFPS=function(){return this.fps};a.getRGB=function(b){b=b.charAt(0)==="#"?b.substring(1,7):b;return{r:parseInt(b.substring(0,2),16),g:parseInt(b.substring(2,4),16),b:parseInt(b.substring(4,6),16)}};a.draw=function(){var b=0,c,a,d,e,h,k,j,r=this.density,s=Math.round(r*this.range),l,i,q=0;i=this.cCon;var g=this.diameter;if(o===p[0]){i.clearRect(0,0,1E3,1E3);t(this.can,{width:g,height:g});for(t(this.cCan,{width:g,height:g});b<
r;){l=b<=s?1-1/s*b:l=0;k=270-360/r*b;j=k/180*Math.PI;i.fillStyle="rgba("+this.cRGB.r+","+this.cRGB.g+","+this.cRGB.b+","+l.toString()+")";switch(this.shape){case f[0]:case f[1]:c=g*0.07;e=g*0.47+Math.cos(j)*(g*0.47-c)-g*0.47;h=g*0.47+Math.sin(j)*(g*0.47-c)-g*0.47;i.beginPath();this.shape===f[1]?i.arc(g*0.5+e,g*0.5+h,c*l,0,Math.PI*2,false):i.arc(g*0.5+e,g*0.5+h,c,0,Math.PI*2,false);break;case f[2]:c=g*0.12;e=Math.cos(j)*(g*0.47-c)+g*0.5;h=Math.sin(j)*(g*0.47-c)+g*0.5;u(i,e,h,j);i.fillRect(e,h-c*0.5,
c,c);break;case f[3]:case f[4]:a=g*0.3,d=a*0.27,e=Math.cos(j)*(d+(g-d)*0.13)+g*0.5,h=Math.sin(j)*(d+(g-d)*0.13)+g*0.5,u(i,e,h,j),this.shape===f[3]?i.fillRect(e,h-d*0.5,a,d):(c=d*0.55,i.moveTo(e+c,h-d*0.5),i.lineTo(e+a-c,h-d*0.5),i.quadraticCurveTo(e+a,h-d*0.5,e+a,h-d*0.5+c),i.lineTo(e+a,h-d*0.5+d-c),i.quadraticCurveTo(e+a,h-d*0.5+d,e+a-c,h-d*0.5+d),i.lineTo(e+c,h-d*0.5+d),i.quadraticCurveTo(e,h-d*0.5+d,e,h-d*0.5+d-c),i.lineTo(e,h-d*0.5+c),i.quadraticCurveTo(e,h-d*0.5,e+c,h-d*0.5))}i.closePath();i.fill();
i.restore();++b}}else{m(this.cont,{width:g,height:g});m(this.vml,{width:g,height:g});switch(this.shape){case f[0]:case f[1]:j="oval";c=140;break;case f[2]:j="roundrect";c=120;break;case f[3]:case f[4]:j="roundrect",c=300}a=d=c;e=500-d;for(h=-d*0.5;b<r;){l=b<=s?1-1/s*b:l=0;k=270-360/r*b;switch(this.shape){case f[1]:a=d=c*l;e=500-c*0.5-c*l*0.5;h=(c-c*l)*0.5;break;case f[0]:case f[2]:v&&(h=0,this.shape===f[2]&&(e=500-d*0.5));break;case f[3]:case f[4]:a=c*0.95,d=a*0.28,v?(e=0,h=500-d*0.5):(e=500-a,h=
-d*0.5),q=this.shape===f[4]?0.6:0}i=t(m(n("group",this.vml),{width:1E3,height:1E3,rotation:k}),{coordsize:"1000,1000",coordorigin:"-500,-500"});i=m(n(j,i,{stroked:false,arcSize:q}),{width:a,height:d,top:h,left:e});n("fill",i,{color:this.color,opacity:l});++b}}this.tick(true)};a.clean=function(){if(o===p[0])this.con.clearRect(0,0,1E3,1E3);else{var b=this.vml;if(b.hasChildNodes())for(;b.childNodes.length>=1;)b.removeChild(b.firstChild)}};a.redraw=function(){this.clean();this.draw()};a.reset=function(){typeof this.timer===
"number"&&(this.hide(),this.show())};a.tick=function(b){var a=this.con,f=this.diameter;b||(this.activeId+=360/this.density*this.speed);o===p[0]?(a.clearRect(0,0,f,f),u(a,f*0.5,f*0.5,this.activeId/180*Math.PI),a.drawImage(this.cCan,0,0,f,f),a.restore()):(this.activeId>=360&&(this.activeId-=360),m(this.vml,{rotation:this.activeId}))};a.show=function(){if(typeof this.timer!=="number"){var a=this;this.timer=self.setInterval(function(){a.tick()},Math.round(1E3/this.fps));m(this.cont,{display:"block"})}};
a.hide=function(){typeof this.timer==="number"&&(clearInterval(this.timer),delete this.timer,m(this.cont,{display:"none"}))};a.kill=function(){var a=this.cont;typeof this.timer==="number"&&this.hide();o===p[0]?(a.removeChild(this.can),a.removeChild(this.cCan)):a.removeChild(this.vml);for(var c in this)delete this[c]};w.CanvasLoader=k})(window);

/* dataset */
(function($) {
	var PREFIX='data-',PATTERN=/^data\-(.*)$/;
	function dataset(name,value){if(value!==undefined){return this.attr(PREFIX+name,value);}
	switch(typeof name){case 'string':return this.attr(PREFIX+name);case 'object':return set_items.call(this,name);case 'undefined':return get_items.call(this);default:throw 'dataset: invalid argument '+name;}}
	function get_items() {return this.foldAttr(function(index,attr,result){var match=PATTERN.exec(this.name);if(match)result[match[1]]=this.value;});}
	function set_items(items){for(var key in items){this.attr(PREFIX+key,items[key]);}return this;}
	function remove(name){if(typeof name=='string'){return this.removeAttr(PREFIX+name);}return remove_names(name);}
	function remove_names(obj){var idx,length=obj&&obj.length;if(length===undefined){for(idx in obj){this.removeAttr(PREFIX+idx);}}else{for (idx=0;idx<length;idx++){this.removeAttr(PREFIX+obj[idx]);}}return this;}
	$.fn.dataset=dataset;$.fn.removeDataset=remove_names;
})(jQuery);
(function($) {
	function each_attr(proc){if(this.length>0){$.each(this[0].attributes, proc);}return this;}
	function fold_attr(proc, acc){return fold((this.length > 0) && this[0].attributes, proc, acc);}
	function fold(object,proc,acc){var length=object&&object.length;if(acc===undefined)acc={};if(!object)return acc;if(length!==undefined){for(var i=0,value=object[i];(i<length)&&(proc.call(value,i,value,acc)!==false);value = object[++i]){}}else{for(var name in object){if(proc.call(object[name],name,object[name],acc)=== false)break;}}return acc;}
	function fold_jquery(proc,acc){if(acc===undefined)acc=[];return fold(this,proc,acc);}
	$.fn.eachAttr=each_attr;$.fn.foldAttr=fold_attr;$.fn.fold=fold_jquery;$.fold=fold;
})(jQuery);		

