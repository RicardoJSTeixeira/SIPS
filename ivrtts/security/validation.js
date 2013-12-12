/**
    * validateInput
    * 
    * Validates an email or password, returns bootstrap labels and boolean validation result 
    *  
    * @param {String} validationString The string to be validated.
    * @param {String} validationType Type of input to be validated. Options: "password" or "email". 
    * @return {Object} Returns an Object with two values. <br><br> .label is the html code for the bootstrap label and .validation is a boolean with the result of the validation. 
**/
function validateInput(validationString, validationType){

    // function config
    var MaxPasswordLength = 16,
        MinPasswordLength = 4;
     //   MaxEmailLength = 128;
     //   MaxUsernameLength = 16,
     //   MinUsernameLength = 4;
    
    // Messages
    var msgDenied = "<span class='label label-important'>Denied :(</span>",
        msgShort = "<span class='label label-inverse'>Too Short!</span>",
        msgLong = "<span class='label label-inverse'>Too Long!</span>",
        msgWeak = "<span class='label label-important'>Weak :(</span>",
        msgAcceptable = "<span class='label label-warning'>Acceptable :|</span>",
        msgGood = "<span class='label label-info'>Good :)</span>",
        msgWrong = "<span class='label label-inverse'>Wrong :(</span>",
        msgVeryGood = "<span class='label label-success'>Very Good! :D</span>",
        msgEmpty = "<span class='label label-inverse'>Empty :|</span>"; // not working, yet :(
    
    // Email Check
    if(validationType === "email"){
       
       var isValid = true;
       
       var atIndex = validationString.indexOf("@"); 
       if ((atIndex === true || atIndex === false) && !atIndex)
       {
          isValid = false;
       }
       else
       {
          var domain = validationString.slice(atIndex+1);
          var local = validationString.substr(0, atIndex);
          var domainLength = domain.length;
          var localLength = local.length;
          
          if(localLength <1 || localLength > 64){
              // local part length exceeded
              isValid = false;
          }
          else if(domainLength < 1 || domainLength > 255){
              // domain part length exceeded
              isValid = false;
          }    
          else if(local[0] === '.' || local[localLength-1] === '.'){
              // local part start or ends with an dot
              isValid = false;
          }
          else if(domain[0] === '.' || domain[domainLength-1] === '.'){
              // domain part start or ends with an dot
              isValid = false;
          }
          else if(/\\.\\./.test(local)){
              // local part has 2 consecutive dots
              isValid = false;
          }
          else if(/\\.\\./.test(domain)){
              // domain part has 2 consecutive dots
              isValid = false;
          }
          else if(!/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/.test(local.replace('\\\\',''))){
              if(!/^"(\\\\"|[^"])+"$/.test(local.replace('\\\\',''))){
              isValid = false;
              }
          }
          else if(!/^[A-Za-z0-9\\-\\.]+$/.test(domain)){
              isValid = false;
          }
          else if(!/.+\@.+\..+/.test(validationString)){
              isValid = false;
          }
          
          if(isValid){ return {'label': msgGood, 'validation': 1}; } else { return {'label': msgWrong, 'validation': 0}; }
        }
   
    }
        
    // Password Check
    if(validationType === "password"){
        var PasswordStrength = 0;
        if(validationString.length === 0) { return {'label': msgEmpty, 'validation': 0}; }    
        if(/[^a-zA-Z0-9!,@,#,$,%,^,&,*,?,_,~]/.test(validationString)) {
            return {
                'label': msgDenied, 
                'validation': 0
            };   
        } 
        if(validationString.length < MinPasswordLength) {
            return {
                'label': msgShort, 
                'validation': 0
            };
        }         
        if(validationString.length > MaxPasswordLength) {
            return {
                'label': msgLong, 
                'validation': 0
            };
        } 
        if(/[a-z]/.test(validationString)){ PasswordStrength++; };
        if(/[A-Z]/.test(validationString)){ PasswordStrength++; };
        if(/[0-9]/.test(validationString)){ PasswordStrength++; };
        if(/[!,@,#,$,%,^,&,*,?,_,~]/.test(validationString)){ PasswordStrength++; };

        switch(PasswordStrength)
        {
            case 1:
                return {
                    'label': msgWeak, 
                    'validation': 1
                };
            case 2:
                return {
                    'label': msgAcceptable, 
                    'validation': 1
                };
            case 3:
                return {
                    'label': msgGood, 
                    'validation': 1
                };
            case 4:
                return {
                    'label': msgVeryGood, 
                    'validation': 1
                };
        } 
    }
};
