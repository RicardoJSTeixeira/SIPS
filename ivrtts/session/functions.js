function LogOut(userToLogout){
    $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "../tools/session/requests.php", 
            data: { zero: 'Logout' },
            success: function(data){ 
                                    // console.log(data.result); 
                                    window.location = '../index.php'; 
                                   /* $.ajax({
                                            type: "POST",
                                            url: "/api_call/functions.php",
                                            data: { zero: 'api_meetme_logout', user_id: userToLogout },
                                            success: function(data){ 
                                                                    // console.log(data);
                                                                    window.location = '/index.php'; 
                                                                    }
                                            }); */
                                      
                                    }
            });
  
} 
