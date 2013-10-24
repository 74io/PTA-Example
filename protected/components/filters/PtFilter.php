<?php
class PtFilter
{
	
	/**
	 * Strips special characters from a string
	 * @param string $attribute The string to be stripped
	 * @return string
	 */
	public function stripSpecialChars($attribute)
	{
		
	    $attribute = utf8_decode(trim($attribute));
	    $attribute = str_replace(array("¬",
	                                "`",
	                                "!",
	                                "\"",
	                                "£",
	                                "$",
	                                "%",
	                                "^",
	                                "&",
	                                "*",
	                                "(",
	                                ")",
	                                "_",
	                                "-",
	                                "+",
	                                "=",
	                                "{",
	                                "}",
	                                "[",
	                                "]",
	                                ":",
	                                ";",
	                                "@",
	                                "'",
	                                "~",
	                                "#",
	                                "<",
	                                ">",
	                                ",",
	                                ".",
	                                "?",
	                                "/",
	                                "|",
	                                "\\"),"", $attribute);
	                                return $attribute;    
		
	}
	
	/**
	 * Strips html tags and encodes the string
	 * @param string $attribute The string to be stripped
	 * @return string
	 */
	public function stripHtml($attribute)
	{
		return CHtml::encode(strip_tags($attribute));
	}
	
	/**
	 * Strip all sepcial chars accept -_/()
	 * @param string $attribute The string to be stripped
	 * @return string
	 */
	public function stripSpecialCharsRelaxed($attribute)
	{
		$attribute = utf8_decode(trim($attribute));
	    $attribute = str_replace(array("¬",
	                                "`",
	                                "!",
	                                "\"",
	                                "£",
	                                "$",
	                                "%",
	                                "^",
	                                "&",
	                                "+",
	                                "=",
	                                "{",
	                                "}",
	                                "[",
	                                "]",
	                                ":",
	                                ";",
	                                "@",
	                                "'",
	                                "~",
	                                "#",
	                                "<",
	                                ">",
	                                ",",
	                                ".",
	                                "?",
	                                "|",
	                                "\\"),"", $attribute);
	                                return $attribute;  
		
	}
}
