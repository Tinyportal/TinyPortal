//Copyright © 2005-2007, John Goodman - john.goodman(at)unverse.net  *date 070927 v6 //No switch statement
//Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
//The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 
 
var regHyph=new RegExp(); 
regHyph.compile("-$");
var regCmt=new RegExp(); 
regCmt.compile("^<!--(.*)-->$"); 

function tidyCmt(tx){ 
 if(regHyph.exec(tx)){ tx+=" "; }
 return "<!--"+tx+"-->"; 
}
function tidyTxt(tx){
 return String(tx).replace(/\n{2,}/g,"\n").replace(/\&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\u00A0/g,"&nbsp;");
}
function tidyAt(tx){
 return String(tx).replace(/\&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
} 
function get_xhtml(node,banTags,ndNl,inPre){//banTags=eg '|style|class|'
banTags='|style|class|span|o|'; //TESTING!!!!!!!!!!!!!!!!!!!!!!!!!!
 var nlTag='|div|p|table|tbody|tr|td|th|title|script|comment|li|h1|h2|h3|h4|h5|h6|hr|ul|ol|option|select|'; 
 var tagNl='|br|div|p|style|';  
 var i,j,atVal,tVal,kid,tagNm,bits,innerTx,attr,atLn,isAlt,atNm,valid_attr; 
 var tx=''; 
 var kids=node.childNodes; 
 var kidsL=kids.length; 
 var tagNm; 
 var doNl=ndNl?true:false; 
 var sz=["small","xx-small","x_small","small","medium","large","x-large","xx-large"];
 for(i=0; i<kidsL; i++){ //for each child node
  kid=kids[i]; 
  if(kid.nodeType==8){         //comment node
   tx+=tidyCmt(kid.nodeValue);
  } else if (kid.nodeType==3){ //text node
   if(!inPre){
    if(kid.nodeValue!='\n'){ tx+=tidyTxt(kid.nodeValue); }
   }
   else {tx+=kid.nodeValue;}
  }else if(kid.nodeType==1){  //element node
    tagNm=String(kid.tagName).toLowerCase(); 
    if(tagNm=='') break; 
    if(banTags.indexOf('|'+tagNm+'|')!=-1) continue;  //NOT WORKING IN FIREFOX?? !!!!!!!!!!!!!!!!!!!!!!!!!!!
    if(tagNm=='font') { 
     if (kid.size) {kid.style.fontSize=sz[kid.size]; kid.removeAttribute('size');} 
     if (kid.face) {kid.style.fontFamily=kid.face; kid.removeAttribute('face');}
     if (kid.color) {kid.style.color=kid.color; kid.removeAttribute('color');}
     tagNm='span'; 
     }
    if(tagNm=='!'){
     bits=regCmt.exec(kid.tx); 
     if(bits){
      innerTx=bits[1]; 
      tx+=tidyCmt(innerTx); 
     }
    }else{
     if(nlTag.indexOf('|'+tagNm+'|')!=-1){
      if((doNl||tx!='')&&!inPre)tx+='\n'; 
      else doNl=true; 
     }
     tx+='<'+tagNm; 
     attr=kid.attributes; 
     atLn=attr.length;
     isAlt=false; 
     for(j=0; j<atLn; j++){
      atNm=attr[j].nodeName.toLowerCase(); 
      if(banTags.indexOf('|'+atNm+'|')!=-1) continue; //?????????????????????????????
      if(!attr[j].specified&&(atNm!='selected'||!kid.selected)
         && (atNm!='style'||kid.style.cssText=='')
         && atNm!='value')
       continue; 
      if(atNm=='_moz_dirty'||atNm=='_moz_resizing'||tagNm=='br'&&atNm=='type'&&kid.getAttribute('type')=='_moz')
       continue; 
      valid_attr=true;
      switch(atNm){
       case "color":atNm="style"; 
        tVal="color:"+kid.color+";";
        if (window.atVal){atVal+=tVal ;}else{atVal=tVal;}
        break;
       case "style":atVal=kid.style.cssText.toLowerCase(); 
        break; 
       case "class":atVal=kid.className; 
        break; 
       case "noshade":
       case "checked":
       case "selected":
       case "multiple":
       case "nowrap":
       case "disabled":atVal=atNm;
        break; 
       default:try{
        atVal=kid.getAttribute(atNm,2); 
       }catch(e){ valid_attr=false; }
      }
      if(valid_attr){
       if(!(tagNm=='li'&&atNm=='value')){
        tx+=' '+atNm+'="'+tidyAt(atVal)+'"';  
       }
      }
      if(atNm=='alt')isAlt=true; 
     }
     if(tagNm=='img'&&!isAlt) tx+=' alt=" "';
    if(kid.canHaveChildren||kid.hasChildNodes()){
     tx+='>'; 
     tx+=get_xhtml(kid,banTags,true,inPre||tagNm=='pre'?true:false); 
     tx+='</'+tagNm+'>'; 
    }else{ 
     if(tagNm=='style'||tagNm=='title'||tagNm=='script'){
      tx+='>';  
      if(tagNm=='script'){
       innerTx=kid.tx; 
      }else innerTx=kid.innerHTML; 
      if(tagNm=='style'){
       innerTx=String(innerTx).replace(/[\n]+/g,'\n'); 
      }
      tx+=innerTx+'</'+tagNm+'>'; 
     }else{ tx+=' />'; }
    }
   } 
   if(tagNl.indexOf('|'+tagNm+'|')!=-1){tx+='\n';}
  } //element node
 } //for each child node
 return tx; 
}