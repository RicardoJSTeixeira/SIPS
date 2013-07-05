var esl = require('modesl'),
url = require('url'),
mysql = require('mysql'),
reqUrl,
http = require('http'),
jquery = require('jquery'),
request = require('request'),
fs = require('fs'),
sys = require('./gocc-system.json');

  console.log(sys);
http.createServer(function(request, response) {
    
    reqUrl = url.parse(request.url, true);
    var program = reqUrl.query.program;
    var action = reqUrl.query.action;
    switch (program) {
        case 'agent': switch (action) {
            case 'login' : agent_login(request, response, reqUrl.query.ext, sys);
        }
    }
}).listen(60000);        
        

function agent_login(request, response, ext, sys) {
    console.log(sys.mysql);
    var mysqlConn = mysql.createConnection({
            host     : sys.mysql.server_ip,
            user     : sys.mysql.user,
            password : sys.mysql.password,
            database : sys.mysql.database
        });
       
       mysqlConn.connect();
       var conf;
       mysqlConn.query("SELECT conf FROM conferences WHERE ext LIKE '' LIMIT 1", function(err, rows, fields) {
        if (err) throw err; 
        conf = rows[0].conf;
        mysqlConn.query("UPDATE conferences set ext = '"+ext+"' WHERE conf LIKE '"+conf+"'", function(err) {
            if (err) throw err; 
        });
       });
       
        conn = new esl.Connection(sys.esl.server_ip, sys.esl.port, sys.esl.password, function() {
            conn.api("originate", "{on_hangup='perl /var/www/htdocs/node/logout.pl'}user/"+ext+"@sips &conference("+conf+")", function(res) {
                var uuid = res.body.split(" ");
                uuid = uuid[1];
                uuid = uuid.replace(/(\r\n|\n|\r)/gm,"");
                mysqlConn.query("UPDATE conferences set uuid = '"+uuid+"' WHERE conf LIKE '"+conf+"'", function(err) {
                    if (err) throw err; 
                    mysqlConn.end();
                    response.write("OK "+ uuid + " " + conf, "utf8");
                    response.end();
                });
            });
        });
}        
        
        
function testCall(request, response) {
    
     response.writeHead(200, {
			'Content-Type'   : 'text/plain',
			'Access-Control-Allow-Origin' : '*'
		});
    var action = reqUrl.query.action;
    var tlf = reqUrl.query.tlf;
    var ext = reqUrl.query.ext;

    if (action === 'kill') {
        response.write('kill OK', 'utf8');
        response.end();   
        process.exit(0);   
    } 
    else if (action === 'login')
    {
        
    }
    else if (action === 'mdial')
    {
        conn = new esl.Connection('127.0.0.1', 8021, 'ClueCon', function() {
        conn.bgapi("originate", "sofia/gateway/sips-gwtesteg9/"+tlf+" &park", function(res) {
            response.write(res.getBody(), "utf8");
        });
            conn.events("json", "CHANNEL_HANGUP CHANNEL_ANSWER", function(){ 
                conn.filter("Event-Name", "CHANNEL_HANGUP", function(){
                    console.log("Filtro de Hangup Adicionado");
                    conn.filter("Event-Name", "CHANNEL_ANSWER", function(){  
                        console.log("Filtro de Answer Adicionado");
                        conn.recvEvent(function(res){
                            console.log(res);
                            response.write(JSON.stringify(res), "utf8");
                            response.end(); 
                        });
                    });
                });
            });    
        });
    }
}

