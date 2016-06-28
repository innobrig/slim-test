<?php

namespace App\Util;


class CMSUtil
{
    public static function _varDump ($data, $recursionLevel=10)
    {
        $text = '';

        if (isset ($data)) {
            if (is_array($data) || is_object($data)) {
                $datatype = gettype($data);
                if (count ($data)) {
                    $text .= "<ol>\n";

                    foreach ($data as $key => $value)  {
                        $type = gettype($value);

                        if ($type == 'array' || ($type == 'object' && get_object_vars($value))) {
                            if ($recursionLevel < 100) {
                                $text .= sprintf ("<li>(%s) <strong>%s</strong>:\n", $type, $key);
                                $text .= self::_varDump ($value, $recursionLevel+1);
                                $text .= '</li>';
                            } else {
                                $text .= sprintf ("<li>more ...</li>");
                            }

                        } elseif (preg_match('/function/i', $type)) {
                             $text .= sprintf ("<li>(%s) <strong>%s</strong> </li>\n", $type, $key, $value);
                             // There doesn't seem to be anything traversable inside functions.
                        } else {
                            if (!isset($value)) {
                                $value='(none)';
                            }

                            if (is_object($value)) {
                                $value = gettype($value);

                            } elseif (is_bool($value)) {
                                $value = (int)$value;
                            }

                            if ($datatype == 'array') {
                                $text .= sprintf ("<li>(%s) <strong>%s</strong> = %s</li>\n", $type, $key, static::safeText($value));

                            } elseif ($datatype == 'object') {
                                $text .= sprintf ("<li>(%s) <strong>%s</strong> -> %s</li>\n", $type, $key, static::safeText($value));
                            }
                        }
                    }

                    $text .= "</ol>\n";
                } else {
                    $text .= '(empty)';
                }
            } else {
                if ($data === false) {
                    $text .= 'false';
                } elseif ($data === null) {
                    $text .= 'null';
                } else {
                    $text .= $data;
                }
            }
        }
        return $text;
    }


    public static function varDump ($data)
    {
        $text  = '<div style="text-align:left;">';
        $text .= self::_varDump($data, 0);
        $text .= '</div>';
        print ($text);
    }


    public static function createAssocArray ($array, $keyField)
    {
        $assoc = array();

        foreach ($array as $v) {
            $assoc[$v[$keyField]] = $v;
        }

        return $assoc;
    }


    public static function formatPermalink ($var)
    {
        // replace all chars $permasearch with the one in $permareplace
        $permasearch = explode(',', "À,Á,Â,Ã,Å,à,á,â,ã,å,Ò,Ó,Ô,Õ,Ø,ò,ó,ô,õ,ø,È,É,Ê,Ë,è,é,ê,ë,Ç,ç,Ì,Í,Î,Ï,ì,í,î,ï,Ù,Ú,Û,ù,ú,û,ÿ,Ñ,ñ,ß,ä,Ä,ö,Ö,ü,Ü");
        $permareplace = explode(',', "A,A,A,A,A,a,a,a,a,a,O,O,O,O,O,o,o,o,o,o,E,E,E,E,e,e,e,e,C,c,I,I,I,I,i,i,i,i,U,U,U,u,u,u,y,N,n,ss,ae,Ae,oe,Oe,ue,Ue");
        foreach ($permasearch as $key => $value) {
            $var = mb_ereg_replace ($value, $permareplace[$key], $var);
        }

        $var = preg_replace ("#(\s*\/\s*|\s*\+\s*|\s+)#", '-', strtolower($var));

        // final clean
        $permalinksseparator = '-';
        $var = mb_ereg_replace ("[^a-z0-9_{$permalinksseparator}]", '', $var, "imsr");
        $var = preg_replace ('/'.$permalinksseparator.'+/', $permalinksseparator, $var); // remove replicated separator
        $var = trim ($var, $permalinksseparator);

        return $var;
    }


    public static function getSelectorGeneric ($name, $values, $selectedValue=0, $defaultValue=0, $defaultText='', $allValue=0, $allText='',
                                               $cssClass='', $submit=false, $onChange='', $disabled=false, $multipleSize=1, $doOptgroup=false)
    {
        if (!$name) {
            return static::registerError('Invalid [name] received');
        }

        $id           = strtr($name, '[]', '__');
        $disabled     = $disabled ? 'disabled="disabled"' : '';
        $multiple     = $multipleSize > 1 ? 'multiple="multiple"' : '';
        $multipleSize = $multipleSize > 1 ? "size=\"$multipleSize\"" : '';
        $trigger      = $submit ? 'onChange="this.form.submit();"' : '';
        $trigger      = $onChange ? "onChange=\"$onChange\"" : $trigger;

        $classes = array();
        $classes[] = 'form-control';
        if ($cssClass) {
            $classes[] = $cssClass;
        }

        $class = 'class="' . implode (' ', $classes) . '"';

        $html = "<select name=\"$name\" id=\"$id\" $multipleSize $multiple $trigger $disabled $class>";

        if ($defaultText && !$selectedValue) {
            $sel = ((string) $defaultValue == (string) $selectedValue ? 'selected="selected"' : '');
            $html .= "<option value=\"$defaultValue\" $sel>$defaultText</option>";
        }

        if ($allText) {
            $sel = ((string) $allValue == (string) $selectedValue ? 'selected="selected"' : '');
            $html .= "<option value=\"$allValue\" $sel>$allText</option>";
        }

        foreach ($values as $k => $v) {
            if ($doOptgroup && strpos($v, 'OPTGROUP_START ') === 0) {
                $v     = str_replace ('OPTGROUP_START ', '', $v);
                $html .= "<optgroup label=\"$v\">";
            } elseif ($doOptgroup && strpos($v, 'OPTGROUP_END') === 0) {
                $v     = str_replace ('OPTGROUP_END ', '', $v);
                $html .= "</optgroup>";
            } else {
                $sel = ((string) $selectedValue == (string) $k ? 'selected="selected"' : '');
                $html .= "<option value=\"$k\" $sel>" . static::safeText($v) . '</option>';
            }
        }

        $html .= '</select>';

        return $html;

    }


    public static function safeText ($text)
    {
        $filter = FILTER_SANITIZE_STRING;
        $args   = array('flags'=>FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
        $value  = filter_var($text, $filter, $args);

        return $value;
    }


    public static function truncateString ($string, $maxlen, $appendIfLonger='...')
    {
        if (strlen($string) > $maxlen) {
            $string = substr($string, 0, $maxlen) . $appendIfLonger;
        }

        return $string;
    }

}

