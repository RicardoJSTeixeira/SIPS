<?php 
/**
 * The following is an example source file for the Asterisk Manager library
 * 
 * PHP version 5
 *
 * @category  Net
 * @package   Net_AsteriskManager
 * @author    Doug Bromley <doug.bromley@gmail.com>
 * @copyright 2008 Doug Bromley
 * @license   New BSD License
 * @link      http://www.straw-dogs.co.uk
 *
 ***
 * Copyright (c) 2008, Doug Bromley <doug.bromley@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * - Redistributions of source code must retain the above copyright notice, 
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, 
 *   this list of conditions and the following disclaimer in the documentation 
 *   and/or other materials provided with the distribution.
 * - Neither the name of the <ORGANIZATION> nor the names of its 
 *   contributors may be used to endorse or promote products derived from 
 *   this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***
 * agi(googletts.agi,"text",[language],[intkey],[speed])
 */

/**
 * Including the Asterisk Manager library
 */
require "AsteriskManager.php";

function dial_out($som) {

/**
 * The parameters for connecting to the server
 */
$params = array('server' => '127.0.0.1', 'port' => '5038');

/**
 * Instantiate Asterisk object and connect to server
 */
$ast = new Net_AsteriskManager($params);

/**
 * Connect to server
 */
try {
    $ast->connect();
} catch (PEAR_Exception $e) {
    echo $e;
}

/**
 * Login to manager API
 */
try {
    $ast->login('cron', '1234');
} catch(PEAR_Exception $e) {
    echo $e;
}
 
//getQueues();

 /**
     * Make a call to an extension with a given channel acting as the originator
     *
     * @param string  $extension The number to dial
     * @param string  $channel   The channel where you wish to originate the call
     * @param string  $context   The context that the call will be dropped into 
     * @param string  $cid       The caller ID to use
     * @param integer $priority  The priority of this command
     * @param integer $timeout   Timeout in milliseconds before attempt dropped
     * @param array   $variables An array of variables to pass to Asterisk
     * @param string  $action_id A unique identifier for this command
     *
     * @return bool
     */
try{	   
	//agi(googletts.agi,"text",[language],[intkey],[speed])
	
	
	$vars = "var_teste=".$som;
	
	$ast->originateCall('01219201281','SIP/103','default', 'João Silva', 1, 30000, $vars);
	

 
} catch(PEAR_Exception $erro) {
	echo $erro; } }
?>
