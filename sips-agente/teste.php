<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript">
  function getPhone() {
  return document.getElementById('phone');
  }
	  function debug(message) {
	  debugdiv = document.getElementById('debug');
	  debugdiv.innerHTML = debugdiv.innerHTML + message + "<br/>";
  }
  function doRegister() {
	  debug("doRegister called");
	  var host = document.getElementById('iaxhost').value;
	  var cnumber = document.getElementById('cnumber').value;
	  var cname = document.getElementById('cname').value;
	  var user = document.getElementById('user').value;
	  var pass = document.getElementById('pass').value;
	  debug("Host: "+host);
	  debug("Calling Number: "+cnumber);
	  debug("Calling Name: "+cname);
	  debug("User: "+user);
	  debug("Secret: *********");
	  phone = getPhone();
	  phone.setHost(host);
	  phone.setCallingNumber(cnumber);
	  phone.setCallingName(cname);
	  phone.setUser(user);
	  phone.setPass(pass);
	  phone.setWantIncoming(true);
	  phone.register();
  }
  function doDial() {
	  debug("doDial invoked");
	  phone = getPhone();
	  var number = document.getElementById('number').value;
	  debug("dial "+number+" called");
	  phone.dial(number);
  }
  function doHangup() {
	  debug("doHangup invoked");
	  phone = getPhone();
	  phone.hangup();
  }
  function setup() {
	  debug("Applet setup");
	  getAudioInDevices();
	  getAudioOutDevices();
	  phone = getPhone();
	  phone.setAudioIn(phone.getAudioInList(0));
	  phone.setAudioOut(phone.getAudioOutList(0));
  }
  // Callback functions here
  function loaded() {
  	debug("Applet loaded");
  }
  function registered(status) {
 	 debug("Registered callback status: "+status);
  }
  function hostreachable(status, roundtrip) {
  	debug("Host reachable message received, status: "+status+", roundtrip: "+roundtrip);
  }
  function newCall(inbound, far, near, answered, callingname) {
	  if (inbound == "true") {
	  // new incoming call
	  debug("Incoming call...");
	  debug("Far: "+far);
	  debug("Near: "+near);
	  debug("Answered: "+answered);
	  debug("callingname: "+callingname);
  } else {
  // new outbound call
	  debug("Outbound call...");
	  debug("Far: "+far);
	  debug("Near: "+near);
	  debug("Answered: "+answered);
	  debug("callingname: "+callingname);
  }
  }
  function hungUp(causecode) {
  	debug("Hungup, CauseCode: "+causecode);
  }
  function ringing(inbound, far, near, answered, callingname) {
	  if (inbound == "true") {
	  // new incoming call
	  debug("Incoming ringing...");
	  debug("Far: "+far);
	  debug("Near: "+near);
	  debug("Answered: "+answered);
	  debug("callingname: "+callingname);
  } else {
  // new outbound call
	  debug("Outbound ringing...");
	  debug("Far: "+far);
	  debug("Near: "+near);
	  debug("Answered: "+answered);
	  debug("callingname: "+callingname);
  }
  }
  function answered(inbound, far, near, answered, callingname) {
	  if (inbound == "true") {
	  // new incoming call
	  debug("Incoming answered...");
	  debug("Far: "+far);
	  debug("Near: "+near);
	  debug("Answered: "+answered);
	  debug("callingname: "+callingname);
  } else {
  // new outbound call
	  debug("Outbound answered...");
	  debug("Far: "+far);
	  debug("Near: "+near);
	  debug("Answered: "+answered);
	  debug("callingname: "+callingname);
  }
  }
  function getAudioInDevices() {
	  phone = getPhone();
	  devices = phone.getAudioInListLen();
	  for(var i=0;i<devices;i++) {
	  debug("Audio In Device: "+phone.getAudioInList(i));
  }
  debug("Current Audio In Device: "+phone.getAudioIn());
  }
	  function getAudioOutDevices() {
	  phone = getPhone();
	  devices = phone.getAudioOutListLen();
	  for(var i=0;i<devices;i++) {
	  debug("Audio Out Device: "+phone.getAudioOutList(i));
  }
  	debug("Current Audio Out Device: "+phone.getAudioOut());
  } 
  
  </script>
  
  
  
  </head>
  
  <body>
  
  <applet code="JavaIAXApplet" archive="JavaIAXApplet.jar" width="500" height="500" alt="IAX2 Java Phone" id="phone" title="IAX2 Java Phone">
  
  </applet>
  
  <div id="debug" style="top:0px;left:0px;width:500px;height:200px;border-width:1px;border-style:solid;overflow:auto;">
  </div>
  
  <form>
  IAX Host:
  <input id="iaxhost" type="text">
  <br>
  Calling Number:
  <input id="cnumber" type="text">
  <br>
  Calling Name:
  <input id="cname" type="text">
  <br>
  Username:
  <input id="user" type="text">
  <br>
  Secret:
  <input id="pass" type="password">
  <br>
  <input id="register" type="button" onclick="javascript:doRegister();" value="Register">
  <br>
  <br>
  Number to dial:
  <input id="number" type="text">
  <input id="dial" type="button" onclick="javascript:doDial();" value="Dial now">
  <br>
  <input id="hangup" type="button" onclick="javascript:doHangup();" value="Hangup">
  <br>
  <input id="answer" type="button" onclick="javascript:answer();" value="Answer">
  <br>
  </form>
  </body>
  </html>
