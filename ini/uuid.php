 <?php
 /**
 * Logging class:
 * - contains lfile, lwrite and lclose public methods
 * - lfile sets path and name of log file
 * - lwrite writes message to the log file (and implicitly opens log file)
 * - lclose closes log file
 * - first call of lwrite method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class UUID {
	public function uuid() {
	    //uuid version 4
	    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		    // 32 bits for "time_low"
		    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		    // 16 bits for "time_mid"
		    mt_rand( 0, 0xffff ),

		    // 16 bits for "time_hi_and_version",
		    // four most significant bits holds version number 4
		    mt_rand( 0, 0x0fff ) | 0x4000,

		    // 16 bits, 8 bits for "clk_seq_hi_res",
		    // 8 bits for "clk_seq_low",
		    // two most significant bits holds zero and one for variant DCE1.1
		    mt_rand( 0, 0x3fff ) | 0x8000,

		    // 48 bits for "node"
		    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	    );
    }
    
    public function __toString() {
       $this->uuid();
    }
}

