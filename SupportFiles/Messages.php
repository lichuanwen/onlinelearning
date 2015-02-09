<?php
/* Description: Class to allow the creation of custom messages via the web interface.
   Dependencies: The output is dependant on the CSS that determines the layout of HTML
   content on the page.
   Usage: $message = new Message("This is a test...","WA","HTML",TRUE,"400");
               $message->showMessage();
 */

class Message{

    private $message;  // the message
    private $type;  // type of message
    private $msgOutputStream;  // HTML or TXT for text output
    private $goBack;  // create a link to return back to the sending page
    private $mWidth;  // width of the message body <div>
    
    function __construct($msg=NULL,$msgType="",$msgOutputStream="",$goBack=FALSE,$msgWidth="400") {
        
        if ($msgType == ""){
            $msgType = "ME";
        }
        
        if ($msgOutputStream == ""){
            $msgOutputStream = "HTML";
        }
        
        if ($msgWidth == ""){
            $msgWidth = "400";
        }
        
        $this->setMessage($msg,$msgType, $msgOutputStream, $goBack,$msgWidth);
    }  // constructor {ends}
    
    
    public function __get($name) {
        return $this->$name;
    }  // __get {ends}
    
    protected function setReturn(){
        $action = $this->goBack;
        if ($action != ""){
            $html = "<p><a href='$action'>Return</a><p>";
            return ($html);
        } else {
            return FALSE;
        }
    }  // setReturn {ends}
    
    public function setMessage($msg,$msgType,$msgOutputStream,$goBack,$msgWidth){
        /* $type can be Message (ME), Warning (WA), Error (ER) or Database (DB)
        * Authentication Error (AE), Success (OK)
        */
        $this->type = $msgType;
        $this->message = $msg;
        $this->msgOutputStream = $msgOutputStream;
        $this->goBack = $goBack;
        $this->mWidth = $msgWidth;
    }  // function setMessage() [ends]

    public function showMessage(){
        $legend = "Important";
        if ($this->type == "ME"){
            $legend = "Message";
        } else if ($this->type == "WA") {
            $legend = "Warning";
        } else if ($this->type == "ER"){
            $legend = "Error";
        } else if ($this->type == "DB"){
            $legend = "Database Error";
        } else if ($this->type == "AE") {
            $legend = "Authentication Error";
        } else if ($this->type == "OK"){
            $legend ="SUCCESS";
        }

        /* prepare message for HTML or TXT */
        if ($this->msgOutputStream == "HTML"){
            $mesg ='
            <html>
            <head><title>Message</title>
            <link rel="stylesheet" type="text/css" href="api/core.css"/>
            </head>
            <body class="errors">
            <div align="center">
            <div class="message"  id="'.$this->type.'" style="width:'.$this->mWidth.'px">
            <div id="message-title">&nbsp;&nbsp;&nbsp;&nbsp;'.$legend.'</div>
            <p>'.$this->message.'</p> 
            <p>&nbsp;</p>
            <span>%s</span>
            </div>
            </div>
            </body>
            </html>';
            printf($mesg, $this->setReturn());
            printf("
            </body>
            </html>");
            exit();
        } else if ($this->msgOutputStream == "TXT"){
            $mesg = $this->message;
            echo $mesg;
            printf("
            </body>
            </html>");
            exit();
        } else if($this->msgOutputStream == "DIV") {
            $mesg = '
            <div align="center">
            <div class="message"  id="'.$this->type.'" style="width:'.$this->mWidth.'px">
            <div id="message-title">&nbsp;&nbsp;&nbsp;&nbsp;'.$legend.'</div>
            <p>'.$this->message.'</p> 
            <p>&nbsp;</p>
            <span>%s</span>
            </div>
            </div> <!-- align center {ends} -->';
            printf($mesg, $this->setReturn());
            printf("
            </body>
            </html>");
            exit();
        }   
    } // function showMessage() {ends}
} // class Message {ends}
?>
