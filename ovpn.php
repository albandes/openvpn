<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 

require 'vendor/autoload.php';


/**
 * ovpn
 *
 * Used to send e-mail with the data of active connections
 *
 * @author  Rogério Albandes <rogerio.albandes@gmail.com>
 * @version 0.1
 * @package henry
 * @example example.php
 * @link    https://github.com/albandes/ovpn
 * @license GNU License
 *
 */
 
class ovpn
{

    /**
     * File openvpn status
     *
     * @var string
     */
    private $_statusFile;

    /**
     * Timezone
     *
     * @var string
     */
    private $_timezone;

    /**
     * Date format
     *
     * @var string
     */
    private $_dateFormat ;
        
    /**
     * Smtp Host
     *
     * @var string
     */
    private $_smtpHost ;    
    
    /**
     * Smtp User
     *
     * @var mixed
     */
    private $_smtpUser ;    
    
    /**
     * Smtp Password
     *
     * @var string
     */
    private $_smtpPassword;  
    
        
    /**
     * _smtpPort
     *
     * @var mixed
     */
    private $_smtpPort;
    
    /**
     * Smtp From Addrees
     *
     * @var mixed
     */
    private $_smtpFrom;   
        
    /**
     * _smtpFromName
     *
     * @var mixed
     */
    private $_smtpFromName;
    
    /**
     * Smtp Address
     *
     * @var mixed
     */
    private $_smtpAddress;

    public function __construct ($statusFile='',$timezone='',$dateFormat='')
    {
        $this->_statusFile = $statusFile;
        $this->_timezone = $timezone;
        $this->_dateFormat = $dateFormat;

    }
    
    function display()
    {
        echo $this->_statusFile . "<br />";    
        echo $this->_timezone . "<br />";
        echo $this->_dateFormat . "<br />";
    }    

    /**
     * Connect to equipment
     * Creates the socket
     *
     * @return string|true Error message or true if connect
     *
     */
    public function getStatusData()
    {

        date_default_timezone_set($this->_timezone);
        $file = fopen($this->_statusFile, 'r');

        while (($line = fgetcsv($file)) !== false)
        {
          $data[] = $line;
        }
        fclose($file);
        
        $aRet = array();
        $i=0;
        foreach ($data as $row) {
            
            if($row[0] == 'ROUTING_TABLE'){
                $ip = $row[1];
                $user = $row[2];
                $sourceIp = explode(":", $row[3])[0];
                $connectedSince = date($this->_dateFormat, $row[5]);
                
                $aRet[$i]['ip'] = $ip; 
                $aRet[$i]['user'] = $user; 
                $aRet[$i]['sourceIp'] = $sourceIp;
                $aRet[$i]['connectedSince'] = $connectedSince;
                $i++;
            }
            
        }

        return $aRet;
    }


    public function sendEmail($arrayData, $subject)
    {
        echo $this->makeBody($arrayData);

        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->_smtpHost;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $this->_smtpUser;                     //SMTP username
            $mail->Password   = $this->_smtpPassword;                               //SMTP password
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom($this->_smtpFrom, 'Mailer');

            foreach ($this->_smtpAddress as $row) {
                $mail->addAddress($row);     
            }
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
            $mail->Body    = $this->makeBody($arrayData);
                    
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }        
    }

    public function makeBody($arrayData)
    {
        $body = '<b>Active connections:</b><br>';
        foreach ($arrayData as $row) {
            $body .= 'Ip: ' . $row['ip'] . '<br>';
            $body .= 'User: ' . $row['user'] . '<br>';
            $body .= 'Source Ip: ' . $row['sourceIp'] . '<br>';
            $body .= 'Connected Since: ' . $row['connectedSince'] . '<br><br>';
            
        } 
        return $body;   
    }


    /**
     * Set the value of _smtpHost
     *
     * @return  self
     */ 
    public function set_smtpHost($_smtpHost)
    {
        $this->_smtpHost = $_smtpHost;

        return $this;
    }

    /**
     * Set the value of _smtpUser
     *
     * @return  self
     */ 
    public function set_smtpUser($_smtpUser)
    {
        $this->_smtpUser = $_smtpUser;

        return $this;
    }

    /**
     * Set the value of _smtpPassword
     *
     * @return  self
     */ 
    public function set_smtpPassword($_smtpPassword)
    {
        $this->_smtpPassword = $_smtpPassword;

        return $this;
    }

    /**
     * Set the value of _smtpFrom
     *
     * @return  self
     */ 
    public function set_smtpFrom($_smtpFrom)
    {
        $this->_smtpFrom = $_smtpFrom;

        return $this;
    }

    /**
     * Set the value of _smtpAddress
     *
     * @return  self
     */ 
    public function set_smtpAddress($_smtpAddress)
    {
        $this->_smtpAddress = $_smtpAddress;

        return $this;
    }

    /**
     * Set file openvpn status
     *
     * @param  string  $_statusFile  File openvpn status
     *
     * @return  self
     */ 
    public function set_statusFile(string $_statusFile)
    {
        $this->_statusFile = $_statusFile;

        return $this;
    }

    /**
     * Set timezone
     *
     * @param  string  $_timezone  Timezone
     *
     * @return  self
     */ 
    public function set_timezone(string $_timezone)
    {
        $this->_timezone = $_timezone;

        return $this;
    }

    /**
     * Set date format
     *
     * @param  string  $_dateFormat  Date format
     *
     * @return  self
     */ 
    public function set_dateFormat(string $_dateFormat)
    {
        $this->_dateFormat = $_dateFormat;

        return $this;
    }


    /**
     * Set _smtpPort
     *
     * @param  mixed  $_smtpPort  _smtpPort
     *
     * @return  self
     */ 
    public function set_smtpPort($_smtpPort)
    {
        $this->_smtpPort = $_smtpPort;

        return $this;
    }

    /**
     * Set _smtpFromName
     *
     * @param  mixed  $_smtpFromName  _smtpFromName
     *
     * @return  self
     */ 
    public function set_smtpFromName($_smtpFromName)
    {
        $this->_smtpFromName = $_smtpFromName;

        return $this;
    }
}
    