<?

// class mime_mail 1.0 [redaxo]
// 
// erstellt 01.12.2001
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus


// todo:
// mime felder -> leerzeilen entfernen

class mime_mail
{
		
	var $parts;
	var $to;
	var $from;
	var $cc;
	var $bcc;
	var $headers;
	var $subject;
	var $body;
	
	function mime_mail()
	{
		$this->parts = array();
		$this->to = "";
		$this->from = "";
		$this->cc = "";
		$this->bcc = "";
		$this->subject = "";
		$this->body = "";
		$this->headers = "";
	}
	
	function add_attachment($message, $name = "", $ctype = "application/octet-stream")
	{
		$this->parts[] = array ("ctype" => $ctype, "message" => $message, "encode" => $encode, "name" => $name );
	}
	
	function build_message($part)
	{
		$message = $part["message"];
	
		// ohne text/plain unterscheidung
		if ($part["ctype"]=="body")
		{			
			return "Content-Type: text/plain; charset=\"iso-8859-1\""."\nContent-Transfer-Encoding: 7bit\n\n$message\n\n";
		}else
		{
			$message = chunk_split(base64_encode($message));
			$encoding = "base64";
			return "Content-Type: ".$part["ctype"]."; name=\"".$part["name"]."\""."\nContent-Transfer-Encoding: $encoding\nContent-Disposition: attachment\n\n$message\n";
		}
	}
	
	function build_multipart() 
	{
		$boundary = "prozer".md5(uniqid(time()));
		$this->boundary = $boundary;
		$multipart = "Content-Type: multipart/mixed; boundary=\"----$boundary\"\n\nThis is a MIME encoded message.\n\n------$boundary";
	
		for($i = sizeof($this->parts)-1; $i >= 0; $i--) 
		{
			$multipart .= "\n".$this->build_message($this->parts[$i])."------$boundary";
		}
	
		return $multipart.= "--\n";
	}
	
	function prepare() 
	{
		$this->mime = "";

		if (!empty($this->from)) $this->mime .= "From: ".$this->from."\n";
		if (!empty($this->cc)) $this->mime .= "CC: ".$this->cc."\n";
		if (!empty($this->bcc)) $this->mime .= "Bcc: ".$this->bcc."\n";
		if (!empty($this->headers)) $this->mime .= $this->headers."\n";
		if (!empty($this->body)) $this->add_attachment($this->body, "", "body");

		$this->mime .= "MIME-Version: 1.0\n".$this->build_multipart();
	}
		
	function send()
	{
		$mail_content_a = explode("------".$this->boundary,$this->mime);
		// echo nl2br(htmlentities($this->mime));
		// $mail_send = mail($this->to, $this->subject,"",$this->mime,"-f".$this->from);
		$mail_send = mail($this->to, $this->subject,"",$this->mime);
	}
	
};

?>