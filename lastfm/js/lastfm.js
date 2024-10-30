/*
    lastFM JS
*/


var lastFM = {
    
    nbSlide : 0,
    nbCurrent : 1,
    elemCurrent : null,
    elem : null,
    timer : null,
    
    init : function(elem){
        this.nbSlide = elem.find(".slide").length;
        
        // Initialisation du lastFM
        this.elem=elem;
        elem.find(".slide").hide();
        elem.find(".slide:first").show();
        this.elemCurrent = elem.find(".slide:first");
        this.elem.find(".next,.prev").css("opacity",0.6);   // On rend la navigation opaque
        this.elem.find(".next,.prev").hover(lastFM.over,lastFM.out);   // On rend la navigation opaque

	$(".prev").click(lastFM.next);
	$(".next").click(lastFM.prev);
    },
    
    gotoSlide : function(num){
        if(num==this.nbCurrent){ return false; }
        var sens = -1;
        if(num<this.nbCurrent){ sens = 1;}
        this.elem.find("#slide"+num).fadeIn(1000);
        this.elemCurrent.fadeOut(1000);
        this.nbCurrent = num;
        this.elemCurrent = this.elem.find("#slide"+num);
    },
    
    next : function(){
        var num  = lastFM.nbCurrent+1;
        if(num  >lastFM.nbSlide){
            num  = 1;
        }
        lastFM.gotoSlide(num);
    },
    prev : function(){
        var num  = lastFM.nbCurrent-1;
        if(num< 1){
            num= lastFM.nbSlide;
        }
        lastFM.gotoSlide(num);
    },
    
    over : function(){
	$(this).stop().fadeTo(500,1);
    },
    
    out : function(){
	$(this).stop().fadeTo(500,0.6);
    }

}

$(function(){
    lastFM.init($(".tracks"));
});