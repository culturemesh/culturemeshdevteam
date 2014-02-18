//for spinner
var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

function refresh(){
    location.reload();
}
function getMembers(){
    var members = "[&quot;stall&quot;,&quot;bathroom&quot;,&quot;clothing&quot;,&quot;shoes&quot;]";//["apples","oranges","houses","grapes"];
    return members;
}