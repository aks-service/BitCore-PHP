<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class InputStream {
    /**
     * The string data we're parsing.
     */
    private $data;

    /**
     * The current integer byte position we are in $data
     */
    private $char;

    /**
     * Length of $data; when $char === $data, we are at the end-of-file.
     */
    private $EOF;

    /**
     * Parse errors.
     */
    public $errors = array();

    /**
     * @param $data Data to parse
     */
    public function __construct($data) {
        $this->data = $data;
        $this->char = 0;
        $this->EOF  = strlen($data);
    }

    /**
     * Returns the current line that the tokenizer is at.
     */
    public function getCurrentLine() {
        // Check the string isn't empty
        if($this->EOF) {
            // Add one to $this->char because we want the number for the next
            // byte to be processed.
            return substr_count($this->data, "\n", 0, min($this->char, $this->EOF)) + 1;
        } else {
            // If the string is empty, we are on the first line (sorta).
            return 1;
        }
    }

    /**
     * Returns the current column of the current line that the tokenizer is at.
     */
    public function getColumnOffset() {
        // strrpos is weird, and the offset needs to be negative for what we
        // want (i.e., the last \n before $this->char). This needs to not have
        // one (to make it point to the next character, the one we want the
        // position of) added to it because strrpos's behaviour includes the
        // final offset byte.
        $lastLine = strrpos($this->data, "\n", $this->char - 1 - strlen($this->data));

        // However, for here we want the length up until the next byte to be
        // processed, so add one to the current byte ($this->char).
        if($lastLine !== false) {
            $findLengthOf = substr($this->data, $lastLine + 1, $this->char - 1 - $lastLine);
        } else {
            $findLengthOf = substr($this->data, 0, $this->char);
        }

        // Get the length for the string we need.
        if(extension_loaded('iconv')) {
            return iconv_strlen($findLengthOf, 'utf-8');
        } elseif(extension_loaded('mbstring')) {
            return mb_strlen($findLengthOf, 'utf-8');
        } elseif(extension_loaded('xml')) {
            return strlen(utf8_decode($findLengthOf));
        } else {
            $count = count_chars($findLengthOf);
            // 0x80 = 0x7F - 0 + 1 (one added to get inclusive range)
            // 0x33 = 0xF4 - 0x2C + 1 (one added to get inclusive range)
            return array_sum(array_slice($count, 0, 0x80)) +
                   array_sum(array_slice($count, 0xC2, 0x33));
        }
    }

    /**
     * Retrieve the currently consume character.
     * @note This performs bounds checking
     */
    public function char() {
        return ($this->char++ < $this->EOF)
            ? $this->data[$this->char - 1]
            : false;
    }
    
    public function nchar() {
        return ($this->char + 1 < $this->EOF)
            ? $this->data[$this->char + 1]
            : false;
    }
    /**
     * Get all characters until EOF.
     * @note This performs bounds checking
     */
    public function remainingChars() {
        if($this->char < $this->EOF) {
            $data = substr($this->data, $this->char);
            $this->char = $this->EOF;
            return $data;
        } else {
            return false;
        }
    }

    /**
     * Matches as far as possible until we reach a certain set of bytes
     * and returns the matched substring.
     * @param $bytes Bytes to match.
     */
    public function charsUntil($bytes, $max = null) {
        if ($this->char < $this->EOF) {
            if ($max === 0 || $max) {
                $len = strcspn($this->data, $bytes, $this->char, $max);
            } else {
                $len = strcspn($this->data, $bytes, $this->char);
            }
            $string = (string) substr($this->data, $this->char, $len);
            $this->char += $len;
            return $string;
        } else {
            return false;
        }
    }

    /**
     * Matches as far as possible with a certain set of bytes
     * and returns the matched substring.
     * @param $bytes Bytes to match.
     */
    public function charsWhile($bytes, $max = null) {
        if ($this->char < $this->EOF) {
            if ($max === 0 || $max) {
                $len = strspn($this->data, $bytes, $this->char, $max);
            } else {
                $len = strspn($this->data, $bytes, $this->char);
            }
            $string = (string) substr($this->data, $this->char, $len);
            $this->char += $len;
            return $string;
        } else {
            return false;
        }
    }

    /**
     * Unconsume one character.
     */
    public function unget() {
        if ($this->char <= $this->EOF) {
            $this->char--;
        }
    }
}