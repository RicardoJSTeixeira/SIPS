package uk.co.westhawk.playgsm;

import java.awt.*;
import java.awt.event.*;
import java.applet.*;
import java.net.URL;

/**
 * <p>Title: </p>
 * <p>Description: </p>
 * <p>Copyright: Copyright (c) 2006</p>
 * <p>Company: </p>
 * @author not attributable
 * @version 1.0
 */

public class GSMPlayerApplet extends Applet {

    String _furl;
    PlayGSM _play;
    private String _error = "";


    //Construct the applet
    public GSMPlayerApplet() {
    }

    //Initialize the applet
    public void init() {
        try {
            _furl = this.getParameter("url");
        }
        catch(Exception e) {
            e.printStackTrace();
        }
        try {
            jbInit();
        }
        catch(Exception e) {
            e.printStackTrace();
        }
    }

    //Component initialization
    private void jbInit() throws Exception {
        _play = new PlayGSM();
    }

    //Start the applet
    public void start() {
        if (_furl != null){
            load(_furl);
            play();
        }
    }

    public void play(){
        _play.play();
    }

    public void stop() {
        _play.astop();
    }
    
    public void pause(){
        _play.pause();
    }
    
    public int getPlayerStatus(){
        return _play.getStatus();
    }

    //Destroy the applet
    public void destroy() {
        _play.audioCleanup();
    }
/*load / play / stop / pause */
    public void load(String url){
        try {
            URL u = new URL(url);
            _play.load(u);
        } catch (Exception x){
            _error = x.getMessage();
            System.err.println(x.getMessage());
        }
    }
    //Get Applet information
    public String getAppletInfo() {
        return "Applet Information";
    }

    //Get parameter info
    public String[][] getParameterInfo() {
        String[][] pinfo = 
            {
            {"url", "String", ""},
            };
        return pinfo;
    }
}
