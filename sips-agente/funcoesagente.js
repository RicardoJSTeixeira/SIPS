// JavaScript Document

function mpass(user) {
	
	
	var url = "alterarpassword.php?user=" + user;
	
	//window.open(url, "Alter Password", "status=no, width=900, height=350, menubar =no, titlebar=no, scrollbars=yes");
	
	
	testwindow = window.open("url", "mywindow", "location=1,status=1,scrollbars=1,width=100,height=100");
    testwindow.moveTo(0, 0);
	
	alert('passou');
	
}