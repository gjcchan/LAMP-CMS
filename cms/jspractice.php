<!DOCTYPE html>
<html>

/// head is for when javascript functions	
	
<head>
<script type="text/javascript">
	
var testvalue = "5";
function myFunction()
{
document.getElementById("demo").innerHTML="My First JavaScript Function";
document.write("just printing");
}
</script>
</head>

///end of head





<body>

<h1>My Web Page</h1>
<p id="demo">A Paragraph.</p>                                       //p id allows you to change properties linking trait with id to modify
<button type="button" onclick="myFunction()">Try it</button>
</body>




//<script type="text/javascript" src="myScript.js"></script>   allows you to import other js



</html> 
	/*

var day=new Date().getDay();
switch (day)
{
case 0:
  x="Today it's Sunday";
  break;
case 1:
  x="Today it's Monday";
  break;
case 2:
  x="Today it's Tuesday";
  break;
case 3:
  x="Today it's Wednesday";
  break;
case 4:
  x="Today it's Thursday";
  break;
case 5:
  x="Today it's Friday";
  break;
case 6:
  x="Today it's Saturday";
  break;
  -----------------------------------------------
  var r=confirm("Press a button");
if (r==true)
  {
  x="You pressed OK!";
  }
else
  {
  x="You pressed Cancel!";
  } 
  
  
  ---------------------------------------------]
  var name=prompt("Please enter your name","Harry Potter");
if (name!=null && name!="")
  {
  x="Hello " + name + "! How are you today?";
  } 
  
  ---------------------------------------------------
  for (i=0; i<5; i++)
  {
  x=x + "The number is " + i + "<br />";
  }
  
  -------------------------------------------------
  
  do
  {
  x=x + "The number is " + i + "<br />";
  i++;
  }
while (i<5);
	
	--------------------------------------------
	
	
	break = if you wanna break out of loop
	continue = break out of current iteration and onto next iteration of loop
	
	------------------------------------------
	onchange of certain properties (i.e. change of text in textbox)
	<input type="text" size="30" id="email" onchange="checkEmail()" />
	
	other variations:
	onsubmit
	onload = loading site
	onunload = when leaving site
	onmouseover = when mouse over
}   */