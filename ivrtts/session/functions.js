function LogOut(userToLogout){
    $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "../session/requests.php", 
            data: { zero: 'Logout' },
            success: function(data){ 
                                    window.location = '../index.php'; 
                                  
                                      
                                    }
            });
  
} 
