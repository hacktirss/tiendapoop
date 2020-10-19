<?php

require_once("phpmailer/PHPMailer.php");
require_once("data/SmtpDAO.php");

class MailSender {

    /**
     * 
     * @param CiasVO $sucursal
     * @param CliVO $cliente
     * @param string $uuid
     * @param string $xml
     * @param string $pdf
     * @param string $message
     * @return boolean
     */
    public static function send($sucursal, $cliente, $folio, $uuid, $xml, $pdf, $message = "") {

        try {
            error_log("Enviando CFDI a " . $cliente->getCorreo());

            $smtp = SmtpDAO::active();
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP(true);
            $mail->isHTML(TRUE);
            $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
            $mail->Debugoutput = 'error_log';
            $mail->Host = $smtp->getServer();
            $mail->Port = $smtp->getPort();

            if ($smtp->getAuth()=="true") {
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $smtp->getAuthtype();
                $mail->SMTPOptions = array (
                            'ssl' => array(
                            'verify_peer'  => false,
                            'verify_peer_name'  => false,
                            'allow_self_signed' => true));
            }

            $mail->Username = $smtp->getLoginuser();
            $mail->Password = $smtp->getLoginpass();

            //Attach an image file
            $mail->AddStringAttachment($pdf, $uuid . ".pdf", "base64", "application/pdf");
            $mail->AddStringAttachment($xml, $uuid . ".xml", "base64", "application/xml");

            $mail->ContentType = 'multipart/mixed';

            $mail->Subject = "Envío de Factura Electrónica " . $folio;

            $mail->Body = "Estimado <b>" . $cliente->getNombre() . "</b>:<br /><br />"
                    . "Le estamos enviando por este medio el <b>CFDI Comprobante Fiscal Digital (Factura Electr&oacute;nica) </b> "
                    . "correspondiente a su consumo con <b>" . $sucursal->getNombre() . "</b>"
                    . "<br /><br />Nos ponemos a sus ordenes para cualquier aclaracion al respecto.<br>"
                    . "<br/>----------------------------------------------------------------------------------------------------------------------------------<br/>"
                    . "Sistema de Facturación Electrónica / DETI Desarrollo y Transferencia de Informática S.A. de C.V. / detisa.com.mx";
            $mail->AltBody = "Estimado " . $cliente->getNombre() . ":"
                    . "\r\n\r\nLe estamos enviando por este medio el CFDI Comprobante Fiscal Digital (Factura Electr&oacute;nica) correspondiente a su consumo con " . $sucursal->getNombre()
                    . "\r\nNos ponemos a sus ordenes para cualquier aclaracion al respecto."
                    . "\r\n----------------------------------------------------------------------------------------------------------------------------------\r\n"
                    . "Sistema de Facturación Electrónica / DETI Desarrollo y Transferencia de Informática S.A. de C.V. / detisa.com.mx";

            if (!empty($message)) {
                $mail->Body .= "<br/><br/>PS: " . $message . "</b></i>";
                $mail->AltBody .= "\r\n\r\nPS: " . $message;
            }
            //Set who the message is to be sent from
            $mail->SetFrom($smtp->getSender(), 'Facturación Electrónica Detisa');
            $mail->AddAddress($cliente->getCorreo(), $cliente->getNombre());
            $sended = $mail->Send();
            error_log($mail->ErrorInfo);
            return $sended;
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
